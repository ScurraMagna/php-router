<?php

namespace App\Entry;

class Route
{
  private $path;

  private $callable;

  private $matches = [];

  private $params = [];

  protected $slugMarker = "&";

  public function __construct($path, $callable, $slugMarker) {
    $this->path = trim($path, '/');  
    $this->callable = $callable;
    $this->slugMarker = $slugMarker;
  }

  public function match($url) {
    $url = trim($url, '/');
    $path = preg_replace_callback('#'.$this->slugMarker.'([\w]+)#', [$this, 'paramMatch'], $this->path);
    $regex = "#^$path$#i";
    if(!preg_match($regex, $url, $matches)) {
      return false;
    }
    array_shift($matches);
    $this->matches = $matches;  // On sauvegarde les paramètre dans l'instance pour plus tard
    return true;
  }

  public function with($param, $regex) {
    $regex = ($regex === "UPPER") ? "/[A-Z]+/" :
             ($regex === "LOWER") ? "/[a-z]+/" :
             ($regex === "ALPHA") ? "/[a-zA-Z]+/" :
             ($regex === "DIGIT") ? "/[0-9]+/" :
             ($regex === "ALNUM") ? "/[a-zA-Z0-9]+/" :
             $regex;
    $this->params[$param] = str_replace('(', '(?:', $regex);
    return $this;
  }

  private function paramMatch($match) {
    if(isset($this->params[$match[1]])) {
      return '(' . $this->params[$match[1]] . ')';
    }
    return '([^/]+)';
  }

  public function call() {
    if(is_string($this->callable)) {
      $params = explode('#', $this->callable);
      $controller = "App\\Controllers\\" . $params[0] . "Controller";
      $controller = new $controller();
      if(method_exists($controller, $params[1])) {
        return call_user_func_array([$controller, $params[1]], $this->matches);
      }
      else {
        echo "Error 404";
      }
    }
    else {
      return call_user_func_array($this->callable, $this->matches);
    }
  }

  public function getUrl($params) {
    $path = $this->path;
    foreach($params as $key => $value) {
      $path = str_replace(":$key", $value, $path);
    }
    return $path;
  }
}
?>
