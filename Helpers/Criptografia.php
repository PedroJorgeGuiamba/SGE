<?php
class Criptografia {
    private $chave;
    private $iv;

    public function __construct() {
        
        $chave_base64 = "oz5lnVK8qewNQ4r1+XIDeTxyXzHd6/F/7aXOL7Mo8Mc";
        if (!$chave_base64) {
            throw new Exception('Chave de criptografia não configurada. Defina a variável de ambiente ENCRYPTION_KEY.');
        }
        $this->chave = base64_decode($chave_base64);
        if (strlen($this->chave) !== 32) {
            throw new Exception('Chave de criptografia inválida: deve ter 32 bytes após decodificação Base64.');
        }
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    }

    public function criptografar($dados) {
        $criptografado = openssl_encrypt(
            $dados,
            'aes-256-cbc',
            $this->chave,
            0,
            $this->iv
        );
        return base64_encode($this->iv . $criptografado);
    }

    public function descriptografar($dados) {
        $dados = base64_decode($dados);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($dados, 0, $iv_length);
        $dados_criptografados = substr($dados, $iv_length);
        return openssl_decrypt(
            $dados_criptografados,
            'aes-256-cbc',
            $this->chave,
            0,
            $iv
        );
    }
}
?>