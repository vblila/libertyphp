Liberty PHP
===========
Fast and extensible micro framework for PHP. Liberty PHP helps you build RESTful web applications quickly and easily.

Philosophy of the project
=========================   
- Large frameworks have a lot of extra functionality that reduce code performance. Preferably to use an extensible framework, where the developer decides which part of the framework he wants to write himself or find a suitable extension.
- The framework should contain only the functionality that will be used by 99% in project. All optional functionality should be on extensions.
- There shouldn't be any "magic" in frameworks (like in Laravel). Only clear method calling and object access.
- Clear code is better than short code.

Installation and quick start
============================

Install
-------
Install Liberty PHP with composer
```
composer require libertyphp/libertyphp
```

Make directories like this:
```text
|--bootstrap
|--config
|--myapp
    |--http
|--public
|--views
```

Class autoload
--------------
Append to ```composer.json``` autoload rule:
```
"autoload": {
    "psr-4": {
        "MyApp\\": "myapp/"
    }
},
```

Error handler
-------------
Create file ```bootstrap/error_handler.php``` with content:
```php
<?php

set_error_handler(function($type, $message, $file, $line) {
    switch ($type) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $error = 'Warning';
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            break;
        default:
            $error = 'Unknown Error';
            break;
    }

    $errorString = sprintf("PHP %s:  %s in %s on line %d", $error, $message, $file, $line);
    throw new \Exception($errorString);
});
```

DI container
------------
Set up a container for HTTP request. Create file ```bootstrap/di.php``` with content:

```php
<?php

use Libertyphp\DependencyInjection\DiContainer;
use GuzzleHttp\Psr7\ServerRequest;

$di = new DiContainer();

$di->singleton('serverRequest', function() {
    $serverRequest = ServerRequest::fromGlobals();
    foreach ($_SERVER as $key => $value) {
        if (str_starts_with($key, 'HTTP_')) {
            $serverRequest = $serverRequest->withHeader(
                str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5))))),
                $value
            );
        }
    }

    return $serverRequest;
});

return $di;
```

Core file
---------
Create file ```bootstrap/core.php``` with content:
```php
<?php

ini_set('display_errors', '1');

use Libertyphp\DependencyInjection\DiContainer;

const APP_SERVER_PATH     = __DIR__ . '/..';
const PUBLIC_SERVER_PATH  = __DIR__ . '/../public';

require __DIR__ . '/error_handler.php';
require __DIR__ . '/../vendor/autoload.php';

/** @var DiContainer $di */
$di = include(__DIR__ . '/di.php');

return $di;
```

Layout view
-----------
Create view file ```views/layout.php``` with content:
```php
<?php

use Libertyphp\Views\View;
use Libertyphp\Views\LayoutView;

/**
 * @var LayoutView $view
 */
?>
<html>
<head>
    <title><?= View::html($view->getTitle()) ?></title>
</head>
<body>
<?= $view->getContentView()->getRenderedContent() ?>
</body>
</html>
```

Index page
----------
Create view file ```views/index.php``` with content:
```php
<?php

use Libertyphp\Views\View;

/**
 * @var View $view
 * @var string $date
 */
?>
<h1>It's my first page</h1>
Today is <?= View::html($date) ?>
```

Controller with many actions is bad solution (violates the Single Responsibility Principle). Each action must be in separated class.
Create action controller file ```myapp/http/IndexActionController.php``` with content:

```php
<?php

namespace MyApp\Http;

use GuzzleHttp\Psr7\Response;
use Libertyphp\Http\ActionController;
use Libertyphp\Views\LayoutView;
use Libertyphp\Views\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexActionController extends ActionController
{
    public function execute(array $routeParams, ServerRequestInterface $request): ResponseInterface
    {
        $contentView = (new View())
            ->setViewPath(APP_SERVER_PATH . '/views/index.php')
            ->setRenderData(['date' => date('Y-m-d')]);
            
        $layoutView = (new LayoutView())
            ->setViewPath(APP_SERVER_PATH . '/views/layout.php')
            ->setTitle('My first page')
            ->setContentView($contentView);

        $body = $layoutView->render()->getRenderedContent();

        return new Response(200, [], $body);
    }
}
```

Router
------
Create file ```config/routes.php``` with content:
```php
<?php

use Libertyphp\Http\Router;
use MyApp\Http\IndexActionController;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $di */
$router = new Router($di);

$router->addRule('GET /', IndexActionController::class);

return $router;
```

Public script
-------------
Create public file ```public/index.php``` with content:

```php
<?php

use Libertyphp\Http\Router;
use Psr\Http\Message\ServerRequestInterface;

try {
    $di = include __DIR__ . '/../bootstrap/core.php';

    /** @var ServerRequestInterface $serverRequest */
    $serverRequest = $di->get('serverRequest');

    /** @var Router $router */
    $router = include __DIR__ . '/../config/routes.php';

    $router->respond($serverRequest);
    
} catch (Throwable $e) {
    // Error handler
    http_response_code(500);
    echo 'Something was wrong';
}
```

Nginx configuration
-------------------
Use this template for Nginx configuration:

```
server {
    listen 80;
    server_name localhost;
    
    root /var/www/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }
    
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Summary
-------
At the end there will be such a directory and file structure:
```text
|--bootstrap
    |--core.php
    |--di.php
    |--error_handler.php
|--config
    |--routes.php
|--myapp
    |--http
        |--IndexActionController.php
|--public
    |--index.php
|--views
    |--index.php
    |--layout.php
```

Now you can open the project in the browser.

Data Access Layer
=================
To work with relational databases, we recommend using [Liberty PHP Datamapper](https://github.com/vblila/libertyphp-datamapper)

Logging
=======
There is only one logger implementation available in the LibertyPHP - SimpleLogger (PSR LoggerInterface implementation). Append this code to ```bootstrap/di.php```:

```php
$di->singleton('appLog', function() {
    $stream    = Utils::streamFor(fopen(__DIR__ . '/../logs/app.log', 'a'));
    $requestId = Random::hex(4);
    
    return new SimpleLogger($stream, $requestId);
});
```
Logger usage example:
```php
/** @var SimpleLogger $appLog */
$appLog = $this->di->get('appLog');
$appLog->info('Index page request', ['query_params' => $request->getQueryParams()]);
```

Routing
=======
Route rules example to demonstrate the possibilities of routing:
```php
$router->addRule('GET /', IndexActionController::class);
$router->addRule('POST /checkout', CheckoutActionController::class);
$router->addRule('GET /orders/@status', OrdersActionController::class, [], null, ['status' => '/^(new|paid|finished)$/']);
$router->addRule('GET /orders/@id', OrdersActionController::class, [], null, ['id' => '/^[0-9]{1,}$/']);
$router->addRule('GET /news/@category/@sort', NewsActionController::class);
```

Use of middlewares (PSR MiddlewareInterface implementation) in routes is available:
```php
// Your custom middleware
$authMiddleware = new AuthMiddleware();

// Any request to this route will be processed by AuthMiddleware before execution of ProfileActionController
$router->addRule('GET /profile', ProfileActionController::class, [$authMiddleware]);
```
Views
=====

The easiest way to render view content is to use the class ```Content```. Example:
```php
$content = Content::render(APP_SERVER_PATH . '/views/user.php', ['user' => $user]);
```

Base view
---------
Instance of class ```View``` allows adding stylesheets and scripts files at render time.

Code example in controller:
```php
$view = (new View())
    ->setViewPath(APP_SERVER_PATH . '/views/orders.php')
    ->setRenderData(['orders' => $orders]);

$content = $view->render();
```

Code example in view:
```php
<?php

use Libertyphp\Views\View;

/**
 * @var View $view
 * @var array $orders
 */
 
$view->addJsFile('js/orders.js');
$view->addCssFile('css/orders.css');

?>
<div class="orders">
    <?php foreach ($orders as $order) { ?>
        <div><?= View::html($order->name) ?></div>
    <?php } ?>
</div>
```

Layout view
-----------
Instance of class ```LayoutView``` allows adding title, keywords and description. Rendering of layout view requires content view. First, the content view will be rendered, then the layout will be.

Code example in controller:
```php
$contentView = (new View())
    ->setViewPath(APP_SERVER_PATH . '/views/orders.php')
    ->setRenderData(['orders' => $orders]);
    
$layoutView = (new LayoutView())
    ->setViewPath(APP_SERVER_PATH . '/views/layout.php')
    ->setContentView($contentView)
    ->setTitle('My first page')
    ->setKeywords('first, page')
    ->setDescription('Layout view demonstration');

$body = $layoutView->render()->getRenderedContent();
```

Code example in layout view:
```php
<?php

use Libertyphp\Views\View;
use Libertyphp\Views\LayoutView;

/**
 * @var LayoutView $view
 */
 
?>
<html>
<head>
    <title><?= View::html($view->getTitle()) ?></title>
    <meta name="description" content="<?= View::html($view->getDescription()) ?>">
    <meta name="keywords" content="<?= View::html($view->getKeywords()) ?>">

    <?php foreach ($view->getCssLinks() as $link) { ?>
        <link rel="stylesheet" href="<?= View::html($view->getCachedAssetUrl($link, $_SERVER['DOCUMENT_ROOT'])) ?>">
    <?php } ?>
    
    <?php foreach ($view->getJsLinks() as $link) { ?>
        <script defer src="<?= View::html(View::getCachedAssetUrl($link, $_SERVER['DOCUMENT_ROOT'])) ?>"></script>
    <?php } ?>
</head>
<body>
<?= $view->getContentView()->getRenderedContent() ?>
</body>
</html>
```

Pagination
----------
The Liberty PHP allows to quickly create a classic pagination. Example code in controller:
```php
$pagination = new Pagination(count: 1500, currentPage: 2, pageLimit: 10, baseUrl: '/orders');
```

Example code in view:
```php
<?php

use Libertyphp\Views\Pagination;

/**
 * @var Pagination $paginator
 */
 
?>
<div class="pagination">
    <?php
    foreach ($paginator->getPages() as $page) {
        if ($page !== null) {
            ?>
            <a href="<?= $paginator->getPageUrl($page) ?>"
               class="page<?= $paginator->getCurrentPage() == $page ? ' active' :'' ?>"
            >
                <?= htmlspecialchars($page) ?>
            </a>
            <?php
        } else {
            ?><span class="page">...</span><?php
        }
    }
    ?>
</div>
```

Utils
=====

Randomizer
----------
The Liberty PHP allows generating cryptographically secure UUID v4. Example:
```php
$uuid = Random::uuidV4();
```
And cryptographically secure hex. Example:
```php
// Generate 20-bytes hex code
$hex = Random::hex(20);
```

List by key
-----------
It often happens that from a list of arrays or from an list of objects you need to create an associative array, in which the key will be the value of the array column or the property of the object. Simple way to do it:
```php

$users = [
    ['id' => 101, 'name' => 'Ivanov', 'age' => 18],
    ['id' => 102, 'name' => 'Petrov', 'age' => 22],
    ['id' => 103, 'name' => 'Sidorov', 'age' => 10],
];

$usersById = ListByKey::get('id', $users);

// Will be printed "Petrov"
echo $usersById[102]['name'];          
```

Copyright
=========
Copyright (c) 2018-2022 Vladimir Lila. See LICENSE for details.
