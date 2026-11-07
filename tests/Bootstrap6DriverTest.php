<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate\Tests;

use PHPUnit\Framework\TestCase;
use Youvision\ThinkPaginate\Driver\Bootstrap6;
use Youvision\ThinkPaginate\Driver\Layui;

class Bootstrap6DriverTest extends TestCase
{
    public function testRenderMarkup(): void
    {
        $html = $this->paginator(50, 1000)->render();

        $this->assertStringContainsString('<ul class="pagination">', $html);
        $this->assertStringContainsString('<li class="page-item active" aria-current="page"><span class="page-link">50</span></li>', $html);
        $this->assertStringContainsString('<li class="page-item"><a class="page-link" href="/list?page=49">上一页</a></li>', $html);
        $this->assertStringContainsString('<li class="page-item"><a class="page-link" href="/list?page=51">下一页</a></li>', $html);
        $this->assertStringContainsString('<li class="page-item disabled"><span class="page-link">…</span></li>', $html);
    }

    public function testFirstPageDisablesPreviousButton(): void
    {
        $html = $this->paginator(1)->render();

        $this->assertStringContainsString('<li class="page-item disabled"><span class="page-link">上一页</span></li>', $html);
    }

    public function testLastPageDisablesNextButton(): void
    {
        $html = $this->paginator(10)->render();

        $this->assertStringContainsString('<li class="page-item disabled"><span class="page-link">下一页</span></li>', $html);
    }

    public function testSinglePageRendersEmpty(): void
    {
        $this->assertSame('', $this->paginator(1, 5)->render());
    }

    public function testSimpleMode(): void
    {
        $html = $this->paginator(5, 100, 10, true)->render();

        $this->assertStringContainsString('>上一页</a>', $html);
        $this->assertStringContainsString('>下一页</a>', $html);
        $this->assertStringNotContainsString('aria-current', $html);
    }

    public function testFirstAndLastButtonsAreIgnoredInSimpleMode(): void
    {
        $html = $this->paginator(5, 100, 10, true, [
            'first_text' => '首页',
            'last_text' => '尾页',
        ])->render();

        $this->assertStringNotContainsString('>首页</a>', $html);
        $this->assertStringNotContainsString('>尾页</a>', $html);
    }

    public function testSizeOption(): void
    {
        $this->assertStringContainsString('<ul class="pagination pagination-sm">', $this->paginator(5, 100, 10, false, ['size' => 'sm'])->render());
        $this->assertStringContainsString('<ul class="pagination pagination-lg">', $this->paginator(5, 100, 10, false, ['size' => 'lg'])->render());
        $this->assertStringContainsString('<ul class="pagination">', $this->paginator(5, 100, 10, false, ['size' => 'xl'])->render());
    }

    public function testFirstAndLastButtons(): void
    {
        $html = $this->paginator(5, 100, 10, false, [
            'first_text' => '首页',
            'last_text' => '尾页',
        ])->render();

        $this->assertStringContainsString('<li class="page-item"><a class="page-link" href="/list?page=1">首页</a></li>', $html);
        $this->assertStringContainsString('<li class="page-item"><a class="page-link" href="/list?page=10">尾页</a></li>', $html);
    }

    public function testSideOption(): void
    {
        $html = $this->paginator(5, 100, 10, false, ['side' => 1])->render();

        $this->assertStringContainsString('href="/list?page=4"', $html);
        $this->assertStringContainsString('href="/list?page=6"', $html);
        $this->assertStringNotContainsString('href="/list?page=3"', $html);
        $this->assertStringNotContainsString('href="/list?page=7"', $html);
    }

    public function testQueryParametersArePreserved(): void
    {
        $html = $this->paginator(5, 100, 10, false, ['query' => ['kw' => 'thinkphp']])->render();

        $this->assertStringContainsString('href="/list?kw=thinkphp&amp;page=4"', $html);
    }

    public function testUrlIsEscaped(): void
    {
        $html = $this->paginator(5, 100, 10, false, [
            'query' => ['kw' => '" onmouseover="alert(1)'],
        ])->render();

        $this->assertStringNotContainsString('onmouseover="alert(1)"', $html);
    }

    private function paginator(int $currentPage = 5, int $total = 100, int $listRows = 10, bool $simple = false, array $options = []): Bootstrap6
    {
        $options = array_merge([
            'path' => '/list',
            'var_page' => 'page',
            'query' => [],
            'fragment' => '',
        ], $options);

        return new Bootstrap6(range(1, $simple ? $listRows + 1 : $listRows), $listRows, $currentPage, $total, $simple, $options);
    }
}