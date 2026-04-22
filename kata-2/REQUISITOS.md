# KATA 2 - ANÁLISE DE REQUISITOS

## Parte A - Ambiguidades e Decisões

### Ambiguidade 1: O que define uma tarefa "concluída"?

**Pergunta ao cliente:**
"Marcar como feita significa apenas mudar o status ou também registrar quando e por quem a tarefa foi concluída?"

**Decisão tomada:**
Foi mantido apenas um campo de status (`pendente` / `concluida`).
O registro de data e usuário foi descartado para manter a solução simples nesta fase.

---

### Ambiguidade 2: Tarefas deletadas são removidas permanentemente?

**Pergunta ao cliente:**
"A exclusão deve ser permanente ou precisamos permitir recuperação depois (soft delete)?"

**Decisão tomada:**
Exclusão física. O endpoint DELETE remove o registro definitivamente, evitando complexidade adicional de recuperação.

---

### Ambiguidade 3: O que é a "prioridade" mencionada?

**Pergunta ao cliente:**
"A prioridade será categórica (Alta/Média/Baixa)? Ela impacta a ordenação das tarefas?"

**Decisão tomada:**
O requisito foi adiado. Não será implementado nesta versão e foi movido para backlog.

---

### Ambiguidade 4: Existe limite de caracteres para o título?

**Pergunta ao cliente:**
"Qual o tamanho máximo permitido para o título?"

**Decisão tomada:**
Definido limite de 100 caracteres, com validação no backend para garantir consistência dos dados.

---

## Requisitos Funcionais (RF)

| Código | Descrição |
|--------|-----------|
| RF01 | Listar todas as tarefas cadastradas |
| RF02 | Filtrar tarefas por status (`pendente` / `concluida`) |
| RF03 | Criar nova tarefa com título |
| RF04 | Atualizar status de uma tarefa |
| RF05 | Excluir uma tarefa |
| RF06 | Validar título obrigatório e limite de 100 caracteres |

---

## Requisitos Não Funcionais (RNF)

| Código | Descrição |
|--------|-----------|
| RNF01 | Tempo de resposta inferior a 500ms |
| RNF02 | Persistência em arquivo JSON |
| RNF03 | Uso correto de códigos HTTP |
| RNF04 | Compatível com navegadores modernos |

---

## Tratamento do Backlog (Prioridade)

O requisito de prioridade foi explicitamente adiado.

**Ação tomada:**
Adicionado ao backlog como **RF07 - Adicionar prioridade às tarefas**.

A estrutura atual já permite incluir o campo `prioridade` futuramente sem necessidade de refatoração significativa.
