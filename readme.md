# Tamshen

一款简洁、克制的 Typecho 博客主题，适合记录设计、技术与日常内容。主题以清晰的文字层级、细线条和留白为主，支持响应式布局、评论、搜索、归档、友情链接、代码高亮和 Mermaid 流程图等功能。

![Tamshen 主题展示](screenshot.png)

## 使用

1. 将主题目录放入 Typecho 的 `usr/themes/`。
2. 在 Typecho 后台进入“控制台 → 外观”，启用 `tamshen`。
3. 在主题设置中配置首页标题、Logo、社交链接、代码高亮、Mermaid 和 CDN 等选项。
4. 友情链接页面使用自定义模板 `page-links.php`，归档页面使用 `page-archives.php`。

主题支持在后台备份和恢复配置，配置备份保存在根目录的 `config.php`。

## 开发

主题没有前端框架，直接编辑 PHP、CSS 和 JavaScript：

- 样式源文件：`static/css/style.css`
- 脚本源文件：`static/js/theme.js`
- 模板与配置：根目录 PHP 文件和 `function/`
- 构建工具：`tools/`

开发时不要直接编辑 `style.min.css` 和 `theme.min.js`。如果这两个文件存在，先删除它们，主题会自动回退加载源文件。

发布前使用 Node.js 18 或更高版本生成压缩资源：

```sh
cd tools
npm install
npm run css:audit
npm run build
npm run check
```

当前浏览器兼容基线为 Chrome/Edge 84、Firefox 78 ESR 和 Safari 14.1。

## License

第三方前端资源的许可证位于 `static/js/vendor/`。主题设计与开发：[@Tamshen](https://github.com/Tamshen)。
