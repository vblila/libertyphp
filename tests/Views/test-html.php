<?php

use Libertyphp\Views\View;

/**
 * @var string $userName
 * @var string $birthDate
 */

?>
<div class="username"><?= View::html($userName) ?></div>
<div class="birth-date"><?= View::html(date('Y-m-d', strtotime($birthDate))) ?></div>
