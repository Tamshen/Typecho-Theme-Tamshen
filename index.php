<?php
/**
 * TAMSHEN Hexo 经典样式的 Typecho 移植版
 *
 * @package Tamshen Blog
 * @author Dalton
 * @version 2.0.0
 * @link https://tamshen.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$hasPosts = $this->have();
?>

<main class="site-width archive-main" id="posts">
    <?php if ($this->is('index') && (int) $this->getCurrentPage() === 1): ?>
        <?php $homeKicker = trim((string) $this->options->homeKicker) ?: 'Hello World / Latest'; ?>
        <?php $homeIntroTitle = trim((string) $this->options->homeIntroTitle) ?: "记录设计、技术\n与生活的灵感。"; ?>
        <section class="home-intro" aria-labelledby="home-intro-title">
            <div class="home-intro-copy">
                <span class="home-kicker"><?php echo htmlspecialchars($homeKicker, ENT_QUOTES, 'UTF-8'); ?></span>
                <h1 id="home-intro-title"><?php echo nl2br(htmlspecialchars($homeIntroTitle, ENT_QUOTES, 'UTF-8')); ?></h1>
            </div>
            <div class="home-intro-side">
                <p><?php echo htmlspecialchars(trim((string) $this->options->description) !== '' ? (string) $this->options->description : '分享创作过程，也收藏值得反复阅读的片段。', ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if ($hasPosts): ?>
                    <a href="#latest-posts">浏览最新文章<?php echo renderIcon('arrow-right', 18); ?></a>
                <?php else: ?>
                    <span class="home-intro-status">这个人很懒，什么都没有留下 :)</span>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php if ($this->is('index') && $hasPosts): ?>
        <header class="home-section-heading" id="latest-posts">
            <div>
                <span><?php echo str_pad((string) $this->getCurrentPage(), 2, '0', STR_PAD_LEFT); ?></span>
                <h2>最新文章</h2>
            </div>
            <span>LATEST STORIES</span>
        </header>
    <?php endif; ?>
    <?php if ($this->is('search') || $this->is('category') || $this->is('tag') || $this->is('author')): ?>
        <header class="home-section-heading search-results-heading">
            <div>
                <span><?php echo str_pad((string) $this->getCurrentPage(), 2, '0', STR_PAD_LEFT); ?></span>
                <h2><?php $this->archiveTitle('', '', ''); ?></h2>
            </div>
            <span><?php echo strtoupper(getArchiveTypeLabel($this)); ?> RESULTS</span>
        </header>
    <?php endif; ?>
    <?php if ($hasPosts || !$this->is('index')): ?>
    <div class="archive-layout">
    <div class="archive-content">
    <section class="post-grid" aria-label="文章列表">
        <?php if (!$this->is('index') && !$this->is('search') && !$this->is('category') && !$this->is('tag') && !$this->is('author')): ?>
            <header class="grid-item archive-card">
                <div class="card-image archive-cover">
                    <div class="card-title archive-title">
                        <span><?php echo getArchiveTypeLabel($this); ?></span>
                        <h1><?php $this->archiveTitle('', '', ''); ?></h1>
                    </div>
                </div>
            </header>
        <?php endif; ?>

        <?php if ($this->have()): ?>
            <?php while ($this->next()): ?>
                <?php $cover = getPostCover($this); ?>
                <article class="grid-item">
                    <a href="<?php $this->permalink(); ?>" aria-label="<?php $this->title(); ?>">
                        <div class="card-image is-placeholder<?php if ($cover): ?> cover-pending<?php endif; ?>"<?php if ($cover): ?> data-cover="<?php echo htmlspecialchars($cover, ENT_QUOTES, 'UTF-8'); ?>"<?php endif; ?>>
                            <span class="card-open" aria-hidden="true"><?php echo renderIcon('arrow-up-right', 20); ?></span>
                            <div class="card-title">
                                <div class="card-meta">
                                    <span><?php echo getCategorySlugs($this); ?></span>
                                    <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y.m.d'); ?></time>
                                </div>
                                <h2><?php $this->title(); ?></h2>
                                <p><?php echo getTextExcerpt((string) $this->content, 100); ?></p>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <h1>没有找到内容</h1>
                <p>换一个关键词，或返回主页继续浏览。</p>
                <a href="<?php $this->options->siteUrl(); ?>">返回主页</a>
            </div>
        <?php endif; ?>
    </section>

    <?php $currentPage = (int) $this->getCurrentPage(); $totalPage = max(1, (int) $this->getTotalPage()); ?>
    <?php if ((int) $this->getTotal() > 0): ?>
    <nav class="pagination" aria-label="分页" data-current-page="<?php echo $currentPage; ?>" data-total-pages="<?php echo $totalPage; ?>">
        <?php if ($currentPage === 1): ?>
            <span class="pagination-disabled pagination-disabled-prev" aria-label="已是第一页" aria-disabled="true">
                <?php echo renderIcon('arrow-left', 18); ?>
            </span>
        <?php endif; ?>
        <?php if ($totalPage === 1): ?>
            <ul><li class="current"><span aria-current="page">1</span></li></ul>
        <?php else: ?>
            <?php $this->pageNav(
                '<span class="visually-hidden">上一页</span>' . renderIcon('arrow-left', 18),
                '<span class="visually-hidden">下一页</span>' . renderIcon('arrow-right', 18),
                1,
                '...',
                array('wrapTag' => 'ul', 'itemTag' => 'li')
            ); ?>
        <?php endif; ?>
        <?php if ($currentPage >= $totalPage): ?>
            <span class="pagination-disabled pagination-disabled-next" aria-label="已是最后一页" aria-disabled="true">
                <?php echo renderIcon('arrow-right', 18); ?>
            </span>
        <?php endif; ?>
        <span class="pagination-summary" aria-label="第 <?php echo $currentPage; ?> 页，共 <?php echo $totalPage; ?> 页">
            <strong><?php echo $currentPage; ?></strong><i>/</i><?php echo $totalPage; ?>
        </span>
    </nav>
    <?php endif; ?>
    </div>

    <?php if (!$this->is('search') && !$this->is('category') && !$this->is('tag') && !$this->is('author') && (!$this->is('index') || (int) $this->getCurrentPage() === 1)): ?>
        <?php $this->need('sidebar.php'); ?>
    <?php endif; ?>
    </div>
    <?php endif; ?>
</main>

<?php $this->need('footer.php'); ?>
