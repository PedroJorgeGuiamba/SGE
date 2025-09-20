<?php
$chave = random_bytes(32);
$chave_base64 = base64_encode($chave);
echo $chave_base64;