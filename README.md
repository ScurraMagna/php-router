# php-router

index.php
```php
<?php
include ("php-router/src/Router.php"); //if no framework used

//create router
$router = new App\Entry\Router ($_GET["url"]);

//define routes
$router->get("url/to/page/&slug", function ($slug) {
  //do
});
$router->post("url/to/page", "HomeController#method"); // using MVC

$router->get("url/to/page/&slug", "HomeController#method")->with("slug", [0-9]+); //impose values to slug with regex

//run the router
router->run();
?>
```
