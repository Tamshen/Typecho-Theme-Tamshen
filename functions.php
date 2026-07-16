<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

require_once __DIR__ . '/function/views.php';
require_once __DIR__ . '/function/template.php';
require_once __DIR__ . '/function/icons.php';
require_once __DIR__ . '/function/comments.php';

function getThemeVersion(): string
{
    static $version;
    if ($version === null) {
        $info = \Typecho\Plugin::parseInfo(__DIR__ . '/index.php');
        $version = !empty($info['version']) ? (string) $info['version'] : '1.0.0';
    }
    return $version;
}

function themeConfig($form)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl', null, null, _t('导航 Logo'), _t('留空时显示站点标题。')
    );
    $form->addInput($logoUrl->addRule('url', _t('请填写正确的 URL 地址')));

    $homeKicker = new \Typecho\Widget\Helper\Form\Element\Text(
        'homeKicker', null, 'Hello World / Latest', _t('首页导语标签'), _t('显示在首页主标题上方。')
    );
    $form->addInput($homeKicker);

    $homeIntroTitle = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'homeIntroTitle', null, "记录设计、技术\n与生活的灵感。", _t('首页主标题'), _t('支持换行。')
    );
    $form->addInput($homeIntroTitle);

    $defaultCover = new \Typecho\Widget\Helper\Form\Element\Text(
        'defaultCover', null, null, _t('默认文章封面'), _t('文章没有 cover/thumb 字段和正文图片时使用。')
    );
    $form->addInput($defaultCover->addRule('url', _t('请填写正确的 URL 地址')));

    $footerSince = new \Typecho\Widget\Helper\Form\Element\Text(
        'footerSince', null, '2017', _t('版权起始年份'), _t('例如 2017。')
    );
    $form->addInput($footerSince);

    $showThemeCredit = new \Typecho\Widget\Helper\Form\Element\Radio(
        'showThemeCredit',
        array('1' => _t('显示'), '0' => _t('隐藏')),
        '1',
        _t('主题设计署名'),
        _t('控制页脚是否显示 Theme Design By TAMSHEN。')
    );
    $form->addInput($showThemeCredit);

    $enableCodeHighlight = new \Typecho\Widget\Helper\Form\Element\Radio(
        'enableCodeHighlight',
        array('1' => _t('开启'), '0' => _t('关闭')),
        '1',
        _t('代码高亮'),
        _t('开启后为 Markdown 代码块加载 Highlight.js。')
    );
    $form->addInput($enableCodeHighlight);

    $enableMermaid = new \Typecho\Widget\Helper\Form\Element\Radio(
        'enableMermaid',
        array('1' => _t('开启'), '0' => _t('关闭')),
        '0',
        _t('Mermaid 流程图'),
        _t('默认关闭。开启后仅在正文包含 Mermaid 代码块时加载渲染库。')
    );
    $form->addInput($enableMermaid);

    $useVendorCdn = new \Typecho\Widget\Helper\Form\Element\Radio(
        'useVendorCdn',
        array('1' => _t('开启'), '0' => _t('关闭')),
        '0',
        _t('Vendor CDN'),
        _t('开启后 Lenis、Highlight.js 和 Mermaid 使用固定版本的 jsDelivr 公共资源。')
    );
    $form->addInput($useVendorCdn);

    $socialLinks = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'socialLinks', null,
        "Typecho|https://typecho.org",
        _t('社交链接'),
        _t('每行一个，格式为 名称|URL。留空则不显示。')
    );
    $form->addInput($socialLinks);

    $configAction = new \Typecho\Widget\Helper\Form\Element\Hidden(
        'themeConfigAction', null, ''
    );
    $form->addInput($configAction);

    $backupConfig = new \Typecho\Widget\Helper\Form\Element\Submit(
        'backupThemeConfig', null, _t('备份配置')
    );
    $backupConfig->input->setAttribute('class', 'btn');
    $backupConfig->input->setAttribute('formnovalidate', 'formnovalidate');
    $backupConfig->input->setAttribute(
        'onclick',
        "this.form.elements['themeConfigAction'].value='backup'"
    );
    $form->addInput($backupConfig);

    $restoreConfig = new \Typecho\Widget\Helper\Form\Element\Submit(
        'restoreThemeConfig', null, _t('恢复配置')
    );
    $restoreConfig->input->setAttribute('class', 'btn');
    $restoreConfig->input->setAttribute('formnovalidate', 'formnovalidate');
    $restoreConfig->input->setAttribute(
        'onclick',
        "if (!confirm('确定从 config.php 恢复主题配置吗？当前数据库设置将被覆盖。')) return false; this.form.elements['themeConfigAction'].value='restore'"
    );
    $form->addInput($restoreConfig);
}

function getThemeSettingKeys(): array
{
    return array(
        'logoUrl',
        'homeKicker',
        'homeIntroTitle',
        'defaultCover',
        'footerSince',
        'showThemeCredit',
        'enableCodeHighlight',
        'enableMermaid',
        'useVendorCdn',
        'socialLinks'
    );
}

function filterThemeSettings(array $settings): array
{
    return array_intersect_key($settings, array_flip(getThemeSettingKeys()));
}

function writeThemeConfigBackup(array $settings): void
{
    $path = __DIR__ . '/config.php';
    $content = "<?php\nif (!defined('__TYPECHO_ROOT_DIR__')) exit;\n\nreturn "
        . var_export(filterThemeSettings($settings), true) . ";\n";
    $temporary = tempnam(__DIR__, '.tamshen-config-');
    if ($temporary === false || file_put_contents($temporary, $content, LOCK_EX) === false) {
        if ($temporary && file_exists($temporary)) unlink($temporary);
        throw new \Typecho\Widget\Exception(_t('主题配置备份无法写入，请检查主题目录权限'));
    }
    @chmod($temporary, 0644);
    if (!rename($temporary, $path)) {
        unlink($temporary);
        throw new \Typecho\Widget\Exception(_t('主题配置备份无法替换，请检查 config.php 权限'));
    }
}

function readThemeConfigBackup(): array
{
    $path = __DIR__ . '/config.php';
    if (!is_file($path) || !is_readable($path)) return array();
    $settings = require $path;
    if (!is_array($settings)) return array();
    return filterThemeSettings($settings);
}

function persistThemeSettings(array $settings): void
{
    $db = \Typecho\Db::get();
    $theme = basename(__DIR__);
    $name = 'theme:' . $theme;
    $rows = $db->fetchAll($db->select()->from('table.options')
        ->where('name = ?', $name)->where('user = ?', 0));
    $value = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (empty($rows)) {
        $db->query($db->insert('table.options')->rows(array(
            'name' => $name,
            'value' => $value,
            'user' => 0
        )));
        return;
    }
    $db->query($db->update('table.options')->rows(array('value' => $value))
        ->where('name = ?', $name)->where('user = ?', 0));
}

function themeConfigHandle(array $settings, bool $isInit): void
{
    $action = !$isInit && isset($settings['themeConfigAction'])
        ? (string) $settings['themeConfigAction'] : '';
    $backup = $action === 'backup';
    $restore = $action === 'restore';

    if ($restore) {
        $settings = readThemeConfigBackup();
        if (!$settings) throw new \Typecho\Widget\Exception(_t('config.php 中没有可恢复的主题配置'));
        persistThemeSettings($settings);
        return;
    }

    $settings = filterThemeSettings($settings);
    persistThemeSettings($settings);
    if ($backup) writeThemeConfigBackup($settings);
}

function themeFields($layout)
{
    $views = new \Typecho\Widget\Helper\Form\Element\Number(
        'views',
        null,
        0,
        _t('浏览量'),
        _t('文章浏览次数，由主题自动更新，也可以手动修正。')
    );
    $views->input->setAttribute('min', 0);
    $layout->addItem($views);

}
