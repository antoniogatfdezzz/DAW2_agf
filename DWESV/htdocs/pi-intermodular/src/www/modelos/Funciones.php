<?php
// Utilidades y funciones de ayuda (portadas de includes/functions.php)

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function format_date($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function format_datetime($datetime, $format = 'd/m/Y H:i') {
    return date($format, strtotime($datetime));
}

function generate_password($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_dni($dni) {
    $dni = strtoupper(str_replace([' ', '-'], '', $dni));
    if (strlen($dni) !== 9) return false;
    $number = substr($dni, 0, 8);
    $letter = substr($dni, 8, 1);
    if (!is_numeric($number)) return false;
    $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
    $calculated_letter = $letters[intval($number) % 23];
    return $letter === $calculated_letter;
}

function success_message($message) {
    return '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . $message . '</div>';
}

function error_message($message) {
    return '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . $message . '</div>';
}
