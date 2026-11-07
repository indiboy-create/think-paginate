<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate;

use InvalidArgumentException;
use think\Paginator;
use Youvision\ThinkPaginate\Driver\Bootstrap6;
use Youvision\ThinkPaginate\Driver\Layui;

final class DriverResolver
{
    public const ALIASES = [
        'layui' => Layui::class,
        'bootstrap' => Bootstrap6::class,
        'bootstrap6' => Bootstrap6::class,
        'bs' => Bootstrap6::class,
        'bs6' => Bootstrap6::class,
    ];

    public static function resolve(?string $type): string
    {
        $type = trim((string) $type);

        if ('' === $type) {
            $type = 'layui';
        }

        $alias = strtolower($type);

        if (isset(self::ALIASES[$alias])) {
            return self::ALIASES[$alias];
        }

        $class = ltrim($type, '\\');

        if (class_exists($class)) {
            if (!is_subclass_of($class, Paginator::class)) {
                throw new InvalidArgumentException(sprintf('分页驱动 [%s] 必须继承 %s', $class, Paginator::class));
            }

            return $class;
        }

        throw new InvalidArgumentException(sprintf('分页驱动 [%s] 不存在，可选值：%s', $type, implode('、', array_keys(self::ALIASES))));
    }
}