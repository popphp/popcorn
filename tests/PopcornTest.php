<?php

namespace PopcornTest;

use Composer\Autoload\ClassLoader;
use Pop\Event\Manager;
use Pop\Router\Router;
use Pop\Service\Locator;
use Popcorn\Pop;

class PopcornTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $app = new Pop();
        $this->assertInstanceOf('Popcorn\Pop', $app);
    }

    public function testConstructorRoutes()
    {
        $config = [
            'routes' => [
                'get' => [
                    '/' => function() {
                        header('HTTP/1.1 200 OK');
                        echo 'Hello World!';
                    }
                ],
                'post' => [
                    '/edit/:id' => function($id){
                        header('HTTP/1.1 200 OK');
                        echo 'Edit ' . $id . PHP_EOL;
                    }
                ],
                'get,post' => [
                    '/' => function() {
                        header('HTTP/1.1 200 OK');
                        echo 'Hello Get and Post!';
                    }
                ],
                '/something/else' => function() {
                    header('HTTP/1.1 200 OK');
                    echo 'Hello Something Else!';
                }
            ],
            'foo' => 'bar',
            'baz' => [
                1, 2, 3
            ]
        ];

        $app = new Pop($config);
        $app2 = new Pop($config, new Router());
        $app3 = new Pop($config, new Router(), new Locator());
        $app4 = new Pop($config, new Router(), new Locator(), new Manager());
        $app4 = new Pop($config, new Router(), new Locator(), new Manager(), new ClassLoader());

        $this->assertInstanceOf('Popcorn\Pop', $app);
        $this->assertTrue(isset($app->getRoute('get', '/')['controller']));
        $this->assertTrue(isset($app->getRoute('post', '/edit/:id')['controller']));
        $this->assertTrue($app->hasRoute('get', '/something/else'));
        $this->assertTrue($app->hasRoute('post', '/something/else'));
        $this->assertFalse($app->hasRoute('delete', '/something/else'));
        $this->assertEquals('bar', $app->config()['foo']);
    }

    public function testAddGetRoute()
    {
        $app = new Pop();
        $app->get('/home', [
            'controller' => function(){
                echo 'home';
            }]);

        $app->get('/hello', function(){
                echo 'hello';
            });
        $this->assertTrue($app->hasRoute('get', '/home'));
        $this->assertTrue($app->hasRoute('get', '/hello'));
    }

    public function testAddHeadRoute()
    {
        $app = new Pop();
        $app->head('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('head', '/home'));
    }

    public function testAddPostRoute()
    {
        $app = new Pop();
        $app->post('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('post', '/home'));
    }

    public function testAddPutRoute()
    {
        $app = new Pop();
        $app->put('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('put', '/home'));
    }

    public function testAddDeleteRoute()
    {
        $app = new Pop();
        $app->delete('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('delete', '/home'));
    }

    public function testAddTraceRoute()
    {
        $app = new Pop();
        $app->trace('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('trace', '/home'));
    }

    public function testAddOptionsRoute()
    {
        $app = new Pop();
        $app->options('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('options', '/home'));
    }

    public function testAddConnectRoute()
    {
        $app = new Pop();
        $app->connect('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('connect', '/home'));
    }

    public function testAddPatchRoute()
    {
        $app = new Pop();
        $app->patch('/home', [
            'controller' => function(){
                echo 'home';
            }]);
        $this->assertTrue($app->hasRoute('patch', '/home'));
    }

    public function testAddRouteException()
    {
        $this->expectException('Popcorn\Exception');
        $app = new Pop();
        $app->setRoute('bad', '/home', [
            'controller' => function(){
                echo 'home';
            }]);
    }

    public function testAddRoutes()
    {
        $app = new Pop();
        $app->setRoutes('get,post', '/home', ['controller' => function(){
            echo 'home';
        }]);
        $this->assertTrue($app->hasRoute('get', '/home'));
        $this->assertTrue($app->hasRoute('post', '/home'));
    }

    public function testAddRoutesException()
    {
        $this->expectException('Popcorn\Exception');
        $app = new Pop();
        $app->setRoutes(new \StdClass, '/home', ['controller' => function(){
            echo 'home';
        }]);
    }

    public function testGetRoute()
    {
        $app = new Pop();
        $app->setRoutes('get,post', '/home', ['controller' => function () {
            echo 'home';
        }]);
        $this->assertTrue(isset($app->getRoute('get', '/home')['controller']));
    }

    public function testGetRoutes()
    {
        $app = new Pop();
        $app->setRoutes('get,post', '/home', ['controller' => function () {
            echo 'home';
        }]);
        $get = $app->getRoutes('get');
        $this->assertTrue(isset($get['/home']));
    }

    public function testGetRoutesException()
    {
        $this->expectException('Popcorn\Exception');
        $app = new Pop();
        $app->setRoutes('get,post', '/home', ['controller' => function () {
            echo 'home';
        }]);
        $get = $app->getRoutes('bad');
    }

    public function testIsAllowed()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $app = new Pop();
        $app->get('/home', [
            'controller' => function(){
                echo '/home';
            }]);
        $this->assertTrue($app->isAllowed('/home'));
    }

    public function testRun()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = 'home';
        $app = new Pop();
        $app->get('home', [
            'controller' => function(){
                echo 'home';
            }]);

        ob_start();
        $app->run(false);
        $result = ob_get_clean();
        $this->assertFalse(ctype_print($result));
    }

}
