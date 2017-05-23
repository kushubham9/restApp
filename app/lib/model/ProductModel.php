<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 23/05/17
 * Time: 8:40 PM
 */

namespace restApi\lib\model;

use restApi\core\Model;

class ProductModel extends Model
{
    public $table = 'products';
    /**
     * @var array The attributes array maps the table columns to the model.
     * Columns if not present in the array will not m
     */
    public static $attributes = ['id', 'name', 'description', 'cost'];

    public function getProducts(){
        $products = $this->fetchAll([]);
        return $products;
    }

    public function updateProduct($records, $productID){
        if (!is_array($records) || sizeof($records) == 0){
            return false;
        }

        else{
            return $this->updateRecord($this->table, ['id' => $productID], $records);
        }
    }

    public function insertProduct($records){
        if (!is_array($records) || sizeof($records) == 0){
            return false;
        }

        else{
            return $this->insertRecord($this->table, $records);
        }
    }

    public function deleteProduct($id){
        return $this->deleteRecord($this->table,['id' => $id]);
    }

    public function searchProduct ($clause){
        if (isset($clause['name']))
        {
            try{
                $dbConnection = $this->_getDbConnection();
                $query = "SELECT * FROM ".$this->table." WHERE name LIKE '%".$clause['name']."%'";

                $result = $dbConnection->query($query);

                if ($result && $result->num_rows>0)
                    return $result->fetch_all();
            } catch (\Exception $e){
                throw $e;
            } finally{
                $dbConnection->close();
            }
        }
        return [];
    }
}