Popcorn PHP Micro Framework
===========================

[![Build Status](https://travis-ci.org/popphp/popcorn.svg?branch=master)](https://travis-ci.org/popphp/popcorn)

RELEASE INFORMATION
-------------------
Popcorn PHP REST-Based Micro Framework 2.0.0  
Release July 11, 2015

OVERVIEW
--------
Popcorn PHP Micro Framework is a REST-based micro framework. The biggest changes
in version 2.0.0 is that it supports 5.4+ only and has been stripped down to the
least amount of internal dependencies as possible. Going beyond that, Popcorn 2.0.0
supports PSR-4 autoloading and it now integrated with Composer, much like the version
of [Pop PHP Framework](http://www.popphp.org/) 2.0.0.

Because of these changes, and the changes to the Pop PHP Framework on which it is
built, it actually makes Popcorn much more streamlined. Before, Popcorn was really
it's own self-contained thing, borrowing and including components from the previous
version of the Pop PHP Framework. Now, Popcorn is simply built on top of the new
version of the core component of [Pop PHP](https://github.com/popphp/popphp) and
acts as a layer to enforce the REST-based routing rules of a web application.

INSTALL
-------

Install `popcorn` using Composer.

    composer require popphp/popcorn

BASIC USAGE
-----------

In a simple index.php file, you can define the routes you want to allow
in your application. In this example, we'll use simple closures as our
controllers. The wildcard route '*' can serve as a "catch-all" to handle
routes that are not found or not allowed.

```php
use Popcorn\Pop;

$app = new Pop();

// Home page http://localhost:8000/
$app->get('/', [
    'controller' => function() {
        header('HTTP/1.1 200 OK');
        echo 'Hello World!';
    }
]);

// Say hello page: http://localhost:8000/hello/nick
$app->get('/hello/:name', [
    'controller' => function($name) {
        header('HTTP/1.1 200 OK');
        echo 'Hello ' . ucfirst($name) . '!';
    }
]);

// Wildcard route to handle errors
$app->get('*', [
    'controller' => function() {
        header('HTTP/1.1 404 Not Found');
        echo 'Page Not Found.';
    }
]);

// Post route to process a login
$app->post('/login', [
    'controller' => function() {
        header('HTTP/1.1 200 OK');
        if ($_POST['token'] == 'mytoken') {
            echo 'Login Successful.';
        } else {
            echo 'Login Failed.';
        }
    }
]);

$app->run();
```

In the above POST example, if you attempted access that URL via GET
(or any method that wasn't POST), it would fail. If you access that URL
via POST, but with the wrong application token, it will return the
'Login Failed' message. Access the URL with the correct application
toke, and it will be successful:

    curl -X POST -dtoken=badtoken http://localhost:8000/login
    Login Failed.

    curl -X POST -dtoken=mytoken http://localhost:8000/login
    Login Successful.

ADVANCED USAGE
--------------

In a more advanced example, we can take advantage of more of an MVC-style
of wiring up an application using Popcorn. Keeping it simple, let's look
at a controller class like this:

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

and two view scripts, 'index.phtml' and 'error.phtml', respectively:

```php
<!DOCTYPE html>
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

// The 'default' parameter sets this controller as the default controller
// to handle routes that aren't found. Typically, there is a default action
// such as an 'error' method to handle this.
$app->get('/', [
    'controller' => 'MyApp\Controller\IndexController',
    'action'     => 'index',
    'default'    => true
]);


$app->run();
```


