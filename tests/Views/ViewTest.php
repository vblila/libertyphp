<?php declare(strict_types=1);

use Libertyphp\Views\LayoutView;
use Libertyphp\Views\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testUniqueJsAndCssLinks(): void
    {
        $view = new View();

        $view->addCssFile('css/1.css');
        $view->addCssFile('css/2.css');
        $view->addCssFile('css/2.css');
        $view->addCssFile('css/1.css');

        $this->assertSame(['css/1.css', 'css/2.css'], array_values($view->getCssLinks()));

        $view->addJsFile('js/1.js');
        $view->addJsFile('js/2.js');
        $view->addJsFile('js/2.js');
        $view->addJsFile('js/1.js');

        $this->assertSame(['js/1.js', 'js/2.js'], array_values($view->getJsLinks()));

        $layoutView = (new LayoutView())
            ->setContentView($view);

        $layoutView->addCssFile('css/1.css');
        $layoutView->addCssFile('css/3.css');
        $layoutView->addCssFile('css/3.css');

        $this->assertSame(['css/1.css', 'css/3.css', 'css/2.css'], array_values($layoutView->getCssLinks()));

        $layoutView->addJsFile('js/1.js');
        $layoutView->addJsFile('js/3.js');
        $layoutView->addJsFile('js/3.js');

        $this->assertSame(['js/1.js', 'js/3.js', 'js/2.js'], array_values($layoutView->getJsLinks()));
    }

    public function testFullCaseViewWithLayout(): void
    {
        $contentView = (new View())
            ->setViewPath(__DIR__ . '/test-content-view.php')
            ->setRenderData(['orderId' => 1051, 'orderName' => 'Notebook']);

        $layoutView = (new LayoutView())
            ->setViewPath(__DIR__ . '/test-layout-view.php')
            ->setContentView($contentView)
            ->setTitle('Test page')
            ->setKeywords('test,page,libertyphp')
            ->setDescription('This is test page for Liberty PHP');

        $renderedHtml = $layoutView->render()->getRenderedContent();

        $exceptedHtml = <<<HTML
<!doctype html>
<html lang="en">
<head>
    <title>Test page</title>
    <meta name="description" content="This is test page for Liberty PHP">
    <meta name="keywords" content="test,page,libertyphp">

            <link rel="stylesheet" href="css/script.js">
            <link rel="stylesheet" href="css/orders.css">
    
            <script defer src="js/script.js"></script>
            <script defer src="js/orders.js"></script>
    </head>
<body>
<div class="order-id">1051</div>
<div class="order-name">Notebook</div>
</body>
</html>

HTML;

        $this->assertSame($exceptedHtml, $renderedHtml);
    }
}
