<?php
/**
 * Popcorn Micro-Framework (http://popcorn.popphp.org/)
 *
 * @link       https://github.com/popphp/popcorn
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://popcorn.popphp.org/license     New BSD License
 * @version    3.1.6
 */
class Pop extends Application
{

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
     * Constructor
     *
     * Instantiate an application object
     *
     * Optional parameters are a service locator instance, a router instance,
     * an event manager instance or a configuration object or array
     */
    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $i => $arg) {
            if (is_array($arg) && isset($arg['routes'])) {
                // Check for combined route matches
                foreach ($arg['routes'] as $key => $value) {
                    if ($key == '*') {
                        $this->addToAll($key, $value);
                        unset($arg['routes'][$key]);
                    } else if (strpos($key, ',') !== false) {
                        foreach ($arg['routes'][$key] as $route => $controller) {
                            $this->setRoutes($key, $route, $controller);
                        }
                        unset($arg['routes'][$key]);
                    }
                }

                // Check for direct route method matches
                $routeKeys = array_keys($this->routes);
                foreach ($routeKeys as $key) {
                    if (isset($arg['routes'][$key])) {
                        foreach ($arg['routes'][$key] as $route => $controller) {
                            $this->setRoute($key, $route, $controller);
                        }
                        unset($arg['routes'][$key]);
                    }
                }

                // Check for static routes that are not assigned to a method,
                // and auto-assign them to get,post for a fallback
                if (count($arg['routes']) > 0) {
                    foreach ($arg['routes'] as $route => $controller) {
                        $this->setRoutes('get,post', $route, $controller);
                    }
                }

                unset($args[$i]['routes']);
            }
        }

        switch (count($args)) {
            case 1:
                parent::__construct($args[0]);
                break;
            case 2:
                parent::__construct($args[0], $args[1]);
                break;
            case 3:
                parent::__construct($args[0], $args[1], $args[2]);
                break;
            case 4:
                parent::__construct($args[0], $args[1], $args[2], $args[3]);
                break;
            case 5:
                parent::__construct($args[0], $args[1], $args[2], $args[3], $args[4]);
                break;
            case 6:
                parent::__construct($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                break;
            default:
                parent::__construct();
        }

    }

    /**
     * Add a GET route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function get($route, $controller)
    {
        return $this->setRoute('get', $route, $controller);
    }

    /**
     * Add a HEAD route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function head($route, $controller)
    {
        return $this->setRoute('head', $route, $controller);
    }

    /**
     * Add a POST route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function post($route, $controller)
    {
        return $this->setRoute('post', $route, $controller);
    }

    /**
     * Add a PUT route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function put($route, $controller)
    {
        return $this->setRoute('put', $route, $controller);
    }

    /**
     * Add a DELETE route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function delete($route, $controller)
    {
        return $this->setRoute('delete', $route, $controller);
    }

    /**
     * Add a TRACE route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function trace($route, $controller)
    {
        return $this->setRoute('trace', $route, $controller);
    }

    /**
     * Add an OPTIONS route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function options($route, $controller)
    {
        return $this->setRoute('options', $route, $controller);
    }

    /**
     * Add a CONNECT route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function connect($route, $controller)
    {
        return $this->setRoute('connect', $route,  $controller);
    }

    /**
     * Add a PATCH route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function patch($route, $controller)
    {
        return $this->setRoute('patch', $route, $controller);
    }

    /**
     * Add a route
     *
     * @param  string $method
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function setRoute($method, $route, $controller)
    {
        if (!array_key_exists(strtolower($method), $this->routes)) {
            throw new Exception('Error: That method is not allowed.');
        }

        if (is_callable($controller)) {
            $controller = ['controller' => $controller];
        }

        if (isset($this->routes[$method][$route]) && is_array($this->routes[$method][$route])) {
            $this->routes[$method][$route] = array_merge($this->routes[$method][$route], $controller);
        } else {
            $this->routes[$method][$route] = $controller;
        }

        return $this;
    }

    /**
     * Add multiple routes
     *
     * @param  array|string $methods
     * @param  string       $route
     * @param  mixed        $controller
     * @throws Exception
     * @return Pop
     */
    public function setRoutes($methods, $route, $controller)
    {
        if (is_string($methods)) {
            $methods = explode(',', str_replace(', ', ',', strtolower($methods)));
        }

        if (!is_array($methods)) {
            throw new Exception('Error: The $methods parameter must be either an array or a comma-delimited string.');
        }

        foreach ($methods as $method) {
            $this->setRoute($method, $route, $controller);
        }
        return $this;
    }


    /**
     * Add to all methods
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function addToAll($route, $controller)
    {
        foreach ($this->routes as $method => $value) {
            $this->setRoute($method, $route, $controller);
        }
        return $this;
    }

    /**
     * Method to get all routes
     *
     * @param  string $method
     * @throws Exception
     * @return array
     */
    public function getRoutes($method = null)
    {
        if ((null !== $method) && !array_key_exists(strtolower($method), $this->routes)) {
            throw new Exception('Error: That method is not allowed.');
        }
        return (null !== $method) ? $this->routes[$method] : $this->routes;
    }

    /**
     * Method to get a route by method
     *
     * @param  string $method
     * @param  string $route
     * @return mixed
     */
    public function getRoute($method, $route)
    {
        return ($this->hasRoute($method, $route)) ? $this->routes[$method][$route] : null;
    }

    /**
     * Method to determine if the application has a route
     *
     * @param  string $method
     * @param  string $route
     * @return boolean
     */
    public function hasRoute($method, $route)
    {
        return (isset($this->routes[$method]) && isset($this->routes[$method][$route]));
    }

    /**
     * Determine if the route is allowed on for the method
     *
     * @param  string $route
     * @return boolean
     */
    public function isAllowed($route)
    {
        $allowed = false;
        $method  = strtolower($_SERVER['REQUEST_METHOD']);

        foreach ($this->routes[$method] as $rte => $ctrl) {
            if (is_array($ctrl) && !isset($ctrl['controller'])) {
                foreach ($ctrl as $r => $c) {
                    if (substr($rte . $r, 0, strlen($route)) == $route) {
                        $allowed = true;
                        break;
                    }
                }
            } else if (substr($rte, 0, strlen($route)) == $route) {
                $allowed = true;
                break;
            }
        }

        return $allowed;
    }

    /**
     * Run the application.
     *
     * @param  boolean $exit
     * @return void
     */
    public function run($exit = true)
    {
        // If route is allowed for this method
        $this->router->addRoutes($this->routes[strtolower($_SERVER['REQUEST_METHOD'])]);
        $this->router->route();

        if ($this->router->hasRoute() && $this->isAllowed($this->router->getRouteMatch()->getOriginalRoute())) {
            parent::run($exit);
        } else {
            $this->trigger('app.error', [
                'exception' => new Exception(
                    'Error: That route was not ' . (($this->router->hasRoute()) ? 'allowed' : 'found') . '.'
                )
            ]);
            $this->router->getRouteMatch()->noRouteFound((bool)$exit);
        }
    }

}
