# 🏥 Intranet — Hospital da Mulher do Pará
**Versão 1.1** · PHP 8 + MySQL · Modular · Dark Mode

---

## 📁 Estrutura de Arquivos

```
intranet-hmp/
├── index.php              # Intranet principal (requer login)
├── login.php              # Tela de login
├── logout.php             # Encerrar sessão
├── public.php             # Área pública (sem login) — sistemas, comunicados, notícias
├── database.sql           # Schema + dados iniciais
├── .htaccess              # Segurança Apache
│
├── includes/
│   ├── config.php         # ⚙️ Configurações (DB, URLs) ← EDITAR ANTES DE INSTALAR
│   ├── database.php       # Classe PDO singleton
│   ├── functions.php      # Helpers de auth, slug, etc.
│   ├── image.php          # Upload/resize/thumbnail de imagens
│   ├── header.php         # Navbar global
│   └── footer.php         # Rodapé global
│
├── admin/
│   ├── index.php          # Dashboard (TI + Comunicação)
│   ├── posts.php          # Editor de comunicados e notícias
│   ├── modules.php        # Sistemas e links rápidos (com upload de ícone)
│   ├── categories.php     # Categorias de publicações
│   ├── nav.php            # Menu de navegação configurável
│   ├── users.php          # Usuários (apenas Admin/TI)
│   └── settings.php       # Configurações gerais (apenas Admin/TI)
│
├── api/
│   ├── toggle_dark.php    # API: alternar modo escuro
│   └── sort_modules.php   # API: reordenar módulos (drag-and-drop)
│
├── assets/
│   ├── css/style.css      # Estilos: modo claro/escuro, imagens, cards
│   └── js/main.js         # Dark mode, editor, drag-sort, toasts
│
└── uploads/
    ├── posts/             # Imagens de capa dos posts
    └── modules/           # Ícones/logos dos sistemas
```

---

## 🚀 Instalação

### Requisitos
- PHP **8.0+** com extensões: `pdo_mysql`, `mbstring`, `gd`, `fileinfo`
- MySQL **8.0+** ou MariaDB **10.4+**
- Apache com `mod_rewrite`

### Passo a passo

**1. Copie os arquivos**
```bash
cp -r intranet-hmp/ /var/www/html/intranet-hmp/
```

**2. Importe o banco de dados**
```bash
mysql -u root -p < database.sql
```

**3. Configure `includes/config.php`**
```php
define('BASE_URL', 'http://seuservidor.com/intranet-hmp');
define('DB_HOST', 'localhost');
define('DB_NAME', 'intranet_hmp');
define('DB_USER', 'usuario_mysql');
define('DB_PASS', 'senha_mysql');
```

**4. Permissões da pasta uploads**
```bash
chmod -R 755 uploads/
chown -R www-data:www-data uploads/
```

**5. Acesse**
- Intranet: `http://seuservidor.com/intranet-hmp/`
- Área Pública: `http://seuservidor.com/intranet-hmp/public.php`
- Admin: `http://seuservidor.com/intranet-hmp/admin/`

---

<<<<<<< HEAD
=======
## 🔑 Credenciais Iniciais

| Usuário | E-mail | Senha | Nível |
|---------|--------|-------|-------|
| Administrador HMP | admin@hmp.pa.gov.br | Admin@2024 | Admin (TI) |
| Equipe Comunicação | comunicacao@hmp.pa.gov.br | Admin@2024 | Editor |

> ⚠️ **Altere as senhas imediatamente após o primeiro acesso!**

---

>>>>>>> 6eab737 (atualização do arquivo post.PHP)
## 👥 Níveis de Acesso

| Nível | Acesso |
|-------|--------|
| **Admin** (TI) | Tudo: usuários, configurações, módulos, posts |
| **Editor** (Comunicação) | Posts, módulos, categorias, menu |
| **Usuário** | Visualizar conteúdo (intranet) |

---

## 🌐 Área Pública (sem login)
A página `public.php` exibe:
- **Todos os sistemas institucionais** (GLPI, SALUTEM, etc.)
- Comunicados marcados como "público"
- Notícias marcadas como "público"
- Post em destaque (featured)
- Links rápidos públicos

---

## 🖼️ Imagens — Formatos Suportados

| Formato | Suporte |
|---------|---------|
| JPG / JPEG | ✅ Nativo |
| PNG (com transparência) | ✅ Nativo |
| GIF | ✅ Nativo |
| WEBP | ✅ Nativo |
| BMP | ✅ Convertido para JPG |
| TIFF | ✅ Convertido para JPG |

**Processamento automático:**
- Redimensionamento para máx. 1920×1080px (posts)
- Thumbnail automático 600×340px
- Qualidade JPEG: 85%
- Limite de upload: **15MB**

---

## 🧩 Sistemas Pré-configurados

Todos com `is_public = 1` (visíveis sem login):

| Sistema | Descrição |
|---------|-----------|
| GLPI | Sistema de Chamados de TI |
| INTERACT | Sistema de Indicadores |
| Sistema de Laboratório | Resultados e Exames |
| EGS | Gestão em Saúde |
| PRORADIS | Radiologia Digital |
| SALUTEM | Prontuário Eletrônico |
| GTR | Gestão de Transportes |
| CHATPRO | Comunicação Interna |

> **Configure as URLs reais** em Admin → Módulos → Sistemas.
> Você pode também fazer upload do **logotipo de cada sistema** como ícone.

---

## 🎨 Personalização

### Paleta de cores
```css
/* assets/css/style.css */
:root {
  --primary:      #00897B;  /* Ciano-esverdeado */
  --primary-dark: #00695C;  /* Verde médio */
  --accent2:      #004D40;  /* Verde água escuro */
}
```

### Modo Escuro
Ativado pelo botão na navbar. Salvo por: sessão PHP + banco de dados + cookie (30 dias).

---

## 🔒 Segurança
- Senhas com **bcrypt** (custo 12)
- **CSRF token** em todos os formulários
- Validação MIME real de imagens (não confia em extensão)
- Upload sanitizado com verificação via `getimagesize()`
- Sanitização de inputs com `strip_tags` + `htmlspecialchars`
- `.htaccess` bloqueia acesso direto a `/includes/`

---

*Hospital da Mulher do Pará · v1.1*
