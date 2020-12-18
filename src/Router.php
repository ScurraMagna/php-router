<?php

namespace App\Entry;

use App\Entry\{Route, RouterException};

class Router
{

  protected $slugMarker = "&";

  private $url;

  private $routes = [];

  private $namedRoutes = [];

  /**
   * read an url and apply functions depending of key words in it
   *
   * exemple:
   * <code>
   *  $router = new Router($_GET['url']);
   *  // list of $router->get() and $router->post() methods
   *   $router->run();
   * </code>
   *
   * @param string $url URL of the page
   */
  public function __construct($url)
  {
    $this->url = $url;
  }

  /**
   * add route using GET method in the routes list of the router
   *
   * exemple:
   * <code>
   *  $router->get('/', function($id){ echo "Hello World!"; }); 
   *   $router->get('/post/:id', function($id){ echo "this is the post $id"; });
   * </code>
   *
   * @param {string} $path the path
   * @param {function} $callable to do when in that path
   */
  public function get($path, $callable, $name=null)
  {
    return $this->add($path, $callable, $name, 'GET');
  }

  /**
   * add route using POST method in the routes list of the router
   *
   * exemple:
   * <code>
   *  $router->post('/', function($id){ echo "Hello World!"; }); 
   *   $router->post('/post/:id', function($id){ echo "this is the post $id"; });
   * </code>
   *
   * @param {string} $path the path
   * @param {function} $callable to do when in that path
   */
  public function post($path, $callable, $name=null)
  {
    return $this->add($path, $callable, $name, 'POST');
  }

  private function add($path, $callable, $name, $method)
  {
    $route = new Route($path, $callable, $this->slugMarker);
    $this->routes[$method][] = $route;
    if(is_string($callable) && $name===null)
    {
      $name = $callable;
    }
    if($name)
    {
      $this->namedRoutes[$name] = $route;
    }
    return $route;
  }

  public function slugMarker($char)
  {
    $this->slugMarker = $char;
  }
  
  /**
   * check if given url match with the list of routes the web site has
   */
  public function run()
  {
    if(!isset($this->routes[$_SERVER['REQUEST_METHOD']]))
    {
      throw new RouterException('REQUEST_METHOD does not exist');
    }
    foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route)
    {
      if($route->match($this->url))
      {
        return $route->call();
      }
    }
    throw new RouterException('No matching routes');
  }

  public function url($name, $params=[])
  {
    if(!isset($this->namedRoutes[$name]))
    {
      throw new RouterException('No route matches this name');
    }
    return $this->namedRoutes[$name]->getUrl($params);
  }
}
?>
