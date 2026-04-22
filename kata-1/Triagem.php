<?php

declare(strict_types=1);

class Triagem
{
    private array $pesos;

    public function __construct()
    {
        $this->pesos = [
            'CRITICA' => 100,
            'ALTA'    => 75,
            'MEDIA'   => 50,
            'BAIXA'   => 25
        ];
    }

    private function ajustarNivel(Paciente $paciente): string
    {
        $nivel = $paciente->getUrgencia();
        $idade = $paciente->getIdade();

        if ($idade >= 60 && $nivel === 'MEDIA') {
            return 'ALTA';
        }

        if ($idade < 18) {
            if ($nivel === 'BAIXA') return 'MEDIA';
            if ($nivel === 'MEDIA') return 'ALTA';
            if ($nivel === 'ALTA') return 'CRITICA';
        }

        return $nivel;
    }

    private function calcularPeso(Paciente $paciente): int
    {
        $nivelAjustado = $this->ajustarNivel($paciente);
        return $this->pesos[$nivelAjustado];
    }

    private function compararPacientes(Paciente $a, Paciente $b): int
    {
        $pesoA = $this->calcularPeso($a);
        $pesoB = $this->calcularPeso($b);

        if ($pesoA !== $pesoB) {
            return $pesoB - $pesoA;
        }

        return strtotime($a->getHorarioChegada()) - strtotime($b->getHorarioChegada());
    }

    public function ordenarFila(array $pacientes): array
    {
        usort($pacientes, [$this, 'compararPacientes']);
        return $pacientes;
    }
}
