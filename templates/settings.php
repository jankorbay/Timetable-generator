<?php
// User template settings ("style"): validation, defaults and per-account storage.
// Used by index.php, save_settings.php and templates/render.php.

const REQUIRED_ICONS = [
    'mark'    => ['Vykřičník', 'images/icons/mark.svg'],
    'hand'    => ['Ruka', 'images/icons/hand.png'],
    'star'    => ['Hvězda', 'images/icons/star.svg'],
    'flag'    => ['Vlajka', 'images/icons/flag.svg'],
    'warning' => ['Výstraha', 'images/icons/warning.svg'],
    'check'   => ['Fajfka', 'images/icons/check.svg'],
    'lilie'   => ['Lilie', 'images/icons/SKAUTBold_lilie.svg'],
    'kotva'   => ['Kotva', 'images/icons/SKAUTBold_kotva.svg'],
    'kvetina' => ['Květina', 'images/icons/SKAUT_kvetina_symbol.svg'],
    'ohen'    => ['Oheň', 'images/icons/SKAUT_ohen_symbol.svg'],
    'uzel'    => ['Uzel přátelství', 'images/icons/SKAUT_uzelpratelstvi_symbol.svg'],
];
const DEFAULT_LEGEND = 'Takto označený program je povinný.';
const DEFAULT_ICON_COLOR = '#808080'; // every icon starts in this gray (the original mark.svg color)

// Keeps only known, valid, non-empty values. $raw is untrusted (GET, POST, JSON body or a stored file).
function cleanSettings($raw, array $templates): array
{
    $raw = is_array($raw) ? $raw : [];
    $text = fn($key, $max) => is_string($raw[$key] ?? null) ? mb_substr(trim($raw[$key]), 0, $max) : '';
    $color = fn($key) => preg_match('/^#[0-9a-fA-F]{6}$/', $text($key, 7)) ? strtolower($text($key, 7)) : '';

    $clean = [
        'template'   => isset($templates[$text('template', 50)]) ? $text('template', 50) : '',
        'title'      => $text('title', 100),
        'c1'         => $color('c1'),
        'c2'         => $color('c2'),
        'c3'         => $color('c3'), // text in normal blocks
        'c4'         => $color('c4'), // required-block icon
        'c5'         => $color('c5'), // event name
        'c6'         => $color('c6'), // day heading
        'c7'         => $color('c7'), // text in highlighted (dark) blocks
        'food'       => $text('food', 300),
        'icon'       => isset(REQUIRED_ICONS[$text('icon', 20)]) ? $text('icon', 20) : '',
        'legend'     => in_array($text('legend', 1), ['0', '1'], true) ? $text('legend', 1) : '',
        'legendText' => $text('legendText', 300),
    ];
    if ($clean['c1'] === '' || $clean['c2'] === '') {
        $clean['c1'] = $clean['c2'] = '';
    }
    return array_filter($clean, fn($value) => $value !== '');
}

// One JSON file per Google account; the name is an HMAC of the email, so it can't be guessed or listed back to a person.
function settingsPath(string $email): string
{
    $config = require __DIR__ . '/../google/config.php';
    return __DIR__ . '/../data/settings/' . hash_hmac('sha256', strtolower($email), $config['client_secret']) . '.json';
}

function loadSettings(string $email, array $templates): array
{
    $json = @file_get_contents(settingsPath($email));
    return $json === false ? [] : cleanSettings(json_decode($json, true), $templates);
}

function saveSettings(string $email, array $settings): bool
{
    $path = settingsPath($email);
    if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0770, true)) {
        return false;
    }
    return file_put_contents($path, json_encode($settings, JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

// True when $text contains one of $words as a whole word: "oběd" matches "Oběd v terénu", "ina" doesn't match "Svačina".
function containsWholeWord(string $text, array $words): bool
{
    foreach ($words as $word) {
        if (preg_match('/(?<![\p{L}\p{N}])' . preg_quote($word, '/') . '(?![\p{L}\p{N}])/iu', $text)) {
            return true;
        }
    }
    return false;
}

// Required-block icon: its shape filled with one color (CSS mask), so all icons share the same color.
// $src comes from REQUIRED_ICONS and $color is a validated hex color, so neither needs escaping.
function iconHtml(string $src, ?string $color, string $classes): string
{
    return '<span class="tt-icon ' . $classes . '" style="--icon: url(\'' . $src . '\'); background-color: ' . ($color ?? DEFAULT_ICON_COLOR) . ';"></span>';
}
