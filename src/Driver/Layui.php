<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate\Driver;

class Layui extends Driver
{
    protected function wrap(string $html): string
    {
        if (!$this->simple && $this->option('show_total', false)) {
            $html .= '<span class="layui-laypage-count">共 ' . $this->total() . ' 条</span>';
        }

        $theme = (string) $this->option('theme', 'default');
        $theme = preg_match('/^[a-zA-Z0-9_-]+$/', $theme) ? $theme : 'default';

        return '<div class="layui-box layui-laypage layui-laypage-' . $theme . '">' . $html . '</div>';
    }

    protected function getPreviousButton(?string $text = null): string
    {
        $text = null === $text ? $this->text('prev_text', '上一页') : $text;

        if ('' === $text) {
            return '';
        }

        if ($this->currentPage() <= 1) {
            return '<a href="javascript:;" class="layui-laypage-prev layui-disabled">' . $this->escape($text) . '</a>';
        }

        return '<a href="' . $this->escape($this->url($this->currentPage() - 1)) . '" class="layui-laypage-prev">' . $this->escape($text) . '</a>';
    }

    protected function getNextButton(?string $text = null): string
    {
        $text = null === $text ? $this->text('next_text', '下一页') : $text;

        if ('' === $text) {
            return '';
        }

        if (!$this->hasMore) {
            return '<a href="javascript:;" class="layui-laypage-next layui-disabled">' . $this->escape($text) . '</a>';
        }

        return '<a href="' . $this->escape($this->url($this->currentPage() + 1)) . '" class="layui-laypage-next">' . $this->escape($text) . '</a>';
    }

    protected function getFirstButton(): string
    {
        $text = $this->text('first_text', '');

        if ('' === $text) {
            return '';
        }

        if ($this->currentPage() <= 1) {
            return '<a href="javascript:;" class="layui-laypage-first layui-disabled">' . $this->escape($text) . '</a>';
        }

        return '<a href="' . $this->escape($this->url(1)) . '" class="layui-laypage-first">' . $this->escape($text) . '</a>';
    }

    protected function getLastButton(): string
    {
        $text = $this->text('last_text', '');

        if ('' === $text) {
            return '';
        }

        if ($this->currentPage() >= $this->lastPage()) {
            return '<a href="javascript:;" class="layui-laypage-last layui-disabled">' . $this->escape($text) . '</a>';
        }

        return '<a href="' . $this->escape($this->url($this->lastPage())) . '" class="layui-laypage-last">' . $this->escape($text) . '</a>';
    }

    protected function getAvailablePageWrapper(string $url, string $page): string
    {
        return '<a href="' . $this->escape($url) . '">' . $this->escape($page) . '</a>';
    }

    protected function getDisabledTextWrapper(string $text): string
    {
        return '<span class="layui-disabled">' . $this->escape($text) . '</span>';
    }

    protected function getActivePageWrapper(string $text): string
    {
        return '<span class="layui-laypage-curr"><em class="layui-laypage-em"></em><em>' . $this->escape($text) . '</em></span>';
    }

    protected function getDots(): string
    {
        return '<span class="layui-laypage-spr">' . $this->escape($this->text('dots_text', '…')) . '</span>';
    }
}