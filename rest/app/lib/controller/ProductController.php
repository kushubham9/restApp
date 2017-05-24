<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 23/05/17
 * Time: 8:38 PM
 */

namespace restApi\lib\controller;

use restApi\core\Controller;
use restApi\lib\model\ProductModel;

class ProductController extends Controller
{
    public function verbs()
    {
        return (array_merge( parent::verbs(), ['search' => ['GET']]));
    }

    public $accessRule = [
        'index' => '@',
        'update' => '@',
        'delete' => '@',
        'create' => '@',
    ];

    public function searchMethod(){
        $model = new ProductModel();

        if (isset($_GET['name']) && $_GET['name'])
        {
            return $model->searchProduct(['name' => $_GET['name']]);
        } else
            return $this->indexMethod();
    }

    public function indexMethod(){
        $model = new ProductModel();
        return $model->getProducts();
    }

    public function updateMethod(){
        $model = new ProductModel();

        if (isset($_GET['id']) && $_GET['id']){
            if (sizeof($model->fetchOne(['where' => ['id'=>$_GET['id']]])) == 0)
                throw new \Exception("Invalid product id specified.");
        }
        else{
            throw new \Exception("Product ID missing.");
        }

        $updateArr = [];
        parse_str(file_get_contents('php://input'), $_PATCH);
        foreach (ProductModel::$attributes as $attribute){
            if (isset($_PATCH[$attribute])){
                $updateArr[$attribute] = $_PATCH[$attribute];
            }
        }
        if ($model->updateProduct($updateArr, $_GET['id'])){
            return [
                'success' => true,
                'message' => 'Record updated'
            ];
        } else{
            throw new \Exception("Unable to update record.");
        }
    }

    public function createMethod(){
        $model = new ProductModel();

        if (isset($_POST['id']) && $_POST['id']){
            if (sizeof($model->fetchOne(['where' => ['id'=>$_POST['id']]])) != 0)
                throw new \Exception("Sorry, the product ID is already taken");
        }

        $insertArr = [];
        foreach (ProductModel::$attributes as $attribute){
            if (isset($_POST[$attribute])){
                $insertArr[$attribute] = $_POST[$attribute];
            }
        }
        if ($model->insertProduct($insertArr)){
            return [
                'success' => true,
                'message' => 'Product created.'
            ];
        } else{
            throw new \Exception("Unable to insert new record.");
        }
    }

    public function deleteMethod(){
        $model = new ProductModel();
        parse_str(file_get_contents('php://input'), $_PATCH);

        if (isset($_PATCH['id']) && $_PATCH['id']){
            if (sizeof($model->fetchOne(['where' => ['id'=>$_PATCH['id']]])) == 0)
                throw new \Exception("Product not found. Check ID.");
        }

        if ($model->deleteProduct($_PATCH['id'])){
            return [
                'success' => true,
                'message' => 'Product created.'
            ];
        } else{
            throw new \Exception("Unable to delete the product");
        }
    }
}