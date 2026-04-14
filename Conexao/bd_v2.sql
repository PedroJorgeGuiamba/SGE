CREATE DATABASE IF NOT EXISTS itc_v3;
USE itc_v3;

-- usuarios
CREATE TABLE usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  Email VARCHAR(255),
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL,
  PRIMARY KEY (id)
);

-- sessao
CREATE TABLE sessao (
  id_sessao INT NOT NULL AUTO_INCREMENT,
  data DATE NOT NULL,
  hora_inicio TIME,
  hora_fim TIME,
  token VARCHAR(255),
  se_valido TINYINT(1),
  utilizador_id INT,
  createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
  updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ip_address VARCHAR(50),
  user_agent TEXT,
  PRIMARY KEY (id_sessao),
  FOREIGN KEY (utilizador_id) REFERENCES usuarios(id)
);

-- actividade
CREATE TABLE actividade (
  id_actividade INT NOT NULL AUTO_INCREMENT,
  id_sessao INT NOT NULL,
  descricao TEXT NOT NULL,
  tipo TEXT,
  duracao INT,
  PRIMARY KEY (id_actividade),
  FOREIGN KEY (id_sessao) REFERENCES sessao(id_sessao)
);

-- modulo
CREATE TABLE modulo (
  id_modulo INT NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(15) NOT NULL,
  descricao VARCHAR(100),
  carga_horaria INT NOT NULL,
  PRIMARY KEY (id_modulo),
  UNIQUE (codigo)
);

-- resultado_aprendizagem
CREATE TABLE resultado_aprendizagem (
  id_resultado INT NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(20) NOT NULL,
  descricao TEXT NOT NULL,
  tipo ENUM('Teórico', 'Prático') NOT NULL,
  observacoes TEXT,
  PRIMARY KEY (id_resultado)
);

-- competencia
CREATE TABLE competencia (
  id_competencia INT NOT NULL AUTO_INCREMENT,
  id_modulo INT NOT NULL,
  id_resultado_aprendizagem INT NOT NULL,
  peso DECIMAL(4,2) DEFAULT 1.00,
  obrigatoria TINYINT(1) DEFAULT 1,
  PRIMARY KEY (id_competencia),
  UNIQUE (id_modulo, id_resultado_aprendizagem),
  FOREIGN KEY (id_modulo) REFERENCES modulo(id_modulo),
  FOREIGN KEY (id_resultado_aprendizagem) REFERENCES resultado_aprendizagem(id_resultado)
);

-- formando
CREATE TABLE formando (
  id_formando INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  apelido VARCHAR(50) NOT NULL,
  codigo INT NOT NULL,
  dataDeNascimento DATE NOT NULL,
  naturalidade VARCHAR(100) NOT NULL,
  tipoDeDocumento VARCHAR(50) NOT NULL,
  numeroDeDocumento VARCHAR(13) NOT NULL,
  localEmitido VARCHAR(100) NOT NULL,
  dataDeEmissao DATE NOT NULL,
  NUIT INT,
  telefone INT NOT NULL,
  email VARCHAR(100) NOT NULL,
  usuario_id INT,
  PRIMARY KEY (id_formando),
  UNIQUE (codigo),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- tipo_tentativa
CREATE TABLE tipo_tentativa (
  id_tentativa INT NOT NULL AUTO_INCREMENT,
  descricao VARCHAR(50) NOT NULL,
  ordem INT NOT NULL,
  PRIMARY KEY (id_tentativa)
);

-- avaliacao_competencia
CREATE TABLE avaliacao_competencia (
  id_avaliacao INT NOT NULL AUTO_INCREMENT,
  id_competencia INT NOT NULL,
  id_formando INT NOT NULL,
  id_tentativa INT NOT NULL,
  percentagem_atingida DECIMAL(5,2) NOT NULL,
  mencao ENUM('A', 'NA') NOT NULL,
  data_avaliacao DATE,
  observacoes TEXT,
  PRIMARY KEY (id_avaliacao),
  UNIQUE (id_competencia, id_formando, id_tentativa),
  FOREIGN KEY (id_competencia) REFERENCES competencia(id_competencia),
  FOREIGN KEY (id_formando) REFERENCES formando(id_formando),
  FOREIGN KEY (id_tentativa) REFERENCES tipo_tentativa(id_tentativa)
);

-- qualificacao
CREATE TABLE qualificacao (
  id_qualificacao INT NOT NULL AUTO_INCREMENT,
  qualificacao INT NOT NULL,
  descricao VARCHAR(100),
  nivel VARCHAR(50),
  PRIMARY KEY (id_qualificacao)
);

-- curso
CREATE TABLE curso (
  id_curso INT NOT NULL AUTO_INCREMENT,
  codigo INT NOT NULL,
  nome VARCHAR(100),
  descricao VARCHAR(255),
  sigla VARCHAR(10),
  codigo_qualificacao INT,
  PRIMARY KEY (id_curso),
  UNIQUE (codigo)
);

-- turma
CREATE TABLE turma (
  codigo INT NOT NULL,
  nome VARCHAR(10),
  codigo_curso INT,
  codigo_qualificacao INT,
  PRIMARY KEY (codigo),
  FOREIGN KEY (codigo_curso) REFERENCES curso(codigo)
);