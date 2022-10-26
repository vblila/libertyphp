<?php

use Libertyphp\Views\LayoutView;
use Libertyphp\Views\View;

/**
 * @var LayoutView $view
 */

$view->addJsFile('js/script.js');
$view->addCssFile('css/script.js');

?>
<!doctype html>
<html lang="en">
<head>
    <title><?= View::html($view->getTitle()) ?></title>
    <meta name="description" content="<?= View::html($view->getDescription()) ?>">
    <meta name="keywords" content="<?= View::html($view->getKeywords()) ?>">

    <?php foreach ($view->getCssLinks() as $link) { ?>
        <link rel="stylesheet" href="<?= View::html($link) ?>">
    <?php } ?>

    <?php foreach ($view->getJsLinks() as $link) { ?>
        <script defer src="<?= View::html($link) ?>"></script>
    <?php } ?>
</head>
<body>
<?= $view->getContentView()->getRenderedContent() ?>
</body>
</html>
