<?php

use \DDev\Model\User;
use \DDev\Model\Cart;

function formatPrice($vlPrice) { // Função que retorna o valor Real passado no formato 9.999,99

	if($vlPrice === NULL)
		return (float) 0;

	return number_format($vlPrice, 2, ',', '.');		
}

function checkLogin($inadmin = true){


	return User::checkLogin($inadmin);


}

function formatDate($date) { // Realiza a formatação da data passada como parâmetro;

	return date('d/m/Y', strtotime($date));
}


//Funções retornar dados do Usuário logado//
function getUserName() { // Retorna o nome do Usuário;

	$user = User::getFromSession();
	
	return $user->getdesperson();
}


//Funções retornar dados do Usuário logado//
function getLogin() { // Retorna o nome do Usuário;

	$user = User::getFromSession();
	
	return $user->getdeslogin();
}

function getDtregister() { // Retorna Data de registro do Usuario;

	$user = User::getFromSession();

	$date = $user->getdtregister();

     $dataRegistro = formatDate($date);
	
	return $dataRegistro;
}

///////////////////////////////////////////////

function getCartNrQtd(){

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

    return $totals['nrqtd'];//Carrinho com o frete

}

function getCartVlSubTotal(){

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return formatPrice($totals['vlprice']);//Carrinho sem o frete

}

?>