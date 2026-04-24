<?php
require_once __DIR__ . '/../Model/Pessoa.php';
use Models\Pessoa;
class Formando extends Pessoa{
    private int $codigo;
    private int $userId;
    public function __construct(
        string $nome,
        string $apelido,
        int $codigo,
        DateTime $dataDeNascimento,
        string $naturalidade,
        string $tipoDeDocumento,
        string $numeroDeDocumento,
        string $localEmitido,
        DateTime $dataDeEmissao,
        string $NUIT,
        string $Telefone,
        string $email,
        int $userId
    ) {
        parent::__construct($nome, $apelido,  $dataDeNascimento,$naturalidade, $tipoDeDocumento, $numeroDeDocumento, $localEmitido, $dataDeEmissao, $NUIT, $Telefone, $email);
        $this->codigo = $codigo;
        $this->userId = $userId;
    }

    public function setCodigo(int $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getCodigo(): int
    {
        return $this->userId;
    }
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function salvar($conn): bool
    {
        $stmt = $conn->prepare(
            "INSERT INTO formando (codigo, nome, apelido, dataDeNascimento, naturalidade, tipoDeDocumento, numeroDeDocumento, localEmitido, dataDeEmissao, NUIT, telefone, email, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Erro na preparação da query: " . $conn->error);
            return false;
        }

        // Converter DateTime para string no formato 'Y-m-d' para o banco de dados
        $dataNascimento = $this->getDataDeNascimento()->format('Y-m-d');
        $dataEmissao = $this->getDataDeEmissao()->format('Y-m-d');

        $nome = $this->getNome();
        $apelido = $this->getApelido();
        $dataNascimento = $this->getDataDeNascimento()->format('Y-m-d');
        $naturalidade = $this->getNaturalidade();
        $tipoDeDocumento = $this->getTipoDeDocumento();
        $numeroDeDocumento = $this->getNumeroDeDocumento();
        $localEmitido = $this->getLocalEmitido();
        $dataEmissao = $this->getDataDeEmissao()->format('Y-m-d');
        $nuit = $this->getNUIT();
        $telefone = $this->getTelefone();
        $email = $this->getEmail();

        $stmt->bind_param(
            "isssssssssssi",
            $this->codigo,
            $nome,
            $apelido,
            $dataNascimento,
            $naturalidade,
            $tipoDeDocumento,
            $numeroDeDocumento,
            $localEmitido,
            $dataEmissao,
            $nuit,
            $telefone,
            $email,
            $this->userId
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Erro na execução da query: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }
}

?>