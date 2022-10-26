<?php declare(strict_types=1);

use Libertyphp\Views\Content;
use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    public function testContentRenderWithParams(): void
    {
        $renderedHtml = Content::render(__DIR__ . '/test-html.php', ['userName' => 'Ivan Ivanov', 'birthDate' => '01.02.1972']);

        $exceptedHtml = <<<HTML
<div class="username">Ivan Ivanov</div>
<div class="birth-date">1972-02-01</div>

HTML;

        $this->assertSame($exceptedHtml, $renderedHtml);
    }
}
