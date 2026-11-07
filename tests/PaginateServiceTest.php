<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use think\App;
use think\Config;
use think\Paginator;
use Youvision\ThinkPaginate\Driver\Bootstrap6;
use Youvision\ThinkPaginate\Driver\Layui;
use Youvision\ThinkPaginate\PaginateService;

class PaginateServiceTest extends TestCase
{
    public function testDefaultConfigIsMerged(): void
    {
        $app = $this->makeApp(['type' => 'bootstrap6']);

        $config = $app->config->get('paginate');

        $this->assertSame('bootstrap6', $config['type']);
        $this->assertSame(3, $config['side']);
        $this->assertSame('上一页', $config['prev_text']);
        $this->assertSame('default', $config['theme']);
        $this->assertFalse($config['show_total']);
    }

    public function testConfigTypeResolvesDriver(): void
    {
        $app = $this->makeApp(['type' => 'bootstrap6']);

        $this->assertInstanceOf(Bootstrap6::class, $this->makePaginator($app));
    }

    public function testDefaultTypeIsLayui(): void
    {
        $app = $this->makeApp([]);

        $this->assertInstanceOf(Layui::class, $this->makePaginator($app));
    }

    public function testQueryOptionsOverrideConfigType(): void
    {
        $app = $this->makeApp(['type' => 'bootstrap6']);

        $this->assertInstanceOf(Layui::class, $this->makePaginator($app, ['type' => 'layui']));
    }

    public function testDriverReadsTextConfig(): void
    {
        $app = $this->makeApp(['type' => 'layui', 'prev_text' => '前一页']);

        $html = $this->makePaginator($app)->render();

        $this->assertStringContainsString('>前一页</a>', $html);
    }

    public function testQueryOptionsOverrideDriverConfig(): void
    {
        $app = $this->makeApp(['type' => 'layui', 'prev_text' => '前一页']);

        $html = $this->makePaginator($app, ['prev_text' => '上一页'])->render();

        $this->assertStringContainsString('>上一页</a>', $html);
        $this->assertStringNotContainsString('>前一页</a>', $html);
    }

    public function testUnknownConfigTypeThrowsException(): void
    {
        $app = $this->makeApp(['type' => 'not-exists']);

        $this->expectException(InvalidArgumentException::class);

        $this->makePaginator($app);
    }

    public function testPaginatorMakeUsesCustomDriver(): void
    {
        $app = $this->makeApp(['type' => 'bootstrap6']);

        Paginator::maker(function (...$args) use ($app) {
            return $app->make(Paginator::class, $args, true);
        });

        $paginator = Paginator::make(range(1, 10), 10, 2, 100, false, ['path' => '/list']);

        $this->assertInstanceOf(Bootstrap6::class, $paginator);
        $this->assertStringContainsString('page-item', $paginator->render());
    }

    private function makePaginator(App $app, array $options = []): Paginator
    {
        return $app->make(Paginator::class, [
            range(1, 10),
            10,
            2,
            100,
            false,
            $options,
        ], true);
    }

    private function makeApp(array $config): App
    {
        $app = new App(sys_get_temp_dir() . 'youvision-think-paginate-tests');
        $app->instance('config', new Config());
        $app->config->set($config, 'paginate');

        (new PaginateService($app))->register();

        return $app;
    }
}