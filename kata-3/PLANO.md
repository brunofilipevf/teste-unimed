# KATA 3 - PLANO DE AÇÃO TÉCNICO

## Seção 1 — Diagnóstico

| Problema | Causa provável | Risco | Classificação |
|----------|---------------|-------|---------------|
| Consulta lenta (8–12s) | Falta de índices e query ineficiente | Timeout e perda de vendas | **Urgente e importante** |
| Pedidos duplicados | Ausência de idempotência no endpoint | Cobrança duplicada e inconsistência de estoque | **Urgente e importante** |
| Deploy direto em produção | Falta de proteção na branch principal | Bugs em produção e ausência de rastreabilidade | **Importante, não urgente** |
| Arquivo com 4.000 linhas | Crescimento sem refatoração | Alto risco de regressão e baixa produtividade | **Importante, não urgente** |
| Ausência de testes automatizados | Falta de priorização histórica | Regressões frequentes | **Importante, não urgente** |

---

## Seção 2 — Plano de ação (Top 3)

### 1. Eliminar pedidos duplicados

| O quê | Esforço | Critério de sucesso |
|-------|--------|---------------------|
| Implementar idempotência no endpoint de criação de pedidos | 4h | Nenhuma duplicidade registrada em produção por 30 dias |

---

### 2. Otimizar consulta de pedidos

| O quê | Esforço | Critério de sucesso |
|-------|--------|---------------------|
| Criar índices adequados e aplicar paginação real | 8h | Tempo de resposta abaixo de 2s no P95 |

---

### 3. Controlar deploy em produção

| O quê | Esforço | Critério de sucesso |
|-------|--------|---------------------|
| Bloquear push direto e exigir PR com aprovação | 2h | 100% dos deploys realizados via PR em 15 dias |

---

## Seção 3 — Decisão de arquitetura

**Escolha:** Refatoração incremental

**Motivos:**

1. Sem testes, uma reescrita completa tende a reintroduzir erros já resolvidos.
2. O sistema está em produção e não tolera interrupções.
3. Permite evolução contínua dentro do fluxo normal de entregas.
4. Preserva regras de negócio acumuladas ao longo do tempo.

**Estratégia:**

Aplicar refatoração progressiva:

- Isolar pequenos trechos do código
- Criar testes antes da alteração
- Substituir gradualmente o código legado

O objetivo é reduzir o arquivo atual até que ele se torne apenas um ponto de orquestração.

---

## Seção 4 — Requisitos Não Funcionais ignorados

| RNF | Evidência | Métrica proposta |
|-----|-----------|------------------|
| **Desempenho** | Consultas entre 8–12s | P95 ≤ 2s |
| **Manutenibilidade** | Arquivo extenso e ausência de testes | Complexidade ciclomática ≤ 10, cobertura ≥ 60% |
| **Confiabilidade** | Pedidos duplicados e inconsistências | Taxa de erro ≤ 0,1% |
| **Rastreabilidade** | Deploy sem controle | 100% dos deploys vinculados a PR |
