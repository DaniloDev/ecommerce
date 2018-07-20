<?php

//Essa é a namesapace principal//
namespace DDev;

// Vai usar namespace da tpl 'Rain' new Tpl é da namespace 'Rain'//
use Rain\Tpl;

class Page{

	private $tpl;
	private $options = [];
	private $defaults = [
		"data"=>[]
	];

	public function __construct($opts = array()){

		//Se passar um parametro e der conflito com $opts , oo opts que vale, e p 'array_merge' , vai mesclar os dados e por em $optios//
		$this->options = array_merge($this->defaults, $opts);

		$config = array(         //'$_SERVER["DOCUMENT_ROOT"]', TRAZ A PASTA QUE P SERVIDOR ESTA CONFIGURADO//
				"tpl_dir"       =>$_SERVER["DOCUMENT_ROOT"] ."/views/",
				"cache_dir"     =>$_SERVER["DOCUMENT_ROOT"] ."/views-cache/",
				"debug"         => false // trás alguns comentarios e etc
				   );

		Tpl::configure( $config );


		$this->tpl = new Tpl;

		$this->setData($this->options["data"]);

		//Iniciando a tela de apresntação para usuário//
		$this->tpl->draw("header");


	}

    private function setData($data){

    	foreach ($data as $key => $value) {
			
			//Atribuições de variavel que tem no template, exemplo tem la variavel titulo ele vai pegar variavel e valor e etc//
			$this->tpl->assign($key, $value);
		}

    }

   // Corpo conteudos do site e etc//
	public function setTpl($name, $data = array(), $returnHTML = false){

		$this->setData($data);

		return $this->tpl->draw($name, $returnHTML);

	}


		public function __destruct(){


			$this->tpl->draw("footer");
		
		}




}




?>