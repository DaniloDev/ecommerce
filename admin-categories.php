<?php

use \DDev\Page;
use \DDev\PageAdmin;
use \DDev\Model\User;
use \DDev\Model\Category;
use \DDev\Model\Product;

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	
	if ($search != '') {
		
		$pagination = Category::getPageSearch($search ,$page);
	}else{

		$pagination = Category::getPage($page);

	}

	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x++) { 
		
		array_push($pages,[
			'href'=>'/admin/categories?' . http_build_query([
			'page'=>$x+1,
			'search'=>$search	
			]), 
			'text'=>$x+1			

		]);
	}


	$page = new PageAdmin();

	$page->setTpl("categories",[

		"categories"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages


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