<?php
namespace Models;

use DateTime;

class Pessoa{
    private string $nome;
    private string $apelido;
    private DateTime $dataDeNascimento;
    private string $naturalidade;
    private string $tipoDeDocumento;
    private string $numeroDeDocumento;
    private string $localEmitido;
    private DateTime $dataDeEmissao;
    private int $NUIT;
    private int $Telefone;
    private string $email;

    public function __construct(string $nome, string $apelido, DateTime $dataDeNascimento,string $naturalidade, string $tipoDeDocumento, string $numeroDeDocumento, string $localEmitido, DateTime $dataDeEmissao, int $NUIT, int $Telefone, string $email) {
        $this->nome = $nome;
        $this->apelido = $apelido;
        $this->dataDeNascimento = $dataDeNascimento;
        $this->naturalidade = $naturalidade;
        $this->tipoDeDocumento = $tipoDeDocumento;
        $this->numeroDeDocumento = $numeroDeDocumento;
        $this->localEmitido = $localEmitido;
        $this->dataDeEmissao = $dataDeEmissao;
        $this->NUIT = $NUIT;
        $this->Telefone = $Telefone;
        $this->email = $email;
    }


    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setApelido(string $apelido): void
    {
        $this->apelido = $apelido;
    }

    public function setDataDeNascimento(DateTime $dataDeNascimento): void
    {
        $this->dataDeNascimento = $dataDeNascimento;
    }

    public function setNaturalidade(string $naturalidade): void
    {
        $this->naturalidade = $naturalidade;
    }

    public function setTipoDeDocumento(string $tipoDeDocumento): void
    {
        $this->tipoDeDocumento = $tipoDeDocumento;
    }

    public function setNumeroDeDocumento(string $numeroDeDocumento): void
    {
        $this->numeroDeDocumento = $numeroDeDocumento;
    }

    public function setLocalEmitido(string $localEmitido): void
    {
        $this->localEmitido = $localEmitido;
    }

    public function setDataDeEmissao(DateTime $dataDeEmissao): void
    {
        $this->dataDeEmissao = $dataDeEmissao;
    }

    public function setNUIT(int $NUIT): void
    {
        $this->NUIT = $NUIT;
    }

    public function setTelefone(int $Telefone): void
    {
        $this->Telefone = $Telefone;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getApelido(): string
    {
        return $this->apelido;
    }
    public function getDataDeNascimento(): DateTime
    {
        return $this->dataDeNascimento;
    }

    public function getNaturalidade(): string
    {
        return $this->naturalidade;
    }

    public function getTipoDeDocumento(): string
    {
        return $this->tipoDeDocumento;
    }

    public function getNumeroDeDocumento(): string
    {
        return $this->numeroDeDocumento;
    }

    public function getLocalEmitido(): string
    {
        return $this->localEmitido;
    }

    public function getDataDeEmissao(): DateTime
    {
        return $this->dataDeEmissao;
    }

    public function getNUIT(): int
    {
        return $this->NUIT;
    }

    public function getTelefone(): int
    {
        return $this->Telefone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

?>