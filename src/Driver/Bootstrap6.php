<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate\Driver;

class Bootstrap6 extends Driver
{
    protected function wrap(string $html): string
    {
        $size = (string) $this->option('size', '');
        $size = in_array($size, ['sm', 'lg'], true) ? $size : '';

        $class = 'pagination' . ('' !== $size ? ' pagination-' . $size : '');

        return '<ul class="' . $class . '">' . $html . '</ul>';
    }

    protected function getAvailablePageWrapper(string $url, string $page): string
    {
        return '<li class="page-item"><a class="page-link" href="' . $this->escape($url) . '">' . $this->escape($page) . '</a></li>';
    }

    protected function getDisabledTextWrapper(string $text): string
    {
        return '<li class="page-item disabled"><span class="page-link">' . $this->escape($text) . '</span></li>';
    }

    protected function getActivePageWrapper(string $text): string
    {
        return '<li class="page-item active" aria-current="page"><span class="page-link">' . $this->escape($text) . '</span></li>';
    }
}