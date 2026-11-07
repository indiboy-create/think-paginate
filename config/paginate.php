<?php

declare(strict_types=1);

/**
 * 分页配置
 *
 * 本文件是包的默认配置，安装后无需复制到应用目录即可使用。
 * 如需自定义，在应用 config 目录新建 config/paginate.php，
 * 只写需要覆盖的键即可，包会自动与默认配置合并（应用配置优先）。
 */
return [
    /**
     * 分页驱动类型
     *
     * 可选值：
     * - layui      Layui 风格（layui-laypage）
     * - bootstrap  Bootstrap 6 风格（page-item / page-link）
     * - bootstrap6 Bootstrap 6 风格
     * - bs、bs6     Bootstrap 6 的别名
     *
     * 也可以填写自定义驱动的完整类名，例如：
     * \app\paginator\MyDriver::class
     * 自定义驱动必须继承 \think\Paginator。
     *
     * 在查询时也可以通过 paginate(['type' => 'bootstrap6']) 临时覆盖本配置。
     */
    'type' => 'layui',

    /**
     * 上一页按钮文本，false 或空字符串表示隐藏该按钮
     */
    'prev_text' => '上一页',

    /**
     * 下一页按钮文本，false 或空字符串表示隐藏该按钮
     */
    'next_text' => '下一页',

    /**
     * 首页按钮文本
     *
     * null 表示不显示首页按钮；设置为 '首页' 等文本后显示。
     */
    'first_text' => null,

    /**
     * 尾页按钮文本
     *
     * null 表示不显示尾页按钮；设置为 '尾页' 等文本后显示。
     */
    'last_text' => null,

    /**
     * 页码省略号文本
     */
    'dots_text' => '…',

    /**
     * 滑动窗口半径
     *
     * 当前页左右两侧各显示多少个连续页码，默认 3 表示左右各 3 个。
     * 页码总数较少时不会出现省略号。
     */
    'side' => 3,

    /**
     * Layui 主题
     *
     * 对应 layui-laypage-{theme} 类名，默认 default；
     * 使用 layui 内置蓝色主题时填写 blue。
     * 该配置仅对 layui 驱动生效。
     */
    'theme' => 'default',

    /**
     * 是否显示总记录数
     *
     * 开启后输出 <span class="layui-laypage-count">共 N 条</span>。
     * 该配置仅对 layui 驱动、非简洁分页模式生效。
     */
    'show_total' => false,

    /**
     * Bootstrap 6 分页尺寸
     *
     * '' 表示默认尺寸，可选 sm（小号）、lg（大号）。
     * 该配置仅对 bootstrap6 驱动生效，非法值会被忽略。
     */
    'size' => '',
];