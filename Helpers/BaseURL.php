<?php

class BaseURL{
    function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        
        // Remove o nome do arquivo (ex: /router.php) para obter a pasta raiz
        $baseDir = rtrim(dirname($scriptName), '/\\');
        
        return $protocol . $host . $baseDir;
    }
}