<?php

namespace DDev\Model;

use \DDev\DB\Sql;
use \DDev\Model;
use \DDev\Mailer;

class Category extends Model{


          public static function listAll(){

              $sql = new Sql();

            return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");


        } 



        public function save(){

              $sql = new Sql(); 

              //Procedure varios comandos//
              $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)",
                      array(
                     ":idcategory"=>$this->getidcategory(),
                     ":descategory"=>$this->getdescategory()   
                     
                     ));

                     $this->setData($results[0]);

                     Category::updateFile();
              }


            public function get($idcategory){

                $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", 
         array(
         ":idcategory"=>$idcategory

            ));

            $this->setData($results[0]);

  }

              public function delete(){

                $sql = new Sql();

                $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
                   ":idcategory"=>$this->getidcategory()    
                ]);


              //Ao deletar é preciso que o arquivo seja modificado//

                Category::updateFile();
         }
              
         //Atuliza a template de categorias do site//
              public function updateFile(){

              $categories = Category::listAll();

              $html = [];

              foreach ($categories as $row){

                array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
                
              }


            file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR ."categories-menu.html", implode('',$html));



          }

          //Metodo de verificação da categoria//

          public function getProducts($related = true){

            $sql = new Sql();

            if ($related === true) {
            
             return $sql->select("SELECT * FROM tb_products WHERE idproduct IN(SELECT a.idproduct
                FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                WHERE b.idcategory = :idcategory
              );

              ",[
                ':idcategory'=>$this->getidcategory()

            ]);

            }else{
           return $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN(SELECT a.idproduct
                FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                WHERE b.idcategory = :idcategory

                 );

             ",[
                ':idcategory'=>$this->getidcategory()

            ]);

            }



          }

          //Classe para fazer paginação, 'ceil', converte aredondando pra cima//

          public function getProductsPage($page = 1, $itemsPerPage = 8){


            $start = ($page - 1) * $itemsPerPage;

            $sql = new Sql();

            $results = $sql->select("
              SELECT SQL_CALC_FOUND_ROWS *
              FROM tb_products a 
              INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct 
              INNER JOIN tb_categories c ON c.idcategory = b.idcategory
              WHERE c.idcategory = :idcategory
              LIMIT $start, $itemsPerPage;
              ", [
                  ':idcategory'=>$this->getidcategory()

                  ]);

              $resulTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

              return [
                  'data'=>Product::checkList($results),
                  'total'=>(int)$resulTotal[0]["nrtotal"],
                  'pages'=>ceil($resulTotal[0]["nrtotal"] / $itemsPerPage)


              ];


          }



          public function addProduct(Product $product){


          $sql = new Sql();

          $sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES (:idcategory, :idproduct)",[

            ':idcategory'=>$this->getidcategory(),
            ':idproduct'=>$product->getidproduct()
          ]);


          }

          public function removeProduct(Product $product){


          $sql = new Sql();

          $sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct",[

            ':idcategory'=>$this->getidcategory(),
            ':idproduct'=>$product->getidproduct()
          ]);


          }



           //Classe para fazer paginação, 'ceil', converte aredondando pra cima//

                public static function getPage($page = 1,  $itemsPerPage = 10){


                  $start = ($page - 1) * $itemsPerPage;

                  $sql = new Sql();
                       
                       $results = $sql->select("
                        SELECT SQL_CALC_FOUND_ROWS *
                        FROM tb_categories 
                        ORDER BY descategory
                        LIMIT $start, $itemsPerPage;
                        ");     

                        $resulTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

                        return [
                            'data'=>$results,
                            'total'=>(int)$resulTotal[0]["nrtotal"],
                            'pages'=>ceil($resulTotal[0]["nrtotal"] / $itemsPerPage)


                        ];


                }


         
          public static function getPageSearch($search, $page = 1,  $itemsPerPage = 10){


                  $start = ($page - 1) * $itemsPerPage;

                  $sql = new Sql();
                        $results = $sql->select("
                        SELECT SQL_CALC_FOUND_ROWS *
                        FROM tb_categories
                        WHERE descategory LIKE :search
                        ORDER BY descategory
                       LIMIT $start, $itemsPerPage;
                      ", [
                        ':search'=>'%'.$search.'%'

                      ]); 

                        $resulTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

                        return [
                            'data'=>$results,
                            'total'=>(int)$resulTotal[0]["nrtotal"],
                            'pages'=>ceil($resulTotal[0]["nrtotal"] / $itemsPerPage)


                        ];


                }







	 }


?>