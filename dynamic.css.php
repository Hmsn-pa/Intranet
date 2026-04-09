<?php
// ============================================================
// CSS DINÂMICO — Intranet Acqua
// IMPORTANTE: Content-Type deve ser enviado ANTES de qualquer
// include que possa iniciar sessão ou emitir output.
// ============================================================

// 1. Enviar header CSS PRIMEIRO — antes de qualquer include
header('Content-Type: text/css; charset=UTF-8');
header('Cache-Control: public, max-age=300');

// 2. Carregar apenas as credenciais do banco — SEM session, SEM output
$cfgFile = __DIR__ . '/includes/config.php';

// Ler as constantes de DB diretamente do arquivo config sem executá-lo
// (evita session_start e outros side-effects)
$cfgContent = file_get_contents($cfgFile);
preg_match("/define\('DB_HOST',\s*'([^']*)'\)/",    $cfgContent, $mHost);
preg_match("/define\('DB_NAME',\s*'([^']*)'\)/",    $cfgContent, $mName);
preg_match("/define\('DB_USER',\s*'([^']*)'\)/",    $cfgContent, $mUser);
preg_match("/define\('DB_PASS',\s*'([^']*)'\)/",    $cfgContent, $mPass);
preg_match("/define\('DB_CHARSET',\s*'([^']*)'\)/", $cfgContent, $mChar);

$dbHost    = $mHost[1] ?? 'localhost';
$dbName    = $mName[1] ?? 'intranet_acqua';
$dbUser    = $mUser[1] ?? 'root';
$dbPass    = $mPass[1] ?? '';
$dbCharset = $mChar[1] ?? 'utf8mb4';

// 3. Cores padrão (usadas em caso de falha no DB)
$primary   = '#00897B';
$secondary = '#004D40';

// 4. Buscar cores do banco
try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}",
        $dbUser, $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 2]
    );
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('primary_color','secondary_color')");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['setting_key'] === 'primary_color')   $primary   = $row['setting_value'];
        if ($row['setting_key'] === 'secondary_color') $secondary = $row['setting_value'];
    }
} catch (Exception $e) {
    // Falha silenciosa — usa cores padrão
}

// 5. Validar hex
$isHex = fn($c) => (bool) preg_match('/^#[0-9a-fA-F]{6}$/', $c);
if (!$isHex($primary))   $primary   = '#00897B';
if (!$isHex($secondary)) $secondary = '#004D40';

// 6. Calcular variações de cor
$shift = function(string $hex, int $amt): string {
    $r = min(255, max(0, hexdec(substr($hex,1,2)) + $amt));
    $g = min(255, max(0, hexdec(substr($hex,3,2)) + $amt));
    $b = min(255, max(0, hexdec(substr($hex,5,2)) + $amt));
    return sprintf('#%02x%02x%02x', $r, $g, $b);
};

$primaryDark  = $shift($primary, -20);
$primaryLight = $shift($primary,  40);

// xlight: muito claro, para fundos de badges/hover
$xlR = min(255, hexdec(substr($primary,1,2)) + 180);
$xlG = min(255, hexdec(substr($primary,3,2)) + 180);
$xlB = min(255, hexdec(substr($primary,5,2)) + 180);
$primaryXlight = sprintf('#%02x%02x%02x', $xlR, $xlG, $xlB);

$accent = $shift($primary, 20);

// RGB da cor primária para rgba() no dark mode
$pR = hexdec(substr($primary,1,2));
$pG = hexdec(substr($primary,3,2));
$pB = hexdec(substr($primary,5,2));

// 7. Emitir o CSS
echo ":root{\n";
echo "  --primary:        {$primary};\n";
echo "  --primary-dark:   {$primaryDark};\n";
echo "  --primary-light:  {$primaryLight};\n";
echo "  --primary-xlight: {$primaryXlight};\n";
echo "  --accent:         {$accent};\n";
echo "  --accent2:        {$secondary};\n";
echo "}\n";
echo "[data-theme=\"dark\"]{\n";
echo "  --primary-xlight: rgba({$pR},{$pG},{$pB},0.15);\n";
echo "}\n";
