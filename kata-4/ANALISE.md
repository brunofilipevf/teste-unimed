# KATA 4 - ANÁLISE DO PIPELINE

## Parte C - Respostas Obrigatórias

### 1. Principais decisões de tratamento

| Problema | Decisão |
|----------|---------|
| Datas em formatos mistos | Tenta múltiplos formatos (`Y-m-d`, `d/m/Y`, `d-m-Y`) e fallback com `strtotime`. Retorna `null` se não for possível normalizar. |
| Valores com vírgula | Remove caracteres não numéricos e trata vírgula como separador decimal quando aplicável. Valores vazios são convertidos para `0.0`. |
| Campos obrigatórios nulos | Registros sem `id_cliente` ou com `valor_total` inválido são descartados. |
| Registros órfãos | Entregas sem pedido correspondente são ignoradas para manter consistência do consolidado. |
| Cidades inconsistentes | Normalização com `lowercase`, capitalização e mapeamento de variações conhecidas (ex: "sao paulo" → "São Paulo"). |

---

### 2. Idempotência do pipeline

**O pipeline é idempotente.**

Execuções repetidas com a mesma entrada geram exatamente o mesmo resultado.

**Justificativa:**

- Não há dependência de estado externo ou variáveis globais.
- Todas as transformações são determinísticas.
- A saída é sempre reescrita por completo, evitando acúmulo ou duplicação.

---

### 3. Pipeline com 10 milhões de linhas

**Limitação atual:**
A abordagem baseada em memória não escala para esse volume.

**Mudanças necessárias:**

| Aspecto | Atual | Escalável |
|---------|------|-----------|
| Leitura | Carregamento completo em memória | Streaming (linha a linha) |
| Processamento | Arrays em memória | Iteradores/geradores (`yield`) |
| Join de dados | Estruturas locais | Merge join em arquivos ordenados |
| Persistência intermediária | Não utilizada | SQLite ou arquivos indexados |
| Execução | Síncrona | Processamento em batch ou paralelo |

**Estratégia proposta:**

1. Ordenar previamente os arquivos por chave (`id_cliente`, `id_pedido`) usando ferramentas do sistema.
2. Aplicar **merge join em streaming**, evitando carregar tudo em memória.
3. Utilizar geradores (`yield`) para processar os dados incrementalmente.
4. Persistir estados intermediários apenas quando necessário (ex: grandes joins).

Essa abordagem reduz consumo de memória e mantém previsibilidade mesmo com alto volume.

---

### 4. Testes para garantir qualidade

| Tipo de teste | O que validar |
|---------------|--------------|
| **Unitário** | `normalizarData()` com formatos válidos, inválidos e limites |
| **Unitário** | `normalizarValor()` com diferentes formatos numéricos |
| **Unitário** | `normalizarCidade()` com variações conhecidas |
| **Integração** | Execução completa com dataset controlado |
| **Regressão** | Comparação de saída (snapshot) para detectar mudanças inesperadas |
| **Borda** | Arquivos vazios, colunas ausentes, dados inconsistentes |

**Exemplo de teste unitário:**

```php
assert(normalizarData('15/01/2026') === '2026-01-15');
assert(normalizarData('2026-01-15') === '2026-01-15');
assert(normalizarData('') === null);
assert(normalizarValor('1.200,50') === 1200.50);
assert(normalizarValor('89.90') === 89.90);
