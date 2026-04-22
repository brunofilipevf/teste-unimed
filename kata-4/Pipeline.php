<?php

declare(strict_types=1);

class Pipeline
{
    private array $clientes = [];
    private array $pedidos = [];
    private array $entregas = [];
    private array $consolidado = [];

    public function carregar(string $dir): void
    {
        $this->clientes = $this->lerCsv($dir . '/clientes.csv');
        $this->pedidos = $this->lerCsv($dir . '/pedidos.csv');
        $this->entregas = $this->lerCsv($dir . '/entregas.csv');
    }

    private function lerCsv(string $arquivo): array
    {
        if (!file_exists($arquivo)) {
            return [];
        }
        
        $dados = [];
        $cabecalho = null;
        
        if (($handle = fopen($arquivo, 'r')) !== false) {
            while (($linha = fgetcsv($handle)) !== false) {
                if ($cabecalho === null) {
                    $cabecalho = $linha;
                    continue;
                }
                $dados[] = array_combine($cabecalho, $linha);
            }
            fclose($handle);
        }

        return $dados;
    }

    private function normalizarData(string $data): ?string
    {
        if (empty($data)) {
            return null;
        }

        $formatos = ['Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'd/m/Y H:i:s', 'd-m-Y', 'd-m-Y H:i:s'];

        foreach ($formatos as $formato) {
            $dt = DateTime::createFromFormat($formato, $data);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }

    private function normalizarValor(string $valor): float
    {
        if (empty($valor)) {
            return 0.0;
        }

        // Remove tudo exceto números, vírgula e ponto
        $valor = preg_replace('/[^0-9,.-]/', '', $valor);
        
        // Se tem vírgula e ponto, vírgula é separador decimal
        if (strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }

        // Se só tem vírgula, é separador decimal
        elseif (strpos($valor, ',') !== false) {
            $valor = str_replace(',', '.', $valor);
        }
        
        return (float) $valor;
    }

    private function normalizarCidade(string $cidade): string
    {
        $cidade = trim($cidade);
        $cidade = strtolower($cidade);
        $cidade = ucwords($cidade);

        // Mapeamento de grafias comuns
        $mapeamento = [
            'Caruaru'   => 'Caruaru',
            'Recife'    => 'Recife',
            'Olinda'    => 'Olinda',
            'Gravata'   => 'Gravatá',
            'Gravatá'   => 'Gravatá',
            'Petrolina' => 'Petrolina'
        ];

        // Corrige acentos e capitalização
        foreach ($mapeamento as $chave => $valor) {
            if (stripos($cidade, $chave) !== false) {
                return $valor;
            }
        }

        return $cidade;
    }

    public function processar(): void
    {
        // Indexa clientes por ID
        $clientesIndex = [];

        foreach ($this->clientes as $c) {
            $id = $c['id_cliente'] ?? null;
            if ($id) {
                $clientesIndex[$id] = $c;
            }
        }

        // Indexa entregas por pedido
        $entregasIndex = [];

        foreach ($this->entregas as $e) {
            $idPedido = $e['id_pedido'] ?? null;
            if ($idPedido) {
                $entregasIndex[$idPedido] = $e;
            }
        }

        // Processa pedidos
        foreach ($this->pedidos as $p) {
            $idPedido = $p['id_pedido'] ?? null;

            if (!$idPedido) {
                continue;
            }

            $cliente = $clientesIndex[$p['id_cliente']] ?? null;
            $entrega = $entregasIndex[$idPedido] ?? null;

            // Pula pedidos sem cliente (órfão)
            if (!$cliente) {
                continue;
            }

            $valorTotal = $this->normalizarValor($p['valor_total'] ?? '0');

            if ($valorTotal <= 0) {
                continue;
            }

            $dataPedido = $this->normalizarData($p['data_pedido'] ?? '');
            $dataPrevista = $entrega ? $this->normalizarData($entrega['data_prevista'] ?? '') : null;
            $dataRealizada = $entrega ? $this->normalizarData($entrega['data_realizada'] ?? '') : null;

            // Calcula atraso
            $atrasoDias = null;

            if ($dataPrevista && $dataRealizada) {
                $dtPrevista = new DateTime($dataPrevista);
                $dtRealizada = new DateTime($dataRealizada);
                $atrasoDias = $dtRealizada->diff($dtPrevista)->days;

                if ($dtRealizada < $dtPrevista) {
                    $atrasoDias = -$atrasoDias;
                }
            }

            $this->consolidado[] = [
                'id_pedido'              => $idPedido,
                'nome_cliente'           => $cliente['nome'] ?? '',
                'cidade_normalizada'     => $this->normalizarCidade($cliente['cidade'] ?? ''),
                'estado'                 => strtoupper($cliente['estado'] ?? ''),
                'valor_total'            => $valorTotal,
                'status_pedido'          => $p['status'] ?? '',
                'data_pedido'            => $dataPedido,
                'data_prevista_entrega'  => $dataPrevista,
                'data_realizada_entrega' => $dataRealizada,
                'atraso_dias'            => $atrasoDias,
                'status_entrega'         => $entrega['status_entrega'] ?? 'SEM_ENTREGA'
            ];
        }
    }

    public function getConsolidado(): array
    {
        return $this->consolidado;
    }

    public function salvarConsolidado(string $arquivo): void
    {
        $dir = dirname($arquivo);

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $fp = fopen($arquivo, 'w');
        
        // Cabeçalho
        fputcsv($fp, array_keys($this->consolidado[0] ?? []));
        
        // Dados
        foreach ($this->consolidado as $linha) {
            fputcsv($fp, $linha);
        }
        
        fclose($fp);
    }

    public function calcularIndicadores(): array
    {
        $totalPedidos = count($this->consolidado);

        if ($totalPedidos === 0) {
            return [];
        }

        // Por status
        $porStatus = [];

        foreach ($this->consolidado as $p) {
            $status = $p['status_pedido'];
            $porStatus[$status] = ($porStatus[$status] ?? 0) + 1;
        }

        // Ticket médio por estado
        $ticketPorEstado = [];
        $contagemPorEstado = [];

        foreach ($this->consolidado as $p) {
            $estado = $p['estado'];
            $ticketPorEstado[$estado] = ($ticketPorEstado[$estado] ?? 0) + $p['valor_total'];
            $contagemPorEstado[$estado] = ($contagemPorEstado[$estado] ?? 0) + 1;
        }

        foreach ($ticketPorEstado as $estado => $total) {
            $ticketPorEstado[$estado] = $total / $contagemPorEstado[$estado];
        }

        // Entregas no prazo vs atraso
        $noPrazo = 0;
        $comAtraso = 0;
        $somaAtraso = 0;
        $contagemAtraso = 0;
        
        foreach ($this->consolidado as $p) {
            if ($p['atraso_dias'] === null) {
                continue;
            }

            if ($p['atraso_dias'] <= 0) {
                $noPrazo++;
            } else {
                $comAtraso++;
                $somaAtraso += $p['atraso_dias'];
                $contagemAtraso++;
            }
        }
        
        $totalEntregues = $noPrazo + $comAtraso;
        $percentualNoPrazo = $totalEntregues > 0 ? round(($noPrazo / $totalEntregues) * 100, 2) : 0;
        $percentualComAtraso = $totalEntregues > 0 ? round(($comAtraso / $totalEntregues) * 100, 2) : 0;
        $mediaAtraso = $contagemAtraso > 0 ? round($somaAtraso / $contagemAtraso, 2) : 0;

        // Top 3 cidades
        $cidades = [];

        foreach ($this->consolidado as $p) {
            $cidade = $p['cidade_normalizada'];
            $cidades[$cidade] = ($cidades[$cidade] ?? 0) + 1;
        }

        arsort($cidades);
        $topCidades = array_slice($cidades, 0, 3, true);

        return [
            'total_por_status'        => $porStatus,
            'ticket_medio_por_estado' => $ticketPorEstado,
            'percentual_no_prazo'     => $percentualNoPrazo,
            'percentual_com_atraso'   => $percentualComAtraso,
            'media_atraso_dias'       => $mediaAtraso,
            'top_3_cidades'           => $topCidades
        ];
    }
}
