<?php

use Libertyphp\Views\View;

/**
 * @var View $view
 * @var int $orderId
 * @var string $orderName
 */

$view->addJsFile('js/orders.js');
$view->addCssFile('css/orders.css');

?>
<div class="order-id"><?= View::html($orderId) ?></div>
<div class="order-name"><?= View::html($orderName) ?></div>
