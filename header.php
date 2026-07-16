<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
if ($this->is('post')) recordPostView($this);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="format-detection" content="telephone=no">
    <title><?php $this->archiveTitle('', '', ' - '); ?><?php $this->options->title(); ?><?php if ($this->is('index')): ?> - <?php $this->options->description(); ?><?php endif; ?></title>
    <?php $themeStyleFile = is_file(__DIR__ . '/static/css/style.min.css') ? 'static/css/style.min.css' : 'static/css/style.css'; ?>
    <link rel="stylesheet" href="<?php $this->options->themeUrl($themeStyleFile); ?>?v=<?php echo rawurlencode(getThemeVersion()); ?>">
    <?php $this->header('generator=&template=&pingback=&xmlrpc=&wlw='); ?>
</head>
<body class="page-entering <?php echo getBodyClass($this); ?>">
<script>
window.__pageLoaderStartedAt = window.performance ? window.performance.now() : Date.now();
try {
    if (window.sessionStorage.getItem('themeInternalNavigation') === '1') {
        window.sessionStorage.removeItem('themeInternalNavigation');
        document.documentElement.classList.add('internal-navigation');
    }
} catch (error) {}
</script>
<div class="page-loader" data-page-loader role="status" aria-label="页面加载中">
    <svg class="carbon-icon" width="28" height="28" viewBox="0 0 18 18" fill="none" aria-hidden="true">
        <circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.3" stroke-dasharray="26 12" />
    </svg>
</div>
<header class="site-menu" id="site-menu">
    <div class="menu-bar">
        <div class="site-width menu-inner">
            <a class="site-logo<?php echo !empty($this->options->logoUrl) ? ' has-image' : ' has-text'; ?>" href="<?php $this->options->siteUrl(); ?>" aria-label="<?php $this->options->title(); ?>">
                <?php if (!empty($this->options->logoUrl)): ?>
                    <img src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->options->title(); ?>">
                <?php else: ?>
                    <span><?php $this->options->title(); ?></span>
                <?php endif; ?>
            </a>

            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" aria-label="打开导航">
                <span class="menu-icon-open"><?php echo renderIcon('menu', 20); ?></span>
                <span class="menu-icon-close"><?php echo renderIcon('close', 20); ?></span>
            </button>

            <nav class="primary-nav" id="primary-nav" aria-label="主导航">
                <a<?php if ($this->is('index')): ?> class="active" aria-current="page"<?php endif; ?> href="<?php $this->options->siteUrl(); ?>">主页</a>
                <?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
                <?php while ($pages->next()): ?>
                    <?php if (trim((string) $pages->title) === '') continue; ?>
                    <a<?php if ($this->is('page', $pages->slug)): ?> class="active" aria-current="page"<?php endif; ?> href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?></a>
                <?php endwhile; ?>
            </nav>
            <button class="search-trigger" type="button" aria-expanded="false" aria-controls="search-panel" aria-label="打开搜索" title="搜索">
                <?php echo renderIcon('search', 20); ?>
            </button>
        </div>
    </div>
    <div class="menu-backdrop" aria-hidden="true"></div>
</header>
<section class="search-panel" id="search-panel" role="dialog" aria-modal="true" aria-labelledby="search-title" hidden>
    <button class="search-backdrop" type="button" aria-label="关闭搜索"></button>
    <div class="search-dialog">
        <header class="search-header">
            <span id="search-title">SEARCH</span>
            <button class="search-close" type="button" aria-label="关闭搜索" title="关闭">
                <?php echo renderIcon('close', 20); ?>
            </button>
        </header>
        <form class="search-form" method="get" action="<?php $this->options->siteUrl(); ?>" role="search">
            <label class="visually-hidden" for="site-search-input">搜索文章</label>
            <input id="site-search-input" name="s" type="search" placeholder="输入关键词" autocomplete="off" required>
            <button type="submit" aria-label="提交搜索" title="搜索">
                <?php echo renderIcon('arrow-right', 22); ?>
            </button>
        </form>
    </div>
</section>
