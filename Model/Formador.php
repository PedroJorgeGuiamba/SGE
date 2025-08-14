<?php
namespace Models;

use DateTime;

class Formador extends Pessoa{
    private string $profissao;

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
        int $NUIT,
        int $Telefone,
        string $email,
        string $profissao
    ) {
        parent::__construct(
            $nome,
            $apelido,
            $codigo,
            $dataDeNascimento,
            $naturalidade,
            $tipoDeDocumento,
            $numeroDeDocumento,
            $localEmitido,
            $dataDeEmissao,
            $NUIT,
            $Telefone,
            $email
        );

        $this->profissao = $profissao;
    }
}
?>