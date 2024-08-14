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
}