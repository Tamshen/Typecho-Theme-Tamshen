<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main class="post-page site-width">
    <article itemscope itemtype="https://schema.org/Article">
        <?php if ($this->title): ?>
        <header class="post-header page-header">
            <h1 itemprop="headline"><?php $this->title(); ?></h1>
        </header>
        <?php endif; ?>
        <div class="post-content" id="post-content" itemprop="articleBody">
            <?php $this->content(); ?>
        </div>
    </article>
    <?php $this->need('comments.php'); ?>
</main>

<?php $this->need('footer.php'); ?>
