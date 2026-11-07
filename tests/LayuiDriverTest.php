<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate\Tests;

use PHPUnit\Framework\TestCase;
use Youvision\ThinkPaginate\Driver\Layui;

class LayuiDriverTest extends TestCase
{
    public function testRenderDefaultMiddlePage(): void
    {
        $html = $this->paginator([], 50, 1000)->render();

        $this->assertStringContainsString('<div class="layui-box layui-laypage layui-laypage-default">', $html);
        $this->assertStringContainsString('<span class="layui-laypage-curr"><em class="layui-laypage-em"></em><em>50</em></span>', $html);
        $this->assertStringContainsString('href="/list?page=49" class="layui-laypage-prev"', $html);
        $this->assertStringContainsString('href="/list?page=51" class="layui-laypage-next"', $html);
        $this->assertStringContainsString('<span class="layui-laypage-spr">…</span>', $html);
        $this->assertStringContainsString('href="/list?page=1"', $html);
        $this->assertStringContainsString('href="/list?page=100"', $html);
    }

    public function testFirstPageDisablesPreviousButton(): void
    {
        $html = $this->paginator([], 1)->render();

        $this->assertStringContainsString('class="layui-laypage-prev layui-disabled">上一页</a>', $html);
        $this->assertStringNotContainsString('href="/list?page=0"', $html);
    }

    public function testLastPageDisablesNextButton(): void
    {
        $html = $this->paginator([], 10)->render();

        $this->assertStringContainsString('class="layui-laypage-next layui-disabled">下一页</a>', $html);
        $this->assertStringNotContainsString('href="/list?page=11"', $html);
    }

    public function testSinglePageRendersEmpty(): void
    {
        $html = $this->paginator([], 1, 5)->render();

        $this->assertSame('', $html);
    }

    public function testSimpleModeOnlyRendersPreviousAndNext(): void
    {
        $html = $this->paginator([], 5, 100, 10, true)->render();

        $this->assertStringContainsString('layui-laypage-prev', $html);
        $this->assertStringContainsString('layui-laypage-next', $html);
        $this->assertStringNotContainsString('layui-laypage-curr', $html);
        $this->assertStringNotContainsString('layui-laypage-spr', $html);
    }

    public function testShowTotalOption(): void
    {
        $html = $this->paginator(['show_total' => true])->render();

        $this->assertStringContainsString('<span class="layui-laypage-count">共 100 条</span>', $html);
    }

    public function testShowTotalIsIgnoredInSimpleMode(): void
    {
        $html = $this->paginator(['show_total' => true], 5, 100, 10, true)->render();

        $this->assertStringNotContainsString('layui-laypage-count', $html);
    }

    public function testFirstAndLastButtonsAreIgnoredInSimpleMode(): void
    {
        $html = $this->paginator([
            'first_text' => '首页',
            'last_text' => '尾页',
        ], 5, 100, 10, true)->render();

        $this->assertStringNotContainsString('layui-laypage-first', $html);
        $this->assertStringNotContainsString('layui-laypage-last', $html);
    }

    public function testThemeOption(): void
    {
        $html = $this->paginator(['theme' => 'blue'])->render();

        $this->assertStringContainsString('layui-laypage layui-laypage-blue', $html);
    }

    public function testInvalidThemeFallsBackToDefault(): void
    {
        $html = $this->paginator(['theme' => 'blue" onclick="alert(1)'])->render();

        $this->assertStringContainsString('layui-laypage layui-laypage-default', $html);
        $this->assertStringNotContainsString('onclick', $html);
    }

    public function testFirstAndLastButtons(): void
    {
        $html = $this->paginator(['first_text' => '首页', 'last_text' => '尾页'])->render();

        $this->assertStringContainsString('class="layui-laypage-first"', $html);
        $this->assertStringContainsString('href="/list?page=1" class="layui-laypage-first">首页</a>', $html);
        $this->assertStringContainsString('href="/list?page=10" class="layui-laypage-last">尾页</a>', $html);
    }

    public function testFirstPageDisablesFirstButton(): void
    {
        $html = $this->paginator(['first_text' => '首页'], 1)->render();

        $this->assertStringContainsString('class="layui-laypage-first layui-disabled">首页</a>', $html);
    }

    public function testLastPageDisablesLastButton(): void
    {
        $html = $this->paginator(['last_text' => '尾页'], 10)->render();

        $this->assertStringContainsString('class="layui-laypage-last layui-disabled">尾页</a>', $html);
    }

    public function testCustomButtonTexts(): void
    {
        $html = $this->paginator([
            'prev_text' => '前一页',
            'next_text' => '后一页',
            'dots_text' => '...',
        ], 50, 1000)->render();

        $this->assertStringContainsString('>前一页</a>', $html);
        $this->assertStringContainsString('>后一页</a>', $html);
        $this->assertStringContainsString('<span class="layui-laypage-spr">...</span>', $html);
    }

    public function testHideButtonWithFalseText(): void
    {
        $html = $this->paginator(['prev_text' => false, 'next_text' => false])->render();

        $this->assertStringNotContainsString('layui-laypage-prev', $html);
        $this->assertStringNotContainsString('layui-laypage-next', $html);
    }

    public function testSideOptionControlsSliderWindow(): void
    {
        $html = $this->paginator(['side' => 1])->render();

        $this->assertStringContainsString('href="/list?page=4"', $html);
        $this->assertStringContainsString('href="/list?page=6"', $html);
        $this->assertStringNotContainsString('href="/list?page=3"', $html);
        $this->assertStringNotContainsString('href="/list?page=7"', $html);
    }

    public function testQueryParametersArePreserved(): void
    {
        $html = $this->paginator(['query' => ['kw' => 'thinkphp']])->render();

        $this->assertStringContainsString('href="/list?kw=thinkphp&amp;page=4"', $html);
    }

    public function testUrlIsEscaped(): void
    {
        $html = $this->paginator(['path' => '/list', 'query' => ['kw' => '" onmouseover="alert(1)']])->render();

        $this->assertStringNotContainsString('onmouseover="alert(1)"', $html);
    }

    private function paginator(array $options = [], int $currentPage = 5, int $total = 100, int $listRows = 10, bool $simple = false): Layui
    {
        $options = array_merge([
            'path' => '/list',
            'var_page' => 'page',
            'query' => [],
            'fragment' => '',
        ], $options);

        return new Layui(range(1, $listRows), $listRows, $currentPage, $total, $simple, $options);
    }
}