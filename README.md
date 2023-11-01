Popcorn PHP Micro Framework
===========================

<img src="http://www.popphp.org/assets/img/popcorn-logo-shadow.png" width="180" height="180" />

[![Build Status](https://github.com/popphp/popcorn/workflows/phpunit/badge.svg)](https://github.com/popphp/popcorn/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=popcorn)](http://cc.popphp.org/popcorn/)

[![Join the chat at https://popphp.slack.com](https://media.popphp.org/img/slack.svg)](https://popphp.slack.com)
[![Join the chat at https://discord.gg/D9JBxPa5](https://media.popphp.org/img/discord.svg)](https://discord.gg/D9JBxPa5)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Advanced](#advanced)
* [Custom Methods](#custom-methods)

RELEASE INFORMATION
-------------------

Popcorn PHP REST-Based Micro Framework 4.0.0  
Released October 16, 2023 

Overview
--------

Popcorn PHP Micro Framework is a REST-based micro framework. It is a small component
that acts as a layer for [Pop PHP](https://github.com/popphp/popphp) to enforce the REST-based routing rules of a
web application. It supports PHP 8.1+.

`popcorn` is a component of [Pop PHP Framework](http://www.popphp.org/).

[Top](#popcorn-php-micro-framework)

Install
-------

Install `popcorn` using Composer.

    composer require popphp/popcorn

Or, require it in your composer.json file

    "require": {
        "popphp/popcorn" : "^4.0.0"
    }

[Top](#popcorn-php-micro-framework)

Quickstart
----------

In a simple `index.php` file, you can define the routes you want to allow
in your application. In this example, simple closures are used as the
controllers. The wildcard route '*' can serve as a "catch-all" to handle
routes that are not found or not allowed.

```php
use Popcorn\Pop;

$app = new Pop();

// Home page: GET http://localhost/
$app->get('/', function() {
    echo 'Hello World!';
});

// Say hello page: GET http://localhost/hello/world
$app->get('/hello/:name', function($name) {
    echo 'Hello ' . ucfirst($name) . '!';
});

// Wildcard route to handle errors
$app->get('*', function() {
    header('HTTP/1.1 404 Not Found');
    echo 'Page Not Found.';
});
```

The above example defines two `GET` routes and wildcard to handle failures.

We can define a `POST` route like in this example below:

```php
// Post auth route: POST http://localhost/auth
$app->post('/auth', function() {
    if ($_SERVER['HTTP_AUTHORIZATION'] == 'my-token') {
        echo 'Auth successful';
    } else {
        echo 'Auth failed';
    }
});

$app->run();
```

If you attempted access that above URL via GET (or any method that wasn't POST),
it would fail. If you access that URL via POST, but with the wrong application
token, it will return the `Auth failed` message as enforced by the application.
Access the URL via POST with the correct application token, and it will be successful.

```bash
$ curl -X POST --header "Authorization: bad-token" http://localhost/auth
  Auth failed
```

```bash
$ curl -X POST --header "Authorization: my-token" http://localhost/auth
  Auth successful
```

[Top](#popcorn-php-micro-framework)

Advanced
--------

In a more advanced example, we can take advantage of more of an MVC-style
of wiring up an application using the core components of Pop PHP with
Popcorn. Keeping it simple, let's look at a controller class
`MyApp\Controller\IndexController` like this:

```php
<?php

namespace MyApp\Controller;

use Pop\Controller\AbstractController;
use Pop\Http\Server\Request;
use Pop\Http\Server\Response;
use Pop\View\View;

class IndexController extends AbstractController
{

    protected $response;
    protected $viewPath;

    public function __construct(Request $request = new Request(), Response $response = new Response())
    {
        $this->request  = $request;
        $this->response = $response;
        $this->viewPath = __DIR__ . '/../view/';
    }

    public function index()
    {
        $view = new View($this->viewPath . '/index.phtml');
        $view->title = 'Welcome';

        $this->response->setBody($view->render());
        $this->response->send();
    }

    public function error()
    {
        $view = new View($this->viewPath . '/error.phtml');
        $view->title =  'Error';

        $this->response->setBody($view->render());
        $this->response->send(404);
    }

}
```

and two view scripts, `index.phtml` and `error.phtml`, respectively:

```php
<!DOCTYPE html>
<!-- index.phtml //-->
<html>

<head>
    <title><?=$title; ?></title>
</head>

<body>
    <h1><?=$title; ?></h1>
    <p>Hello World.</p>
</body>

</html>
```

```php
<!DOCTYPE html>
<!-- error.phtml //-->
<html>

<head>
    <title><?=$title; ?></title>
</head>

<body>
    <h1 style="color: #f00;"><?=$title; ?></h1>
    <p>Sorry, that page was not found.</p>
</body>

</html>
```

Then we can set the app like this:

```php
use Popcorn\Pop;

$app = new Pop();

$app->get('/', [
    'controller' => 'MyApp\Controller\IndexController',
    'action'     => 'index',
    'default'    => true
]);


$app->run();
```

The 'default' parameter sets the controller as the default controller
to handle routes that aren't found. Typically, there is a default action
such as an 'error' method to handle this.

[Top](#popcorn-php-micro-framework)

Custom Methods
--------------

If your web server allows for you to configure custom HTTP methods, Popcorn
supports that and allows you to register custom HTTP methods with the application.

```php
use Popcorn\Pop;

$app = new Pop();
$app->addCustomMethod('PURGE')
    ->addCustomMethod('COPY');

$app->purge('/image/:id', function(){
    // Do something with the PURGE method on the image URL
});

$app->copy('/image/:id', function(){
    // Do something with the COPY method on the image URL
});

$app->run();
```

Then you can submit requests with your custom HTTP methods like this:

```bash
$ curl -X PURGE http://localhost/image/1
```
```bash
$ curl -X COPY http://localhost/image/1
```

[Top](#popcorn-php-micro-framework)
