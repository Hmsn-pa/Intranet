<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$page = $_GET['page'] ?? 'home';
$pageTitle = match($page) {
    'comunicados' => 'Comunicados',
    'noticias'    => 'Notícias Externas',
    'sistemas'    => 'Sistemas',
    'post'        => '',
    'busca'       => 'Busca',
    'perfil'      => 'Meu Perfil',
    default       => ''
};

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($page === 'home'): ?>
<!-- ========================================================
     HOME — Comunicados e Notícias como conteúdo principal
     ======================================================== -->

  <!-- Hero — nome da unidade em destaque -->
  <?php
  $heroTitle    = getSetting('hero_title',    getSetting('site_tagline', 'Unidade de Saúde'));
  $heroSubtitle = getSetting('hero_subtitle', 'Portal de Comunicação Institucional');
  ?>
  <div class="hero" style="padding:44px 0 40px">
    <div class="container hero-content">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px">
        <div>
          <h1 style="font-size:42px;margin-bottom:6px;font-weight:800;letter-spacing:-.8px;line-height:1.15;color:#fff">
            <?= htmlspecialchars($heroTitle) ?>
          </h1>
          <p style="font-size:16px;opacity:.82;font-family:var(--font-body);font-weight:400;letter-spacing:.2px;color:#fff">
            <?= htmlspecialchars($heroSubtitle) ?>
          </p>
        </div>
        <!-- Links rápidos no hero -->
        <?php $quickLinks = Database::fetchAll("SELECT * FROM modules WHERE category='link_rapido' AND active=1 ORDER BY sort_order LIMIT 4"); ?>
        <?php if ($quickLinks): ?>
        <div style="display:flex;flex-wrap:wrap;gap:8px">
          <?php foreach ($quickLinks as $lnk): ?>
          <a href="<?= htmlspecialchars($lnk['url']) ?>" target="<?= $lnk['target'] ?>"
             style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:30px;background:rgba(255,255,255,.15);color:#fff;font-size:13px;font-weight:600;border:1px solid rgba(255,255,255,.3);transition:.2s;text-decoration:none;backdrop-filter:blur(4px)"
             onmouseover="this.style.background='rgba(255,255,255,.25)'"
             onmouseout="this.style.background='rgba(255,255,255,.15)'">
            <span class="material-icons" style="font-size:15px"><?= htmlspecialchars($lnk['icon']) ?></span>
            <?= htmlspecialchars($lnk['name']) ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="container main-content">

    <!-- ── SISTEMAS (barra compacta de acesso rápido) ── -->
    <?php $sistemas = Database::fetchAll("SELECT * FROM modules WHERE category='sistema' AND active=1 ORDER BY sort_order"); ?>
    <?php if ($sistemas): ?>
    <div class="card mb-3" style="padding:16px 20px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <span style="font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;color:var(--text)">
          <span class="material-icons" style="font-size:18px;color:var(--primary)">apps</span>
          Sistemas Institucionais
        </span>
        <a href="index.php?page=sistemas" class="btn btn-ghost btn-sm">Ver todos</a>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:10px">
        <?php foreach ($sistemas as $sys): ?>
        <a href="<?= htmlspecialchars($sys['url']) ?>" target="<?= $sys['target'] ?>"
           style="display:flex;align-items:center;gap:8px;padding:9px 16px;border-radius:10px;background:var(--bg);border:1px solid var(--border);text-decoration:none;transition:.2s;color:var(--text)"
           onmouseover="this.style.borderColor='<?= htmlspecialchars($sys['color']) ?>';this.style.background='<?= htmlspecialchars($sys['color']) ?>15'"
           onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--bg)'">
          <div style="width:30px;height:30px;border-radius:8px;background:<?= htmlspecialchars($sys['color']) ?>20;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <?php if (!empty($sys['icon_image']) && file_exists(UPLOAD_DIR . 'modules/' . $sys['icon_image'])): ?>
            <img src="<?= UPLOAD_URL ?>modules/<?= htmlspecialchars($sys['icon_image']) ?>" style="width:18px;height:18px;object-fit:contain" alt="">
            <?php else: ?>
            <span class="material-icons" style="font-size:16px;color:<?= htmlspecialchars($sys['color']) ?>"><?= htmlspecialchars($sys['icon']) ?></span>
            <?php endif; ?>
          </div>
          <span style="font-size:13px;font-weight:600"><?= htmlspecialchars($sys['name']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- ── POST EM DESTAQUE (se houver) ── -->
    <?php $featured = Database::fetch(
      "SELECT p.*,u.name as author_name FROM posts p LEFT JOIN users u ON u.id=p.author_id
       WHERE p.status='published' AND p.is_featured=1 ORDER BY p.published_at DESC LIMIT 1"
    ); ?>
    <?php if ($featured): ?>
    <div class="mb-3">
      <a href="index.php?page=post&slug=<?= urlencode($featured['slug']) ?>" class="post-featured">
        <div class="pf-image">
          <?php if ($featured['cover_image'] && file_exists(UPLOAD_DIR . $featured['cover_image'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($featured['cover_image']) ?>"
               alt="<?= htmlspecialchars($featured['cover_image_alt'] ?? $featured['title']) ?>">
          <?php else: ?>
          <div class="pf-image-placeholder">
            <span class="material-icons"><?= $featured['type']==='comunicado'?'campaign':'newspaper' ?></span>
          </div>
          <?php endif; ?>
          <span class="post-cover-badge <?= $featured['type'] ?>">
            <?= $featured['type']==='comunicado'?'Comunicado':'Notícia' ?>
          </span>
        </div>
        <div class="pf-body">
          <div class="pf-label"><span class="material-icons">star</span> Em Destaque</div>
          <div class="pf-title"><?= htmlspecialchars($featured['title']) ?></div>
          <?php if ($featured['summary']): ?>
          <div class="pf-summary"><?= htmlspecialchars(mb_substr($featured['summary'], 0, 180)) ?>…</div>
          <?php endif; ?>
          <div class="pf-meta">
            <div class="avatar-xs"><?= mb_strtoupper(mb_substr($featured['author_name'], 0, 1)) ?></div>
            <?= htmlspecialchars($featured['author_name']) ?>
            <span>·</span><?= formatDate($featured['published_at']) ?>
          </div>
        </div>
      </a>
    </div>
    <?php endif; ?>

    <!-- ── COMUNICADOS E NOTÍCIAS LADO A LADO ── -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px" class="two-col-grid">

      <!-- COMUNICADOS INTERNOS -->
      <div>
        <div class="section-header">
          <span class="section-title">
            <span class="material-icons">campaign</span> Comunicados
          </span>
          <a href="index.php?page=comunicados" class="btn btn-ghost btn-sm">Ver todos →</a>
        </div>
        <?php
        $comunicados = Database::fetchAll(
          "SELECT p.*,u.name as author_name FROM posts p
           LEFT JOIN users u ON u.id=p.author_id
           WHERE p.type='comunicado' AND p.status='published'
           ORDER BY p.published_at DESC LIMIT 5"
        );
        ?>
        <div style="display:flex;flex-direction:column;gap:14px">
          <?php foreach ($comunicados as $post): ?>
          <a href="index.php?page=post&slug=<?= urlencode($post['slug']) ?>" style="text-decoration:none" class="post-list-item">
            <div class="card" style="padding:0;overflow:hidden;display:flex;min-height:88px;transition:.2s">
              <!-- Imagem miniatura ou barra colorida -->
              <?php if ($post['cover_image'] && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
              <div style="width:100px;flex-shrink:0;overflow:hidden;position:relative">
                <img src="<?= UPLOAD_URL . htmlspecialchars($post['cover_image']) ?>"
                     alt="" style="width:100%;height:100%;object-fit:cover;display:block;transition:.3s"
                     class="post-list-thumb">
              </div>
              <?php else: ?>
              <div style="width:5px;background:var(--primary);flex-shrink:0"></div>
              <?php endif; ?>
              <!-- Conteúdo -->
              <div style="padding:14px 16px;display:flex;flex-direction:column;justify-content:center;flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;flex-wrap:wrap">
                  <span class="badge badge-comunicado">Comunicado</span>
                  <?php if ($post['is_featured']): ?>
                  <span class="material-icons" style="font-size:14px;color:#ffc107">star</span>
                  <?php endif; ?>
                  <span style="font-size:11px;color:var(--text-muted)"><?= formatDate($post['published_at']) ?></span>
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--text);line-height:1.35;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">
                  <?= htmlspecialchars($post['title']) ?>
                </div>
                <?php if ($post['summary']): ?>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical">
                  <?= htmlspecialchars($post['summary']) ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
          <?php if (empty($comunicados)): ?>
          <div class="card card-body text-muted" style="text-align:center;font-size:14px">
            <span class="material-icons" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4">campaign</span>
            Nenhum comunicado publicado ainda.
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- NOTÍCIAS EXTERNAS -->
      <div>
        <div class="section-header">
          <span class="section-title">
            <span class="material-icons">newspaper</span> Notícias Externas
          </span>
          <a href="index.php?page=noticias" class="btn btn-ghost btn-sm">Ver todas →</a>
        </div>
        <?php
        $noticias = Database::fetchAll(
          "SELECT p.*,u.name as author_name FROM posts p
           LEFT JOIN users u ON u.id=p.author_id
           WHERE p.type='noticia' AND p.status='published'
           ORDER BY p.published_at DESC LIMIT 5"
        );
        ?>
        <div style="display:flex;flex-direction:column;gap:14px">
          <?php foreach ($noticias as $post): ?>
          <a href="index.php?page=post&slug=<?= urlencode($post['slug']) ?>" style="text-decoration:none" class="post-list-item">
            <div class="card" style="padding:0;overflow:hidden;display:flex;min-height:88px;transition:.2s">
              <?php if ($post['cover_image'] && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
              <div style="width:100px;flex-shrink:0;overflow:hidden;position:relative">
                <img src="<?= UPLOAD_URL . htmlspecialchars($post['cover_image']) ?>"
                     alt="" style="width:100%;height:100%;object-fit:cover;display:block;transition:.3s"
                     class="post-list-thumb">
              </div>
              <?php else: ?>
              <div style="width:5px;background:var(--accent);flex-shrink:0"></div>
              <?php endif; ?>
              <div style="padding:14px 16px;display:flex;flex-direction:column;justify-content:center;flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;flex-wrap:wrap">
                  <span class="badge badge-noticia">Notícia</span>
                  <?php if ($post['is_featured']): ?>
                  <span class="material-icons" style="font-size:14px;color:#ffc107">star</span>
                  <?php endif; ?>
                  <span style="font-size:11px;color:var(--text-muted)"><?= formatDate($post['published_at']) ?></span>
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--text);line-height:1.35;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">
                  <?= htmlspecialchars($post['title']) ?>
                </div>
                <?php if ($post['summary']): ?>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical">
                  <?= htmlspecialchars($post['summary']) ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
          <?php if (empty($noticias)): ?>
          <div class="card card-body text-muted" style="text-align:center;font-size:14px">
            <span class="material-icons" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4">newspaper</span>
            Nenhuma notícia publicada ainda.
          </div>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- /.two-col-grid -->
  </div>

<!-- ========================================================
     PÁGINA DE LISTAGEM — /comunicados ou /noticias
     ======================================================== -->
<?php elseif (in_array($page, ['comunicados','noticias'])): ?>
  <?php
  $type     = $page === 'comunicados' ? 'comunicado' : 'noticia';
  $label    = $page === 'comunicados' ? 'Comunicados' : 'Notícias Externas';
  $icon     = $page === 'comunicados' ? 'campaign' : 'newspaper';
  $perPage  = (int) getSetting('posts_per_page', '10');
  $curPage  = max(1, (int) ($_GET['p'] ?? 1));
  $offset   = ($curPage - 1) * $perPage;
  $cat      = (int) ($_GET['cat'] ?? 0);
  $catWhere = $cat ? "AND p.category_id = $cat" : '';
  $total    = Database::count("SELECT COUNT(*) FROM posts p WHERE p.type='$type' AND p.status='published' $catWhere");
  $posts    = Database::fetchAll(
    "SELECT p.*,u.name as author_name,c.name as cat_name,c.color as cat_color
     FROM posts p LEFT JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id
     WHERE p.type='$type' AND p.status='published' $catWhere
     ORDER BY p.published_at DESC LIMIT $perPage OFFSET $offset"
  );
  $cats   = Database::fetchAll("SELECT * FROM categories WHERE type='$type' ORDER BY name");
  $pages  = ceil($total / $perPage);
  ?>
  <div class="container main-content fade-in">
    <div class="section-header">
      <span class="section-title">
        <span class="material-icons"><?= $icon ?></span> <?= $label ?>
      </span>
      <span class="text-muted text-sm"><?= $total ?> publicação(ões)</span>
    </div>

    <!-- Filtros por categoria -->
    <?php if ($cats): ?>
    <div class="quick-links mb-3">
      <a href="?page=<?= $page ?>" class="quick-link <?= !$cat?'':'btn-ghost' ?>">Todos</a>
      <?php foreach ($cats as $c): ?>
      <a href="?page=<?= $page ?>&cat=<?= $c['id'] ?>" class="quick-link <?= $cat==$c['id']?'':'btn-ghost' ?>"
         style="background:<?= $cat==$c['id'] ? $c['color'] : '' ?>;color:<?= $cat==$c['id'] ? '#fff' : '' ?>">
        <?= htmlspecialchars($c['name']) ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Grid de posts com imagem grande -->
    <div class="post-grid">
      <?php foreach ($posts as $post): ?>
      <a href="index.php?page=post&slug=<?= urlencode($post['slug']) ?>" class="post-card">
        <div class="post-cover">
          <?php if ($post['cover_image'] && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($post['cover_image']) ?>"
               alt="<?= htmlspecialchars($post['cover_image_alt'] ?? $post['title']) ?>" loading="lazy">
          <?php else: ?>
          <div class="post-cover-placeholder">
            <span class="material-icons"><?= $icon ?></span>
          </div>
          <?php endif; ?>
          <span class="post-cover-badge <?= $type ?>"><?= $label ?></span>
          <?php if ($post['is_featured']): ?>
          <div class="featured-star"><span class="material-icons">star</span> Destaque</div>
          <?php endif; ?>
        </div>
        <div class="post-body">
          <div class="post-meta">
            <?php if ($post['cat_name']): ?>
            <span class="badge" style="background:<?= $post['cat_color'] ?>22;color:<?= $post['cat_color'] ?>">
              <?= htmlspecialchars($post['cat_name']) ?>
            </span>
            <?php endif; ?>
            <span class="post-date"><?= formatDate($post['published_at']) ?></span>
          </div>
          <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
          <?php if ($post['summary']): ?>
          <div class="post-summary"><?= htmlspecialchars(mb_substr($post['summary'], 0, 130)) ?>…</div>
          <?php endif; ?>
          <div class="post-footer">
            <div class="post-author">
              <div class="avatar-xs"><?= mb_strtoupper(mb_substr($post['author_name'], 0, 1)) ?></div>
              <?= htmlspecialchars($post['author_name']) ?>
            </div>
            <span class="text-muted text-xs">
              <span class="material-icons" style="font-size:13px;vertical-align:middle">visibility</span>
              <?= $post['views'] ?>
            </span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($posts)): ?>
    <div class="card card-body text-muted" style="text-align:center;padding:48px;margin-top:16px">
      <span class="material-icons" style="font-size:48px;display:block;margin-bottom:12px;opacity:.3"><?= $icon ?></span>
      Nenhuma publicação encontrada.
    </div>
    <?php endif; ?>

    <!-- Paginação -->
    <?php if ($pages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?page=<?= $page ?>&p=<?= $i ?><?= $cat ? '&cat='.$cat : '' ?>"
         class="page-btn <?= $i === $curPage ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>

<!-- ========================================================
     SINGLE POST
     ======================================================== -->
<?php elseif ($page === 'post'): ?>
  <?php
  $slug = $_GET['slug'] ?? '';
  $post = Database::fetch(
    "SELECT p.*,u.name as author_name,u.sector,c.name as cat_name,c.color as cat_color
     FROM posts p LEFT JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id
     WHERE p.slug=? AND p.status='published'", [$slug]
  );
  if (!$post) { header('Location: ' . BASE_URL . '/index.php'); exit; }
  Database::query('UPDATE posts SET views=views+1 WHERE id=?', [$post['id']]);
  $pageTitle = $post['title'];
  $backPage  = $post['type'] === 'comunicado' ? 'comunicados' : 'noticias';
  ?>
  <div class="container main-content fade-in" style="max-width:880px">
    <div class="mb-2" style="display:flex;align-items:center;gap:10px">
      <a href="index.php?page=<?= $backPage ?>" class="btn btn-ghost btn-sm">
        <span class="material-icons">arrow_back</span>
        <?= $post['type']==='comunicado' ? 'Comunicados' : 'Notícias Externas' ?>
      </a>
    </div>
    <div class="card" style="overflow:hidden">
      <!-- Imagem hero com destaque total -->
      <?php if ($post['cover_image'] && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
      <figure class="post-hero-image">
        <img src="<?= UPLOAD_URL . htmlspecialchars($post['cover_image']) ?>"
             alt="<?= htmlspecialchars($post['cover_image_alt'] ?? $post['title']) ?>">
        <?php if ($post['cover_image_caption']): ?>
        <figcaption><?= htmlspecialchars($post['cover_image_caption']) ?></figcaption>
        <?php endif; ?>
      </figure>
      <?php endif; ?>

      <div class="card-body" style="padding:36px 44px">
        <!-- Meta topo -->
        <div class="post-meta mb-2">
          <span class="badge badge-<?= $post['type'] ?>">
            <?= $post['type']==='comunicado' ? 'Comunicado' : 'Notícia Externa' ?>
          </span>
          <?php if ($post['cat_name']): ?>
          <span class="badge" style="background:<?= $post['cat_color'] ?>22;color:<?= $post['cat_color'] ?>">
            <?= htmlspecialchars($post['cat_name']) ?>
          </span>
          <?php endif; ?>
          <?php if ($post['is_featured']): ?>
          <span style="display:inline-flex;align-items:center;gap:3px;font-size:11px;font-weight:700;color:#d39e00">
            <span class="material-icons" style="font-size:14px">star</span> Destaque
          </span>
          <?php endif; ?>
          <span class="text-muted text-sm">
            <?= formatDate($post['published_at'], 'd/m/Y \à\s H:i') ?>
          </span>
        </div>

        <!-- Título -->
        <h1 style="font-size:28px;margin-bottom:18px;line-height:1.3">
          <?= htmlspecialchars($post['title']) ?>
        </h1>

        <!-- Resumo em destaque -->
        <?php if ($post['summary']): ?>
        <p style="font-size:16px;color:var(--text-muted);border-left:4px solid var(--primary);padding:14px 20px;background:var(--primary-xlight);border-radius:0 var(--radius-sm) var(--radius-sm) 0;margin-bottom:28px;line-height:1.7">
          <?= htmlspecialchars($post['summary']) ?>
        </p>
        <?php endif; ?>

        <!-- Conteúdo HTML -->
        <div class="post-content"><?= $post['content'] ?></div>

        <!-- Rodapé do post -->
        <div style="margin-top:36px;padding-top:22px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
          <div style="display:flex;align-items:center;gap:12px">
            <div class="avatar-xs" style="width:38px;height:38px;font-size:15px">
              <?= mb_strtoupper(mb_substr($post['author_name'], 0, 1)) ?>
            </div>
            <div>
              <div style="font-weight:700;font-size:14px"><?= htmlspecialchars($post['author_name']) ?></div>
              <div class="text-muted text-xs"><?= htmlspecialchars($post['sector'] ?? '') ?></div>
            </div>
          </div>
          <span class="text-muted text-sm">
            <span class="material-icons" style="font-size:15px;vertical-align:middle">visibility</span>
            <?= $post['views'] ?> visualizações
          </span>
        </div>
      </div>
    </div>

    <!-- Relacionados -->
    <?php
    $related = Database::fetchAll(
      "SELECT p.*,u.name as author_name FROM posts p LEFT JOIN users u ON u.id=p.author_id
       WHERE p.type=? AND p.status='published' AND p.id!=?
       ORDER BY p.published_at DESC LIMIT 3",
      [$post['type'], $post['id']]
    );
    ?>
    <?php if ($related): ?>
    <div style="margin-top:32px">
      <div class="section-header">
        <span class="section-title">
          <span class="material-icons"><?= $post['type']==='comunicado'?'campaign':'newspaper' ?></span>
          Mais <?= $post['type']==='comunicado' ? 'Comunicados' : 'Notícias Externas' ?>
        </span>
      </div>
      <div class="post-grid">
        <?php foreach ($related as $r): ?>
        <a href="index.php?page=post&slug=<?= urlencode($r['slug']) ?>" class="post-card">
          <div class="post-cover">
            <?php if ($r['cover_image'] && file_exists(UPLOAD_DIR . $r['cover_image'])): ?>
            <img src="<?= UPLOAD_URL . htmlspecialchars($r['cover_image']) ?>" alt="" loading="lazy">
            <?php else: ?>
            <div class="post-cover-placeholder">
              <span class="material-icons"><?= $post['type']==='comunicado'?'campaign':'newspaper' ?></span>
            </div>
            <?php endif; ?>
            <span class="post-cover-badge <?= $r['type'] ?>">
              <?= $r['type']==='comunicado'?'Comunicado':'Notícia' ?>
            </span>
          </div>
          <div class="post-body">
            <div class="post-meta"><span class="post-date"><?= formatDate($r['published_at']) ?></span></div>
            <div class="post-title"><?= htmlspecialchars($r['title']) ?></div>
            <?php if ($r['summary']): ?>
            <div class="post-summary"><?= htmlspecialchars(mb_substr($r['summary'], 0, 100)) ?>…</div>
            <?php endif; ?>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

<!-- ========================================================
     SISTEMAS
     ======================================================== -->
<?php elseif ($page === 'sistemas'): ?>
  <?php $sistemas = Database::fetchAll("SELECT * FROM modules WHERE category='sistema' AND active=1 ORDER BY sort_order"); ?>
  <div class="container main-content fade-in">
    <div class="section-header">
      <span class="section-title"><span class="material-icons">apps</span> Sistemas Institucionais</span>
      <span class="text-muted text-sm"><?= count($sistemas) ?> disponíveis</span>
    </div>
    <div class="systems-grid">
      <?php foreach ($sistemas as $sys): ?>
      <a href="<?= htmlspecialchars($sys['url']) ?>" target="<?= $sys['target'] ?>"
         class="system-card" style="--card-color:<?= htmlspecialchars($sys['color']) ?>">
        <div class="sys-icon">
          <?php if (!empty($sys['icon_image']) && file_exists(UPLOAD_DIR . 'modules/' . $sys['icon_image'])): ?>
          <img src="<?= UPLOAD_URL ?>modules/<?= htmlspecialchars($sys['icon_image']) ?>"
               alt="<?= htmlspecialchars($sys['name']) ?>">
          <?php else: ?>
          <span class="material-icons"><?= htmlspecialchars($sys['icon']) ?></span>
          <?php endif; ?>
        </div>
        <div class="sys-name"><?= htmlspecialchars($sys['name']) ?></div>
        <div class="sys-desc"><?= htmlspecialchars($sys['description']) ?></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

<!-- ========================================================
     BUSCA
     ======================================================== -->
<?php elseif ($page === 'busca'): ?>
  <?php
  $q = sanitize($_GET['q'] ?? '');
  $results = strlen($q) > 2
    ? Database::fetchAll(
        "SELECT p.*,u.name as author_name FROM posts p LEFT JOIN users u ON u.id=p.author_id
         WHERE p.status='published' AND (p.title LIKE ? OR p.summary LIKE ? OR p.content LIKE ?)
         ORDER BY p.published_at DESC LIMIT 24",
        ["%$q%","%$q%","%$q%"]
      )
    : [];
  ?>
  <div class="container main-content fade-in">
    <div class="section-header">
      <span class="section-title">
        <span class="material-icons">search</span>
        Resultados para: <em style="color:var(--primary)">"<?= htmlspecialchars($q) ?>"</em>
      </span>
      <span class="text-muted text-sm"><?= count($results) ?> resultado(s)</span>
    </div>
    <div class="post-grid">
      <?php foreach ($results as $post): ?>
      <a href="index.php?page=post&slug=<?= urlencode($post['slug']) ?>" class="post-card">
        <div class="post-cover">
          <?php if ($post['cover_image'] && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($post['cover_image']) ?>" alt="" loading="lazy">
          <?php else: ?>
          <div class="post-cover-placeholder">
            <span class="material-icons"><?= $post['type']==='comunicado'?'campaign':'newspaper' ?></span>
          </div>
          <?php endif; ?>
          <span class="post-cover-badge <?= $post['type'] ?>"><?= $post['type'] ?></span>
        </div>
        <div class="post-body">
          <div class="post-meta"><span class="post-date"><?= formatDate($post['published_at']) ?></span></div>
          <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
          <?php if ($post['summary']): ?>
          <div class="post-summary"><?= htmlspecialchars(mb_substr($post['summary'],0,110)) ?>…</div>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php if (empty($results) && $q): ?>
    <div class="card card-body text-muted" style="text-align:center;padding:48px">
      <span class="material-icons" style="font-size:48px;display:block;margin-bottom:12px;opacity:.3">search_off</span>
      Nenhum resultado para "<?= htmlspecialchars($q) ?>".
    </div>
    <?php endif; ?>
  </div>

<?php endif; ?>

<style>
/* Hover nos cards de lista */
.post-list-item .card:hover { box-shadow: var(--shadow); transform: translateY(-2px); }
.post-list-item:hover .post-list-thumb { transform: scale(1.08); }
@media(max-width:900px){ .two-col-grid{ grid-template-columns:1fr!important; } }
@media(max-width:600px){ .card-body{ padding:20px 18px!important; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
