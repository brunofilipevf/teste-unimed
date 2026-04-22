<?php

declare(strict_types=1);

class TaskRepository
{
    private string $arquivo;
    private array $tasks = [];
    private int $proximoId = 1;

    public function __construct(string $arquivo = null)
    {
        $this->arquivo = $arquivo ?? __DIR__ . '/tasks.json';
        $this->carregar();
    }

    private function carregar(): void
    {
        if (!file_exists($this->arquivo)) {
            file_put_contents($this->arquivo, json_encode([]));
        }

        $conteudo = file_get_contents($this->arquivo);
        $dados = json_decode($conteudo, true) ?? [];

        foreach ($dados as $item) {
            $task = Task::fromArray($item);

            $this->tasks[$task->id] = $task;

            if ($task->id >= $this->proximoId) {
                $this->proximoId = $task->id + 1;
            }
        }
    }

    private function salvar(): void
    {
        $dados = array_map(fn(Task $t) => $t->toArray(), array_values($this->tasks));
        file_put_contents($this->arquivo, json_encode($dados, JSON_PRETTY_PRINT));
    }

    public function listar(?string $status = null): array
    {
        $tasks = array_values($this->tasks);

        if ($status) {
            $tasks = array_filter($tasks, fn(Task $t) => $t->status === $status);
        }

        usort($tasks, fn(Task $a, Task $b) => $b->id <=> $a->id);
        return $tasks;
    }

    public function buscar(int $id): ?Task
    {
        return $this->tasks[$id] ?? null;
    }

    public function criar(Task $task): Task
    {
        $task->id = $this->proximoId++;

        $this->tasks[$task->id] = $task;
        $this->salvar();

        return $task;
    }

    public function atualizar(Task $task): bool
    {
        if (!isset($this->tasks[$task->id])) {
            return false;
        }

        $this->tasks[$task->id] = $task;
        $this->salvar();

        return true;
    }

    public function deletar(int $id): bool
    {
        if (!isset($this->tasks[$id])) {
            return false;
        }

        unset($this->tasks[$id]);

        $this->salvar();
        return true;
    }
}
