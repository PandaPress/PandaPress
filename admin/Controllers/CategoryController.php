<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;
use MongoDB\BSON\ObjectId;


class CategoryController extends BaseController {


    public function __construct() {
        parent::__construct();
    }


    public function index() {
        global $pandadb;

        $categoriesCollection = $pandadb->selectCollection("categories");

        $pipeline = [
            [
                '$addFields' => [
                    'stringId' => ['$toString' => '$_id']
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'posts',
                    'localField' => 'stringId',
                    'foreignField' => 'category',
                    'as' => 'posts'
                ]
            ]
        ];

        $documents = $categoriesCollection->aggregate($pipeline);

        $categoriesWithPostCount = [];

        foreach ($documents as $document) {
            $category = [
                "_id" => $document["_id"]->__toString(),
                "title" => $document["title"],
                "description" => $document["description"],
                "slug" => $document["slug"],
                "postCount" => $document["posts"]->count(),
            ];
            array_push($categoriesWithPostCount, $category);
        }

        return $this->template_engine->render("$this->views/categories/index.latte", $this->appendUserData([
            "categories" => $categoriesWithPostCount
        ]));
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


    public function delete() {
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

    public function update($id) {
        global $pandadb;
        global $router;

        try {
            $collection = $pandadb->selectCollection("categories");
            $category = $collection->findOne([
                "_id" => new ObjectId($id)
            ]);

            if ($category === null) {
                return $router->simpleRedirect("/admin/error", [
                    "error_message" => "Category not found"
                ]);
            }

            return $this->template_engine->render("$this->views/categories/update.latte", [
                "category" => $category,
            ]);
        } catch (Exception | \Exception $e) {
            $error_message = $e->getMessage();
            var_dump($error_message);
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        }
    }

    public function upsave() {
        global $pandadb;
        global $router;

        try {
            $id = $_POST['_id'];
            $title = $_POST['title'];
            $slug = $_POST['slug'];
            $description = $_POST['description'];

            $collection = $pandadb->selectCollection("categories");

            // make sure the slug is unique
            $category = $collection->findOne([
                "slug" => $slug
            ]);

            if ($category !== null) {
                return $router->simpleRedirect("/admin/categories/update/$id", [
                    "error_message" => "Slug already exists in the database",
                ]);
            }

            $collection->updateOne(
                [
                    "_id" => new ObjectId($id)
                ],
                [
                    '$set' => [
                        "title" => $title,
                        "slug" => $slug,
                        "description" => $description
                    ]
                ]
            );

            return $router->simpleRedirect("/admin/success", [
                "success_message" => "Category updated successfully"
            ]);
        } catch (Exception | \Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        }
    }
}
