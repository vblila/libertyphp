<?php declare(strict_types=1);

namespace Libertyphp\Tests\Logger;

use GuzzleHttp\Psr7\Utils;
use Libertyphp\Logger\SimpleLogger;
use PHPUnit\Framework\TestCase;

final class SimpleLoggerTest extends TestCase
{
    public function testCorrectLogData(): void
    {
        $stream    = Utils::streamFor(fopen('php://memory', 'w+'));
        $requestId = 'aabbcc12345';

        $memoryLogger = new SimpleLogger($stream, $requestId);

        $memoryLogger->emergency('First log message');
        $memoryLogger->alert('Second log message');
        $memoryLogger->critical('Third log message');
        $memoryLogger->error('Fourth log message');
        $memoryLogger->warning('Fifth log message');
        $memoryLogger->notice('Sixth log message');
        $memoryLogger->info('Seventh log message');
        $memoryLogger->debug('Eighth log message');

        $stream->rewind();
        $contents = trim($stream->getContents());

        $lines = explode("\n", $contents);

        $this->assertCount(8, $lines);

        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 emergency: First log message$/', $lines[0]));
        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 alert: Second log message$/', $lines[1]));
        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 critical: Third log message$/', $lines[2]));
        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 error: Fourth log message$/', $lines[3]));
        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 warning: Fifth log message$/', $lines[4]));
        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 notice: Sixth log message$/', $lines[5]));
        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 info: Seventh log message$/', $lines[6]));
        $this->assertSame(1, preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{6} aabbcc12345 debug: Eighth log message$/', $lines[7]));
    }

    public function testContextLogData(): void
    {
        $stream = Utils::streamFor(fopen('php://memory', 'w+'));
        $memoryLogger = new SimpleLogger($stream);

        $memoryLogger->info('Log with context', ['a' => 1, 'b' => 2, 'e' => ['p1' => 10, 's1' => 'state']]);

        $stream->rewind();
        $contents = trim($stream->getContents());

        $contextLog = <<<EOD
array (
  'a' => 1,
  'b' => 2,
  'e' => 
  array (
    'p1' => 10,
    's1' => 'state',
  ),
)
EOD;

        $this->assertTrue(str_ends_with($contents, $contextLog));
    }
}
