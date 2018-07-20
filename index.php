<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \DDev\Page;
use \DDev\PageAdmin;


$app = new Slim();

//'debug', mostra os erros formatados e etc//
$app->config('debug', true);

$app->get('/', function() {

	$page = new Page();

	$page->setTpl("index"); 

});

$app->get('/admin', function() {

	$page = new PageAdmin();

	$page->setTpl("index"); 

});

$app->run();

 ?>