## ThinkPHP-PanGu - 盘古ThinkPHP开源索引!
面向 ThinkPHP 的开源工程索引：组件、框架、服务与发布工具，按能力组织，持续整理。
仓库地址：**<https://github.com/indiboy-create/ThinkPHP-PanGu/>**
在线站点：**<https://indiboy-create.github.io/ThinkPHP-PanGu/>**

# youvision/think-paginate

面向 ThinkPHP 8 的分页扩展组件，内置 **Layui**、**Bootstrap 6** 两套分页驱动。

ThinkPHP 8 默认的 Bootstrap 分页驱动面向旧版标记，在 Layui 或 Bootstrap 6 项目中需要额外适配。本组件通过覆盖 `think\Paginator` 的容器绑定，让 `Model::paginate()` 直接输出目标 CSS 框架的分页结构，无需修改业务代码。

## 环境要求

| 依赖 | 版本 |
| --- | --- |
| PHP | >= 8.0 |
| ThinkPHP | >= 8.0 |
| topthink/think-orm | 由 ThinkPHP 自动安装 |

## 安装

```bash
composer require youvision/think-paginate
```

组件通过 Composer 的 `extra.think.services` 暴露服务。安装完成后检查项目根目录的 `vendor/services.php` 是否包含：

```php
Youvision\ThinkPaginate\PaginateService::class
```

如果不存在，执行一次：

```bash
php think service:discover
```

> ThinkPHP 应用通常会在 `composer.json` 的 `post-autoload-dump` 中自动执行 `service:discover`，安装依赖后一般无需手动处理。

## 配置

组件自带 `config/paginate.php` 默认配置，安装后即可使用。如需自定义，在应用 `config` 目录下新建 `config/paginate.php`，**只写需要覆盖的键**，组件会自动与默认配置合并，应用配置优先。

```php
<?php
// config/paginate.php
return [
    'type'       => 'layui',
    'prev_text'  => '上一页',
    'next_text'  => '下一页',
    'first_text' => '首页',
    'last_text'  => '尾页',
    'dots_text'  => '…',
    'side'       => 3,
    'theme'      => 'default',
    'show_total' => true,
    'size'       => '',
];
```

### 配置项说明

| 配置项 | 类型 | 默认值 | 说明 | 生效驱动 |
| --- | --- | --- | --- | --- |
| `type` | string | `layui` | 分页驱动，可选 `layui`、`bootstrap`、`bootstrap6`、`bs`、`bs6`，也可填写自定义驱动完整类名 | 全部 |
| `prev_text` | string\|false | `上一页` | 上一页按钮文本，`false` 或空字符串时隐藏 | 全部 |
| `next_text` | string\|false | `下一页` | 下一页按钮文本，`false` 或空字符串时隐藏 | 全部 |
| `first_text` | string\|null | `null` | 首页按钮文本，`null` 时不显示 | 全部 |
| `last_text` | string\|null | `null` | 尾页按钮文本，`null` 时不显示 | 全部 |
| `dots_text` | string | `…` | 页码省略号文本 | 全部 |
| `side` | int | `3` | 滑动窗口半径，当前页左右各显示的连续页码数量 | 全部 |
| `theme` | string | `default` | Layui 主题，对应 `layui-laypage-{theme}`，可用 `default`、`blue`；非法值回退为 `default` | layui |
| `show_total` | bool | `false` | 是否输出总记录数（`layui-laypage-count`），简洁分页模式下自动忽略 | layui |
| `size` | string | 空 | Bootstrap 6 分页尺寸，可用 `sm`、`lg`；非法值忽略 | bootstrap6 |

> 每页条数 `list_rows`、分页变量 `var_page` 由 ThinkPHP 的 `paginate()` 方法参数决定，不放在本组件配置中。例如 `paginate(20)` 表示每页 20 条。

## 使用

控制器：

```php
namespace app\controller;

use app\model\User;
use think\facade\View;

class Index
{
    public function index()
    {
        $list = User::where('status', 1)->paginate(15);

        return View::fetch('index', ['list' => $list]);
    }
}
```

模板中直接输出分页 HTML（必须使用 `raw` 避免被转义）：

```html
{volist name="list" id="item"}
    <div>{$item.title}</div>
{/volist}

{$list|raw}
```

### 运行时切换驱动

`paginate()` 的第一个参数支持传入配置数组，数组中的配置优先于 `config/paginate.php`：

```php
$list = User::paginate([
    'type'      => 'bootstrap6',
    'list_rows' => 20,
    'size'      => 'sm',
]);
```

```php
$list = User::paginate([
    'type'       => 'layui',
    'show_total' => true,
    'theme'      => 'blue',
]);
```

也可以通过 `type` 临时切换为自定义驱动：

```php
$list = User::paginate(['type' => \app\paginator\MyDriver::class]);
```

### 简洁分页

```php
$list = User::paginate(15, true);
```

简洁模式只输出上一页、下一页按钮，忽略页码、总数、尾页等依赖总页数的内容。

## 输出结构

### Layui

```html
<div class="layui-box layui-laypage layui-laypage-default">
    <a href="/index/index?page=1" class="layui-laypage-first">首页</a>
    <a href="/index/index?page=4" class="layui-laypage-prev">上一页</a>
    <a href="/index/index?page=1">1</a>
    <a href="/index/index?page=2">2</a>
    <span class="layui-laypage-spr">…</span>
    <a href="/index/index?page=4">4</a>
    <span class="layui-laypage-curr">
        <em class="layui-laypage-em"></em><em>5</em>
    </span>
    <a href="/index/index?page=6">6</a>
    <a href="/index/index?page=6" class="layui-laypage-next">下一页</a>
    <a href="/index/index?page=10" class="layui-laypage-last">尾页</a>
    <span class="layui-laypage-count">共 100 条</span>
</div>
```

页码使用真实 `href` 链接而非 Layui 的 `javascript:;` + `data-page`，服务端渲染即可直接跳转，无需额外 JavaScript。

### Bootstrap 6

```html
<ul class="pagination">
    <li class="page-item"><a class="page-link" href="/index/index?page=4">上一页</a></li>
    <li class="page-item"><a class="page-link" href="/index/index?page=4">4</a></li>
    <li class="page-item active" aria-current="page"><span class="page-link">5</span></li>
    <li class="page-item"><a class="page-link" href="/index/index?page=6">6</a></li>
    <li class="page-item"><a class="page-link" href="/index/index?page=6">下一页</a></li>
</ul>
```

Bootstrap 6 推荐用 `<nav aria-label="...">` 包裹分页列表，可在模板中自行添加：

```html
<nav aria-label="分页导航">
    {$list|raw}
</nav>
```

## 自定义驱动

自定义驱动继承 `Youvision\ThinkPaginate\Driver\Driver`（已封装页码窗口、省略号、首页尾页等逻辑），只需实现 HTML 包装方法：

```php
<?php

namespace app\paginator;

use Youvision\ThinkPaginate\Driver\Driver;

class MyDriver extends Driver
{
    protected function wrap(string $html): string
    {
        return '<nav class="my-pagination">' . $html . '</nav>';
    }

    protected function getAvailablePageWrapper(string $url, string $page): string
    {
        return '<a href="' . $this->escape($url) . '">' . $this->escape($page) . '</a>';
    }

    protected function getDisabledTextWrapper(string $text): string
    {
        return '<span class="disabled">' . $this->escape($text) . '</span>';
    }

    protected function getActivePageWrapper(string $text): string
    {
        return '<span class="active">' . $this->escape($text) . '</span>';
    }
}
```

配置 `'type' => MyDriver::class` 即可使用。也可以直接继承 `\think\Paginator`，但需要自行实现 `render()` 等方法。

## 工作原理

ThinkPHP 8 在 `PaginatorService` 中把 `think\Paginator` 绑定到官方 Bootstrap 驱动，并通过 `Paginator::maker()` 调用容器解析实例。本组件的 `PaginateService` 在 `register()` 阶段重新绑定 `think\Paginator` 为工厂闭包，在每次分页时按以下优先级解析驱动：

1. `paginate()` 传入的 `type`
2. `config/paginate.php` 中的 `type`
3. 默认 `layui`

因此切换驱动不需要修改业务查询代码。

## 测试

```bash
composer install
composer test
```

或直接运行：

```bash
php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml
```

## 常见问题

**分页仍然是旧版 Bootstrap 样式**

1. 确认已执行 `php think service:discover`，且 `vendor/services.php` 中存在 `Youvision\ThinkPaginate\PaginateService`。
2. 确认已清除 ThinkPHP 配置缓存（删除 `runtime` 目录下的缓存文件）。
3. 确认应用的 `config/paginate.php` 没有把 `type` 写成 TP 官方驱动的完整类名。

**修改配置后没有变化**

驱动从当前请求的 `config('paginate')` 读取配置，修改配置文件后需要清理配置缓存；如果使用了 `paginate()` 参数配置，查询参数优先级最高。

**不再支持 Bootstrap 3/4/5**

本组件只保留 Layui 与 Bootstrap 6 两套驱动。`bootstrap3`、`bootstrap4`、`bootstrap5`、`bs3`、`bs4`、`bs5` 会抛出驱动不存在的异常。如需旧版标记，可继承 `Youvision\ThinkPaginate\Driver\Driver` 自行实现，并通过完整类名配置。

**简洁分页缺少页码和总数**

这是 ThinkPHP 简洁分页的设计：不统计总数，因此无法生成页码、尾页与总条数。

**分页链接参数丢失**

ThinkPHP 会自动携带当前请求参数。如需追加额外参数，使用 `appends()` 或 `paginate(['query' => [...]])`：

```php
$list = User::paginate(['query' => ['keyword' => $keyword]]);
```

## 目录结构

```
config/paginate.php          默认配置
src/PaginateService.php      服务注册与驱动工厂
src/DriverResolver.php       驱动别名解析
src/Driver/Driver.php        分页驱动基类
src/Driver/Layui.php         Layui 驱动
src/Driver/Bootstrap6.php    Bootstrap 6 驱动
tests/                       PHPUnit 测试
```

## License

MIT