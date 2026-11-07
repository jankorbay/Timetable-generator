<?php
// Every selectable template. 'file' = layout in templates/ (defaults to the key), 'theme' = extra body class from styles.css,
// 'colors' = [main, program background]; only templates with colors let the user pick their own.
// 'darkText' = default text color of highlighted blocks (white unless the theme has no dark fill).
// Defaults the user can override: 'icon' (key of REQUIRED_ICONS, default 'mark'), 'legend' (shown by default?), 'legendText'.
// 'required' => false: the layout never shows the required-block icon, so icon and legend options are hidden.
return [
    'default'  => ['name' => 'Výchozí (šedá)',          'colors' => ['#1f2937', '#cecece']],
    'forest'   => ['name' => 'Les (zelená)',            'file' => 'default', 'colors' => ['#1f4d2b', '#cfe5c8']],
    'ocean'    => ['name' => 'Moře (modrá)',            'file' => 'default', 'colors' => ['#0c4a6e', '#cde8f6']],
    'sunset'   => ['name' => 'Západ slunce (oranžová)', 'file' => 'default', 'colors' => ['#9a3412', '#fde2c4']],
    'lavender' => ['name' => 'Levandule (fialová)',     'file' => 'default', 'colors' => ['#5b2a86', '#e6dcf3']],
    'minimal'  => ['name' => 'Minimalistická (linky, šetří inkoust)', 'file' => 'default', 'theme' => 'theme-minimal', 'colors' => ['#111827', '#ffffff'], 'darkText' => '#111827'],
    'ursus'    => ['name' => 'VK Ursus', 'icon' => 'hand', 'legend' => true,
                   'legendText' => 'Takto označený program souvisí s povinným úkolem ve stezce. K jeho splnění nebude další příležitost.'],
    'pecka'    => ['name' => 'RK Pecka', 'required' => false],
];
