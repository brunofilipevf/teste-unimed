CREATE TABLE pacientes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome VARCHAR(100) NOT NULL,
    idade INTEGER NOT NULL,
    urgencia_original VARCHAR(20) NOT NULL CHECK (urgencia_original IN ('CRITICA', 'ALTA', 'MEDIA', 'BAIXA')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE filas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    horario_chegada TIME NOT NULL,
    urgencia_ajustada VARCHAR(20) NOT NULL,
    posicao INTEGER,
    status VARCHAR(20) DEFAULT 'AGUARDANDO' CHECK (status IN ('AGUARDANDO', 'EM_ATENDIMENTO', 'ATENDIDO')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
);

CREATE TABLE atendimentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    fila_id INTEGER NOT NULL,
    horario_inicio DATETIME,
    horario_fim DATETIME,
    FOREIGN KEY (fila_id) REFERENCES filas(id)
);

CREATE INDEX idx_filas_status ON filas(status, posicao);
