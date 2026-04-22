<?php

declare(strict_types=1);

require_once __DIR__ . '/Pipeline.php';

if (!file_exists(__DIR__ . '/dados/pedidos.csv')) {
    require_once __DIR__ . '/gerarDados.php';
}

echo "========================================\n";
echo "KATA 4 - PIPELINE DE RELATÓRIO\n";
echo "========================================\n\n";

$pipeline = new Pipeline();
$pipeline->carregar(__DIR__ . '/dados');
$pipeline->processar();
$pipeline->salvarConsolidado(__DIR__ . '/saida/consolidado.csv');

echo "Pipeline executado.\n";
echo "Arquivo consolidado: /saida/consolidado.csv\n\n";
echo "========================================\n";
echo "INDICADORES\n";
echo "========================================\n\n";

$indicadores = $pipeline->calcularIndicadores();

echo "1. Total de pedidos por status:\n";

foreach ($indicadores['total_por_status'] as $status => $total) {
    echo "   - $status: $total\n";
}

echo "\n2. Ticket médio por estado:\n";

foreach ($indicadores['ticket_medio_por_estado'] as $estado => $ticket) {
    echo "   - $estado: R$ " . number_format($ticket, 2, ',', '.') . "\n";
}

echo "\n3. Entregas no prazo vs com atraso:\n";
echo "   - No prazo: {$indicadores['percentual_no_prazo']}%\n";
echo "   - Com atraso: {$indicadores['percentual_com_atraso']}%\n";
echo "\n4. Média de atraso: {$indicadores['media_atraso_dias']} dias\n";
echo "\n5. Top 3 cidades com maior volume:\n";

$pos = 1;

foreach ($indicadores['top_3_cidades'] as $cidade => $total) {
    echo "   $pos. $cidade: $total pedidos\n";
    $pos++;
}

echo "\n========================================\n";
echo "Fim do relatório\n";
echo "========================================\n";
