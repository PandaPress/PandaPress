<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;

class PostController extends BaseController
{
    private string $errorMessage = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->template_engine->render("$this->views/posts/index.latte");
    }

    public function compose()
    {
        return $this->template_engine->render("$this->views/posts/compose.latte");
    }

    public function save()
    {
        global $pandadb;
        global $router;

        try {
            $title = $_POST["title"];
            $slug = $_POST["slug"];
            $content = $_POST["editor"];
            $status = $_POST["status"]; // "draft" or "published";
            $author = isset($_POST["author"]) ? $_POST["author"] : "admin";

            $_tags = $_POST["tags"];
            $tags = isset($_tags) && strlen($_tags) > 0 ? explode(',', $_tags) : []; 

            $category = $_POST["category"];

            $pandadb->selectCollection("posts")->insertOne([
                "title" => $title,
                "slug" => $slug,
                "status" => $status,
                "author" => $author,
                "content" => $content,
                "tags" => $tags,
                "category" => $category,
                "updated_at" => time(),
                "created_at" => time()
            ]);
   
            return $router->simpleRedirect("/admin/posts/success");
        } catch (Exception $e) {
            return $router->simpleRedirect("/admin/posts/error");
        } catch (\Exception $e) {
            return $router->simpleRedirect("/admin/posts/error");
        }
    }

    public function success(){
        return $this->template_engine->render("$this->views/posts/success.latte");
    }

    public function error(){
        return $this->template_engine->render("$this->views/posts/error.latte");
    }
}
