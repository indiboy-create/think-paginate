<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate;

use think\Paginator;
use think\Service;

class PaginateService extends Service
{
    public function register(): void
    {
        $defaults = include dirname(__DIR__) . '/config/paginate.php';
        $config = $this->app->config->get('paginate', []);

        $this->app->config->set(array_merge($defaults, is_array($config) ? $config : []), 'paginate');

        $this->app->bind(Paginator::class, function ($items, int $listRows, int $currentPage = 1, ?int $total = null, bool $simple = false, array $options = []) {
            $config = $this->app->config->get('paginate', []);
            $options = array_merge(is_array($config) ? $config : [], $options);

            $driver = DriverResolver::resolve(isset($options['type']) ? (string) $options['type'] : null);

            return new $driver($items, $listRows, $currentPage, $total, $simple, $options);
        });
    }
}