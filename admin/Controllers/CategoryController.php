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

        // !TODO pagination
        $documents = $pandadb->selectCollection("categories")->find();

        $categories = [];

        foreach ($documents as $document) {
            $category = [
                "_id" => $document["_id"]->__toString(),
                "name" => $document["name"],
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
        
        try {
            $title = $_POST["name"];
            $slug = $_POST["slug"];
            $description = $_POST["description"];

            $pandadb->selectCollection("categories")->insertOne([
                "title" => $title,
                "slug" => $slug,
                "description" => $description,
            ]);
   
            return $router->simpleRedirect("/admin/success", [
                "success_message" => "Category saved successfully"
            ]);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        }

    }
}