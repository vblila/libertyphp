<?php declare(strict_types=1);

namespace Libertyphp\Tests\Di;

use Libertyphp\DependencyInjection\DiContainer;
use Libertyphp\DependencyInjection\NotFoundException;
use PHPUnit\Framework\TestCase;

final class DiTest extends TestCase
{
    public function testGeneralCaseDi(): void
    {
        $di = new DiContainer();

        $i = 0;

        $di->set('masterDb', function() use (&$i) {
            $i++;
            return 'masterDbResult_' . $i;
        });

        $this->assertTrue($di->has('masterDb'));
        $this->assertSame('masterDbResult_1', $di->get('masterDb'));

        // Repeatability
        $this->assertTrue($di->has('masterDb'));
        $this->assertSame('masterDbResult_2', $di->get('masterDb'));
        $this->assertSame('masterDbResult_3', $di->get('masterDb'));
    }

    public function testSingletonCaseDi(): void
    {
        $di = new DiContainer();

        $i = 0;

        $di->singleton('masterDb', function() use ($i) {
            $i++;
            return 'masterDbResult_' . $i;
        });

        $this->assertTrue($di->has('masterDb'));
        $this->assertSame('masterDbResult_1', $di->get('masterDb'));

        // Singleton
        $this->assertTrue($di->has('masterDb'));
        $this->assertSame('masterDbResult_1', $di->get('masterDb'));
        $this->assertSame('masterDbResult_1', $di->get('masterDb'));
    }

    public function testNotFoundCaseDi(): void
    {
        $di = new DiContainer();

        $di->set('masterDb', function() {
            return 'masterDbResult';
        });

        $this->assertFalse($di->has('slaveDb'));

        $this->expectException(NotFoundException::class);
        $di->get('slaveDb');
    }
}
