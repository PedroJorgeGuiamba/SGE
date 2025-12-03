CREATE SCHEMA IF NOT EXISTS `itc_v3` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `itc_v3` ;

-- -----------------------------------------------------
-- Table `itc_v3`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Email` VARCHAR(255) NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 31
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`sessao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`sessao` (
  `id_sessao` INT NOT NULL AUTO_INCREMENT,
  `data` DATE NOT NULL,
  `hora_inicio` TIME NULL DEFAULT NULL,
  `hora_fim` TIME NULL DEFAULT NULL,
  `token` VARCHAR(255) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `se_valido` TINYINT(1) NULL DEFAULT NULL,
  `utilizador_id` INT NULL DEFAULT NULL,
  `createdAt` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` VARCHAR(50) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id_sessao`),
  INDEX `fk_user_sessao` (`utilizador_id` ASC) VISIBLE,
  CONSTRAINT `fk_user_sessao`
    FOREIGN KEY (`utilizador_id`)
    REFERENCES `itc_v3`.`usuarios` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 99
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`actividade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`actividade` (
  `id_actividade` INT NOT NULL AUTO_INCREMENT,
  `id_sessao` INT NOT NULL,
  `descricao` TEXT NOT NULL,
  `tipo` TEXT NULL DEFAULT NULL,
  `duracao` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id_actividade`),
  INDEX `fk_actividade_sessao` (`id_sessao` ASC) VISIBLE,
  CONSTRAINT `fk_actividade_sessao`
    FOREIGN KEY (`id_sessao`)
    REFERENCES `itc_v3`.`sessao` (`id_sessao`))
ENGINE = InnoDB
AUTO_INCREMENT = 45
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`modulo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`modulo` (
  `id_modulo` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(15) CHARACTER SET 'utf8mb3' NOT NULL,
  `descricao` VARCHAR(100) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `carga_horaria` INT NOT NULL,
  PRIMARY KEY (`id_modulo`),
  UNIQUE INDEX `codigo` (`codigo` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`resultado_aprendizagem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`resultado_aprendizagem` (
  `id_resultado` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(20) NOT NULL,
  `descricao` TEXT NOT NULL,
  `tipo` ENUM('Teórico', 'Prático') NOT NULL,
  `observacoes` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id_resultado`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`competencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`competencia` (
  `id_competencia` INT NOT NULL AUTO_INCREMENT,
  `id_modulo` INT NOT NULL,
  `id_resultado_aprendizagem` INT NOT NULL,
  `peso` DECIMAL(4,2) NULL DEFAULT '1.00',
  `obrigatoria` TINYINT(1) NULL DEFAULT '1',
  PRIMARY KEY (`id_competencia`),
  UNIQUE INDEX `unique_modulo_resultado` (`id_modulo` ASC, `id_resultado_aprendizagem` ASC) VISIBLE,
  INDEX `idx_competencia_modulo` (`id_modulo` ASC) VISIBLE,
  INDEX `idx_competencia_resultado` (`id_resultado_aprendizagem` ASC) VISIBLE,
  CONSTRAINT `competencia_ibfk_1`
    FOREIGN KEY (`id_modulo`)
    REFERENCES `itc_v3`.`modulo` (`id_modulo`),
  CONSTRAINT `competencia_ibfk_2`
    FOREIGN KEY (`id_resultado_aprendizagem`)
    REFERENCES `itc_v3`.`resultado_aprendizagem` (`id_resultado`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`formando`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`formando` (
  `id_formando` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `apelido` VARCHAR(50) NOT NULL,
  `codigo` INT NOT NULL,
  `dataDeNascimento` DATE NOT NULL,
  `naturalidade` VARCHAR(100) NOT NULL,
  `tipoDeDocumento` VARCHAR(50) NOT NULL,
  `numeroDeDocumento` VARCHAR(13) NOT NULL,
  `localEmitido` VARCHAR(100) NOT NULL,
  `dataDeEmissao` DATE NOT NULL,
  `NUIT` INT NULL DEFAULT NULL,
  `telefone` INT NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `usuario_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id_formando`),
  UNIQUE INDEX `codigo` (`codigo` ASC) VISIBLE,
  INDEX `fk_formando_usuario` (`usuario_id` ASC) VISIBLE,
  CONSTRAINT `fk_formando_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `itc_v3`.`usuarios` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`tipo_tentativa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`tipo_tentativa` (
  `id_tentativa` INT NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(50) NOT NULL,
  `ordem` INT NOT NULL,
  PRIMARY KEY (`id_tentativa`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`avaliacao_competencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`avaliacao_competencia` (
  `id_avaliacao` INT NOT NULL AUTO_INCREMENT,
  `id_competencia` INT NOT NULL,
  `id_formando` INT NOT NULL,
  `id_tentativa` INT NOT NULL,
  `percentagem_atingida` DECIMAL(5,2) NOT NULL,
  `mencao` ENUM('A', 'NA') NOT NULL,
  `data_avaliacao` DATE NULL DEFAULT NULL,
  `observacoes` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id_avaliacao`),
  UNIQUE INDEX `unique_competencia_formando_tentativa` (`id_competencia` ASC, `id_formando` ASC, `id_tentativa` ASC) VISIBLE,
  INDEX `id_tentativa` (`id_tentativa` ASC) VISIBLE,
  INDEX `idx_avaliacao_competencia_formando` (`id_formando` ASC) VISIBLE,
  INDEX `idx_avaliacao_competencia_competencia` (`id_competencia` ASC) VISIBLE,
  CONSTRAINT `avaliacao_competencia_ibfk_1`
    FOREIGN KEY (`id_competencia`)
    REFERENCES `itc_v3`.`competencia` (`id_competencia`),
  CONSTRAINT `avaliacao_competencia_ibfk_2`
    FOREIGN KEY (`id_formando`)
    REFERENCES `itc_v3`.`formando` (`id_formando`),
  CONSTRAINT `avaliacao_competencia_ibfk_3`
    FOREIGN KEY (`id_tentativa`)
    REFERENCES `itc_v3`.`tipo_tentativa` (`id_tentativa`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`qualificacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`qualificacao` (
  `id_qualificacao` INT NOT NULL AUTO_INCREMENT,
  `qualificacao` INT NOT NULL,
  `descricao` VARCHAR(100) NULL DEFAULT NULL,
  `nivel` VARCHAR(50) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  PRIMARY KEY (`id_qualificacao`),
  UNIQUE INDEX `id_qualificacao` (`id_qualificacao` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 17
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`curso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`curso` (
  `id_curso` INT NOT NULL AUTO_INCREMENT,
  `codigo` INT NOT NULL,
  `nome` VARCHAR(100) NULL DEFAULT NULL,
  `descricao` VARCHAR(255) NULL DEFAULT NULL,
  `sigla` VARCHAR(10) NULL DEFAULT NULL,
  `codigo_qualificacao` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id_curso`),
  UNIQUE INDEX `codigo` (`codigo` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`turma`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`turma` (
  `codigo` INT NOT NULL,
  `nome` VARCHAR(10) NULL DEFAULT NULL,
  `codigo_curso` INT NULL DEFAULT NULL,
  `codigo_qualificacao` INT NULL DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  INDEX `fk_turma_curso` (`codigo_curso` ASC) VISIBLE,
  CONSTRAINT `fk_turma_curso`
    FOREIGN KEY (`codigo_curso`)
    REFERENCES `itc_v3`.`curso` (`codigo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`pedido_carta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`pedido_carta` (
  `id_pedido_carta` INT NOT NULL AUTO_INCREMENT,
  `numero` INT NULL DEFAULT NULL,
  `nome` VARCHAR(50) NULL DEFAULT NULL,
  `apelido` VARCHAR(100) NULL DEFAULT NULL,
  `codigo_formando` INT NULL DEFAULT NULL,
  `codigo_turma` INT NULL DEFAULT NULL,
  `data_do_pedido` DATE NULL DEFAULT NULL,
  `hora_do_pedido` TIME NULL DEFAULT NULL,
  `empresa` VARCHAR(100) NULL DEFAULT NULL,
  `data_de_levantamento` DATE NULL DEFAULT NULL,
  `contactoPrincipal` INT NULL DEFAULT NULL,
  `contactoSecundario` INT NULL DEFAULT NULL,
  `email` VARCHAR(100) NULL DEFAULT NULL,
  `qualificacao` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id_pedido_carta`),
  INDEX `fk_pedido_carta_qualificacao` (`qualificacao` ASC) VISIBLE,
  INDEX `fk_pedido_turma` (`codigo_turma` ASC) VISIBLE,
  INDEX `fk_pedido_formando` (`codigo_formando` ASC) VISIBLE,
  CONSTRAINT `fk_pedido_carta_qualificacao`
    FOREIGN KEY (`qualificacao`)
    REFERENCES `itc_v3`.`qualificacao` (`id_qualificacao`),
  CONSTRAINT `fk_pedido_formando`
    FOREIGN KEY (`codigo_formando`)
    REFERENCES `itc_v3`.`formando` (`codigo`),
  CONSTRAINT `fk_pedido_turma`
    FOREIGN KEY (`codigo_turma`)
    REFERENCES `itc_v3`.`turma` (`codigo`))
ENGINE = InnoDB
AUTO_INCREMENT = 84
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`resposta_carta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`resposta_carta` (
  `id_resposta` INT NOT NULL AUTO_INCREMENT,
  `numero_carta` INT NOT NULL,
  `status_resposta` ENUM('Pendente', 'Aceito', 'Recusado') NOT NULL DEFAULT 'Pendente',
  `data_resposta` DATE NULL DEFAULT NULL,
  `contato_responsavel` VARCHAR(100) NULL DEFAULT NULL,
  `data_inicio_estagio` DATE NULL DEFAULT NULL,
  `data_fim_estagio` DATE NULL DEFAULT NULL,
  `status_estagio` ENUM('Concluido', 'Nao Concluido', 'Pendente') NULL DEFAULT NULL,
  PRIMARY KEY (`id_resposta`),
  INDEX `fk_resposta_pedido` (`numero_carta` ASC) VISIBLE,
  CONSTRAINT `fk_resposta_pedido`
    FOREIGN KEY (`numero_carta`)
    REFERENCES `itc_v3`.`pedido_carta` (`id_pedido_carta`))
ENGINE = InnoDB
AUTO_INCREMENT = 40
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`avaliacao_estagio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`avaliacao_estagio` (
  `id_avaliacao` INT NOT NULL AUTO_INCREMENT,
  `id_resposta` INT NOT NULL,
  `numero_pedido` INT NOT NULL,
  `qualificacao` INT NULL DEFAULT NULL,
  `resultado` ENUM('NA', 'A') NULL DEFAULT NULL,
  `docPath` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id_avaliacao`),
  INDEX `fk_avaliacao_pedido` (`numero_pedido` ASC) VISIBLE,
  INDEX `fk_avaliacao_resposta` (`id_resposta` ASC) VISIBLE,
  INDEX `fk_avaliacao_qualificacao` (`qualificacao` ASC) VISIBLE,
  CONSTRAINT `fk_avaliacao_pedido`
    FOREIGN KEY (`numero_pedido`)
    REFERENCES `itc_v3`.`pedido_carta` (`id_pedido_carta`),
  CONSTRAINT `fk_avaliacao_qualificacao`
    FOREIGN KEY (`qualificacao`)
    REFERENCES `itc_v3`.`qualificacao` (`id_qualificacao`),
  CONSTRAINT `fk_avaliacao_resposta`
    FOREIGN KEY (`id_resposta`)
    REFERENCES `itc_v3`.`resposta_carta` (`id_resposta`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`tipo_avaliacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`tipo_avaliacao` (
  `id_tipo` INT NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(50) NOT NULL,
  `tipo` ENUM('Teórica', 'Prática') NOT NULL,
  PRIMARY KEY (`id_tipo`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`criterio_avaliacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`criterio_avaliacao` (
  `id_criterio` INT NOT NULL AUTO_INCREMENT,
  `id_modulo` INT NOT NULL,
  `id_tipo_avaliacao` INT NOT NULL,
  `percentual_minimo` INT NOT NULL,
  `observacoes` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id_criterio`),
  UNIQUE INDEX `unique_modulo_tipo` (`id_modulo` ASC, `id_tipo_avaliacao` ASC) VISIBLE,
  INDEX `id_tipo_avaliacao` (`id_tipo_avaliacao` ASC) VISIBLE,
  INDEX `idx_criterio_modulo` (`id_modulo` ASC) VISIBLE,
  CONSTRAINT `criterio_avaliacao_ibfk_1`
    FOREIGN KEY (`id_modulo`)
    REFERENCES `itc_v3`.`modulo` (`id_modulo`),
  CONSTRAINT `criterio_avaliacao_ibfk_2`
    FOREIGN KEY (`id_tipo_avaliacao`)
    REFERENCES `itc_v3`.`tipo_avaliacao` (`id_tipo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`instituicao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`instituicao` (
  `nuit` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `abreviatura` VARCHAR(15) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `endereco` VARCHAR(200) NULL DEFAULT NULL,
  `provincia` VARCHAR(50) NULL DEFAULT NULL,
  `distrito` VARCHAR(50) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `bairro` VARCHAR(50) NULL DEFAULT NULL,
  `numero` INT NULL DEFAULT NULL,
  `andar` INT NULL DEFAULT NULL,
  `telefone` VARCHAR(20) NULL DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `sector` ENUM('Publico', 'Privado') NOT NULL DEFAULT 'Publico',
  `sector_atividade` VARCHAR(100) NULL DEFAULT NULL,
  `telefone_contato` VARCHAR(20) NULL DEFAULT NULL,
  `email_contato` VARCHAR(100) NULL DEFAULT NULL,
  `observacoes` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`nuit`),
  UNIQUE INDEX `nome` (`nome` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`curso_instituicao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`curso_instituicao` (
  `codigo_instituicao` INT NULL DEFAULT NULL,
  `codigo_curso` INT NULL DEFAULT NULL,
  INDEX `fk_ci_curso` (`codigo_curso` ASC) VISIBLE,
  INDEX `fk_ci_inst` (`codigo_instituicao` ASC) VISIBLE,
  CONSTRAINT `fk_ci_curso`
    FOREIGN KEY (`codigo_curso`)
    REFERENCES `itc_v3`.`curso` (`codigo`),
  CONSTRAINT `fk_ci_inst`
    FOREIGN KEY (`codigo_instituicao`)
    REFERENCES `itc_v3`.`instituicao` (`nuit`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`empresa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`empresa` (
  `id_empresa` INT NOT NULL AUTO_INCREMENT,
  `nuit` INT NULL DEFAULT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `abreviatura` VARCHAR(15) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `endereco` VARCHAR(200) NULL DEFAULT NULL,
  `provincia` VARCHAR(50) NULL DEFAULT NULL,
  `distrito` VARCHAR(50) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `bairro` VARCHAR(50) NULL DEFAULT NULL,
  `numero` INT NULL DEFAULT NULL,
  `andar` INT NULL DEFAULT NULL,
  `telefone` VARCHAR(20) NULL DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `sector` ENUM('Publico', 'Privado') NOT NULL DEFAULT 'Publico',
  `sector_atividade` VARCHAR(100) NULL DEFAULT NULL,
  `telefone_contato` VARCHAR(20) NULL DEFAULT NULL,
  `email_contato` VARCHAR(100) NULL DEFAULT NULL,
  `observacoes` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id_empresa`),
  UNIQUE INDEX `nome` (`nome` ASC) VISIBLE,
  UNIQUE INDEX `nuit` (`nuit` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`estagio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`estagio` (
  `id_estagio` INT NOT NULL AUTO_INCREMENT,
  `codigo_formando` INT NOT NULL,
  `id_empresa` INT NULL DEFAULT '0',
  `id_supervisor` INT NULL DEFAULT NULL,
  `data_inicio` DATE NOT NULL,
  `data_fim` DATE NULL DEFAULT NULL,
  `status` ENUM('Pendente', 'Em curso', 'Concluído') NULL DEFAULT 'Pendente',
  `observacoes` TEXT NULL DEFAULT NULL,
  `id_resposta` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id_estagio`),
  INDEX `fk_estagio_formando` (`codigo_formando` ASC) VISIBLE,
  INDEX `fk_estagio_resposta` (`id_resposta` ASC) VISIBLE,
  INDEX `fk_estagio_supervisor` (`id_supervisor` ASC) VISIBLE,
  INDEX `fk_estagio_empresa` (`id_empresa` ASC) VISIBLE,
  CONSTRAINT `fk_estagio_empresa`
    FOREIGN KEY (`id_empresa`)
    REFERENCES `itc_v3`.`empresa` (`id_empresa`),
  CONSTRAINT `fk_estagio_formando`
    FOREIGN KEY (`codigo_formando`)
    REFERENCES `itc_v3`.`formando` (`codigo`),
  CONSTRAINT `fk_estagio_resposta`
    FOREIGN KEY (`id_resposta`)
    REFERENCES `itc_v3`.`resposta_carta` (`id_resposta`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`formador`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`formador` (
  `id_formador` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `apelido` VARCHAR(50) NOT NULL,
  `codigo` INT NOT NULL,
  `dataDeNascimento` DATE NOT NULL,
  `naturalidade` VARCHAR(100) NOT NULL,
  `tipoDeDocumento` VARCHAR(50) NOT NULL,
  `numeroDeDocumento` VARCHAR(13) NOT NULL,
  `localEmitido` VARCHAR(100) NOT NULL,
  `dataDeEmissao` DATE NOT NULL,
  `NUIT` INT NULL DEFAULT NULL,
  `telefone` INT NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `profissao` VARCHAR(50) CHARACTER SET 'utf8mb3' NOT NULL,
  `usuario_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id_formador`),
  UNIQUE INDEX `codigo` (`codigo` ASC) VISIBLE,
  INDEX `fk_formador_usuario` (`usuario_id` ASC) VISIBLE,
  CONSTRAINT `fk_formador_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `itc_v3`.`usuarios` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`formador_instituicao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`formador_instituicao` (
  `codigo_formador` INT NULL DEFAULT NULL,
  `codigo_instituicao` INT NULL DEFAULT NULL,
  INDEX `fk_fi_formador` (`codigo_formador` ASC) VISIBLE,
  INDEX `fk_fi_inst` (`codigo_instituicao` ASC) VISIBLE,
  CONSTRAINT `fk_fi_formador`
    FOREIGN KEY (`codigo_formador`)
    REFERENCES `itc_v3`.`formador` (`codigo`),
  CONSTRAINT `fk_fi_inst`
    FOREIGN KEY (`codigo_instituicao`)
    REFERENCES `itc_v3`.`instituicao` (`nuit`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`formador_modulo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`formador_modulo` (
  `codigo_formador` INT NULL DEFAULT NULL,
  `codigo_modulo` INT NULL DEFAULT NULL,
  INDEX `fk_fm_formador` (`codigo_formador` ASC) VISIBLE,
  INDEX `fk_fm_modulo` (`codigo_modulo` ASC) VISIBLE,
  CONSTRAINT `fk_fm_formador`
    FOREIGN KEY (`codigo_formador`)
    REFERENCES `itc_v3`.`formador` (`codigo`),
  CONSTRAINT `fk_fm_modulo`
    FOREIGN KEY (`codigo_modulo`)
    REFERENCES `itc_v3`.`modulo` (`id_modulo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`modulo_qualificacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`modulo_qualificacao` (
  `codigo_modulo` INT NOT NULL,
  `codigo_qualificacao` INT NOT NULL,
  PRIMARY KEY (`codigo_modulo`, `codigo_qualificacao`),
  INDEX `fk_modulo_qualificacao` (`codigo_qualificacao` ASC) VISIBLE,
  CONSTRAINT `fk_modulo_qualificacao`
    FOREIGN KEY (`codigo_qualificacao`)
    REFERENCES `itc_v3`.`qualificacao` (`id_qualificacao`),
  CONSTRAINT `fk_mq_modulo`
    FOREIGN KEY (`codigo_modulo`)
    REFERENCES `itc_v3`.`modulo` (`id_modulo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`notificacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`notificacao` (
  `id_notificacao` INT NOT NULL AUTO_INCREMENT,
  `id_utilizador` INT NULL DEFAULT NULL,
  `mensagem` TEXT NULL DEFAULT NULL,
  `lida` TINYINT(1) NULL DEFAULT NULL,
  `data` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_notificacao`),
  INDEX `user_notificacao` (`id_utilizador` ASC) VISIBLE,
  CONSTRAINT `user_notificacao`
    FOREIGN KEY (`id_utilizador`)
    REFERENCES `itc_v3`.`usuarios` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`pedido_carta_backup`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`pedido_carta_backup` (
  `numero` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `apelido` VARCHAR(100) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `codigo_formando` INT NULL DEFAULT NULL,
  `codigo_turma` INT NULL DEFAULT NULL,
  `data_do_pedido` DATE NULL DEFAULT NULL,
  `hora_do_pedido` TIME NULL DEFAULT NULL,
  `empresa` VARCHAR(100) NULL DEFAULT NULL,
  `data_de_levantamento` DATE NULL DEFAULT NULL,
  `contactoPrincipal` INT NULL DEFAULT NULL,
  `contactoSecundario` INT NULL DEFAULT NULL,
  `email` VARCHAR(100) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  `qualificacao` INT NULL DEFAULT NULL,
  PRIMARY KEY (`numero`),
  INDEX `fk_pedido_formando` (`codigo_formando` ASC) VISIBLE,
  INDEX `fk_pedido_turma` (`codigo_turma` ASC) VISIBLE,
  INDEX `fk_pedido_carta_turma` (`qualificacao` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 52
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`qualificacao_curso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`qualificacao_curso` (
  `codigo_curso` INT NOT NULL,
  `codigo_qualificacao` INT NOT NULL,
  PRIMARY KEY (`codigo_curso`, `codigo_qualificacao`),
  INDEX `qualificacao_curso_ibfk_2` (`codigo_qualificacao` ASC) VISIBLE,
  CONSTRAINT `qualificacao_curso_ibfk_1`
    FOREIGN KEY (`codigo_curso`)
    REFERENCES `itc_v3`.`curso` (`codigo`),
  CONSTRAINT `qualificacao_curso_ibfk_2`
    FOREIGN KEY (`codigo_qualificacao`)
    REFERENCES `itc_v3`.`qualificacao` (`id_qualificacao`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`supervisor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`supervisor` (
  `id_supervisor` INT NOT NULL AUTO_INCREMENT,
  `nome_supervisor` VARCHAR(50) CHARACTER SET 'utf8mb3' NOT NULL,
  `id_qualificacao` INT NULL DEFAULT NULL,
  `usuario_id` INT NULL DEFAULT NULL,
  `area` VARCHAR(255) CHARACTER SET 'utf8mb3' NULL DEFAULT NULL,
  PRIMARY KEY (`id_supervisor`),
  INDEX `fk_supervisor_usuario` (`usuario_id` ASC) VISIBLE,
  INDEX `fk_supervisor_qualificacao` (`id_qualificacao` ASC) VISIBLE,
  CONSTRAINT `fk_supervisor_qualificacao`
    FOREIGN KEY (`id_qualificacao`)
    REFERENCES `itc_v3`.`qualificacao` (`id_qualificacao`),
  CONSTRAINT `fk_supervisor_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `itc_v3`.`usuarios` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`supervisor_estagio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`supervisor_estagio` (
  `id_supervisor` INT NOT NULL,
  `id_estagio` INT NOT NULL,
  PRIMARY KEY (`id_supervisor`, `id_estagio`),
  CONSTRAINT `supervisor_estagio_ibfk_1`
    FOREIGN KEY (`id_supervisor`)
    REFERENCES `itc_v3`.`supervisor` (`id_supervisor`),
  CONSTRAINT `supervisor_estagio_ibfk_2`
    FOREIGN KEY (`id_supervisor`)
    REFERENCES `itc_v3`.`estagio` (`id_estagio`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`turma_formando`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`turma_formando` (
  `codigo_turma` INT NULL DEFAULT NULL,
  `codigo_formando` INT NULL DEFAULT NULL,
  INDEX `fk_tf_turma` (`codigo_turma` ASC) VISIBLE,
  INDEX `fk_tf_formando` (`codigo_formando` ASC) VISIBLE,
  CONSTRAINT `fk_tf_formando`
    FOREIGN KEY (`codigo_formando`)
    REFERENCES `itc_v3`.`formando` (`codigo`),
  CONSTRAINT `fk_tf_turma`
    FOREIGN KEY (`codigo_turma`)
    REFERENCES `itc_v3`.`turma` (`codigo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `itc_v3`.`user_otps`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itc_v3`.`user_otps` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `otp_code` VARCHAR(6) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `is_used` TINYINT(1) NULL DEFAULT '0',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `user_id` (`user_id` ASC) VISIBLE,
  CONSTRAINT `user_otps_ibfk_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `itc_v3`.`usuarios` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 126
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
