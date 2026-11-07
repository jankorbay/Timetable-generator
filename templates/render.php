<?php
// Renders $days with the user's template settings. Included from the top level of sample.php and google_export.php,
// so $food, $requiredIcon and $iconColor stay global (printEvent() reads them from $GLOBALS).
// Expects: $settings (untrusted, see cleanSettings()), $fallback (template key), $days, optional $preview.
require_once __DIR__ . '/settings.php';
$templates = require __DIR__ . '/list.php';
$userSettings = cleanSettings($settings, $templates);

$key = $userSettings['template'] ?? $fallback;
$template = $templates[$key];
$file = $template['file'] ?? $key;
$theme = $template['theme'] ?? '';
$colors = isset($template['colors'], $userSettings['c1']) ? [$userSettings['c1'], $userSettings['c2']] : ($template['colors'] ?? null);
// Only validated hex colors reach the style attribute. Text colors only for templates with editable colors.
$style = $colors ? "--tt-main: {$colors[0]}; --tt-program: {$colors[1]};" : '';
foreach (['c3' => '--tt-text', 'c5' => '--tt-title', 'c6' => '--tt-heading', 'c7' => '--tt-dark-text'] as $setting => $var) {
    if ($colors && isset($userSettings[$setting])) {
        $style .= " $var: {$userSettings[$setting]};";
    }
}
$iconColor = $userSettings['c4'] ?? null;
$title = $userSettings['title'] ?? '';

$requiredIcon = REQUIRED_ICONS[$userSettings['icon'] ?? ($template['icon'] ?? 'mark')][1];
$legend = ($template['required'] ?? true) && (isset($userSettings['legend']) ? $userSettings['legend'] === '1' : ($template['legend'] ?? false));
$legendText = $userSettings['legendText'] ?? ($template['legendText'] ?? DEFAULT_LEGEND);

$week_days = ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'];
$food = array_values(array_filter(array_map(fn($word) => mb_strtolower(trim($word)), explode(',', $userSettings['food'] ?? '')), 'strlen'))
    ?: ['snídaně', 'svačina', 'oběd', 'večeře'];

include __DIR__ . '/' . $file . '.php';
