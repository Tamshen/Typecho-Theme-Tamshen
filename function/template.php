<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function getBodyClass(\Widget\Archive $archive): string
{
    if ($archive->is('post')) return 'is-post';
    if ($archive->is('page')) return 'is-page';
    if ($archive->is('search')) return 'is-home home-page-paged is-search';
    if ($archive->is('category')) return 'is-home home-page-paged is-category';
    if ($archive->is('tag')) return 'is-home home-page-paged is-tag';
    if ($archive->is('author')) return 'is-home home-page-paged is-author';
    if ($archive->is('index')) {
        return (int) $archive->getCurrentPage() === 1 ? 'is-home home-page-first' : 'is-home home-page-paged';
    }
    return 'is-archive';
}

function getArchiveTypeLabel(\Widget\Archive $archive): string
{
    if ($archive->is('category')) return 'Category';
    if ($archive->is('tag')) return 'Tag';
    if ($archive->is('search')) return 'Search';
    if ($archive->is('author')) return 'Author';
    if ($archive->is('date')) return 'Archive';
    return 'Posts';
}

function getPostCover(\Widget\Archive $archive): string
{
    foreach (array('cover', 'thumb') as $field) {
        if (isset($archive->fields->{$field}) && trim((string) $archive->fields->{$field}) !== '') {
            return trim((string) $archive->fields->{$field});
        }
    }

    $content = (string) $archive->content;
    if (preg_match('/<img[^>]+(?:data-original|src)=["\']([^"\']+)["\']/i', $content, $match)) {
        return html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
    }
    if (preg_match('/!\[[^\]]*\]\((?:<)?([^\s)>]+)(?:>)?(?:\s+["\'][^"\']*["\'])?\)/', $content, $match)) {
        return html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
    }

    return !empty($archive->options->defaultCover) ? trim((string) $archive->options->defaultCover) : '';
}

function escapeCssUrl(string $url): string
{
    return htmlspecialchars(str_replace(array('\\', "'", "\n", "\r"), array('\\\\', "\\'", '', ''), $url), ENT_QUOTES, 'UTF-8');
}

function getTextExcerpt(string $text, int $length = 48): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)));
    if (function_exists('mb_strimwidth')) {
        $text = mb_strimwidth($text, 0, $length, '...', 'UTF-8');
    } elseif (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function getCategorySlugs(\Widget\Archive $archive): string
{
    $slugs = array();
    foreach ((array) $archive->categories as $category) {
        if (!empty($category['slug'])) $slugs[] = (string) $category['slug'];
    }
    return htmlspecialchars($slugs ? implode(' / ', $slugs) : 'uncategorized', ENT_QUOTES, 'UTF-8');
}

function getNameInitial(string $name): string
{
    $name = trim(strip_tags($name));
    if ($name === '') return '?';
    if (function_exists('mb_substr')) {
        return htmlspecialchars(mb_substr($name, 0, 1, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }
    preg_match('/^./us', $name, $match);
    return htmlspecialchars($match[0] ?? substr($name, 0, 1), ENT_QUOTES, 'UTF-8');
}

function getNameMark(string $name): string
{
    $name = trim(preg_replace('/\s+/u', ' ', strip_tags($name)));
    if ($name === '') return '??';
    $parts = preg_split('/\s+/u', $name);
    if (count($parts) > 1) {
        $mark = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $mark .= function_exists('mb_substr') ? mb_substr($part, 0, 1, 'UTF-8') : substr($part, 0, 1);
        }
    } else {
        $mark = function_exists('mb_substr') ? mb_substr($name, 0, 2, 'UTF-8') : substr($name, 0, 2);
    }
    if (function_exists('mb_strtoupper')) $mark = mb_strtoupper($mark, 'UTF-8');
    else $mark = strtoupper($mark);
    return htmlspecialchars($mark, ENT_QUOTES, 'UTF-8');
}

function getAvatarVariant(string $name): int
{
    return (hexdec(substr(md5(trim(strip_tags($name))), 0, 2)) % 6) + 1;
}

function renderSocialLinks(string $value): string
{
    $html = '';
    foreach (preg_split('/\r\n|\r|\n/', trim($value)) as $line) {
        $parts = array_map('trim', explode('|', $line, 2));
        if (count($parts) !== 2 || $parts[0] === '' || !filter_var($parts[1], FILTER_VALIDATE_URL)) continue;
        $name = htmlspecialchars($parts[0], ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($parts[1], ENT_QUOTES, 'UTF-8');
        $html .= '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $name . '</a>';
    }
    return $html;
}
