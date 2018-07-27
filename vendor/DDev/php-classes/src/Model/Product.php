<?php

namespace DDev\Model;

use \DDev\DB\Sql;
use \DDev\Model;
use \DDev\Mailer;

class Product extends Model{


          public static function listAll(){

              $sql = new Sql();

            return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");


        } 


        //Metodo para fazer passar pelo 'getValues', pra sobreescrever a lista , por que no BD não salva as fotos, só recupera//
        public static function checkList($list){

                 //'&$row' , & comercial para ser manipulada pela mesma variavel na memoria//
                foreach ($list as &$row) {

                  $p = new Product();
                  $p->setData($row);
                  $row = $p->getValues();
                 

                }

                 return $list;

        }
//////////////////////////////////////////////////////
        public function save(){

              $sql = new Sql(); 

              //Procedure varios comandos//
              $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)",
                      array(
                     ":idproduct"=>$this->getidproduct(),
                     ":desproduct"=>$this->getdesproduct(),
                     ":vlprice"=>$this->getvlprice(),
                     ":vlwidth"=>$this->getvlwidth(),
                     ":vlheight"=>$this->getvlheight(),
                     ":vllength"=>$this->getvllength(),
                     ":vlweight"=>$this->getvlweight(),
                     ":desurl"=>$this->getdesurl()            
                     ));

                     $this->setData($results[0]);        

              }


            public function get($idproduct){

                $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", 
         array(
         ":idproduct"=>$idproduct

            ));

            $this->setData($results[0]);

             }

              public function delete(){

                $sql = new Sql();

                $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
                   ":idproduct"=>$this->getidproduct()    
                ]);

         }

           public function checkPhoto(){

            if (file_exists($_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR. 
              "res" . DIRECTORY_SEPARATOR .
              "site" . DIRECTORY_SEPARATOR .
              "img" . DIRECTORY_SEPARATOR .
              "products" . DIRECTORY_SEPARATOR .
              $this->getidproduct() . ".jpg"
              )) {
             
             // Em url não se passa 'DIRECTORY_SEPARATOR', só quaNDO VAI DERECIONAR PASTAS//
              $url =  "/res/site/img/products/".$this->getidproduct() . ".jpg";

            }
            else{

              $url = "/res/site/img/product.jpg";

                }


            return $this->setdesphoto($url);
           }


          //Metodo 'getValues' , sendo sobreEscrito, pois n tem no BD, logo o get e set da foto é feito no codigo//
          public function getValues(){

            $this->checkPhoto();

           $values = parent::getValues();

            return $values;


         }

         public function setPhoto($file){

          $extension =  explode('.', $file['name']);
          $extension = end($extension);


          switch ($extension) {

            case "jpg":
            case "jpeg":
            $image = imagecreatefromjpeg($file["tmp_name"]);
            break;

            case "gif":
            $image = imagecreatefromgif($file["tmp_name"]);
            break;

            case "png":
            $image = imagecreatefrompng($file["tmp_name"]);
            break;

          }

           $dist =  $_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR. 
              "res" . DIRECTORY_SEPARATOR .
              "site" . DIRECTORY_SEPARATOR .
              "img" . DIRECTORY_SEPARATOR .
              "products" . DIRECTORY_SEPARATOR .
              $this->getidproduct() . ".jpg";


              imagejpeg($image, $dist);

              imagedestroy($image);


              $this->checkPhoto();


         }

         public function getFromURL($desurl){


          $sql =  new sql();

          $rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", [
            ':desurl'=>$desurl


          ]);

          $this->setData($rows[0]);


         }
           
           public function getCategories(){

            $sql = new Sql();

            return $sql->select("
              SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory
              WHERE b.idproduct = 
              :idproduct
            ", [

                ':idproduct'=>$this->getidproduct()
            ]);



           }   

	 }


?>