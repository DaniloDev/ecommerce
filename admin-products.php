<?php

use \DDev\PageAdmin;
use \DDev\Model\User;
use \DDev\Model\Product;

$app->get("/admin/products", function(){

    User::verifyLogin();
    
    $products = Product::listAll();

    $page = new PageAdmin();
  
	$page->setTpl("products",[

		"products"=>$products


	]);
});

$app->get("/admin/products/create", function(){

    User::verifyLogin();

    $page = new PageAdmin();
  
	$page->setTpl("products-create");
});



$app->post("/admin/products/create", function(){

    User::verifyLogin();

    $products = new Product();

    $products->setData($_POST);

    $products->save();

    header("Location: /admin/products");
    exit;
  
	
});

$app->get("/admin/products/:idproduct", function($idproduct){

    User::verifyLogin();

    $product = new Product();

    $product->get((int)$idproduct);

    $page = new PageAdmin();
  
    $page->setTpl("products-update", [
     'product'=>$product->getValues()   

    ]);
});


$app->post("/admin/products/:idproduct", function($idproduct){

    User::verifyLogin();

    $product = new Product();

    $product->get((int)$idproduct);

    $product->setData($_POST);

    $product->save();

    $product->setPhoto($_FILES["file"]);


$Quantidade = 5;//(isset($_POST["quantidade"]) && is_int(intval($_POST["quantidade"]))) ? (int)$_POST["quantidade"] : 5; 

// Abre formulário de upload 
echo "<form action='processa_upload.php' method='POST' enctype='multipart/form-data'>"; 
echo "<b>Envio das fotos</b><br />"; 

// Imprime os campos para upload, de acordo com a quantidade pedida 
for($i = 1; $i <= $Quantidade; ++$i) 
{ 
echo "Foto #" . $i . ": <input type='file' name='fotos[]'' /><br/>"; 
} 

// Fecha formulário 
echo "<br /><input type='submit' value='OK'/>"; 
echo "</form>"; 

    header("Location: /admin/products");
    exit;


});






$app->get("/admin/products/:idproduct/delete", function($idproduct){

    User::verifyLogin();

    $product = new Product();

    $product->get((int)$idproduct);

    $product->delete();

    header("Location: /admin/products");
    exit;


});




?>