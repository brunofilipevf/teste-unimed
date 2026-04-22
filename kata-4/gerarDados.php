<?php

declare(strict_types=1);

$dir = __DIR__ . '/dados';

if (!is_dir($dir)) {
    mkdir($dir);
}

// pedidos.csv
$pedidos = [
    ['id_pedido', 'data_pedido', 'id_cliente', 'valor_total', 'status'],
    [1, '2026-01-10', 101, '150,50', 'ENTREGUE'],
    [2, '15/01/2026', 102, '89.90', 'ENTREGUE'],
    [3, '2026-01-20 14:30:00', 103, '1.200,00', 'CANCELADO'],
    [4, '22/01/2026', 104, '45,75', 'ENTREGUE'],
    [5, '2026-01-25', 105, '320,00', 'PENDENTE'],
    [6, '28/01/2026', 106, '', 'ENTREGUE'],
    [7, '2026-02-01', 101, '75,90', 'ENTREGUE']
];

$fp = fopen($dir . '/pedidos.csv', 'w');
foreach ($pedidos as $linha) fputcsv($fp, $linha);
fclose($fp);

// clientes.csv
$clientes = [
    ['id_cliente', 'nome', 'cidade', 'estado', 'data_cadastro'],
    [101, 'João Silva', 'Caruaru', 'PE', '2023-01-01'],
    [102, 'Maria Souza', 'recife', 'PE', '2023-02-15'],
    [103, 'Pedro Santos', 'Olinda', 'PE', '2023-03-10'],
    [104, 'Ana Oliveira', 'RECIFE', 'PE', '2023-04-20'],
    [105, 'Carlos Lima', 'Gravatá', 'PE', '2023-05-05'],
    [106, 'Julia Costa', 'petrolina', 'PE', '2023-06-12'],
];

$fp = fopen($dir . '/clientes.csv', 'w');
foreach ($clientes as $linha) fputcsv($fp, $linha);
fclose($fp);

// entregas.csv
$entregas = [
    ['id_entrega', 'id_pedido', 'data_prevista', 'data_realizada', 'status_entrega'],
    [1, 1, '2026-01-15', '2026-01-14', 'ENTREGUE'],
    [2, 2, '20/01/2026', '21/01/2026', 'ENTREGUE'],
    [3, 4, '2026-01-28', '', 'PENDENTE'],
    [4, 5, '30/01/2026', '', 'PENDENTE'],
    [5, 6, '2026-02-05', '2026-02-07', 'ENTREGUE'],
    [6, 7, '05/02/2026', '04/02/2026', 'ENTREGUE'],
    [7, 99, '2026-01-10', '2026-01-12', 'ENTREGUE']
];

$fp = fopen($dir . '/entregas.csv', 'w');
foreach ($entregas as $linha) fputcsv($fp, $linha);
fclose($fp);

echo "Arquivos gerados em /dados\n";
