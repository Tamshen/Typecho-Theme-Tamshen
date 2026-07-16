# Repository Guidelines（仓库指南）

## 项目概览与目录结构

本仓库是 Typecho 主题，根目录中的 PHP 文件是当前生效的模板：

- `index.php`、`search.php`：文章列表与搜索结果。
- `post.php`、`page.php`、`404.php`：文章、独立页面与错误页面。
- `page-archives.php`、`page-links.php`：自定义页面模板。
- `header.php`、`footer.php`、`sidebar.php`、`comments.php`：公共界面。
- `functions.php`、`config.php`：主题钩子与配置；可复用辅助逻辑放在 `function/`。

前端资源位于 `static/css/` 和 `static/js/`。Typecho 从 `index.php` 文件头读取主题元数据，`screenshot.png` 是后台预览图。Node.js 构建工具位于 `tools/`。

## 本地开发与构建

PHP 模板无需编译。CSS 和 JavaScript 的压缩仅用于发布。

### 前端资源工作流

- `static/css/style.css` 和 `static/js/theme.js` 是唯一允许编辑的前端源文件。
- `static/css/style.min.css` 和 `static/js/theme.min.js` 是生成产物，禁止直接编辑。
- 主题优先加载 `.min` 文件。开发前删除两个 `.min` 文件，使主题自动回退到源文件。
- 开发期间不要生成或持续监听 `.min` 文件，避免旧产物掩盖源文件的实际效果。
- 仅在准备发布时进入 `tools/`，执行 `npm install` 和 `npm run build`，重新生成并提交两个 `.min` 文件。
- 发布前执行 `npm run check`，确认压缩产物与源文件一致，并通过兼容性检查。

### 常用命令

- `find . -maxdepth 2 -name '*.php' -exec php -l {} \;`：检查当前主题 PHP 文件语法。
- `php -l functions.php`：快速检查主题主集成文件。
- `cd tools && npm run build`：仅发布时生成压缩资源。
- `cd tools && npm run check`：检查浏览器兼容基线和压缩产物一致性。

在 Typecho 安装根目录运行 `php -S 127.0.0.1:8000` 启动本地服务。随后在 Typecho 后台启用 `tamshen`，访问 `http://127.0.0.1:8000`。

## 编码风格与安全

- PHP、JavaScript 和展开的 CSS 代码块使用四空格缩进。
- PHP 函数的左花括号另起一行；控制语句的左花括号与语句同行。
- 仅在当前文件已经使用短数组语法时继续使用。
- 可复用 PHP 辅助函数统一添加 `tamshen` 前缀，例如 `tamshenCover`。
- CSS 类名和资源文件名使用 kebab-case。
- JavaScript 保持现有的无依赖写法，并通过 `tools/` 中的构建工具转换到项目声明的浏览器基线。
- 根据输出上下文使用 `htmlspecialchars` 转义内容，验证外部 URL，并优先使用 Typecho API，避免直接访问数据库。

## 界面与图标

新增或替换界面图标时，优先从 [Carbon Icons](https://icones.js.org/collection/carbon) 选择风格一致的图标，并复用 `static/img/` 中已有资源。图标文件名使用 kebab-case；不要为同一用途重复引入近似图标。

## 测试与验收

仓库暂未配置自动化测试框架或覆盖率目标。提交前应：

- 对所有改动过的 PHP 文件执行语法检查。
- 手动验证首页、归档或搜索、文章、独立页面、评论和 404 页面。
- 检查桌面端与移动端布局、导航、图片回退、分页和评论提交。
- 修改 JavaScript 后确认浏览器控制台无报错。
- 发布前运行 `cd tools && npm run check`。

## 提交与拉取请求

历史提交采用“表情符号 + Conventional Commit 类型”的格式，例如 `🎉 feat: 添加独立下载页面` 和 `🎉 init: 初始化主题`。标题应简洁、使用祈使语气，并且一次提交只处理一类变更。

拉取请求应说明用户可见的变化、列出手动验证内容并关联相关 Issue。涉及布局或样式时附上修改前后截图；影响响应式行为时同时提供桌面端和移动端截图。不要混入无关的 Typecho 核心文件、生成文件或旧版文件变更。
