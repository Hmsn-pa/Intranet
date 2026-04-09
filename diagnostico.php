<?php
// ============================================================
// DIAGNÓSTICO — Intranet Acqua
// Acesse: http://10.10.254.49/intranet-acqua/diagnostico.php
// APAGUE após confirmar instalação!
// ============================================================
require_once __DIR__ . '/includes/config.php';

$dbOk = false; $dbMsg = '';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]);
    $dbOk = true;
    $dbMsg = 'OK — MySQL ' . $pdo->query('SELECT VERSION()')->fetchColumn();
} catch (Exception $e) { $dbMsg = $e->getMessage(); }

$uploadOk = is_dir(UPLOAD_DIR);
$uploadWr = is_writable(UPLOAD_DIR);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Diagnóstico — Acqua</title>
<style>
body{font-family:system-ui,sans-serif;max-width:860px;margin:30px auto;padding:0 20px;background:#f5f5f5;color:#222;font-size:14px}
h1{color:#00897B;border-bottom:3px solid #00897B;padding-bottom:8px}
h2{color:#004D40;font-size:15px;margin:24px 0 8px}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);margin-bottom:16px}
th,td{padding:10px 14px;border-bottom:1px solid #eee;text-align:left}
th{background:#004D40;color:#fff;font-size:11px;text-transform:uppercase;letter-spacing:.5px}
tr:last-child td{border:none}
.ok{color:#28a745;font-weight:700}.err{color:#dc3545;font-weight:700}
code{font-family:monospace;background:#eee;padding:1px 6px;border-radius:4px;font-size:13px;color:#0066cc;word-break:break-all}
.box{padding:14px 18px;border-radius:8px;margin:12px 0;font-size:14px}
.box-ok{background:#d4edda;border:1px solid #28a745}
.box-err{background:#f8d7da;border:1px solid #dc3545}
.box-info{background:#cce5ff;border:1px solid #004085}
a{color:#00897B}
</style>
</head>
<body>
<h1>🏥 Diagnóstico — Intranet Acqua</h1>

<div class="box box-info">
  ℹ️ Acesse esta página para verificar se a instalação está correta.<br>
  <strong>Apague <code>diagnostico.php</code> após confirmar que tudo funciona.</strong>
</div>

<h2>Detecção de URL</h2>
<table>
  <tr><th>Variável</th><th>Valor detectado</th></tr>
  <tr><td><strong>BASE_URL</strong> (usada em todos os redirects)</td><td><code><?= htmlspecialchars(BASE_URL) ?></code></td></tr>
  <tr><td>BASE_PATH (disco)</td><td><code><?= htmlspecialchars(BASE_PATH) ?></code></td></tr>
  <tr><td>$_SERVER['DOCUMENT_ROOT']</td><td><code><?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? '(vazio)') ?></code></td></tr>
  <tr><td>$_SERVER['HTTP_HOST']</td><td><code><?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? '(vazio)') ?></code></td></tr>
  <tr><td>$_SERVER['SCRIPT_NAME']</td><td><code><?= htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? '(vazio)') ?></code></td></tr>
  <tr><td>Nome da pasta do projeto</td><td><code><?= htmlspecialchars(basename(BASE_PATH)) ?></code></td></tr>
</table>

<?php
$urlOk = str_contains(BASE_URL, basename(BASE_PATH));
if (!$urlOk): ?>
<div class="box box-err">
  ❌ <strong>BASE_URL incorreta!</strong> O caminho <code>/<?= htmlspecialchars(basename(BASE_PATH)) ?></code> não foi detectado.<br><br>
  <strong>Solução:</strong> Abra <code>includes/config.php</code> e descomente + ajuste a linha:<br>
  <code>define('BASE_URL', 'http://<?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? '10.10.254.49') ?>/<?= htmlspecialchars(basename(BASE_PATH)) ?>');</code>
</div>
<?php else: ?>
<div class="box box-ok">✅ BASE_URL detectada corretamente: <a href="<?= BASE_URL ?>"><?= htmlspecialchars(BASE_URL) ?></a></div>
<?php endif; ?>

<h2>Sistema</h2>
<table>
  <tr><th>Item</th><th>Status</th><th>Detalhe</th></tr>
  <tr><td>PHP</td><td class="<?= version_compare(PHP_VERSION,'8.0','>=') ? 'ok' : 'err' ?>"><?= version_compare(PHP_VERSION,'8.0','>=') ? '✅ OK' : '❌ Antigo' ?></td><td><code><?= PHP_VERSION ?></code></td></tr>
  <tr><td>Banco de dados</td><td class="<?= $dbOk ? 'ok' : 'err' ?>"><?= $dbOk ? '✅ OK' : '❌ ERRO' ?></td><td><code><?= htmlspecialchars($dbMsg) ?></code></td></tr>
  <tr><td>Pasta uploads/</td><td class="<?= $uploadOk ? 'ok' : 'err' ?>"><?= $uploadOk ? '✅ Existe' : '❌ Não encontrada' ?></td><td><code><?= htmlspecialchars(UPLOAD_DIR) ?></code></td></tr>
  <tr><td>uploads/ gravável</td><td class="<?= $uploadWr ? 'ok' : 'err' ?>"><?= $uploadWr ? '✅ Sim' : '❌ Sem permissão' ?></td><td><?= $uploadWr ? '' : 'No Windows: clique direito → Propriedades → Segurança → Controle total' ?></td></tr>
  <tr><td>GD (imagens)</td><td class="<?= extension_loaded('gd') ? 'ok' : 'err' ?>"><?= extension_loaded('gd') ? '✅ Ativo' : '❌ Desativado' ?></td><td><?= extension_loaded('gd') ? 'Upload de imagens funcionando' : 'Habilite extension=gd no php.ini' ?></td></tr>
</table>

<h2>Links de Acesso</h2>
<table>
  <tr><th>Página</th><th>URL</th></tr>
  <tr><td>Área Pública</td><td><a href="<?= BASE_URL ?>/public.php" target="_blank"><code><?= BASE_URL ?>/public.php</code></a></td></tr>
  <tr><td>Login</td><td><a href="<?= BASE_URL ?>/login.php" target="_blank"><code><?= BASE_URL ?>/login.php</code></a></td></tr>
  <tr><td>Intranet (requer login)</td><td><a href="<?= BASE_URL ?>/index.php" target="_blank"><code><?= BASE_URL ?>/index.php</code></a></td></tr>
  <tr><td>Admin (requer login)</td><td><a href="<?= BASE_URL ?>/admin/index.php" target="_blank"><code><?= BASE_URL ?>/admin/index.php</code></a></td></tr>
</table>

<p style="margin-top:24px;font-size:12px;color:#999">
  Apache <?= $_SERVER['SERVER_SOFTWARE'] ?? '' ?> · PHP <?= PHP_VERSION ?> · <?= date('d/m/Y H:i:s') ?>
</p>
</body>
</html>
