<?php
// ============================================================
// AUTENTICAÇÃO E PERMISSÕES
// ============================================================

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

function isEditor(): bool {
    return isLoggedIn() && in_array($_SESSION['user_role'] ?? '', ['admin', 'editor']);
}

function requireLogin(string $redirect = 'login.php'): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/' . $redirect);
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/index.php?error=forbidden');
        exit;
    }
}

function requireEditor(): void {
    requireLogin();
    if (!isEditor()) {
        header('Location: ' . BASE_URL . '/index.php?error=forbidden');
        exit;
    }
}

function login(string $email, string $password): array {
    $user = Database::fetch('SELECT * FROM users WHERE email = ? AND active = 1', [$email]);
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'E-mail ou senha inválidos.'];
    }
    session_regenerate_id(true);
    $_SESSION['user_id']     = $user['id'];
    $_SESSION['user_name']   = $user['name'];
    $_SESSION['user_email']  = $user['email'];
    $_SESSION['user_role']   = $user['role'];
    $_SESSION['user_sector'] = $user['sector'];
    $_SESSION['dark_mode']   = (bool) $user['dark_mode'];
    $_SESSION['login_time']  = time();
    Database::query('UPDATE users SET last_login = NOW() WHERE id = ?', [$user['id']]);
    return ['success' => true, 'user' => $user];
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']
        );
    }
    session_destroy();
    header('Location: ' . BASE_URL . '/login.php?logout=1');
    exit;
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return Database::fetch('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]);
}

function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateSlug(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(['ã','â','á','à','ä','Ã','Â','Á','À','Ä'], 'a', $text);
    $text = str_replace(['ê','é','è','ë','Ê','É','È','Ë'], 'e', $text);
    $text = str_replace(['î','í','ì','ï','Î','Í','Ì','Ï'], 'i', $text);
    $text = str_replace(['õ','ô','ó','ò','ö','Õ','Ô','Ó','Ò','Ö'], 'o', $text);
    $text = str_replace(['û','ú','ù','ü','Û','Ú','Ù','Ü'], 'u', $text);
    $text = str_replace(['ç','Ç'], 'c', $text);
    $text = str_replace(['ñ','Ñ'], 'n', $text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function uniqueSlug(string $slug, string $table, int $excludeId = 0): string {
    $original = $slug;
    $i = 1;
    while (true) {
        $exists = Database::count(
            "SELECT COUNT(*) FROM $table WHERE slug = ? AND id != ?",
            [$slug, $excludeId]
        );
        if (!$exists) break;
        $slug = $original . '-' . $i++;
    }
    return $slug;
}

function getSetting(string $key, string $default = ''): string {
    $row = Database::fetch('SELECT setting_value FROM settings WHERE setting_key = ?', [$key]);
    if (!$row) return $default;
    $val = $row['setting_value'] ?? '';
    // Retorna o default se o valor salvo for string vazia
    return ($val !== '' && $val !== null) ? $val : $default;
}

function csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function formatDate(string $date, string $format = 'd/m/Y'): string {
    return date($format, strtotime($date));
}

function timeAgo(string $datetime): string {
    $time = time() - strtotime($datetime);
    if ($time < 60) return 'agora mesmo';
    if ($time < 3600) return floor($time/60) . ' min atrás';
    if ($time < 86400) return floor($time/3600) . 'h atrás';
    if ($time < 2592000) return floor($time/86400) . 'd atrás';
    return date('d/m/Y', strtotime($datetime));
}

/**
 * Retorna um bloco <style> inline com as variáveis CSS de cor
 * lidas do banco de dados. Injete no <head> APÓS o style.css.
 * Não usa header(), não conflita com session_start().
 */
function getColorVarsStyle(): string {
    $primary   = getSetting('primary_color',   '#00897B');
    $secondary = getSetting('secondary_color', '#004D40');

    // Validar hex
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $primary))   $primary   = '#00897B';
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $secondary)) $secondary = '#004D40';

    // Calcular variações
    $shift = function(string $hex, int $amt): string {
        $r = min(255, max(0, hexdec(substr($hex,1,2)) + $amt));
        $g = min(255, max(0, hexdec(substr($hex,3,2)) + $amt));
        $b = min(255, max(0, hexdec(substr($hex,5,2)) + $amt));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    };

    $primaryDark  = $shift($primary, -20);
    $primaryLight = $shift($primary,  40);
    $xlR = min(255, hexdec(substr($primary,1,2)) + 180);
    $xlG = min(255, hexdec(substr($primary,3,2)) + 180);
    $xlB = min(255, hexdec(substr($primary,5,2)) + 180);
    $primaryXlight = sprintf('#%02x%02x%02x', $xlR, $xlG, $xlB);
    $accent  = $shift($primary, 20);
    $pR = hexdec(substr($primary,1,2));
    $pG = hexdec(substr($primary,3,2));
    $pB = hexdec(substr($primary,5,2));

    return "<style>:root{"
        . "--primary:{$primary};"
        . "--primary-dark:{$primaryDark};"
        . "--primary-light:{$primaryLight};"
        . "--primary-xlight:{$primaryXlight};"
        . "--accent:{$accent};"
        . "--accent2:{$secondary};"
        . "}"
        . "[data-theme=\"dark\"]{--primary-xlight:rgba({$pR},{$pG},{$pB},0.15);}"
        . "</style>";
}

