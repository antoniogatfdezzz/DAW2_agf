<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace(basename($scriptName), '', $scriptName), '/');

define('BASE_URL', $protocol . $host . $basePath . '/');


function redirect(string $path): void {
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function calcularEdad(string $fecha): ?int {
    if (!$fecha) return null;
    try {
        $n = new DateTime($fecha);
        $hoy = new DateTime('today');
        return $n->diff($hoy)->y;
    } catch (Exception $e) { return null; }
}

function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return $needle === '' || mb_strpos($haystack, $needle) !== false;
    }
}


// --- Autenticación ---
define('AUTH_COOKIE', 'VI_AUTH');
define('AUTH_SECRET', 'cod-secr');

function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function base64url_decode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) $data .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($data, '-_', '+/')) ?: '';
}

function auth_set(array $user): void {
    $payload = [
        'id' => $user['id'] ?? null,
        'nombre' => $user['nombre'] ?? '',
        'email' => $user['email'] ?? '',
        'iat' => time()
    ];
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    $sig = hash_hmac('sha256', $json, AUTH_SECRET);
    $token = base64url_encode($json) . '.' . $sig;
    setcookie(AUTH_COOKIE, $token, [
        'expires' => time() + 60*60*8,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function auth_clear(): void {
    setcookie(AUTH_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function auth_user(): ?array {
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (!$token || !str_contains($token, '.')) return null;
    [$b64, $sig] = explode('.', $token, 2);
    $json = base64url_decode($b64);
    if (!$json) return null;
    $calc = hash_hmac('sha256', $json, AUTH_SECRET);
    if (!hash_equals($calc, $sig)) return null;
    $payload = json_decode($json, true);
    if (!is_array($payload)) return null;
    return $payload;
}

function require_auth(): void {
    if (!auth_user()) redirect('login.php');
}

?>