<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
} else {
    $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
}

$allowedLanguages = ['tr', 'en', 'ar'];
if (!in_array($lang, $allowedLanguages)) {
    $lang = 'tr';
    $_SESSION['lang'] = $lang;
}

$langFile = __DIR__ . '/lang/' . $lang . '.php';
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    $translations = [];
}

function __($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}
?>
