<?php

namespace PopcornTest;

use Popcorn\Pop;

class PopcornTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $app = new Pop();
        $this->assertInstanceOf('Popcorn\Pop', $app);
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
        $this->setExpectedException('Popcorn\Exception');
        $app = new Pop();
        $app->addRoute('bad', '/home', [
            'controller' => function(){
                echo 'home';
            }]);
    }

    public function testAddRoutes()
    {
        $app = new Pop();
        $app->addRoutes('get,post', '/home', ['controller' => function(){
            echo 'home';
        }]);
        $this->assertTrue($app->hasRoute('get', '/home'));
        $this->assertTrue($app->hasRoute('post', '/home'));
    }

    public function testAddRoutesException()
    {
        $this->setExpectedException('Popcorn\Exception');
        $app = new Pop();
        $app->addRoutes(new \StdClass, '/home', ['controller' => function(){
            echo 'home';
        }]);
    }

    public function testGetRoute()
    {
        $app = new Pop();
        $app->addRoutes('get,post', '/home', ['controller' => function () {
            echo 'home';
        }]);
        $this->assertTrue(isset($app->getRoute('get', '/home')['controller']));
    }

    public function testGetRoutes()
    {
        $app = new Pop();
        $app->addRoutes('get,post', '/home', ['controller' => function () {
            echo 'home';
        }]);
        $get = $app->getRoutes('get');
        $this->assertTrue(isset($get['/home']));
    }

    public function testGetRoutesException()
    {
        $this->setExpectedException('Popcorn\Exception');
        $app = new Pop();
        $app->addRoutes('get,post', '/home', ['controller' => function () {
            echo 'home';
        }]);
        $get = $app->getRoutes('bad');
    }

    public function testCompareVersion()
    {
        $this->assertEquals(1, Pop::compareVersion(1.0));
    }

    public function testGetLatest()
    {
        $this->assertEquals('2.0.0', Pop::getLatest());
    }

    public function testIsLatest()
    {
        $this->assertTrue(Pop::isLatest());
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
        $app->run();
    }

}
