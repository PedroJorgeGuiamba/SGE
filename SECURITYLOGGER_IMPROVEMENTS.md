# SecurityLogger - Melhorias Implementadas

## 📋 Resumo das Melhorias

Foram implementadas as seguintes melhorias na classe `SecurityLogger`:

### 1. **Salt Variável e Seguro** ✅
- Salt agora é gerado uma única vez e armazenado em `config/.security_salt`
- Permissões de arquivo: `0600` (apenas o proprietário consegue ler)
- Salt é carregado automaticamente e rotacionado se necessário
- **Antes:** Salt hardcoded como `'salt'`
- **Depois:** Salt dinâmico e seguro, gerado com `random_bytes(32)`

### 2. **Rotação Automática de Logs** ✅
- Logs são rotacionados automaticamente quando atingem 10 MB
- Arquivos rotacionados são nomeados com timestamp: `security_2025-12-12_14-30-45.log`
- Suporta compressão de logs rotacionados (gzip - comentado por enquanto)
- **Configurações:**
  - `$maxLogSize = 10485760` (10 MB)
  - `$logRetentionDays = 90` (retenção de 90 dias)

### 3. **Limpeza Automática de Logs Antigos** ✅
- Logs com mais de 90 dias são automaticamente eliminados
- Evita crescimento indefinido do espaço em disco
- Executado junto com a rotação

### 4. **Criação Automática de Pastas** ✅
- Pasta `logs/` é criada automaticamente se não existir
- Pasta `config/` é criada com permissões restritivas
- Validação de permissões de escrita
- **Antes:** Falha silenciosa se pasta não existisse
- **Depois:** Criação automática e validação

### 5. **Sanitização Expandida** ✅
- Agora redacta mais campos sensíveis:
  - `email`, `password`, `token`, `csrf_token`, `otp`, `otp_code`, `api_key`, `secret`
- **Antes:** Apenas 3 campos (`email`, `password`, `token`)
- **Depois:** 8 campos + extensível

### 6. **Níveis de Severidade** ✅
- Eventos agora suportam níveis: `INFO`, `WARNING`, `ERROR`, `CRITICAL`
- Logs de erro/críticos são separados em `security_errors.log`
- **Estrutura de dados melhorada com campo `severity`**

### 7. **Métodos Convenientes** ✅
Novos métodos para facilitar logging:
```php
SecurityLogger::logLoginSuccess($userId, $email);          // LOGIN_SUCCESS
SecurityLogger::logLoginFailed($email, $reason);           // LOGIN_FAILED
SecurityLogger::logPasswordChange($userId);                 // PASSWORD_CHANGED
SecurityLogger::logRoleChange($userId, $oldRole, $newRole); // ROLE_CHANGED
SecurityLogger::logUnauthorizedAccess($userId, $resource);  // UNAUTHORIZED_ACCESS
SecurityLogger::logSuspiciousActivity($userId, $activity);  // SUSPICIOUS_ACTIVITY
```

### 8. **Função de Status/Debug** ✅
```php
$status = SecurityLogger::getStatus();
// Retorna:
// [
//     'initialized' => true,
//     'logs_directory' => '/path/to/logs',
//     'logs_writable' => true,
//     'salt_configured' => true,
//     'max_log_size' => '10485760 bytes',
//     'retention_days' => 90
// ]
```

### 9. **Logs Expandidos nos Controllers** ✅
- **RegisterController:** Agora registra eventos de registo com razões específicas
- **AuthController:** Logs detalhados de tentativas de login, sucesso e falhas
- **AuthConfirmationController:** Logs de validação OTP, tentativas de reutilização, etc.

## 📁 Estrutura de Pastas Criada

```
projeto/
├── config/
│   ├── .gitkeep
│   └── .security_salt  (gerado automaticamente - NÃO commitr!)
├── logs/
│   ├── .gitkeep
│   ├── security.log    (log principal)
│   ├── security_errors.log (erros críticos)
│   └── security_*.log  (logs rotacionados)
├── Helpers/
│   ├── SecurityLogger.php (melhorado)
│   └── SecurityLoggerTest.php (testes)
└── Controller/Auth/
    ├── RegisterController.php (melhorado)
    ├── AuthController.php (melhorado)
    └── AuthConfirmationController.php (melhorado)
```

## 🚀 Como Usar

### Teste Rápido
```bash
# Aceda ao arquivo de teste
http://localhost/estagio/Helpers/SecurityLoggerTest.php

# Verifique os logs
tail -f logs/security.log
```

### Integração Manual
```php
require_once 'Helpers/SecurityLogger.php';

// Evento simples
SecurityLogger::logSecurityEvent('CUSTOM_EVENT', $userId, ['action' => 'description'], 'INFO');

// Métodos convenientes
SecurityLogger::logLoginSuccess(123, 'user@example.com');
SecurityLogger::logSuspiciousActivity(456, 'Tentativa de acesso não autorizado');

// Verificar status
$status = SecurityLogger::getStatus();
var_dump($status);
```

## 📊 Exemplo de Arquivo de Log

```json
{"timestamp":"2025-12-12T14:30:45+00:00","event":"LOGIN_SUCCESS","severity":"INFO","user_id":5,"ip_hash":"abc123def456...","user_agent_hash":"ghi789jkl012...","context":{"email_hash":"hash_value","role":"formando"}}
{"timestamp":"2025-12-12T14:31:20+00:00","event":"LOGIN_FAILED","severity":"WARNING","user_id":"anonymous","ip_hash":"xyz789abc123...","user_agent_hash":"def456ghi789...","context":{"email_hash":"hash_value","reason":"INVALID_PASSWORD"}}
```

## 🔒 Segurança

### Protecção de Dados Sensíveis
- ✅ Emails com hash SHA256 (não reversível)
- ✅ Senhas sempre redactadas
- ✅ Tokens redactados
- ✅ IPs hasheados com salt
- ✅ User-Agent hasheado com salt

### Protecção de Arquivos
- ✅ `.security_salt` com permissões 0600
- ✅ Logs com LOCK_EX para evitar race conditions
- ✅ Validação de permissões de escrita

## 📋 Configuração (Opcional)

Se quiser personalizar, edite o topo de `SecurityLogger.php`:

```php
private static $maxLogSize = 10485760;     // Tamanho máximo do arquivo
private static $logRetentionDays = 90;     // Dias de retenção
```

## ⚠️ Antes de Publicar em Produção

1. **Remova `SecurityLoggerTest.php`** - é apenas para desenvolvimento
2. **Configure backup dos logs** - não acumule indefinidamente
3. **Configure alertas** para eventos CRITICAL e ERROR
4. **Teste a rotação** em ambiente de testes antes de produção
5. **Configure .gitignore** para excluir:
   ```
   /logs/
   /config/.security_salt
   ```

## 🔄 Rotação em Cron (Opcional)

Para forçar rotação em horário específico, adicione cron:

```bash
0 2 * * * php /path/to/scripts/rotate_logs.php
```

Script exemplo:
```php
<?php
require 'SecurityLogger.php';
// Force rotation by logging a marker event
SecurityLogger::logSecurityEvent('CRON_LOG_ROTATION', null, ['action' => 'cron_triggered'], 'INFO');
?>
```

## 📞 Troubleshooting

### "Diretório de logs sem permissão de escrita"
```bash
chmod 755 /path/to/logs
chmod 700 /path/to/config
```

### "Salt file not found"
- É normal, será criado automaticamente no primeiro uso
- Verifique permissões da pasta `config/`

### Logs não aparecem
1. Verifique `error_log` do PHP
2. Confirme que `logs/` existe e é gravável
3. Execute `SecurityLoggerTest.php` para diagnóstico

## 📝 Changelog

### v2.0.0 (Atual)
- ✅ Salt dinâmico e seguro
- ✅ Rotação automática de logs
- ✅ Limpeza automática
- ✅ Criação automática de pastas
- ✅ Níveis de severidade
- ✅ Métodos convenientes
- ✅ Sanitização expandida

### v1.0.0 (Anterior)
- Salt hardcoded
- Sem rotação
- Sanitização mínima
