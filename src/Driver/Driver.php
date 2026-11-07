<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate\Driver;

use think\Paginator;

abstract class Driver extends Paginator
{
    public function render(): string
    {
        if (!$this->hasPages()) {
            return '';
        }

        return $this->wrap($this->renderLinks());
    }

    abstract protected function wrap(string $html): string;

    protected function renderLinks(): string
    {
        if ($this->simple) {
            return $this->getPreviousButton() . $this->getNextButton();
        }

        return $this->getFirstButton()
            . $this->getPreviousButton()
            . $this->getLinks()
            . $this->getNextButton()
            . $this->getLastButton();
    }

    protected function getPreviousButton(?string $text = null): string
    {
        $text = null === $text ? $this->text('prev_text', '上一页') : $text;

        if ('' === $text) {
            return '';
        }

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        return $this->getAvailablePageWrapper($this->url($this->currentPage() - 1), $text);
    }

    protected function getNextButton(?string $text = null): string
    {
        $text = null === $text ? $this->text('next_text', '下一页') : $text;

        if ('' === $text) {
            return '';
        }

        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        return $this->getAvailablePageWrapper($this->url($this->currentPage() + 1), $text);
    }

    protected function getFirstButton(): string
    {
        $text = $this->text('first_text', '');

        if ('' === $text) {
            return '';
        }

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        return $this->getAvailablePageWrapper($this->url(1), $text);
    }

    protected function getLastButton(): string
    {
        $text = $this->text('last_text', '');

        if ('' === $text) {
            return '';
        }

        if ($this->currentPage() >= $this->lastPage()) {
            return $this->getDisabledTextWrapper($text);
        }

        return $this->getAvailablePageWrapper($this->url($this->lastPage()), $text);
    }

    protected function getLinks(): string
    {
        if ($this->simple) {
            return '';
        }

        $side = max(1, (int) $this->option('side', 3));
        $window = $side * 2;
        $lastPage = $this->lastPage();

        if ($lastPage < $window + 6) {
            $blocks = [
                $this->getUrlRange(1, $lastPage),
            ];
        } elseif ($this->currentPage() <= $window) {
            $blocks = [
                $this->getUrlRange(1, $window + 2),
                null,
                $this->getUrlRange($lastPage - 1, $lastPage),
            ];
        } elseif ($this->currentPage() > ($lastPage - $window)) {
            $blocks = [
                $this->getUrlRange(1, 2),
                null,
                $this->getUrlRange($lastPage - ($window + 2), $lastPage),
            ];
        } else {
            $blocks = [
                $this->getUrlRange(1, 2),
                null,
                $this->getUrlRange($this->currentPage() - $side, $this->currentPage() + $side),
                null,
                $this->getUrlRange($lastPage - 1, $lastPage),
            ];
        }

        $html = '';

        foreach ($blocks as $urls) {
            $html .= null === $urls ? $this->getDots() : $this->getUrlLinks($urls);
        }

        return $html;
    }

    protected function getUrlLinks(array $urls): string
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, (string) $page);
        }

        return $html;
    }

    protected function getPageLinkWrapper(string $url, string $page): string
    {
        if ((string) $this->currentPage() === $page) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    protected function getDots(): string
    {
        return $this->getDisabledTextWrapper($this->text('dots_text', '…'));
    }

    protected function option(string $name, mixed $default = null): mixed
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    protected function text(string $name, string $default): string
    {
        $value = $this->option($name, $default);

        if (false === $value || null === $value) {
            return '';
        }

        return (string) $value;
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    abstract protected function getAvailablePageWrapper(string $url, string $page): string;

    abstract protected function getDisabledTextWrapper(string $text): string;

    abstract protected function getActivePageWrapper(string $text): string;
}