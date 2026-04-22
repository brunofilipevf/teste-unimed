<?php

declare(strict_types=1);

class TriagemTest
{
    private Triagem $triagem;
    private array $resultados;
    private int $testesExecutados;
    private int $testesPassados;

    public function __construct()
    {
        $this->triagem = new Triagem();
        $this->resultados = [];
        $this->testesExecutados = 0;
        $this->testesPassados = 0;
    }

    private function assertFila(array $esperado, array $resultado): bool
    {
        $nomesResultado = array_map(fn(Paciente $p) => $p->getNome(), $resultado);
        return $nomesResultado === $esperado;
    }

    private function executarTeste(string $descricao, array $pacientes, array $esperado): void
    {
        $this->testesExecutados++;

        $fila = $this->triagem->ordenarFila($pacientes);
        $passou = $this->assertFila($esperado, $fila);

        if ($passou) {
            $this->testesPassados++;
        }

        $this->resultados[] = [
            'descricao' => $descricao,
            'passou'    => $passou,
            'esperado'  => $esperado,
            'obtido'    => array_map(fn(Paciente $p) => $p->getNome(), $fila)
        ];
    }

    public function executarTodos(): void
    {
        $this->executarTeste('Regra 1 e 2 - Ordenação por urgência', [
                new Paciente('Ana', 30, 'BAIXA', '09:00'),
                new Paciente('Bruno', 25, 'CRITICA', '09:05'),
                new Paciente('Carla', 40, 'MEDIA', '08:55'),
                new Paciente('Diego', 35, 'ALTA', '09:10')
            ], [
                'Bruno',
                'Diego',
                'Carla',
                'Ana'
            ]
        );

        $this->executarTeste('Regra 3 - FIFO dentro do mesmo nível', [
                new Paciente('Ana', 30, 'ALTA', '09:10'),
                new Paciente('Bruno', 25, 'ALTA', '09:00'),
                new Paciente('Carla', 40, 'ALTA', '09:05')
            ], [
                'Bruno',
                'Carla',
                'Ana'
            ]
        );

        $this->executarTeste('Regra 4 - Idoso com MEDIA sobe para ALTA', [
                new Paciente('Jovem', 30, 'ALTA', '09:00'),
                new Paciente('Idoso', 65, 'MEDIA', '08:55'),
                new Paciente('Normal', 40, 'MEDIA', '08:50')
            ], [
                'Idoso',
                'Jovem',
                'Normal'
            ]
        );

        $this->executarTeste('Regra 5 - Menor de 18 ganha +1 nível', [
                new Paciente('Adulto', 30, 'MEDIA', '09:00'),
                new Paciente('Crianca', 10, 'MEDIA', '08:55')
            ], [
                'Crianca',
                'Adulto'
            ]
        );

        $this->executarTeste('Interação Regras 4 e 5 - Menor com MEDIA', [
                new Paciente('Adulto', 30, 'ALTA', '09:00'),
                new Paciente('Crianca', 15, 'MEDIA', '08:55')
            ], [
                'Crianca',
                'Adulto'
            ]
        );

        $this->executarTeste('Borda - Menor com ALTA vira CRITICA', [
                new Paciente('Adulto', 40, 'CRITICA', '09:10'),
                new Paciente('Crianca', 10, 'ALTA', '09:00')
            ], [
                'Crianca',
                'Adulto'
            ]
        );
    }

    public function getResultados(): array
    {
        return $this->resultados;
    }

    public function getTestesExecutados(): int
    {
        return $this->testesExecutados;
    }

    public function getTestesPassados(): int
    {
        return $this->testesPassados;
    }
}
