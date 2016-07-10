<?php
/**
 * Popcorn Micro-Framework (http://popcorn.popphp.org/)
 *
 * @link       https://github.com/popphp/popcorn
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://popcorn.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Pop extends Application
{

    /**
     * Current version
     */
    const VERSION = '3.0.0';

    /**
     * Version source from GitHub
     */
    const VERSION_SOURCE_GITHUB = 'GITHUB';

    /**
     * Version source from popcorn.popphp.org
     */
    const VERSION_SOURCE_POP = 'POP';

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
     *
     * @return Pop
     */
    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $i => $arg) {
            if (is_array($arg) && isset($arg['routes'])) {
                // Check for combined route matches
                foreach ($arg['routes'] as $key => $value) {
                    if (strpos($key, ',') !== false) {
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

                if (count($arg['routes']) == 0) {
                    unset($args[$i]['routes']);
                }
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

        $this->routes[$method][$route] = $controller;

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

        foreach ($this->routes[$method] as $r => $c) {
            if (substr($r, 0, strlen($route)) == $route) {
                $allowed = true;
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
        $this->router->match();

        if ($this->router->hasRoute() && $this->isAllowed($this->router->getRouteMatch()->getRoute())) {
            parent::run();
        } else {
            $this->trigger('app.error', [
                'exception' => new Exception(
                    'Error: That route was not ' . (($this->router->hasRoute()) ? 'allowed' : 'found') . '.'
                )
            ]);
            $this->router->getRouteMatch()->noRouteFound((bool)$exit);
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
        return version_compare(self::VERSION, $version);
    }

    /**
     * Returns the latest version available.
     *
     * @param  string $source
     * @return mixed
     */
    public static function getLatest($source = 'POP')
    {
        return ($source == self::VERSION_SOURCE_GITHUB) ? self::getLatestFromGitHub() : self::getLatestFromPop();
    }

    /**
     * Returns the latest version available from GitHub.
     *
     * @return mixed
     */
    public static function getLatestFromGitHub()
    {
        $latest = null;

        $context = stream_context_create([
            'http' => [
                //'user_agent' => sprintf('Popcorn-Version/%s', self::VERSION),
                'user_agent' => 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:40.0) Gecko/20100101 Firefox/40.0'
            ],
        ]);
        $json   = json_decode(
            file_get_contents('https://api.github.com/repos/popphp/popcorn/releases/latest', false, $context), true
        );
        $latest = $json['tag_name'];

        return trim($latest);
    }

    /**
     * Returns the latest version available from www.popphp.org.
     *
     * @return mixed
     */
    public static function getLatestFromPop()
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
     * @param  string $source
     * @return mixed
     */
    public static function isLatest($source = 'POP')
    {
        return (self::compareVersion(self::getLatest($source)) >= 0);
    }

}
