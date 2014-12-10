<?php
/**
 * Popcorn Micro-Framework (http://popcorn.popphp.org/)
 *
 * @link       https://github.com/nicksagona/Popcorn
 * @category   Popcorn
 * @package    Popcorn
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://popcorn.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Popcorn;

use Pop\Application;

/**
 * This is the main class for the Popcorn Micro-Framework.
 *
 * @category   Popcorn
 * @package    Popcorn
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://popcorn.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
class Pop extends Application
{

    /**
     * Current version
     */
    const VERSION = '2.0.0a';

    /**
     * Routes array
     * @var array
     */
    protected $routes = [
        'get'     => [],
        'head'    => [],
        'post'    => [],
        'put'     => [],
        'delete'  => [],
        'trace'   => [],
        'options' => [],
        'connect' => [],
        'patch'   => []
    ];

    /**
     * Add a GET route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function get($route, array $controller)
    {
        return $this->addRoute('get', $route, $controller);
    }

    /**
     * Add a HEAD route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function head($route, array $controller)
    {
        return $this->addRoute('head', $route, $controller);
    }

    /**
     * Add a POST route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function post($route, array $controller)
    {
        return $this->addRoute('post', $route, $controller);
    }

    /**
     * Add a PUT route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function put($route, array $controller)
    {
        return $this->addRoute('put', $route, $controller);
    }

    /**
     * Add a DELETE route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function delete($route, array $controller)
    {
        return $this->addRoute('delete', $route, $controller);
    }

    /**
     * Add a TRACE route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function trace($route, array $controller)
    {
        return $this->addRoute('trace', $route, $controller);
    }

    /**
     * Add an OPTIONS route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function options($route, array $controller)
    {
        return $this->addRoute('options', $route, $controller);
    }

    /**
     * Add a CONNECT route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function connect($route, array $controller)
    {
        return $this->addRoute('connect', $route,  $controller);
    }

    /**
     * Add a PATCH route
     *
     * @param  string $route
     * @param  array  $controller
     * @return Pop
     */
    public function patch($route, array $controller)
    {
        return $this->addRoute('patch', $route, $controller);
    }

    /**
     * Add a route
     *
     * @param  string $method
     * @param  string $route
     * @param  array  $controller
     * @throws Exception
     * @return Pop
     */
    public function addRoute($method, $route, array $controller)
    {
        if (!array_key_exists(strtolower($method), $this->routes)) {
            throw new Exception('Error: That method is not allowed.');
        }

        $this->routes[$method][$route] = $controller;

        return $this;
    }

    /**
     * Add multiple routes
     *
     * @param  array|string $methods
     * @param  string       $route
     * @param  array        $controller
     * @throws Exception
     * @return Pop
     */
    public function addRoutes($methods, $route, array $controller)
    {
        if (is_string($methods)) {
            $methods = explode(',', str_replace(', ', ',', strtolower($methods)));
        }

        if (!is_array($methods)) {
            throw new Exception('Error: The $methods parameter must be either an array or a comma-delimited string.');
        }

        foreach ($methods as $method) {
            $this->addRoute($method, $route, $controller);
        }
        return $this;
    }

    /**
     * Method to get a route
     *
     * @param  string $method
     * @throws Exception
     * @return array
     */
    public function getRoute($method)
    {
        if (!array_key_exists(strtolower($method), $this->routes)) {
            throw new Exception('Error: That method is not allowed.');
        }
        return $this->routes[$method];
    }

    /**
     * Method to get all routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Run the application.
     *
     * @return void
     */
    public function run()
    {
        // If route is allowed for this method
        $this->router->addRoutes($this->routes[strtolower($_SERVER['REQUEST_METHOD'])]);
        if ($this->router->hasRoute() && $this->isAllowed($this->router->getRouteMatch()->getRoute())) {
            parent::run();
        } else {
            $this->trigger('app.error', [
                'exception' => new Exception(
                    'Error: That route was not ' . (($this->router->hasRoute()) ? 'allowed' : 'found') . '.'
                )
            ]);
        }
    }

    /**
     * Compares the local version to the latest version available
     *
     * @param  string $version
     * @return mixed
     */
    public static function compareVersion($version)
    {
        return version_compare($version, self::VERSION);
    }

    /**
     * Returns the latest version available.
     *
     * @return mixed
     */
    public static function getLatest()
    {
        $latest = null;

        $handle = fopen('http://popcorn.popphp.org/version', 'r');
        if ($handle !== false) {
            $latest = stream_get_contents($handle);
            fclose($handle);
        }

        return trim($latest);
    }

    /**
     * Returns whether or not this is the latest version.
     *
     * @return mixed
     */
    public static function isLatest()
    {
        return (self::compareVersion(self::getLatest()) < 1);
    }

    /**
     * Determine if the route is allowed on for the method
     *
     * @param  string $route
     * @return boolean
     */
    protected function isAllowed($route)
    {
        $allowed = false;
        $method  = strtolower($_SERVER['REQUEST_METHOD']);

        foreach ($this->routes[$method] as $r => $c) {
            if (substr($r, 0, strlen($route)) == $route) {
                $allowed = true;
            }
        }

        return $allowed;
    }

}
