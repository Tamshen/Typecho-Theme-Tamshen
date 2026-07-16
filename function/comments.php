<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function threadedComments($comments, $options)
{
    static $commentAuthors = array();
    $classes = array('comment-body');
    $classes[] = $comments->levels > 0 ? 'comment-child' : 'comment-parent';
    if ($comments->authorId && $comments->authorId == $comments->ownerId) {
        $classes[] = 'comment-by-author';
    }
    $parentAuthor = $comments->parent && isset($commentAuthors[$comments->parent])
        ? $commentAuthors[$comments->parent]
        : '';
    $commentAuthors[$comments->coid] = (string) $comments->author;
    $commentInitial = getNameMark((string) $comments->author);
    $avatarVariant = getAvatarVariant((string) $comments->author);
    $canDeleteComment = \Widget\User::alloc()->pass('administrator', true);
    $deleteCommentUrl = $canDeleteComment
        ? \Widget\Security::alloc()->getIndex('/action/comments-edit?do=delete&coid=' . (int) $comments->coid)
        : '';
?>
    <li id="<?php $comments->theId(); ?>" class="<?php echo implode(' ', $classes); ?>" itemscope itemtype="https://schema.org/Comment">
        <div class="comment-main">
            <span class="comment-avatar avatar-variant-<?php echo $avatarVariant; ?>" aria-hidden="true"><?php echo $commentInitial; ?></span>
            <?php if ($comments->levels > 0): ?>
                <div class="comment-compact-copy">
                    <header class="comment-compact-header">
                        <div class="comment-compact-identity">
                            <cite class="comment-author" itemprop="author"><?php $comments->author(); ?></cite>
                            <?php if ($comments->authorId && $comments->authorId == $comments->ownerId): ?>
                                <span class="comment-badge">博主</span>
                            <?php endif; ?>
                            <?php if ($comments->status !== 'approved'): ?>
                                <span class="comment-awaiting-moderation"><?php echo htmlspecialchars((string) $options->commentStatus, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <a class="comment-meta" href="<?php $comments->permalink(); ?>">
                            <time datetime="<?php $comments->date('c'); ?>" itemprop="dateCreated"><?php $comments->date('Y.m.d H:i'); ?></time>
                        </a>
                        <div class="comment-reply comment-inline-actions">
                            <?php $comments->reply($options->replyWord); ?>
                            <?php if ($canDeleteComment): ?>
                                <a class="comment-delete" href="<?php echo htmlspecialchars($deleteCommentUrl, ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('确定删除这条评论吗？删除后无法恢复。')">删除</a>
                            <?php endif; ?>
                        </div>
                    </header>
                    <div class="comment-compact-body">
                        <?php if ($parentAuthor !== ''): ?>
                            <span>回复 </span><a class="comment-reference" href="#comment-<?php echo (int) $comments->parent; ?>">@<?php echo htmlspecialchars($parentAuthor, ENT_QUOTES, 'UTF-8'); ?></a><span>：</span>
                        <?php endif; ?>
                        <div class="comment-content" itemprop="text"><?php $comments->content(); ?></div>
                    </div>
                </div>
            <?php else: ?>
            <?php if ($parentAuthor !== ''): ?>
                <a class="comment-reference" href="#comment-<?php echo (int) $comments->parent; ?>">回复 <?php echo htmlspecialchars($parentAuthor, ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endif; ?>
            <header class="comment-header">
                <div class="comment-identity" itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <cite class="comment-author" itemprop="name"><?php $comments->author(); ?></cite>
                    <?php if ($comments->authorId && $comments->authorId == $comments->ownerId): ?>
                        <span class="comment-badge">博主</span>
                    <?php endif; ?>
                    <?php if ($comments->status !== 'approved'): ?>
                        <span class="comment-awaiting-moderation"><?php echo htmlspecialchars((string) $options->commentStatus, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
                <a class="comment-meta" href="<?php $comments->permalink(); ?>">
                    <time datetime="<?php $comments->date('c'); ?>" itemprop="dateCreated"><?php $comments->date('Y.m.d H:i'); ?></time>
                </a>
                <div class="comment-reply comment-inline-actions">
                    <?php $comments->reply($options->replyWord); ?>
                    <?php if ($canDeleteComment): ?>
                        <a class="comment-delete" href="<?php echo htmlspecialchars($deleteCommentUrl, ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('确定删除这条评论吗？删除后无法恢复。')">删除</a>
                    <?php endif; ?>
                </div>
            </header>

            <div class="comment-content" itemprop="text"><?php $comments->content(); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($comments->children): ?>
            <div class="comment-children" itemprop="comment"><?php $comments->threadedComments(); ?></div>
        <?php endif; ?>
    </li>
<?php
}
