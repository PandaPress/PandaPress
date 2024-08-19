<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;
use Latte\Exception\CompileException;
use MongoDB\BSON\ObjectId;

class CategoryController extends BaseController
{
    private string $errorMessage = "";

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        global $pandadb;

    
        $documents = $pandadb->selectCollection("categories")->find();

        $categories = [];

        foreach ($documents as $document) {
            $category = [
                "_id" => $document["_id"]->__toString(),
                "title" => $document["title"],
                "description" => $document["description"],
                "slug" => $document["slug"],
            ];
            array_push($categories, $category);
        }

        return $this->template_engine->render("$this->views/categories/index.latte", [
            "categories" => $categories
        ]);
    }

    public function create() {
        return $this->template_engine->render("$this->views/categories/create.latte");
    }

    public function save() {
        global $pandadb;
        global $router;

        $collection = $pandadb->selectCollection("categories");

        $_category = $collection->findOne([
            "slug" => $_POST["slug"]
        ]);

        if ($_category !== null) {
            return $router->simpleRedirect("/admin/categories/create", [
                "error_message" => "Slug already exists in the database",
                "category_form_data" => $_POST
            ]);
        } 
        
        try {
            $title = $_POST["title"];
            $slug = $_POST["slug"];
            $description = $_POST["description"];

            $result = $collection->insertOne([
                "title" => $title,
                "slug" => $slug,
                "description" => $description,
            ]);

            unset_session_keys(['error_message', 'category_form_data']);
            
            return $router->simpleRedirect("/admin/success", [
                "success_message" => "Category saved successfully"
            ]);         
        } catch (Exception | \Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        } 
    }


    public function delete(){
        global $pandadb;
        global $router;

        try {
            $category_id = $_POST['_id'];
       
            // delete the category
            $collection = $pandadb->selectCollection("categories");
            $deleteResult = $collection->deleteOne([
                "_id" => new ObjectId($category_id)
            ]);

            // update all posts that have the deleted category to be uncategorized
            $updateResult = $pandadb->selectCollection("posts")
                ->updateMany(
                    ['category' => ['$elemMatch' => ['_id' => new ObjectId($category_id)]]],
                    ['$set' => ['category' => []]]
                );

            unset_session_keys(['error_message', 'category_form_data']);  // remove error message and form data from session 
            
            // redirect to success page with success message 
            return $router->simpleRedirect("/admin/success", [
                "success_message" => "Category deleted successfully"
            ]);
      
        } catch (Exception | \Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        } 
    }
}