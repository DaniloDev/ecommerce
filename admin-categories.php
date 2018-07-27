<?php

use \DDev\Page;
use \DDev\PageAdmin;
use \DDev\Model\User;
use \DDev\Model\Category;
use \DDev\Model\Product;

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories",[

		"categories"=>$categories


	]);
});

$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});


$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;
	
});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

		User::verifyLogin();

		$category = new Category();
		//Carregando a categoria pra ver se a pagina carrega se carregar o usuario existe no BD//
		$category->get((int)$idcategory);

		$category->delete();

		header("Location: /admin/categories");
	   	exit;
	
});

$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	//Carregando a categoria pra ver se a pagina carrega se carregar o usuario existe no BD//
	$category->get((int)$idcategory);

	$page = new PageAdmin();

	//PAssando id pra dentro do template para a categoria ser editada//
	$page->setTpl("categories-update",[ 

	'category'=>$category->getValues()
	]);

});

$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	//Carregando a categoria pra ver se a pagina carrega se carregar o usuario existe no BD//
	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save(); 
	
	header("Location: /admin/categories");
	exit;
	

});


$app->get("/admin/categories/:idcategory/products", function($idcategory){

	User::verifyLogin();
	
	$category = new Category();
   
	 //Tendo certeza que é um núemero//
	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products", [
		'category'=>$category->getValues(),
		'productsRelated'=>$category->getProducts(),//<-Nâo precisa passar true , pq npo metoto na classe category ja é true
		'productsNotRelated'=>$category->getProducts(false)
	]);

});


$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){

	User::verifyLogin();
	
	$category = new Category();
   
	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->addProduct($product);


	header("Location: /admin/categories/" . $idcategory . "/products");
	exit;

});




$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){

	User::verifyLogin();
	
	$category = new Category();
   
	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->removeProduct($product);


	header("Location: /admin/categories/" . $idcategory . "/products");
	exit;

});




?>