<?php

declare(strict_types=1);

require_once __DIR__ . '/Paciente.php';
require_once __DIR__ . '/Triagem.php';
require_once __DIR__ . '/TriagemTest.php';

echo "========================================\n";
echo "KATA 1 - SISTEMA DE TRIAGEM\n";
echo "========================================\n\n";

$test = new TriagemTest();
$test->executarTodos();

echo "----------------------------------------\n";
echo "Executando testes unitários\n";
echo "----------------------------------------\n\n";

foreach ($test->getResultados() as $r) {
    echo "Teste: {$r['descricao']}\n";
    if ($r['passou']) {
        echo "✓ OK\n\n";
    } else {
        echo "✗ FALHOU\n";
        echo "  Esperado: " . implode(', ', $r['esperado']) . "\n";
        echo "  Obtido:   " . implode(', ', $r['obtido']) . "\n\n";
    }
}

echo "----------------------------------------\n";
echo "Resultado: {$test->getTestesPassados()} de {$test->getTestesExecutados()} testes passaram\n";
echo "----------------------------------------\n\n";

echo "========================================\n";
echo "Exemplo final:\n";
echo "========================================\n\n";

$triagem = new Triagem();

$todos = [
    new Paciente('João', 70, 'MEDIA', '10:00'),
    new Paciente('Maria', 15, 'MEDIA', '09:55'),
    new Paciente('Pedro', 30, 'CRITICA', '10:10'),
    new Paciente('Ana', 25, 'ALTA', '10:05'),
    new Paciente('Lucas', 10, 'BAIXA', '09:50'),
];

$fila = $triagem->ordenarFila($todos);

echo "Fila ordenada:\n";

foreach ($fila as $i => $p) {
    $pos = $i + 1;
    echo "{$pos}. {$p->getNome()} ({$p->getIdade()} anos) - {$p->getUrgencia()} - {$p->getHorarioChegada()}\n";
}
