<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section id="comments" class="comments-area">
    <?php $this->comments()->to($comments); ?>
    <?php $hasComments = $comments->have(); ?>
    <header class="comments-heading">
        <h2>评论<?php if ($hasComments): ?> <span><?php $this->commentsNum('0', '1', '%d'); ?></span><?php endif; ?></h2>
        <?php if ($this->allow('comment')): ?>
            <button class="comment-compose-trigger" type="button" data-comment-compose>
                <?php echo renderIcon('chat', 17); ?><span>写评论</span>
            </button>
        <?php elseif ($hasComments): ?>
            <span class="comments-closed-state">讨论已结束</span>
        <?php endif; ?>
    </header>

    <?php if ($hasComments): ?>
        <?php $comments->listComments(array(
            'commentStatus' => '你的评论正等待审核',
            'replyWord' => '回复'
        )); ?>
        <nav class="comment-pagination"><?php $comments->pageNav('上一页', '下一页', 3, '...', array('wrapTag' => 'ul', 'itemTag' => 'li')); ?></nav>
    <?php endif; ?>

    <?php if ($this->allow('comment')): ?>
        <div class="comment-modal" data-comment-modal hidden>
            <button class="comment-modal-backdrop" type="button" data-comment-modal-close tabindex="-1" aria-label="关闭评论窗口"></button>
            <section class="comment-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="response">
                <header class="comment-modal-heading">
                    <div>
                        <span>DISCUSSION</span>
                        <h3 id="response">发表评论</h3>
                    </div>
                    <button class="comment-modal-close" type="button" data-comment-modal-close aria-label="关闭评论窗口">
                        <?php echo renderIcon('close', 18); ?>
                    </button>
                </header>
                <div id="<?php $this->respondId(); ?>" class="comment-respond">
                    <div class="cancel-comment-reply"><?php $comments->cancelReply(); ?></div>
                    <form method="post" action="<?php $this->commentUrl(); ?>" id="comment-form">
                <div class="comment-form-status" role="status" aria-live="polite"></div>
                <div class="comment-text-field">
                    <label class="visually-hidden" for="textarea">评论内容</label>
                    <textarea name="text" id="textarea" rows="5" placeholder="写下你的评论..." required><?php $this->remember('text'); ?></textarea>
                </div>
                <?php if ($this->user->hasLogin()): ?>
                    <p class="logged-in">以 <a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a> 身份登录 · <a href="<?php $this->options->logoutUrl(); ?>">退出</a></p>
                <?php else: ?>
                    <div class="comment-fields">
                        <div><label for="author">名字</label><input type="text" name="author" id="author" placeholder="你的称呼" value="<?php $this->remember('author'); ?>" required></div>
                        <div><label for="mail">邮箱</label><input type="email" name="mail" id="mail" placeholder="不会公开" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?>></div>
                        <div><label for="url">网站</label><input type="url" name="url" id="url" placeholder="选填" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireUrl): ?> required<?php endif; ?>></div>
                    </div>
                <?php endif; ?>
                <div class="comment-form-footer">
                    <p>友善交流，认真讨论</p>
                    <button type="submit"><span>提交评论</span><?php echo renderIcon('arrow-right', 18); ?></button>
                </div>
                    </form>
                </div>
            </section>
        </div>
    <?php elseif (!$hasComments): ?>
        <div class="comments-closed-empty">
            <?php echo renderIcon('chat', 20); ?>
            <strong>评论已关闭</strong>
            <span>暂无历史评论</span>
        </div>
    <?php endif; ?>
</section>
