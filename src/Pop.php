<?php
/**
 * Popcorn Micro-Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popcorn
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
class Pop extends Application
{

    /**
     * Routes array
     * @var array
     */
    protected array $routes = [
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
     * @throws Exception
     */
    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $i => $arg) {
            if (is_array($arg)) {
                // Handle custom methods config
                if (isset($arg['custom_methods'])) {
                    if (is_array($arg['custom_methods'])) {
                        $this->addCustomMethods($arg['custom_methods']);
                    } else {
                        $this->addCustomMethod($arg['custom_methods']);
                    }
                    unset($args[$i]['custom_methods']);
                }

                // Handle routes config
                if (isset($arg['routes'])) {
                    // Check for combined route matches
                    foreach ($arg['routes'] as $key => $value) {
                        // Handle route wildcard
                        if ($key == '*') {
                            foreach ($arg['routes'][$key] as $route => $controller) {
                                $this->addToAll($route, $controller);
                            }
                            unset($arg['routes'][$key]);
                        // Handle multiple route methods
                        } else if (str_contains((string)$key, ',')) {
                            foreach ($arg['routes'][$key] as $route => $controller) {
                                $this->setRoutes($key, $route, $controller);
                            }
                            unset($arg['routes'][$key]);
                        // Handle route prefixes
                        } else if (str_starts_with($key, '/') && is_array($value)) {
                            foreach ($value as $methods => $methodRoutes) {
                                foreach ($methodRoutes as $route => $controller) {
                                    $this->setRoutes($methods, $key . $route, $controller);
                                }
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
     * @param  mixed $controller
     * @throws Exception
     * @return Pop
     */
    public function get(string $route, mixed $controller): Pop
    {
        return $this->setRoute('get', $route, $controller);
    }

    /**
     * Add a HEAD route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function head(string $route, mixed $controller): Pop
    {
        return $this->setRoute('head', $route, $controller);
    }

    /**
     * Add a POST route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function post(string $route, mixed $controller): Pop
    {
        return $this->setRoute('post', $route, $controller);
    }

    /**
     * Add a PUT route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function put(string $route, mixed $controller): Pop
    {
        return $this->setRoute('put', $route, $controller);
    }

    /**
     * Add a DELETE route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function delete(string $route, mixed $controller): Pop
    {
        return $this->setRoute('delete', $route, $controller);
    }

    /**
     * Add a TRACE route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function trace(string $route, mixed $controller): Pop
    {
        return $this->setRoute('trace', $route, $controller);
    }

    /**
     * Add an OPTIONS route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function options(string $route, mixed $controller): Pop
    {
        return $this->setRoute('options', $route, $controller);
    }

    /**
     * Add a CONNECT route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function connect(string $route, mixed $controller): Pop
    {
        return $this->setRoute('connect', $route,  $controller);
    }

    /**
     * Add a PATCH route
     *
     * @param  string $route
     * @param  mixed  $controller
     * @throws Exception
     * @return Pop
     */
    public function patch(string $route, mixed $controller): Pop
    {
        return $this->setRoute('patch', $route, $controller);
    }

    /**
     * Add to any and all methods (alias method to addToAll)
     *
     * @param  string $route
     * @param  mixed  $controller
     * @return Pop
     */
    public function any(string $route, mixed $controller): Pop
    {
        return $this->addToAll($route, $controller);
    }

    /**
     * Add a custom method
     *
     * @param  string $customMethod
     * @return Pop
     */
    public function addCustomMethod(string $customMethod): Pop
    {
        $this->routes[strtolower($customMethod)] = [];
        return $this;
    }

    /**
     * Add custom methods
     *
     * @param  array $customMethods
     * @return Pop
     */
    public function addCustomMethods(array $customMethods): Pop
    {
        foreach ($customMethods as $customMethod) {
            $this->addCustomMethod($customMethod);
        }
        return $this;
    }

    /**
     * Has a custom method
     *
     * @param  string $customMethod
     * @return bool
     */
    public function hasCustomMethod(string $customMethod): bool
    {
        return isset($this->routes[strtolower($customMethod)]);
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
    public function setRoute(string $method, string $route, mixed $controller): Pop
    {
        if (!array_key_exists(strtolower((string)$method), $this->routes)) {
            throw new Exception("Error: The method '" . $method . "' is not allowed.");
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
    public function setRoutes(array|string $methods, string $route, mixed $controller): Pop
    {
        if (is_string($methods)) {
            $methods = array_map('trim', explode(',', strtolower($methods)));
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
     * @throws Exception
     * @return Pop
     */
    public function addToAll(string $route, mixed $controller): Pop
    {
        foreach ($this->routes as $method => $value) {
            $this->setRoute($method, $route, $controller);
        }
        return $this;
    }

    /**
     * Method to get all routes
     *
     * @param  ?string $method
     * @throws Exception
     * @return array
     */
    public function getRoutes(?string $method = null): array
    {
        if (($method !== null) && !array_key_exists(strtolower($method), $this->routes)) {
            throw new Exception("Error: The method '" . strtoupper($method) . "' is not allowed.");
        }
        return ($method !== null) ? $this->routes[$method] : $this->routes;
    }

    /**
     * Method to get a route by method
     *
     * @param  string $method
     * @param  string $route
     * @return mixed
     */
    public function getRoute(string $method, string $route): mixed
    {
        return ($this->hasRoute($method, $route)) ? $this->routes[$method][$route] : null;
    }

    /**
     * Method to determine if the application has a route
     *
     * @param  string $method
     * @param  string $route
     * @return bool
     */
    public function hasRoute(string $method, string $route): bool
    {
        return (isset($this->routes[$method]) && isset($this->routes[$method][$route]));
    }

    /**
     * Determine if the route is allowed on for the method
     *
     * @param  ?string $route
     * @return bool
     */
    public function isAllowed(?string $route = null): bool
    {
        $allowed = false;
        $method  = strtolower($_SERVER['REQUEST_METHOD']);
        $route   = (string)$route;

        foreach ($this->routes[$method] as $rte => $ctrl) {
            if (is_array($ctrl) && !isset($ctrl['controller'])) {
                foreach ($ctrl as $r => $c) {
                    if (str_starts_with($rte . $r, $route)) {
                        $allowed = true;
                        break;
                    }
                }
            } else if (str_starts_with($rte, $route)) {
                $allowed = true;
                break;
            }
        }

        return $allowed;
    }

    /**
     * Run the application.
     *
     * @param bool $exit
     * @param  ?string $forceRoute
     * @throws Exception|\Pop\Event\Exception|\Pop\Router\Exception|\ReflectionException
     * @return void
     */
    public function run(bool $exit = true, ?string $forceRoute = null): void
    {
        // If method is not allowed
        if (!isset($this->routes[strtolower((string)$_SERVER['REQUEST_METHOD'])])) {
            throw new Exception(
                "Error: The method '" . strtoupper((string)$_SERVER['REQUEST_METHOD']) . "' is not allowed.", 405
            );
        }

        // Route request
        $this->router->addRoutes($this->routes[strtolower((string)$_SERVER['REQUEST_METHOD'])]);
        $this->router->route();

        // If route is allowed for this method
        if ($this->router->hasRoute() && $this->isAllowed($this->router->getRouteMatch()->getOriginalRoute())) {
            parent::run($exit, $forceRoute);
        // Else, handle error
        } else {
            if ($this->router->hasRoute()) {
                $message = "Error: The route '" . $_SERVER['REQUEST_URI'] .
                    "' is not allowed on the '" . strtoupper((string)$_SERVER['REQUEST_METHOD']) . "' method";
            } else {
                $message = "Error: That route '" . $_SERVER['REQUEST_URI'] . "' was not found for the '" .
                    strtoupper((string)$_SERVER['REQUEST_METHOD']) . "' method";
            }

            $this->trigger('app.error', ['exception' => new Exception($message, 404)]);
            $this->router->getRouteMatch()->noRouteFound((bool)$exit);
        }
    }

    /**
     * Magic method to check for a custom method
     *
     * @param  string $methodName
     * @param  array  $arguments
     * @throws Exception
     * @return void
     */
    public function __call(string $methodName, array $arguments): void
    {
        if (!isset($this->routes[strtolower($methodName)])) {
            throw new Exception("Error: The custom method '" . strtoupper($methodName) . "' is not allowed.");
        }

        if (count($arguments) != 2) {
            throw new Exception("Error: You must pass a route and a controller.");
        }

        [$route, $controller] = $arguments;

        $this->setRoute(strtolower((string)$methodName), $route, $controller);
    }

}
