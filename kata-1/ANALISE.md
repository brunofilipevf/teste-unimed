# KATA 1 - ANÁLISE

## Parte B - Respostas Obrigatórias

### 1. Estrutura de dados utilizada

**Escolha:** Array simples com `usort()` do PHP.

**Motivo:**
Para este cenário, um array é suficiente e evita complexidade desnecessária. O `usort()` permite aplicar critérios personalizados de ordenação (como prioridade e horário) de forma direta e legível.

---

### 2. Complexidade de tempo

O algoritmo atual possui complexidade **O(n log n)** devido à ordenação.

Com um volume moderado de dados, isso é aceitável.
No entanto, em um cenário com grande escala (ex: 1 milhão de pacientes), reordenar toda a lista a cada nova chegada não é eficiente. Nesse caso, uma estrutura como fila de prioridade (heap) seria mais adequada, pois evita reprocessamento completo da lista.

---

### 3. Interação entre Regras 4 e 5

**Cenário:** Paciente com 15 anos e urgência MÉDIA.

- Não se aplica a regra de idade ≥ 60
- Aplica-se a regra de idade < 18

**Resultado:** A prioridade é elevada de **MÉDIA para ALTA**

---

### 4. Extensibilidade para novas regras

A lógica de priorização está centralizada no método `ajustarNivel()`, o que facilita manutenção e evolução.

Novas regras podem ser adicionadas diretamente nesse ponto sem afetar o restante do fluxo.
Caso o número de regras cresça significativamente, seria recomendável evoluir para uma abordagem mais modular (como uma cadeia de regras ou strategy), evitando acoplamento excessivo.

---

## Exemplo Final - Explicação da Ordenação

**Dados de entrada:**

| Nome  | Idade | Urgência | Horário |
|-------|-------|----------|---------|
| João  | 70    | MEDIA    | 10:00   |
| Maria | 15    | MEDIA    | 09:55   |
| Pedro | 30    | CRITICA  | 10:10   |
| Ana   | 25    | ALTA     | 10:05   |
| Lucas | 10    | BAIXA    | 09:50   |

**Aplicação das regras:**

- **Pedro** → CRITICA → Peso 100
- **João** → 70 anos, MEDIA → sobe para ALTA → Peso 75
- **Maria** → 15 anos, MEDIA → sobe para ALTA → Peso 75
- **Ana** → ALTA → Peso 75
- **Lucas** → 10 anos, BAIXA → sobe para MEDIA → Peso 50

**Desempate (mesmo peso):**
Ordem de chegada é utilizada como critério secundário:

Maria (09:55) → João (10:00) → Ana (10:05)

**Resultado final:**

1. Pedro (CRITICA)
2. Maria (ALTA)
3. João (ALTA)
4. Ana (ALTA)
5. Lucas (MEDIA)
