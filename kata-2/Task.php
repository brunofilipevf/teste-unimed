<?php

declare(strict_types=1);

class Task
{
    public ?int $id = null;
    public string $titulo;
    public string $status;

    public function __construct(string $titulo, string $status = 'pendente')
    {
        $this->titulo = $titulo;
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'id'     => $this->id,
            'titulo' => $this->titulo,
            'status' => $this->status
        ];
    }

    public static function fromArray(array $dados): self
    {
        $task = new self($dados['titulo'], $dados['status']);
        $task->id = $dados['id'] ?? null;

        return $task;
    }
}
