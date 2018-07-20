<?php

namespace DDev\Model;

use \DDev\DB\Sql;
use \DDev\Model;

class User extends Model{

	   const SESSION = 	"User";


	public static function login($login, $password){


		$sql = new Sql();

		$results = $sql->select("SELECT * FROM TB_USERS WHERE DESLOGIN = :LOGIN", array(

			":LOGIN"=>$login
		));
       
       	if (count($results) === 0) {
       		
       		//Como não criamos a propria Exception, temos que colocar a \ para ele achar a principal
       		throw new \Exception("Usuário inexistente ou senha invalida");
       		
       	}

       	$data = $results[0];
        
       	if(password_verify($password, $data["despassword"]) === true){


       		$user = new User();

       		$user->setData($data);
             //Pega os valores de ssssão e etc//
       		$_SESSION[User::SESSION] = $user->getValues();

       		return $user;

       	}

       	else{
       


			throw new \Exception("Usuário inexistente ou senha invalida");

       		}



       	} 

       	public static function verifyLogin($inadmin = true){

       			if (
       				!isset($_SESSION[User::SESSION])
       				||
       				!$_SESSION[User::SESSION]
       				||
       				!(int)$_SESSION[User::SESSION]["iduser"] > 0
       				||
       				(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin

       			) {

       				header("Location: /admin/login");
       				exit;
       				
       			}
       		}


       	public static function logout(){

       		$_SESSION[User::SESSION] = NULL;


       	}


	 } 
?>