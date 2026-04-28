# 🏥 Intranet Acqua — 
**Versão 1.3.1** · PHP 8 + MySQL · Apache · Dark Mode · Modular

Portal de comunicação institucional para colaboradores, com gestão de
comunicados, notícias, sistemas, links rápidos, ramais e menu de navegação
configurável pelo painel administrativo.

---

## 📋 Índice

1. [Requisitos](#-requisitos)
2. [Estrutura de Arquivos](#-estrutura-de-arquivos)
3. [Instalação](#-instalação)
4. [Configuração do Banco de Dados](#-configuração-do-banco-de-dados)
5. [Configuração do Apache](#-configuração-do-apache)
6. [Configuração do config.php](#-configuração-do-configphp)
7. [Permissões](#-permissões)
8. [Verificação](#-verificação)
9. [Credenciais Iniciais](#-credenciais-iniciais)
10. [Perfis de Acesso](#-perfis-de-acesso)
11. [Problemas Comuns](#-problemas-comuns)

---

## ✅ Requisitos

### Software obrigatório

| Componente | Versão mínima | Recomendado |
|---|---|---|
| PHP | 8.0 | 8.2+ |
| MySQL | 8.0 | 8.0+ |
| Apache | 2.4 | 2.4+ |
| Sistema Operacional | Ubuntu 20.04 | Ubuntu 22.04 LTS |

### Extensões PHP obrigatórias

```bash
# Verifica se todas estão instaladas
php -m | grep -E "pdo_mysql|gd|mbstring|fileinfo|json|session"
```

| Extensão | Função |
|---|---|
| `pdo_mysql` | Conexão com o banco de dados |
| `gd` | Redimensionamento e thumbnail de imagens |
| `mbstring` | Manipulação de strings UTF-8 |
| `fileinfo` | Validação real de tipo de arquivo no upload |
| `json` | APIs internas |
| `session` | Autenticação de usuários |

### Instalação das extensões (Ubuntu/Debian)

```bash
apt install php8.2 php8.2-mysql php8.2-gd php8.2-mbstring php8.2-fileinfo -y
```

### Módulos Apache obrigatórios

```bash
a2enmod rewrite    # URLs e .htaccess
a2enmod headers    # Cabeçalhos de segurança
a2enmod expires    # Cache de assets
a2enmod deflate    # Compressão GZIP
systemctl restart apache2
```

---

## 📁 Estrutura de Arquivos

```
intranet-acqua/
│
├── 📄 index.php              # Intranet principal (requer login)
├── 📄 public.php             # Área pública (sem login)
├── 📄 login.php              # Tela de autenticação
├── 📄 logout.php             # Encerra a sessão
├── 📄 dynamic.css.php        # CSS dinâmico com cores do banco
├── 📄 diagnostico.php        # ⚠️ Diagnóstico — apague após instalar!
├── 📄 database.sql           # Schema completo + dados iniciais
├── 📄 .htaccess              # Segurança e SameSite cookie
├── 📄 .user.ini              # Limites de upload PHP (20MB)
│
├── 📁 includes/              # Núcleo do sistema (sem acesso público)
│   ├── config.php            # ⚙️ CONFIGURAÇÃO PRINCIPAL — editar antes de instalar
│   ├── database.php          # Classe PDO singleton
│   ├── functions.php         # Auth, CSRF, getSetting(), getColorVarsStyle()
│   ├── image.php             # Upload, resize e thumbnail automático
│   ├── header.php            # Navbar com itens dinâmicos do banco
│   └── footer.php            # Scripts e fechamento HTML
│
├── 📁 admin/                 # Painel administrativo
│   ├── index.php             # Dashboard (Admin + Editor)
│   ├── posts.php             # Comunicados e notícias
│   ├── modules.php           # Sistemas e links rápidos
│   ├── categories.php        # Categorias de publicações
│   ├── nav.php               # Menu de navegação configurável
│   ├── ramais.php            # Ramais telefônicos
│   ├── users.php             # Usuários (somente Admin)
│   └── settings.php          # Configurações gerais (somente Admin)
│
├── 📁 api/                   # Endpoints internos AJAX
│   ├── toggle_dark.php       # Alterna modo escuro
│   └── sort_modules.php      # Reordena módulos (drag-and-drop)
│
├── 📁 assets/
│   ├── css/style.css         # Estilos globais (claro/escuro, grid, cards)
│   └── js/main.js            # Dark mode, toasts, editor, drag-sort
│
├── 📁 pages/
│   └── ramais.php            # Página pública de ramais
│
└── 📁 uploads/               # ⚠️ Requer permissão de escrita (775)
    ├── posts/                # Imagens de comunicados e notícias
    ├── modules/              # Ícones dos sistemas
    └── avatars/              # Fotos de perfil dos usuários
```

### Tabelas do banco de dados

| Tabela | Conteúdo |
|---|---|
| `users` | Usuários do sistema (admin, editor) |
| `posts` | Comunicados e notícias |
| `categories` | Categorias das publicações |
| `modules` | Sistemas e links rápidos do dashboard |
| `nav_items` | Itens do menu de navegação |
| `ramais` | Ramais telefônicos institucionais |
| `settings` | Configurações gerais (nome, cores, logo) |

---

## 🚀 Instalação

### Passo 1 — Copie os arquivos para o servidor

**Cenário A — acesso via subpasta** `http://IP/intranet-acqua`
```bash
cp -r intranet-acqua/ /var/www/html/intranet-acqua/
```

**Cenário B — acesso direto pelo IP** `http://IP`
```bash
cp -r intranet-acqua/ /var/www/html/
# O DocumentRoot do Apache deve apontar para /var/www/html/intranet-acqua
```

---

## 🗄️ Configuração do Banco de Dados

### Passo 2 — Crie o banco e o usuário

```bash
mysql -u root -p
```

```sql
-- Cria o banco
CREATE DATABASE intranet_acqua
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Cria usuário dedicado (recomendado em produção)
CREATE USER 'acqua_user'@'localhost' IDENTIFIED BY 'SuaSenhaForte123';
GRANT ALL PRIVILEGES ON intranet_acqua.* TO 'acqua_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> Em ambiente local/desenvolvimento pode usar `root` diretamente,
> sem precisar criar usuário separado.

### Passo 3 — Importe o schema

```bash
mysql -u acqua_user -p intranet_acqua < /var/www/html/intranet-acqua/database.sql
```

### Passo 4 — Confirme as tabelas

```bash
mysql -u acqua_user -p intranet_acqua -e "SHOW TABLES;"
```

Saída esperada:
```
+---------------------------+
| Tables_in_intranet_acqua  |
+---------------------------+
| categories                |
| modules                   |
| nav_items                 |
| posts                     |
| ramais                    |
| settings                  |
| users                     |
+---------------------------+
```

---

## 🌐 Configuração do Apache

### Passo 5 — Crie o VirtualHost

```bash
nano /etc/apache2/sites-available/intranet-acqua.conf
```

**Cenário A — acesso via subpasta** `http://IP/intranet-acqua`

```apache
<VirtualHost *:80>
    ServerName   IP
    DocumentRoot /var/www/html

    <Directory /var/www/html/intranet-acqua>
        Options -Indexes -ExecCGI
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/html/intranet-acqua/includes>
        Require all denied
    </Directory>

    <Directory /var/www/html/intranet-acqua/uploads>
        Options -Indexes -ExecCGI
        AllowOverride None
        <FilesMatch "\.php$">
            Require all denied
        </FilesMatch>
    </Directory>

    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css               "access plus 1 week"
        ExpiresByType application/javascript "access plus 1 week"
        ExpiresByType image/png              "access plus 1 month"
        ExpiresByType image/jpeg             "access plus 1 month"
        ExpiresByType image/webp             "access plus 1 month"
    </IfModule>

    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/css application/javascript
    </IfModule>

    <IfModule mod_headers.c>
        Header always set X-Frame-Options        "SAMEORIGIN"
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-XSS-Protection       "1; mode=block"
    </IfModule>

    ErrorLog  ${APACHE_LOG_DIR}/intranet-acqua-error.log
    CustomLog ${APACHE_LOG_DIR}/intranet-acqua-access.log combined
</VirtualHost>
```

**Cenário B — acesso direto pelo IP** `http://IP`

```apache
<VirtualHost *:80>
    ServerName   IP
    DocumentRoot /var/www/html/intranet-acqua

    <Directory /var/www/html/intranet-acqua>
        Options -Indexes -ExecCGI
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/html/intranet-acqua/includes>
        Require all denied
    </Directory>

    <Directory /var/www/html/intranet-acqua/uploads>
        Options -Indexes -ExecCGI
        AllowOverride None
        <FilesMatch "\.php$">
            Require all denied
        </FilesMatch>
    </Directory>

    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css               "access plus 1 week"
        ExpiresByType application/javascript "access plus 1 week"
        ExpiresByType image/png              "access plus 1 month"
        ExpiresByType image/jpeg             "access plus 1 month"
        ExpiresByType image/webp             "access plus 1 month"
    </IfModule>

    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/css application/javascript
    </IfModule>

    <IfModule mod_headers.c>
        Header always set X-Frame-Options        "SAMEORIGIN"
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-XSS-Protection       "1; mode=block"
    </IfModule>

    ErrorLog  ${APACHE_LOG_DIR}/intranet-acqua-error.log
    CustomLog ${APACHE_LOG_DIR}/intranet-acqua-access.log combined
</VirtualHost>
```

### Passo 6 — Ative o site e reinicie

```bash
# Ativa o VirtualHost
a2ensite intranet-acqua.conf

# Desativa o padrão (se não houver outros sites)
a2dissite 000-default.conf

# Testa a sintaxe — deve retornar "Syntax OK"
apache2ctl configtest

# Reinicia
systemctl restart apache2
```

---

## ⚙️ Configuração do config.php

Arquivo: `/var/www/html/intranet-acqua/includes/config.php`

```bash
nano /var/www/html/intranet-acqua/includes/config.php
```

### Linha 15 — BASE_URL

A URL é **detectada automaticamente** por padrão. Só descomente esta linha se:
- CSS ou imagens carregarem do endereço errado
- Estiver usando proxy reverso (Nginx na frente do Apache)
- O nome da pasta no servidor for diferente de `intranet-acqua`

```php
// Cenário A — subpasta:
define('BASE_URL', 'http://IP/intranet-acqua');

// Cenário B — raiz direta:
define('BASE_URL', 'http://IP');

// Com porta personalizada:
define('BASE_URL', 'http://IP:8060/intranet-acqua');

// Com domínio HTTPS:
define('BASE_URL', 'https://intranet.hmp.pa.gov.br');
```

### Linhas 50 a 53 — Banco de dados

```php
define('DB_HOST', 'localhost');          // linha 50 — não alterar se MySQL local
define('DB_NAME', 'intranet_acqua');     // linha 51 — nome do banco criado
define('DB_USER', 'acqua_user');         // linha 52 — usuário MySQL
define('DB_PASS', 'SuaSenhaForte123');   // linha 53 — senha do usuário
```

### Linha 65 — Modo debug

```php
define('DEBUG_MODE', true);   // durante instalação e testes
define('DEBUG_MODE', false);  // SEMPRE false em produção
```

### Tabela de referência rápida

| Linha | Constante | Quando alterar |
|---|---|---|
| 15 | `BASE_URL` | Se automático errar (descomente a linha) |
| 50 | `DB_HOST` | Se MySQL estiver em outro servidor |
| 51 | `DB_NAME` | Se criou o banco com outro nome |
| 52 | `DB_USER` | Usuário MySQL criado no Passo 2 |
| 53 | `DB_PASS` | Senha do usuário MySQL |
| 65 | `DEBUG_MODE` | `false` em produção |

---

## 🔒 Permissões

```bash
# Dono correto para o Apache
chown -R www-data:www-data /var/www/html/intranet-acqua/

# Diretórios: 755
find /var/www/html/intranet-acqua/ -type d -exec chmod 755 {} \;

# Arquivos: 644
find /var/www/html/intranet-acqua/ -type f -exec chmod 644 {} \;

# Uploads: 775 (Apache precisa gravar imagens)
chmod -R 775 /var/www/html/intranet-acqua/uploads/
chown -R www-data:www-data /var/www/html/intranet-acqua/uploads/
```

| Pasta / Arquivo | Permissão | Motivo |
|---|---|---|
| Todos os diretórios | `755` | Apache lê, outros não escrevem |
| Arquivos `.php` `.css` `.js` | `644` | Apache lê, ninguém executa direto |
| `uploads/` e subpastas | `775` | Apache precisa gravar arquivos |
| `includes/config.php` | `644` | Nunca deixar como `777` |

> ⚠️ **Nunca use `chmod 777`** — é uma falha de segurança grave.

---

## 🔍 Verificação

### Passo 7 — Diagnóstico automático

```
http://SEU_IP/intranet-acqua/diagnostico.php
```

| Item verificado | Status esperado |
|---|---|
| BASE_URL detectada | ✅ URL correta |
| Conexão com banco | ✅ Conectado |
| Tabelas existem | ✅ 7 tabelas encontradas |
| Pasta uploads/ gravável | ✅ OK |
| Extensão GD | ✅ Ativa |
| Extensão PDO MySQL | ✅ Ativa |
| Extensão mbstring | ✅ Ativa |

> 🗑️ **Apague o `diagnostico.php` após confirmar tudo:**
> ```bash
> rm /var/www/html/intranet-acqua/diagnostico.php
> ```

### Verificações manuais no servidor

```bash
# Módulos Apache ativos
apache2ctl -M | grep -E "rewrite|headers|expires|deflate"

# PHP e extensões
php -v
php -m | grep -E "pdo_mysql|gd|mbstring|fileinfo"

# Permissões da pasta uploads
ls -la /var/www/html/intranet-acqua/uploads/

# Logs de erro em tempo real
tail -f /var/log/apache2/intranet-acqua-error.log

# Sintaxe do Apache
apache2ctl configtest
```

---

## 🔑 Credenciais Iniciais

| Nome | E-mail | Senha | Nível |
|---|---|---|---|
| Administrador | admin@admin.com | `Admin@2024` | Admin |
| Equipe Comunicação | comunicacao@admin.com | `Admin@2024` | Editor |

> ⚠️ **Altere as senhas imediatamente após o primeiro acesso!**
> Painel Admin → Usuários → Editar

---

## 👥 Perfis de Acesso

| Perfil | Quem usa | Permissões |
|---|---|---|
| **Admin** | TI | Acesso total: usuários, configurações, módulos, posts, cores, logo |
| **Editor** | Comunicação | Criar e editar posts, módulos, categorias, menu de navegação |
| **Visitante** | Colaboradores | Visualizar a intranet (área pública sem login) |

---

## ❓ Problemas Comuns

| Sintoma | Causa provável | Solução |
|---|---|---|
| CSS não carrega | `BASE_URL` errada ou banco offline | Descomentar linha 15 com URL correta |
| Página em branco | Erro PHP oculto | Habilitar `DEBUG_MODE = true` temporariamente |
| "Token inválido" no login | Sessão expirada | Limpar cookies do navegador |
| Upload não funciona | Sem permissão na pasta | `chmod -R 775 uploads/` |
| 404 em subpáginas | `mod_rewrite` inativo | `a2enmod rewrite && systemctl restart apache2` |
| Imagens quebradas | `UPLOAD_URL` errada | Verificar `BASE_URL` no `config.php` |
| Erro de conexão com banco | Credenciais erradas | Corrigir linhas 50–53 do `config.php` |
| Menu nav não aparece | `nav_items` vazia | Cadastrar itens em Admin → Menu Nav |
| Apache 403 Forbidden | Permissão incorreta | `chown -R www-data:www-data /var/www/html/intranet-acqua/` |

---

## 🗂️ URLs do Sistema

| Página | URL (Cenário A — subpasta) | URL (Cenário B — raiz) |
|---|---|---|
| Área pública | `http://IP/intranet-acqua/public.php` | `http://IP/public.php` |
| Login | `http://IP/intranet-acqua/login.php` | `http://IP/login.php` |
| Intranet | `http://IP/intranet-acqua/index.php` | `http://IP/index.php` |
| Painel Admin | `http://IP/intranet-acqua/admin/` | `http://IP/admin/` |
| Diagnóstico | `http://IP/intranet-acqua/diagnostico.php` | `http://IP/diagnostico.php` |

---

## 📦 Dependências Externas (CDN)

| Recurso | Uso |
|---|---|
| Material Icons (Google) | Ícones da interface |
| Google Fonts | Tipografia |

> Se o servidor **não tiver acesso à internet**, faça o download dessas
> fontes e sirva localmente, alterando os links no `includes/header.php`.

---

*Intranet Acqua v1.3.1 ·  · PHP 8 + MySQL + Apache*
