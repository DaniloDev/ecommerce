<?php

namespace DDev\Model;

use \DDev\DB\Sql;
use \DDev\Model;
use \DDev\Mailer;
use \DDev\Model\User;

  class Cart extends Model{
  
       const SESSION =  "Cart";
       const SESSION_ERROR = "CartError";

       public static function getFromSession(){


          $cart = new Cart();

          if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0){

            $cart->get((int)$_SESSION[Cart::SESSION]['idcart']);

          }else{

              $cart->getFromSessionID();


              if (!(int)$cart->getidcart() > 0) {
                
                $data = [
                  'dessessionid'=>session_id()

                ];

                if (User::checkLogin(false)) {
                 
                  $user = User::getFromSession();

                  $data['iduser'] = $user->getiduser();

                }

                $cart->setData($data);

                $cart->save(); 

                $cart->setToSession();
               

                
              }

            }

          return $cart;

          
      }

          public function setToSession(){


            $_SESSION[Cart::SESSION] = $this->getValues();   


          }



        public function getFromSessionID(){

             $sql = new Sql();

             $results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [

             ':dessessionid'=>session_id()   

             ]);       
             
           if (count($results) > 0) {
              
                    $this->setData($results[0]);
               }       

        } 



        public function get(int $idcart){

             $sql = new Sql();

             $results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [

             ':idcart'=>$idcart   

             ]);

             if (count($results) > 0) {

                    $this->setData($results[0]);
               }       
             
           

        } 


          public function save(){

            $sql = new Sql();

            $results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)",[
            
              ':idcart'=>$this->getidcart(),
              ':dessessionid'=>$this->getdessessionid(),
              ':iduser'=>$this->getiduser(),
              ':deszipcode'=>$this->getdeszipcode(),
              ':vlfreight'=>$this->getvlfreight(),
              ':nrdays'=>$this->getnrdays()  

            ]);

            $this->setdata($results[0]);

          }



          public function addProduct(Product $product){

            $sql = new Sql();

            $sql->query("INSERT INTO tb_cartsproducts (idcart, idproduct) VALUES(:idcart, :idproduct)", [

              ':idcart'=>$this->getidcart(),
              ':idproduct'=>$product->getidproduct()
              ]);


            $this->getCalculeteTotal();

          }


          public function removeProduct(Product $product, $all = false){

              
             $sql = new Sql();

             if($all){

              $sql->query("UPDATE tb_cartsproducts SET dtremoved =  NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL", [
                ':idcart'=>$this->getidcart(),
                ':idproduct'=>$product->getidproduct()

                ]);

             } else {

              $sql->query("UPDATE tb_cartsproducts SET dtremoved =  NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL LIMIT 1", [
                ':idcart'=>$this->getidcart(),
                ':idproduct'=>$product->getidproduct()

                   ]);
             }

             $this->getCalculeteTotal();

          }


          public function getProducts(){

          $sql =  new Sql();

          $rows = $sql->select("
              SELECT b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight ,b.desurl, COUNT(*) AS nrqtd, SUM(b.vlprice) AS vltotal
                FROM tb_cartsproducts a
                INNER JOIN tb_products b ON a.idproduct = b.idproduct
                WHERE a.idcart = :idcart AND a.dtremoved IS NULL
                GROUP BY b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight,b.desurl
                ORDER BY b.desproduct", 
                [
                  ':idcart'=>$this->getidcart() 
                ]);
              //Checa a foto e outros//
              return Product::checkList($rows);

          } 


          public function getProductsTotals(){

            $sql = new Sql();

            $results = $sql->select("
              SELECT SUM(vlprice) AS vlprice, SUM(vlwidth) AS vlwidth, SUM(vlheight) AS vlheight, SUM(vllength) AS vllength, SUM(vlweight) AS vlweight, COUNT(*) AS nrqtd
              FROM tb_products a
              INNER JOIN tb_cartsproducts b ON a.idproduct = b.idproduct
              WHERE b.idcart = :idcart And dtremoved IS NULL;

              ",[
                ':idcart'=>$this->getidcart()

              ]);


              if (count($results) > 0) {
               
                 return $results[0];

              } else {


                return [];
              }

          }

          public function setFreight($nrzipcode){


          $nrzipcode = str_replace('-', '', $nrzipcode);

          $totals = $this->getProductsTotals();

          if ($totals['nrqtd'] > 0) {

             ////// Validações a serem testadas//////////
            if ($totals['vlheight'] < 2)  $totals['vlheight'] = 2;
            if ($totals['vllength'] < 16) $totals['vllength'] = 16;
            if ($totals['vlwidth'] < 11)  $totals['vlwidth'] = 11;
           /////////////////////////////////////////////
            $qs = http_build_query([
              'nCdEmpresa'=>'',
              'sDsSenha'=>'',
              'nCdServico'=>'40010',
              'sCepOrigem'=>'09853120',
              'sCepDestino'=>$nrzipcode,
              'nVlPeso'=>$totals['vlweight'],
              'nCdFormato'=>'1',
              'nVlComprimento'=>$totals['vllength'],
              'nVlAltura'=>$totals['vlheight'],
              'nVlLargura'=>$totals['vlwidth'],
              'nVlDiametro'=>'0',
              'sCdMaoPropria'=>'S',
              'nVlValorDeclarado'=>$totals['vlprice'],
              'sCdAvisoRecebimento'=>'S'

            ]);
           
           $xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);

            /*echo json_encode(($xml));
            exit();
            */
            $result = $xml->Servicos->cServico; // Seleciona dentro do objeto serviços o objeto cServico;
           
            if($result->MsgErro != '') {  // Caso alguma mensagem de erro seja encontrada;

            Cart::setMsgError($result->MsgErro); // Define a mensagem de erro na variável de sessão, coloca na sessao

            } else {

            Cart::clearMsgError(); // Limpa a mensagem de erro na variável de sessão;


              }

            $this->setnrdays($result->PrazoEntrga);
          
           $this->setvlfreight(Cart::formatValueToDecimal($result->Valor));  // Atribui o valor do frete como atributo;
           
           $this->setdeszipcode($nrzipcode);  // Atribui o CEP do destinatário como atributo;

            $this->save();

          //  return $result;

          }else{



              }


     }
           

            public static function formatValueToDecimal($value) : float { // Converte uma um valor no padrão brasileiro para o norte , para salvqar em bd, pe tem qa salvar assim
                                                              // americano;
              $value = str_replace('.', '', $value); // Troca todos os '.' do valor por '';

              return str_replace(',', '.', $value); // Troca todas as ',' por '.';
              }

              public static function setMsgError($msg) { // Atribui uma Mensagem de erro a variável de sessão especificada;

                $_SESSION[Cart::SESSION_ERROR] = $msg;
                
                }

               public static function getMsgError() { // Retorna a mensagem de erro presente na variável de sessão ou "", caso contrário;

               $msg = (isset($_SESSION[Cart::SESSION_ERROR])) ? $_SESSION[Cart::SESSION_ERROR] : "";

                    Cart::clearMsgError(); // Realiza a limpeza da variável de sessão;

                      return $msg;
                  }

                 public static function clearMsgError() { // Realiza a limpeza da variável de sessão especificada;

                  $_SESSION[Cart::SESSION_ERROR] = NULL;
                      
                }


                public function updateFreight(){

                  //Verifica se tem cep //
                  if ($this->getdeszipcode() != '') {
                    
                    $this->setFreight($this->getdeszipcode());
                  }




                }

                // Sobreescrecer metodo//
                public function getValues(){


                  $this->getCalculeteTotal();


                  return parent::getValues();


                }


                public function getCalculeteTotal(){

                  $this->updateFreight();

                  $totals = $this->getProductsTotals();

                  $this->setvlsubtotal($totals['vlprice']);
                  $this->setvltotal($totals['vlprice'] + $this->getvlfreight());

                }
	     
        }



?>