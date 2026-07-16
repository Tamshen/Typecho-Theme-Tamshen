<?php
/**
 * 时间归档
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

\Widget\Contents\Post\Recent::alloc(array('pageSize' => 10000))->to($archivePosts);
$archiveGroups = array();
$archiveTotal = 0;

while ($archivePosts->next()) {
    $year = $archivePosts->date->format('Y');
    $archiveGroups[$year][] = array(
        'title' => (string) $archivePosts->title,
        'permalink' => (string) $archivePosts->permalink,
        'date' => $archivePosts->date->format('m/d'),
        'iso' => $archivePosts->date->format('c'),
    );
    $archiveTotal++;
}

$this->need('header.php');
?>

<main class="archive-page site-width">
    <header class="post-header page-header">
        <h1><?php $this->title(); ?></h1>
        <p class="post-byline"><?php echo $archiveTotal; ?> 篇文章 · <?php echo count($archiveGroups); ?> 个年份</p>
    </header>

    <?php if ($archiveGroups): ?>
        <div class="archive-timeline">
            <?php foreach ($archiveGroups as $year => $posts): ?>
                <section class="archive-year" aria-labelledby="archive-year-<?php echo $year; ?>">
                    <header>
                        <h2 id="archive-year-<?php echo $year; ?>"><?php echo $year; ?></h2>
                        <span><?php echo count($posts); ?> POSTS</span>
                    </header>
                    <ol>
                        <?php foreach ($posts as $post): ?>
                            <li>
                                <time datetime="<?php echo htmlspecialchars($post['iso'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $post['date']; ?></time>
                                <a href="<?php echo htmlspecialchars($post['permalink'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $post['title']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><p>还没有文章。</p></div>
    <?php endif; ?>
</main>

<?php $this->need('footer.php'); ?>
