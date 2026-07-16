<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<main class="post-page site-width">
    <section class="empty-state">
        <h1>404</h1>
        <p>当前页面不存在，可能已经被移动或删除。</p>
        <a href="<?php $this->options->siteUrl(); ?>">返回主页</a>
    </section>
</main>
<?php $this->need('footer.php'); ?>
