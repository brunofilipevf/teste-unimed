<?php

declare(strict_types=1);

class Paciente
{
    private string $nome;
    private int $idade;
    private string $urgencia;
    private string $horarioChegada;

    public function __construct(string $nome, int $idade, string $urgencia, string $horarioChegada)
    {
        $this->nome = $nome;
        $this->idade = $idade;
        $this->urgencia = strtoupper($urgencia);
        $this->horarioChegada = $horarioChegada;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getIdade(): int
    {
        return $this->idade;
    }

    public function getUrgencia(): string
    {
        return $this->urgencia;
    }

    public function getHorarioChegada(): string
    {
        return $this->horarioChegada;
    }
}
