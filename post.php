<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main class="post-page site-width<?php if ($this->hidden): ?> protected-post-page<?php endif; ?>">
    <article itemscope itemtype="https://schema.org/BlogPosting">
        <header class="post-header">
            <h1 itemprop="headline"><?php $this->title(); ?></h1>
            <div class="post-byline">
                <a href="<?php $this->author->permalink(); ?>" itemprop="author"><?php $this->author(); ?></a>
                <span aria-hidden="true"> / </span>
                <?php $this->category(', ', true, '未分类'); ?>
                <span aria-hidden="true"> / </span>
                <?php echo number_format(getPostViews((int) $this->cid)); ?> 阅读
            </div>
        </header>

        <aside class="post-toc" aria-label="文章目录" hidden>
            <div class="post-toc-inner">
                <h2>目录</h2>
                <nav aria-label="本文目录"></nav>
            </div>
        </aside>

        <div class="post-content" id="post-content" itemprop="articleBody">
            <?php $this->content(); ?>
        </div>

        <footer class="post-footer">
            <div class="post-details">
                <div class="post-detail-group">
                    <span>DATE</span>
                    <time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date('M j, Y'); ?></time>
                </div>
                <div class="post-detail-group post-detail-tags">
                    <span>TAGS</span>
                    <div class="post-tags"><?php $this->tags(' / ', true, '未添加标签'); ?></div>
                </div>
            </div>
        </footer>
    </article>

    <nav class="post-navigation" aria-label="相邻文章">
        <div class="post-navigation-item post-navigation-prev">
            <span class="post-navigation-label"><?php echo renderIcon('arrow-left', 16); ?>上一篇</span>
            <?php $this->thePrev('%s', '<span class="post-navigation-empty">没有更早的文章</span>'); ?>
        </div>
        <div class="post-navigation-item post-navigation-next">
            <span class="post-navigation-label">下一篇<?php echo renderIcon('arrow-right', 16); ?></span>
            <?php $this->theNext('%s', '<span class="post-navigation-empty">没有更新的文章</span>'); ?>
        </div>
    </nav>

    <?php $this->need('comments.php'); ?>

    <div class="image-lightbox" data-image-lightbox role="dialog" aria-modal="true" aria-label="图片预览" hidden>
        <button class="image-lightbox-backdrop" type="button" data-lightbox-close aria-label="关闭图片预览"></button>
        <div class="image-lightbox-stage">
            <img src="" alt="">
            <p data-lightbox-caption hidden></p>
        </div>
        <span class="image-lightbox-count" data-lightbox-count></span>
        <button class="image-lightbox-close" type="button" data-lightbox-close aria-label="关闭图片预览" title="关闭">
            <?php echo renderIcon('close', 20); ?>
        </button>
        <button class="image-lightbox-nav image-lightbox-prev" type="button" data-lightbox-prev aria-label="上一张图片" title="上一张">
            <?php echo renderIcon('arrow-left', 20); ?>
        </button>
        <button class="image-lightbox-nav image-lightbox-next" type="button" data-lightbox-next aria-label="下一张图片" title="下一张">
            <?php echo renderIcon('arrow-right', 20); ?>
        </button>
    </div>
</main>

<?php $this->need('footer.php'); ?>
