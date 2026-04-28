<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$action  = $_GET['action'] ?? 'list';
$id      = (int) ($_GET['id'] ?? 0);
$isDark  = ($_SESSION['dark_mode'] ?? false);
$success = $error = '';

// ── DELETE ────────────────────────────────────────────────────
if ($action === 'delete' && $id) {
    if (!verifyCsrf($_GET['csrf'] ?? '')) {
        $error = 'Token inválido.';
    } else {
        Database::query('DELETE FROM nav_items WHERE id=?', [$id]);
        header('Location: nav.php?deleted=1');
        exit;
    }
}

// ── TOGGLE ATIVO/INATIVO ──────────────────────────────────────
if ($action === 'toggle' && $id) {
    Database::query('UPDATE nav_items SET active = NOT active WHERE id=?', [$id]);
    header('Location: nav.php');
    exit;
}

// ── SALVAR (POST) ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // BUG CORRIGIDO #1: pega action e id do POST (campo hidden),
    // não mais apenas de $_GET — assim o form de edição funciona.
    $postAction = $_POST['_action'] ?? 'new';
    $postId     = (int) ($_POST['_id'] ?? 0);

    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Token inválido.';
    } else {
        $label    = sanitize($_POST['label'] ?? '');
        $url      = trim($_POST['url'] ?? '');           // BUG CORRIGIDO #2: não sanitize a URL
        $icon     = sanitize($_POST['icon'] ?? '');
        $parentId = (int) ($_POST['parent_id'] ?? 0) ?: null;
        $sortOrd  = (int) ($_POST['sort_order'] ?? 0);
        $newTab   = isset($_POST['open_new_tab']) ? 1 : 0;
        $active   = isset($_POST['active']) ? 1 : 0;

        if (!$label) {
            $error = 'Rótulo obrigatório.';
        } elseif ($postAction === 'new') {
            Database::insert(
                "INSERT INTO nav_items (label, url, icon, parent_id, sort_order, open_new_tab, active)
                 VALUES (?, ?, ?, ?, ?, ?, 1)",
                [$label, $url, $icon, $parentId, $sortOrd, $newTab]
            );
            header('Location: nav.php?saved=1');
            exit;
        } else {
            // BUG CORRIGIDO #3: usa $postId vindo do campo hidden,
            // garantindo que o UPDATE atualiza o item correto.
            Database::query(
                "UPDATE nav_items
                 SET label=?, url=?, icon=?, parent_id=?, sort_order=?, open_new_tab=?, active=?
                 WHERE id=?",
                [$label, $url, $icon, $parentId, $sortOrd, $newTab, $active, $postId]
            );
            header('Location: nav.php?updated=1');
            exit;
        }
    }
}

// ── DADOS PARA A VIEW ─────────────────────────────────────────
$navItems = Database::fetchAll('SELECT * FROM nav_items ORDER BY sort_order');
$navItem  = ($action === 'edit' && $id)
    ? Database::fetch('SELECT * FROM nav_items WHERE id=?', [$id])
    : null;
$parents  = Database::fetchAll(
    'SELECT * FROM nav_items WHERE parent_id IS NULL AND id != ? ORDER BY sort_order',
    [$id ?: 0]
);
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?= $isDark ? 'dark' : 'light' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Menu Navegação — Admin Acqua</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <?= getColorVarsStyle() ?>
</head>
<body class="fade-in">

<nav class="navbar">
  <a href="<?= BASE_URL ?>/admin/index.php" class="navbar-brand">
    <div class="logo-icon"><span class="material-icons">admin_panel_settings</span></div>
    <div class="brand-text">Admin Acqua <small>Menu Nav</small></div>
  </a>
  <div class="navbar-end">
    <button class="dark-toggle <?= $isDark ? 'on' : '' ?>"></button>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-ghost btn-sm">
      <span class="material-icons">home</span>
    </a>
    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-ghost btn-sm" style="color:#dc3545">
      <span class="material-icons">logout</span>
    </a>
    <button class="btn btn-ghost btn-icon mobile-menu-btn" id="sidebarToggle">
      <span class="material-icons">menu</span>
    </button>
  </div>
</nav>

<div class="admin-layout">
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-label">Conteúdo</div>
    <a href="index.php"                      class="sidebar-link"><span class="material-icons">dashboard</span> Dashboard</a>
    <a href="posts.php?type=comunicado"      class="sidebar-link"><span class="material-icons">campaign</span> Comunicados</a>
    <a href="posts.php?type=noticia"         class="sidebar-link"><span class="material-icons">newspaper</span> Notícias</a>
    <a href="posts.php?action=new"           class="sidebar-link"><span class="material-icons">add_circle</span> Novo Post</a>
    <a href="categories.php"                 class="sidebar-link"><span class="material-icons">label</span> Categorias</a>
    <div class="sidebar-label">Módulos</div>
    <a href="modules.php?cat=sistema"        class="sidebar-link"><span class="material-icons">apps</span> Sistemas</a>
    <a href="modules.php?cat=link_rapido"    class="sidebar-link"><span class="material-icons">bolt</span> Links Rápidos</a>
    <a href="nav.php"                        class="sidebar-link active"><span class="material-icons">menu_open</span> Menu Nav</a>
    <div class="sidebar-label">Administração</div>
    <a href="ramais.php"                     class="sidebar-link"><span class="material-icons">phone_in_talk</span> Ramais</a>
    <a href="users.php"                      class="sidebar-link"><span class="material-icons">group</span> Usuários</a>
    <a href="settings.php"                   class="sidebar-link"><span class="material-icons">settings</span> Configurações</a>
  </aside>

  <main class="admin-main">

    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success" data-auto-dismiss>
      <span class="material-icons">check_circle</span> Item removido com sucesso.
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success" data-auto-dismiss>
      <span class="material-icons">check_circle</span> Novo item criado com sucesso!
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success" data-auto-dismiss>
      <span class="material-icons">check_circle</span> Item atualizado com sucesso!
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger">
      <span class="material-icons">error</span> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 400px;gap:24px;align-items:start">

      <!-- ── LISTA DE ITENS ── -->
      <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
          <h1 style="font-size:22px">Itens do Menu de Navegação</h1>
          <a href="nav.php?action=new" class="btn btn-primary btn-sm">
            <span class="material-icons">add</span> Novo Item
          </a>
        </div>

        <div class="alert alert-info" style="margin-bottom:16px">
          <span class="material-icons">info</span>
          Os itens abaixo são exibidos na barra de navegação da intranet.
          Use o campo <strong>Ordem</strong> para controlar a sequência.
        </div>

        <div class="card">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Ícone</th>
                  <th>Rótulo</th>
                  <th>URL</th>
                  <th>Ordem</th>
                  <th>Ativo</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($navItems as $item): ?>
                <tr>
                  <td>
                    <?php if ($item['icon']): ?>
                    <span class="material-icons" style="font-size:20px;color:var(--primary)">
                      <?= htmlspecialchars($item['icon']) ?>
                    </span>
                    <?php else: ?>—<?php endif; ?>
                  </td>
                  <td style="font-weight:600;<?= $item['parent_id'] ? 'padding-left:24px' : '' ?>">
                    <?= $item['parent_id'] ? '↳ ' : '' ?>
                    <?= htmlspecialchars($item['label']) ?>
                  </td>
                  <td class="text-sm text-muted"
                      style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                      title="<?= htmlspecialchars($item['url'] ?? '') ?>">
                    <?= htmlspecialchars($item['url'] ?? '—') ?>
                  </td>
                  <td><?= (int)$item['sort_order'] ?></td>
                  <td>
                    <a href="nav.php?action=toggle&id=<?= $item['id'] ?>" class="btn btn-ghost btn-sm btn-icon" title="<?= $item['active'] ? 'Desativar' : 'Ativar' ?>">
                      <span class="material-icons" style="color:<?= $item['active'] ? '#28a745' : '#dc3545' ?>">
                        <?= $item['active'] ? 'toggle_on' : 'toggle_off' ?>
                      </span>
                    </a>
                  </td>
                  <td>
                    <a href="nav.php?action=edit&id=<?= $item['id'] ?>"
                       class="btn btn-ghost btn-sm btn-icon" title="Editar">
                      <span class="material-icons" style="font-size:18px">edit</span>
                    </a>
                    <a href="nav.php?action=delete&id=<?= $item['id'] ?>&csrf=<?= csrf() ?>"
                       class="btn btn-ghost btn-sm btn-icon" style="color:#dc3545" title="Remover"
                       data-confirm="Remover o item '<?= htmlspecialchars($item['label']) ?>'?">
                      <span class="material-icons" style="font-size:18px">delete</span>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($navItems)): ?>
                <tr>
                  <td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted)">
                    <span class="material-icons" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4">menu_open</span>
                    Nenhum item cadastrado. Crie o primeiro!
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── FORMULÁRIO ── -->
      <div>
        <div class="card card-body">
          <h2 style="font-size:16px;margin-bottom:20px;display:flex;align-items:center;gap:8px">
            <span class="material-icons" style="color:var(--primary)">
              <?= $action === 'edit' ? 'edit' : 'add_circle' ?>
            </span>
            <?= $action === 'edit' ? 'Editar Item do Menu' : 'Novo Item do Menu' ?>
          </h2>

          <!-- CORREÇÃO: action aponta para nav.php?action=X&id=Y para GETs,
               e os campos hidden _action/_id passam os valores no POST -->
          <form method="POST" action="nav.php">

            <!-- Campos hidden que passam action e id para o PHP no POST -->
            <input type="hidden" name="_action" value="<?= $action === 'edit' ? 'edit' : 'new' ?>">
            <input type="hidden" name="_id"     value="<?= $navItem['id'] ?? 0 ?>">
            <input type="hidden" name="csrf"    value="<?= csrf() ?>">

            <div class="form-group">
              <label class="form-label">Rótulo *</label>
              <input type="text" name="label" class="form-control" required
                     value="<?= htmlspecialchars($navItem['label'] ?? '') ?>"
                     placeholder="Ex: Início, Comunicados...">
            </div>

            <div class="form-group">
              <label class="form-label">URL</label>
              <input type="text" name="url" class="form-control"
                     value="<?= htmlspecialchars($navItem['url'] ?? '') ?>"
                     placeholder="index.php?page=comunicados">
              <small class="text-muted" style="font-size:11px;margin-top:4px;display:block">
                Use URL relativa (ex: <code>index.php?page=comunicados</code>) ou absoluta (ex: <code>https://site.com</code>)
              </small>
            </div>

            <div class="form-group">
              <label class="form-label">Ícone (Material Icons)</label>
              <div style="display:flex;gap:8px;align-items:center">
                <input type="text" name="icon" id="iconInput" class="form-control"
                       value="<?= htmlspecialchars($navItem['icon'] ?? '') ?>"
                       placeholder="home, campaign, newspaper..."
                       oninput="document.getElementById('iconPreview').textContent=this.value">
                <span class="material-icons" id="iconPreview"
                      style="font-size:28px;color:var(--primary);flex-shrink:0;width:32px;text-align:center">
                  <?= htmlspecialchars($navItem['icon'] ?? 'link') ?>
                </span>
              </div>
              <small class="text-muted" style="font-size:11px;margin-top:4px;display:block">
                Veja os ícones em: <a href="https://fonts.google.com/icons" target="_blank">fonts.google.com/icons</a>
              </small>
            </div>

            <div class="form-group">
              <label class="form-label">Item pai (submenu)</label>
              <select name="parent_id" class="form-control">
                <option value="">— Nível raiz —</option>
                <?php foreach ($parents as $p): ?>
                <option value="<?= $p['id'] ?>"
                  <?= ($navItem['parent_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($p['label']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Ordem de exibição</label>
              <input type="number" name="sort_order" class="form-control"
                     value="<?= (int)($navItem['sort_order'] ?? 0) ?>" min="0">
              <small class="text-muted" style="font-size:11px;margin-top:4px;display:block">
                Menor número aparece primeiro (0, 1, 2...)
              </small>
            </div>

            <div style="display:flex;gap:20px;margin-bottom:20px">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="open_new_tab" value="1"
                       <?= ($navItem['open_new_tab'] ?? 0) ? 'checked' : '' ?>>
                <span style="font-size:14px">Abrir em nova aba</span>
              </label>
              <?php if ($action === 'edit'): ?>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="active" value="1"
                       <?= ($navItem['active'] ?? 1) ? 'checked' : '' ?>>
                <span style="font-size:14px">Item ativo</span>
              </label>
              <?php endif; ?>
            </div>

            <div style="display:flex;gap:10px">
              <button type="submit" class="btn btn-primary">
                <span class="material-icons">save</span>
                <?= $action === 'edit' ? 'Salvar Alterações' : 'Criar Item' ?>
              </button>
              <?php if ($action === 'edit'): ?>
              <a href="nav.php" class="btn btn-ghost">
                <span class="material-icons">close</span> Cancelar
              </a>
              <?php endif; ?>
            </div>

          </form>
        </div>

        <?php if ($action !== 'edit'): ?>
        <!-- Dica de ícones -->
        <div class="card card-body" style="margin-top:16px">
          <p style="font-size:13px;font-weight:700;margin-bottom:10px;color:var(--text-muted)">
            <span class="material-icons" style="font-size:15px;vertical-align:middle">tips_and_updates</span>
            Ícones sugeridos
          </p>
          <div style="display:flex;flex-wrap:wrap;gap:8px">
            <?php foreach (['home','campaign','newspaper','apps','phone_in_talk','people','bolt','public','info','calendar_month','folder','star','link','settings'] as $ic): ?>
            <button type="button" onclick="document.querySelector('[name=icon]').value='<?= $ic ?>';document.getElementById('iconPreview').textContent='<?= $ic ?>'"
                    style="display:flex;align-items:center;gap:4px;padding:5px 10px;border:1px solid var(--border);border-radius:6px;background:var(--bg);cursor:pointer;font-size:12px;color:var(--text);transition:.15s"
                    title="<?= $ic ?>">
              <span class="material-icons" style="font-size:16px;color:var(--primary)"><?= $ic ?></span>
              <?= $ic ?>
            </button>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
