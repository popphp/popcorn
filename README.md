Popcorn PHP Micro Framework
===========================

<img src="http://www.popphp.org/assets/img/popcorn-logo-shadow.png" width="180" height="180" />

[![Build Status](https://travis-ci.org/popphp/popcorn.svg?branch=master)](https://travis-ci.org/popphp/popcorn)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=popcorn)](http://cc.popphp.org/popcorn/)

RELEASE INFORMATION
-------------------
Popcorn PHP REST-Based Micro Framework 3.3.0  
Released October 29, 2019

OVERVIEW
--------
Popcorn PHP Micro Framework is a REST-based micro framework.  It is a component of
[Pop PHP Framework](http://www.popphp.org/). It is a small component that acts as
a layer for [Pop PHP](https://github.com/popphp/popphp) to enforce the REST-based
routing rules of a web application. It supports PHP 7.1+.

INSTALL
-------
Install `popcorn` using Composer.

    composer require popphp/popcorn

BASIC USAGE
-----------
In a simple `index.php` file, you can define the routes you want to allow
in your application. In this example, a simple closures is used as the
controllers. The wildcard route '*' can serve as a "catch-all" to handle
routes that are not found or not allowed.

```php
use Popcorn\Pop;

$app = new Pop();

// Home page: http://localhost:8000/
$app->get('/', function() {
    echo 'Hello World!';
});

// Say hello page: http://localhost:8000/hello/world
$app->get('/hello/:name', function($name) {
    echo 'Hello ' . ucfirst($name) . '!';
});

// Wildcard route to handle errors
$app->get('*', function() {
    header('HTTP/1.1 404 Not Found');
    echo 'Page Not Found.';
});

// Post route to process an auth request
$app->post('/auth', function() {
    if ($_SERVER['HTTP_AUTHORIZATION'] == 'my-token') {
        echo 'Auth successful';
    } else {
        echo 'Auth failed';
    }
});

$app->run();
```

In the above POST example, if you attempted access that URL via GET
(or any method that wasn't POST), it would fail. If you access that URL
via POST, but with the wrong application token, it will return the
'Auth failed' message as enforced by the application. Access the URL
via POST with the correct application token, and it will be successful:

    curl -X POST --header "Authorization: bad-token" http://localhost:8000/auth
    Auth failed

    curl -X POST --header "Authorization: my-token" http://localhost:8000/auth
    Auth successful

ADVANCED USAGE
--------------

In a more advanced example, we can take advantage of more of an MVC-style
of wiring up an application using the core components of Pop PHP with
Popcorn. Keeping it simple, let's look at a controller class
`MyApp\Controller\IndexController` like this:

```php
<?php

namespace MyApp\Controller;

use Pop\Controller\AbstractController;
use Pop\Http\Request;
use Pop\Http\Response;
use Pop\View\View;

class IndexController extends AbstractController
{

    protected $response;
    protected $viewPath;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
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

CUSTOM METHODS
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

    curl -X PURGE http://localhost:8000/image/1

    curl -X COPY http://localhost:8000/image/1

