<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/image.php';

requireEditor();

$action   = $_GET['action'] ?? 'list';
$type     = $_GET['type'] ?? 'comunicado';
$id       = (int) ($_GET['id'] ?? 0);
$isDark   = ($_SESSION['dark_mode'] ?? false);
$success  = $error = '';

// DELETE
if ($action === 'delete' && $id && isEditor()) {
    if (!verifyCsrf($_GET['csrf'] ?? '')) { $error = 'Token inválido.'; }
    else {
        $post = Database::fetch('SELECT cover_image FROM posts WHERE id=?', [$id]);
        if ($post && $post['cover_image']) deleteImage($post['cover_image']);
        Database::query('DELETE FROM posts WHERE id=?', [$id]);
        header('Location: posts.php?type=' . $type . '&deleted=1'); exit;
    }
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['new','edit'])) {
    // Detectar se o PHP descartou o POST por excesso de tamanho (upload_max_filesize ou post_max_size)
    // Nesse caso $_POST fica vazio mas $_SERVER['CONTENT_LENGTH'] existe
    if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $maxPost   = ini_get('post_max_size');
        $error = "Arquivo muito grande para o servidor. Limite atual: {$maxPost}. "
               . "Aumente post_max_size e upload_max_filesize no php.ini.";
    } elseif (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Token de segurança inválido. Recarregue a página e tente novamente.';
    } else {
        $title      = sanitize($_POST['title'] ?? '');
        $summary    = trim($_POST['summary'] ?? '');  // Sem sanitize: aspas e caracteres especiais permitidos no resumo
        $content    = $_POST['content'] ?? '';
        $imgAlt     = sanitize($_POST['cover_image_alt'] ?? '');
        $imgCaption = sanitize($_POST['cover_image_caption'] ?? '');
        $postType   = in_array($_POST['post_type']??'', ['comunicado','noticia']) ? $_POST['post_type'] : 'comunicado';
        $catId      = (int)($_POST['category_id']??0) ?: null;
        $status     = in_array($_POST['status']??'', ['draft','published','archived']) ? $_POST['status'] : 'draft';
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $isPublic   = isset($_POST['is_public']) ? 1 : 0;
        $pubAt      = $status === 'published' ? date('Y-m-d H:i:s') : null;
        $removeImg  = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';

        if (!$title) { $error = 'O título é obrigatório.'; }
        if (!$content) { $error = 'O conteúdo é obrigatório.'; }

        $cover = '';
        if (!$error) {
            // Upload nova imagem
            if (!empty($_FILES['cover_image']['name']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $upload = uploadImage($_FILES['cover_image'], 'posts');
                if (!$upload['success']) { $error = $upload['message']; }
                else { $cover = $upload['filename']; }
            }

            if (!$error) {
                if ($action === 'new') {
                    $slug = uniqueSlug(generateSlug($title), 'posts');
                    $newId = Database::insert(
                        "INSERT INTO posts (title,slug,summary,content,cover_image,cover_image_alt,cover_image_caption,type,category_id,author_id,status,is_featured,is_public,published_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                        [$title,$slug,$summary,$content,$cover,$imgAlt,$imgCaption,$postType,$catId,$_SESSION['user_id'],$status,$isFeatured,$isPublic,$pubAt]
                    );
                    header('Location: posts.php?action=edit&id=' . $newId . '&type=' . $postType . '&saved=1'); exit;
                } else {
                    $existing = Database::fetch('SELECT slug,cover_image FROM posts WHERE id=?', [$id]);
                    // Remover imagem existente se pedido
                    if ($removeImg && $existing['cover_image']) {
                        deleteImage($existing['cover_image']);
                        $cover = '';
                    } elseif (!$cover) {
                        $cover = $existing['cover_image'];
                    } else {
                        // Nova imagem: deletar antiga
                        if ($existing['cover_image']) deleteImage($existing['cover_image']);
                    }
                    $slug = uniqueSlug(generateSlug($title), 'posts', $id);
                    // Se publicando pela primeira vez, definir data de publicação
                    $existingRow = Database::fetch('SELECT published_at FROM posts WHERE id=?', [$id]);
                    $finalPubAt  = $existingRow['published_at'] ?? null;
                    if ($status === 'published' && !$finalPubAt) {
                        $finalPubAt = date('Y-m-d H:i:s');
                    }
                    Database::query(
                        "UPDATE posts SET title=?,slug=?,summary=?,content=?,cover_image=?,cover_image_alt=?,cover_image_caption=?,type=?,category_id=?,status=?,is_featured=?,is_public=?,published_at=? WHERE id=?",
                        [$title,$slug,$summary,$content,$cover,$imgAlt,$imgCaption,$postType,$catId,$status,$isFeatured,$isPublic,$finalPubAt,$id]
                    );
                    $success = 'Publicação salva com sucesso!';
                }
            }
        }
    }
}

$categories = Database::fetchAll('SELECT * FROM categories ORDER BY name');
$post = $id ? Database::fetch('SELECT * FROM posts WHERE id=?', [$id]) : null;

function sidebarLinks(string $active = ''): void { ?>
    <div class="sidebar-label">Conteúdo</div>
    <a href="index.php" class="sidebar-link <?= $active==='dash'?'active':'' ?>"><span class="material-icons">dashboard</span> Dashboard</a>
    <a href="posts.php?type=comunicado" class="sidebar-link <?= $active==='com'?'active':'' ?>"><span class="material-icons">campaign</span> Comunicados</a>
    <a href="posts.php?type=noticia" class="sidebar-link <?= $active==='not'?'active':'' ?>"><span class="material-icons">newspaper</span> Notícias</a>
    <a href="posts.php?action=new" class="sidebar-link"><span class="material-icons">add_circle</span> Novo Post</a>
    <a href="categories.php" class="sidebar-link"><span class="material-icons">label</span> Categorias</a>
    <div class="sidebar-label">Módulos</div>
    <a href="modules.php?cat=sistema" class="sidebar-link"><span class="material-icons">apps</span> Sistemas</a>
    <a href="modules.php?cat=link_rapido" class="sidebar-link"><span class="material-icons">bolt</span> Links Rápidos</a>
    <a href="nav.php" class="sidebar-link"><span class="material-icons">menu_open</span> Menu Nav</a>
    <?php if (isAdmin()): ?>
    <div class="sidebar-label">Administração</div>
    <a href="ramais.php" class="sidebar-link"><span class="material-icons">phone_in_talk</span> Ramais</a>
    <a href="users.php" class="sidebar-link"><span class="material-icons">group</span> Usuários</a>
    <a href="settings.php" class="sidebar-link"><span class="material-icons">settings</span> Configurações</a>
    <?php endif; ?>
<?php }
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?= $isDark ? 'dark' : 'light' ?>">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $action === 'list' ? 'Publicações' : ($action === 'new' ? 'Nova Publicação' : 'Editar') ?> — Admin Acqua</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <?= getColorVarsStyle() ?>
</head>
<body class="fade-in">
<nav class="navbar">
  <a href="<?= BASE_URL ?>/admin/index.php" class="navbar-brand">
    <div class="logo-icon"><span class="material-icons">admin_panel_settings</span></div>
    <div class="brand-text">Admin Acqua <small>Publicações</small></div>
  </a>
  <div class="navbar-end">
    <button class="dark-toggle <?= $isDark?'on':'' ?>"></button>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-ghost btn-sm"><span class="material-icons">home</span></a>
    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-ghost btn-sm" style="color:#dc3545"><span class="material-icons">logout</span></a>
    <button class="btn btn-ghost btn-icon mobile-menu-btn" id="sidebarToggle"><span class="material-icons">menu</span></button>
  </div>
</nav>
<div class="admin-layout">
  <aside class="admin-sidebar" id="adminSidebar"><?php sidebarLinks($type==='comunicado'?'com':'not'); ?></aside>
  <main class="admin-main">

  <?php if ($action === 'list'): ?>
    <?php $label = $type==='comunicado'?'Comunicados':'Notícias Externas'; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success" data-auto-dismiss><span class="material-icons">check_circle</span> Publicação excluída.</div><?php endif; ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h1 style="font-size:22px"><?= $label ?></h1>
      <a href="posts.php?action=new&type=<?= $type ?>" class="btn btn-primary"><span class="material-icons">add</span> Novo</a>
    </div>
    <?php $posts = Database::fetchAll("SELECT p.*,u.name as author FROM posts p LEFT JOIN users u ON u.id=p.author_id WHERE p.type='$type' ORDER BY p.created_at DESC"); ?>
    <div class="card">
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Imagem</th><th>Título</th><th>Status</th><th>Público</th><th>Destaque</th><th>Data</th><th>Ações</th></tr></thead>
          <tbody>
            <?php foreach ($posts as $p): ?>
            <tr>
              <td>
                <?php if ($p['cover_image'] && file_exists(UPLOAD_DIR . $p['cover_image'])): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($p['cover_image']) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px;display:block" alt="">
                <?php else: ?>
                <div style="width:60px;height:40px;background:var(--primary-xlight);border-radius:6px;display:flex;align-items:center;justify-content:center"><span class="material-icons" style="font-size:18px;color:var(--primary-light)">image</span></div>
                <?php endif; ?>
              </td>
              <td style="font-weight:600;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><a href="posts.php?action=edit&id=<?= $p['id'] ?>&type=<?= $type ?>"><?= htmlspecialchars($p['title']) ?></a></td>
              <td><?php $sc=['published'=>'badge-success','draft'=>'badge-warning','archived'=>'badge-info'][$p['status']]??'badge-info'; ?><span class="badge <?= $sc ?>"><?= $p['status'] ?></span></td>
              <td><?= $p['is_public'] ? '<span class="material-icons" style="color:#28a745">check_circle</span>' : '<span class="material-icons" style="color:var(--text-muted)">lock</span>' ?></td>
              <td><?= $p['is_featured'] ? '<span class="material-icons" style="color:#ffc107">star</span>' : '—' ?></td>
              <td class="text-sm text-muted"><?= formatDate($p['created_at']) ?></td>
              <td>
                <a href="posts.php?action=edit&id=<?= $p['id'] ?>&type=<?= $type ?>" class="btn btn-ghost btn-sm btn-icon"><span class="material-icons" style="font-size:18px">edit</span></a>
                <a href="posts.php?action=delete&id=<?= $p['id'] ?>&type=<?= $type ?>&csrf=<?= csrf() ?>" class="btn btn-ghost btn-sm btn-icon" style="color:#dc3545" data-confirm="Excluir esta publicação?"><span class="material-icons" style="font-size:18px">delete</span></a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?><tr><td colspan="7" style="text-align:center;color:var(--text-muted)">Nenhuma publicação.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  <?php elseif (in_array($action, ['new','edit'])): ?>
    <?php if (isset($_GET['saved'])): ?><div class="alert alert-success" data-auto-dismiss><span class="material-icons">check_circle</span> Salvo com sucesso!</div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success" data-auto-dismiss><span class="material-icons">check_circle</span><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><span class="material-icons">error</span><?= $error ?></div><?php endif; ?>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
      <a href="posts.php?type=<?= $post['type']??$type ?>" class="btn btn-ghost btn-sm"><span class="material-icons">arrow_back</span></a>
      <h1 style="font-size:20px"><?= $action==='new'?'Nova Publicação':'Editar Publicação' ?></h1>
    </div>

    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= csrf() ?>">
      <input type="hidden" name="remove_image" id="removeImageFlag" value="0">

      <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
        <!-- Editor principal -->
        <div>
          <div class="form-group">
            <label class="form-label">Título *</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($post['title']??'') ?>" placeholder="Título da publicação" style="font-size:16px;font-weight:600;padding:13px 16px">
          </div>
          <div class="form-group">
            <label class="form-label">Resumo / Chamada</label>
            <textarea name="summary" class="form-control" rows="3" placeholder="Resumo exibido nos cards de listagem (aprox. 120 caracteres)"><?= htmlspecialchars($post['summary']??'') ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Conteúdo *</label>
            <div class="editor-toolbar">
              <?php $btns = [
                ['bold','format_bold'],['italic','format_italic'],['underline','format_underlined'],['strikeThrough','format_strikethrough'],
                '|',
                ['justifyLeft','format_align_left'],['justifyCenter','format_align_center'],['justifyRight','format_align_right'],
                '|',
                ['insertUnorderedList','format_list_bulleted'],['insertOrderedList','format_list_numbered'],
                '|',
                ['h2','title','h2'],['h3','title','h3'],
                '|',
                ['createLink','link'],['unlink','link_off'],
                '|',
                ['formatBlock','format_quote','blockquote'],
              ];
              foreach ($btns as $b) {
                if ($b === '|') { echo '<div class="editor-divider"></div>'; continue; }
                $val = isset($b[2]) ? ' data-val="'.$b[2].'"' : '';
                echo '<button class="editor-btn" data-cmd="'.$b[0].'"'.$val.' type="button" title="'.$b[0].'"><span class="material-icons" style="font-size:18px">'.$b[1].'</span></button>';
              } ?>
            </div>
            <div id="content-editor" contenteditable="true"><?= $post['content']??'' ?></div>
            <input type="hidden" name="content" id="content-hidden" value="<?= htmlspecialchars($post['content']??'') ?>">
          </div>
        </div>

        <!-- Painel lateral -->
        <div style="display:flex;flex-direction:column;gap:16px">
          <!-- Publicar -->
          <div class="card card-body">
            <div class="form-group">
              <label class="form-label">Status</label>
              <select name="status" class="form-control">
                <option value="draft" <?= ($post['status']??'draft')==='draft'?'selected':'' ?>>📝 Rascunho</option>
                <option value="published" <?= ($post['status']??'')==='published'?'selected':'' ?>>✅ Publicado</option>
                <option value="archived" <?= ($post['status']??'')==='archived'?'selected':'' ?>>📦 Arquivado</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Tipo</label>
              <select name="post_type" class="form-control">
                <option value="comunicado" <?= ($post['type']??$type)==='comunicado'?'selected':'' ?>>📢 Comunicado</option>
                <option value="noticia" <?= ($post['type']??$type)==='noticia'?'selected':'' ?>>📰 Notícia</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Categoria</label>
              <select name="category_id" class="form-control">
                <option value="">— Sem categoria —</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($post['category_id']??'')==$cat['id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <label style="display:flex;align-items:center;gap:10px;margin-bottom:10px;cursor:pointer;font-size:14px">
              <input type="checkbox" name="is_public" value="1" <?= ($post['is_public']??1)?'checked':'' ?>> Visível na área pública
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px">
              <input type="checkbox" name="is_featured" value="1" <?= ($post['is_featured']??0)?'checked':'' ?>> ⭐ Publicação em destaque
            </label>
          </div>

          <!-- Imagem de capa -->
          <div class="card card-body">
            <div style="font-size:14px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px">
              <span class="material-icons" style="color:var(--primary);font-size:18px">image</span>
              Imagem de Capa
            </div>

            <?php $hasCover = !empty($post['cover_image']) && file_exists(UPLOAD_DIR . $post['cover_image']); ?>

            <!-- Preview atual -->
            <div id="imagePreviewWrap" style="<?= $hasCover ? '' : 'display:none' ?>">
              <div class="image-preview-wrap">
                <img id="coverPreviewImg" src="<?= $hasCover ? UPLOAD_URL . htmlspecialchars($post['cover_image']) : '' ?>" alt="Preview">
                <div class="image-preview-overlay">
                  <button type="button" onclick="removeImage()">
                    <span class="material-icons">delete</span> Remover imagem
                  </button>
                </div>
              </div>
              <?php if ($hasCover): ?>
              <div class="image-info">
                <span class="material-icons">info</span>
                <?= htmlspecialchars(basename($post['cover_image'])) ?>
              </div>
              <?php endif; ?>
            </div>

            <!-- Área de upload drag-drop -->
            <div class="upload-area" id="uploadArea" style="<?= $hasCover ? 'display:none' : '' ?>">
              <input type="file" name="cover_image" id="coverFile" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/bmp,image/tiff" onchange="previewImage(this)">
              <div class="upload-icon"><span class="material-icons">cloud_upload</span></div>
              <p><strong>Clique ou arraste</strong> uma imagem aqui</p>
              <div class="upload-hint">JPG, PNG, GIF, WEBP, BMP, TIFF — Máx. 15MB</div>
            </div>

            <!-- Metadados da imagem -->
            <div id="imageMetaFields" style="margin-top:12px">
              <div class="form-group">
                <label class="form-label" style="font-size:12px">Texto alternativo (Alt)</label>
                <input type="text" name="cover_image_alt" class="form-control" style="font-size:13px" value="<?= htmlspecialchars($post['cover_image_alt']??'') ?>" placeholder="Descrição da imagem para acessibilidade">
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" style="font-size:12px">Legenda / Crédito</label>
                <input type="text" name="cover_image_caption" class="form-control" style="font-size:13px" value="<?= htmlspecialchars($post['cover_image_caption']??'') ?>" placeholder="Foto: Nome Sobrenome / Fonte">
              </div>
            </div>
          </div>

          <div style="display:flex;flex-direction:column;gap:10px">
            <button type="submit" class="btn btn-primary"><span class="material-icons">save</span> Salvar Publicação</button>
            <a href="posts.php?type=<?= $post['type']??$type ?>" class="btn btn-ghost">Cancelar</a>
          </div>
        </div>
      </div>
    </form>
  <?php endif; ?>

  </main>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('coverPreviewImg').src = e.target.result;
    document.getElementById('imagePreviewWrap').style.display = 'block';
    document.getElementById('uploadArea').style.display = 'none';
    document.getElementById('removeImageFlag').value = '0';
  };
  reader.readAsDataURL(file);
}

function removeImage() {
  if (!confirm('Remover a imagem de capa?')) return;
  document.getElementById('coverPreviewImg').src = '';
  document.getElementById('imagePreviewWrap').style.display = 'none';
  document.getElementById('uploadArea').style.display = 'block';
  document.getElementById('removeImageFlag').value = '1';
  const fi = document.getElementById('coverFile');
  if (fi) fi.value = '';
}

// Drag & drop
const uploadArea = document.getElementById('uploadArea');
if (uploadArea) {
  uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
  uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
  uploadArea.addEventListener('drop', e => {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    const fi = document.getElementById('coverFile');
    if (fi && e.dataTransfer.files.length) {
      fi.files = e.dataTransfer.files;
      previewImage(fi);
    }
  });
}
</script>
</body>
</html>
