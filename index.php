<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \DDev\Page;
use \DDev\PageAdmin;
use \DDev\Model\User;
use \DDev\Model\Category;
use \DDev\Model\Product;

$app = new Slim();

//'debug', mostra os erros formatados e etc//

$app->config('debug', true);

require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");
require_once("admin-orders.php");
require_once("functions.php");

$app->run();

 ?>