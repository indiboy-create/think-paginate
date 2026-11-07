<?php

declare(strict_types=1);

namespace Youvision\ThinkPaginate\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Youvision\ThinkPaginate\Driver\Bootstrap6;
use Youvision\ThinkPaginate\Driver\Layui;
use Youvision\ThinkPaginate\DriverResolver;

class DriverResolverTest extends TestCase
{
    public function testResolveDefaultDriver(): void
    {
        $this->assertSame(Layui::class, DriverResolver::resolve(null));
        $this->assertSame(Layui::class, DriverResolver::resolve(''));
        $this->assertSame(Layui::class, DriverResolver::resolve('   '));
    }

    public function testResolveAliases(): void
    {
        $this->assertSame(Layui::class, DriverResolver::resolve('layui'));
        $this->assertSame(Layui::class, DriverResolver::resolve('LAYUI'));
        $this->assertSame(Bootstrap6::class, DriverResolver::resolve('bootstrap'));
        $this->assertSame(Bootstrap6::class, DriverResolver::resolve('bootstrap6'));
        $this->assertSame(Bootstrap6::class, DriverResolver::resolve('bs'));
        $this->assertSame(Bootstrap6::class, DriverResolver::resolve('bs6'));
    }

    public function testResolveFullClassName(): void
    {
        $this->assertSame(Layui::class, DriverResolver::resolve(Layui::class));
        $this->assertSame(Layui::class, DriverResolver::resolve('\\' . Layui::class));
    }

    public function testRemovedBootstrapVersionsAreRejected(): void
    {
        foreach (['bootstrap3', 'bootstrap4', 'bootstrap5', 'bs3', 'bs4', 'bs5'] as $type) {
            try {
                DriverResolver::resolve($type);
                $this->fail(sprintf('驱动 [%s] 不应再被支持', $type));
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString($type, $e->getMessage());
            }
        }
    }

    public function testResolveUnknownDriverThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DriverResolver::resolve('not-exists');
    }

    public function testResolveNonPaginatorClassThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DriverResolver::resolve(\stdClass::class);
    }
}