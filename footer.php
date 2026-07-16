<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<footer class="site-footer site-width">
    <?php $footerSocialLinks = (string) $this->options->socialLinks; ?>
    <?php if (trim($footerSocialLinks) !== ''): ?>
        <nav class="social-links" aria-label="社交链接">
            <?php echo renderSocialLinks($footerSocialLinks); ?>
        </nav>
    <?php endif; ?>
    <p>Copyright &copy; <?php echo (int) $this->options->footerSince; ?> - <?php echo date('Y'); ?> <?php $this->options->title(); ?>. All Rights Reserved.<?php if ((string) $this->options->showThemeCredit !== '0'): ?> Theme Design By <a href="https://github.com/Tamshen" target="_blank" rel="noopener noreferrer">TAMSHEN</a><?php endif; ?></p>
</footer>
<div class="floating-tools" aria-label="页面快捷操作">
    <?php if ($this->is('post')): ?>
        <button class="jump-to-comments" type="button" aria-label="跳转到评论区" title="评论">
            <?php echo renderIcon('chat', 18); ?>
        </button>
    <?php endif; ?>
    <button class="back-to-top" type="button" aria-label="返回顶部" title="返回顶部">
        <?php echo renderIcon('up', 18); ?>
    </button>
</div>
<div class="reading-progress" aria-hidden="true"><span></span></div>
<?php $useVendorCdn = (string) $this->options->useVendorCdn === '1'; ?>
<?php $enableCodeHighlight = (string) $this->options->enableCodeHighlight !== '0'; ?>
<?php $enableMermaid = (string) $this->options->enableMermaid === '1'; ?>
<?php $themeUrl = (string) $this->options->themeUrl; ?>
<?php $themeScriptFile = is_file(__DIR__ . '/static/js/theme.min.js') ? 'static/js/theme.min.js' : 'static/js/theme.js'; ?>
<script
    src="<?php $this->options->themeUrl($themeScriptFile); ?>?v=<?php echo rawurlencode(getThemeVersion()); ?>"
    data-use-vendor-cdn="<?php echo $useVendorCdn ? '1' : '0'; ?>"
    data-enable-code-highlight="<?php echo $enableCodeHighlight ? '1' : '0'; ?>"
    data-enable-mermaid="<?php echo $enableMermaid ? '1' : '0'; ?>"
    data-lenis-local="<?php echo htmlspecialchars(\Typecho\Common::url('static/js/vendor/lenis.min.js', $themeUrl), ENT_QUOTES, 'UTF-8'); ?>"
    data-lenis-css-local="<?php echo htmlspecialchars(\Typecho\Common::url('static/css/vendor/lenis.css', $themeUrl), ENT_QUOTES, 'UTF-8'); ?>"
    data-highlight-local="<?php echo htmlspecialchars(\Typecho\Common::url('static/js/vendor/highlight.min.js', $themeUrl), ENT_QUOTES, 'UTF-8'); ?>"
    data-mermaid-local="<?php echo htmlspecialchars(\Typecho\Common::url('static/js/vendor/mermaid.min.js', $themeUrl), ENT_QUOTES, 'UTF-8'); ?>"
></script>
<?php $this->footer(); ?>
</body>
</html>
