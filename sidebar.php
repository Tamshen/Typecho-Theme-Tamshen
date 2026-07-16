<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

PopularPosts::alloc('pageSize=5')->to($hotPosts);
\Widget\Comments\Recent::alloc(array('ignoreAuthor' => false))->to($recentComments);
\Widget\Metas\Tag\Cloud::alloc(array(
    'sort' => 'count',
    'ignoreZeroCount' => true,
    'desc' => true,
    'limit' => 18
))->to($hotTags);

$hasHotPosts = $hotPosts->have();
$hasRecentComments = $recentComments->have();
$hasHotTags = $hotTags->have();
?>
<?php if ($hasHotPosts || $hasRecentComments || $hasHotTags): ?>
<aside class="site-sidebar" aria-label="侧栏">
    <?php if ($hasHotPosts): ?>
    <section class="sidebar-section">
        <header class="sidebar-heading">
            <h2><?php echo renderIcon('chart', 18); ?>热门文章</h2>
            <span>POPULAR</span>
        </header>
        <ol class="sidebar-posts">
            <?php $hotIndex = 0; ?>
            <?php while ($hotPosts->next()): $hotIndex++; ?>
                <li>
                    <span class="sidebar-index"><?php echo str_pad((string) $hotIndex, 2, '0', STR_PAD_LEFT); ?></span>
                    <a class="sidebar-post-link" href="<?php $hotPosts->permalink(); ?>">
                        <span class="sidebar-post-title"><?php $hotPosts->title(); ?></span>
                        <span class="sidebar-post-foot">
                            <small><?php echo renderIcon('view', 13); ?><?php echo number_format((int) $hotPosts->postViews); ?> 次浏览</small>
                            <?php echo renderIcon('arrow-right', 15); ?>
                        </span>
                    </a>
                </li>
            <?php endwhile; ?>
        </ol>
    </section>
    <?php endif; ?>

    <?php if ($hasRecentComments): ?>
    <section class="sidebar-section">
        <header class="sidebar-heading">
            <h2><?php echo renderIcon('chat', 18); ?>最新评论</h2>
            <span>RECENT</span>
        </header>
        <div class="sidebar-comment-carousel" data-comment-carousel>
                <div class="sidebar-comment-track">
                    <?php $commentIndex = 0; ?>
                    <?php while ($recentComments->next()): ?>
                        <?php $commentIndex++; ?>
                        <article class="sidebar-comment-slide<?php if ($commentIndex === 1): ?> active<?php endif; ?>"<?php if ($commentIndex !== 1): ?> hidden<?php endif; ?>>
                            <p>“<?php echo getTextExcerpt((string) $recentComments->text, 88); ?>”</p>
                            <footer>
                                <div class="sidebar-comment-source">
                                    <strong><?php $recentComments->author(false); ?></strong>
                                    <a href="<?php $recentComments->permalink(); ?>"><span><?php $recentComments->title(); ?></span><?php echo renderIcon('arrow-right', 14); ?></a>
                                </div>
                                <time datetime="<?php $recentComments->date('c'); ?>" title="<?php $recentComments->date('Y.m.d H:i'); ?>"><?php $recentComments->date('Y.m.d'); ?></time>
                            </footer>
                        </article>
                    <?php endwhile; ?>
                </div>
                <div class="sidebar-comment-controls">
                    <span><b>01</b> / <?php echo str_pad((string) $commentIndex, 2, '0', STR_PAD_LEFT); ?></span>
                    <div>
                        <button type="button" data-comment-prev aria-label="上一条评论"><?php echo renderIcon('arrow-left', 15); ?></button>
                        <button type="button" data-comment-next aria-label="下一条评论"><?php echo renderIcon('arrow-right', 15); ?></button>
                    </div>
                </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($hasHotTags): ?>
    <section class="sidebar-section">
        <header class="sidebar-heading">
            <h2><?php echo renderIcon('tag', 18); ?>热门标签</h2>
            <span>TAGS</span>
        </header>
        <div class="sidebar-tags">
            <?php while ($hotTags->next()): ?>
                <a href="<?php $hotTags->permalink(); ?>"><?php $hotTags->name(); ?><span><?php $hotTags->count(); ?></span></a>
            <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>
</aside>
<?php endif; ?>
