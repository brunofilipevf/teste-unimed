# KATA 2 - DECISÕES DE ENGENHARIA

## Parte D - Respostas Obrigatórias

### 1. Decisões de arquitetura no backend

**Estrutura adotada:**

- **`api.php`** — ponto único de entrada (Front Controller), responsável por rotear as requisições.
- **`Task.php`** — entidade que representa uma tarefa.
- **`TaskRepository.php`** — camada de persistência, responsável por abstrair o acesso ao arquivo JSON.

**Separação de responsabilidades:**

- `api.php` lida exclusivamente com HTTP (request/response).
- `TaskRepository` concentra leitura e escrita dos dados.
- `Task` mantém apenas a estrutura da entidade.

Essa divisão reduz acoplamento e permite evoluir a persistência (ex: migrar de JSON para SQLite ou MySQL) sem impactar a lógica principal.

---

### 2. Garantia de confiabilidade em produção

**Tratamento de erros:**

- Operações de leitura e escrita protegidas com `try/catch`.
- Respostas padronizadas em JSON, com campo `error` e uso adequado de códigos HTTP (400, 404, 500).

**Observabilidade:**

- Em ambiente produtivo, seria adicionado logging estruturado (arquivo ou serviço externo).
- Monitoramento de tempo de resposta e taxa de erro por endpoint.

Essas medidas permitem identificar falhas rapidamente e manter previsibilidade no comportamento da API.

---

### 3. Suporte a múltiplos usuários com autenticação

**Mudanças necessárias:**

1. Substituir JSON por banco relacional (SQLite ou MySQL).
2. Criar tabela `users` (id, nome, email, senha_hash).
3. Adicionar `user_id` na entidade `tasks`.
4. Implementar autenticação (JWT ou sessão).
5. Introduzir middleware para validação de autenticação.
6. Restringir acesso às tarefas pelo `user_id` autenticado.

**Impacto na arquitetura atual:**

Baixo. A separação entre camada HTTP, domínio e persistência permite introduzir autenticação e multiusuário sem reestruturar o sistema. A principal mudança se concentra na persistência e na camada de entrada (middleware).
