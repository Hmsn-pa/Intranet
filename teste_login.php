<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

echo '<pre>';
echo 'Session ID: ' . session_id() . "\n";
echo 'Session status: ' . session_status() . "\n";
echo 'SESSION: '; print_r($_SESSION);

// Testa conexão banco
$user = Database::fetch('SELECT id, email, password, active FROM users WHERE email = ?', ['admin@admin.com']);
echo 'Usuário encontrado: '; print_r($user);

// Testa senha
if ($user) {
    $senhaOk = password_verify('Admin@2024', $user['password']);
    echo 'Senha correta: ' . ($senhaOk ? 'SIM' : 'NÃO') . "\n";
}

// Testa CSRF
echo 'CSRF token: ' . csrf() . "\n";
echo '</pre>';
