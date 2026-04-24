<?php

class SafeUnlink{
    function safe_unlink($filepath, $base_dir = null) {
        if (!file_exists($filepath)) {
            return false;
        }
        
        // Restringir a um diretório base
        if ($base_dir) {
            $real_base = realpath($base_dir);
            $real_file = realpath($filepath);
            
            if ($real_file === false || strpos($real_file, $real_base) !== 0) {
                error_log("Tentativa de deletar arquivo fora do diretório permitido: $filepath");
                return false;
            }
        }
        
        // Whitelist de extensões permitidas
        $allowed_extensions = ['pdf', 'tmp', 'zip', 'json'];
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        
        if (!in_array($ext, $allowed_extensions)) {
            error_log("Tentativa de deletar arquivo com extensão não permitida: $ext");
            return false;
        }
        
        // Verificar permissões
        if (!is_writable($filepath)) {
            error_log("Arquivo não pode ser deletado (sem permissão): $filepath");
            return false;
        }
        
        // Deletar arquivo
        return unlink($filepath);
    }
}