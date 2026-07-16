<?php
/**
 * 友情链接
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$linksValue = isset($this->fields->linksJson) ? trim((string) $this->fields->linksJson) : '';
$linksData = $linksValue === '' ? array() : json_decode($linksValue, true);
$linksValid = $linksValue === '' || is_array($linksData);
$links = array();

if ($linksValid) {
    foreach ($linksData as $link) {
        if (!is_array($link)) continue;
        $name = trim((string) ($link['name'] ?? ''));
        $url = trim((string) ($link['url'] ?? ''));
        if ($name === '' || !filter_var($url, FILTER_VALIDATE_URL)) continue;
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if ($scheme !== 'http' && $scheme !== 'https') continue;

        $image = trim((string) ($link['image'] ?? ''));
        $imageScheme = $image === '' ? '' : strtolower((string) parse_url($image, PHP_URL_SCHEME));
        if ($image !== '' && (!filter_var($image, FILTER_VALIDATE_URL)
            || ($imageScheme !== 'http' && $imageScheme !== 'https'))) $image = '';
        $links[] = array(
            'name' => $name,
            'url' => $url,
            'description' => trim((string) ($link['description'] ?? '')),
            'image' => $image,
        );
    }
}

$this->need('header.php');
?>

<main class="post-page links-page site-width">
    <header class="post-header page-header links-page-header">
        <h1><?php $this->title(); ?></h1>
        <p class="post-byline"><?php echo str_pad((string) count($links), 2, '0', STR_PAD_LEFT); ?> LINKS</p>
    </header>

    <?php if (!$linksValid): ?>
        <div class="empty-state">
            <h2>配置格式有误</h2>
            <p>请检查页面自定义字段 linksJson 是否为有效 JSON。</p>
        </div>
    <?php elseif ($links): ?>
        <div class="links-grid">
            <?php foreach ($links as $linkIndex => $link): ?>
                <a class="friend-link" href="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                    <span class="friend-link-index"><?php echo str_pad((string) ($linkIndex + 1), 2, '0', STR_PAD_LEFT); ?></span>
                    <span class="friend-link-content">
                        <span class="friend-link-mark" aria-hidden="true">
                            <?php if ($link['image'] !== ''): ?>
                                <img src="<?php echo htmlspecialchars($link['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <span><?php echo getNameInitial($link['name']); ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="friend-link-copy">
                            <strong><?php echo htmlspecialchars($link['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            <small><?php echo htmlspecialchars($link['description'] !== '' ? $link['description'] : $link['url'], ENT_QUOTES, 'UTF-8'); ?></small>
                        </span>
                    </span>
                    <?php echo renderIcon('arrow-up-right', 19); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><p>还没有添加友情链接。</p></div>
    <?php endif; ?>

    <?php if (trim((string) $this->content) !== ''): ?>
        <div class="links-content post-content" id="post-content"><?php $this->content(); ?></div>
    <?php endif; ?>

    <?php $this->need('comments.php'); ?>
</main>

<?php $this->need('footer.php'); ?>
