<?php 

require_once("vendor/autoload.php");

$app = new \Slim\Slim();

//'debug', mostra os erros formatados e etc//
$app->config('debug', true);

$app->get('/', function() {
    
	$sql = new DDev\DB\Sql();

	$results = $sql->select("SELECT * FROM TB_USERS");

	echo json_encode($results);


});

$app->run();

 ?>