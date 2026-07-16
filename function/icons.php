<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function renderIcon(string $name, int $size = 20): string
{
    $icons = array(
        'arrow-up-right' => '<path d="M5 13 13 5M6 5h7v7" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square" stroke-linejoin="miter"/>',
        'arrow-left' => '<path d="M16 9H3M7 5 3 9l4 4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square" stroke-linejoin="miter"/>',
        'arrow-right' => '<path d="M2 9h13M11 5l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square" stroke-linejoin="miter"/>',
        'calendar' => '<path d="M3 4.5h12v11H3zM3 7.5h12M6 2v3M12 2v3" fill="none" stroke="currentColor" stroke-width="1.3"/>',
        'chart' => '<path d="M2.5 15.5h13M3.5 13l3.5-4 3 2 4.5-6M12 5h2.5v2.5" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="square" stroke-linejoin="miter"/>',
        'chat' => '<path d="M15.5 8.5a6.5 6.5 0 0 1-9.7 5.7L2.5 15l.8-3.3A6.5 6.5 0 1 1 15.5 8.5Z" fill="none" stroke="currentColor" stroke-width="1.3"/><circle cx="6" cy="8.5" r=".7"/><circle cx="9" cy="8.5" r=".7"/><circle cx="12" cy="8.5" r=".7"/>',
        'menu' => '<path d="M2 4h14v1.5H2zm0 4.25h14v1.5H2zm0 4.25h14V14H2z"/>',
        'close' => '<path d="m3 3 12 12M15 3 3 15" fill="none" stroke="currentColor" stroke-width="1.3"/>',
        'search' => '<circle cx="7.5" cy="7.5" r="5" fill="none" stroke="currentColor" stroke-width="1.3"/><path d="m11.2 11.2 4.3 4.3" fill="none" stroke="currentColor" stroke-width="1.3"/>',
        'tag' => '<path d="M2.5 3h5l8 8-4.5 4.5-8-8ZM6 6h.01" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="square" stroke-linejoin="miter"/>',
        'view' => '<path d="M1.5 9s2.7-4.5 7.5-4.5S16.5 9 16.5 9 13.8 13.5 9 13.5 1.5 9 1.5 9Z" fill="none" stroke="currentColor" stroke-width="1.2"/><circle cx="9" cy="9" r="2" fill="none" stroke="currentColor" stroke-width="1.2"/>',
        'up' => '<path d="M9 16V2M4.5 6.5 9 2l4.5 4.5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/>'
    );
    if (!isset($icons[$name])) return '';
    return '<svg class="carbon-icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true" focusable="false">' . $icons[$name] . '</svg>';
}
