<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;
use MongoDB\BSON\ObjectId;

class PostController extends BaseController
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
        $documents = $pandadb->selectCollection("posts")->find();

        $posts = [];

        foreach ($documents as $document) {
            $post = [
                "_id" => (string) $document["_id"],
                "title" => $document["title"],
                "slug" => $document["slug"],
                "content" => $document["content"],
                "author" => $document["author"],
                "status" => $document["status"],
                "created_at" => $document["created_at"],
                "updated_at" => $document["updated_at"],
                "category" => $document["category"],
                "tags" => $document["tags"]
            ];
            array_push($posts, $post);
        }

        return $this->template_engine->render("$this->views/posts/index.latte", [
            "posts" => $posts
        ]);
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
            $status = $_POST["status"]; // "draft" or "published" or "deleted"
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
   
            return $router->simpleRedirect("/admin/posts/success", [
                "success_message" => "Post saved successfully"
            ]);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/posts/error", [
                "error_message" => $error_message
            ]);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/posts/error", [
                "error_message" => $error_message
            ]);
        }
    }

    public function success(){
        $success_message = $_SESSION['panda_success_message'];
        unset($_SESSION['panda_success_message']);
        return $this->template_engine->render("$this->views/posts/success.latte", [
            "success_message" => $success_message
        ]);
    }

    public function error(){
        $error_message = $_SESSION['panda_error_message'];
        unset($_SESSION['panda_error_message']);
        return $this->template_engine->render("$this->views/posts/error.latte", [
            "error_message" => $error_message
        ]);
    }

    public function delete(){
        global $pandadb;
        global $router;

        try {
            $id = $_POST["_id"];

            $post = $pandadb->selectCollection("posts")->findOne(["_id" => new ObjectId($id)]);

            if ($post['status'] === "published") {
                $pandadb->selectCollection("posts")->updateOne(
                    ["_id" => new ObjectId($id)],
                    [ '$set' => [ 'status' => 'deleted' ]]
                );
                return $router->simpleRedirect("/admin/posts");
            } else {
                $pandadb->selectCollection("posts")->deleteOne(
                    ["_id" => new ObjectId($id)]
                );

                return $router->simpleRedirect("/admin/posts/success", [
                    "success_message" => "Post deleted successfully"
                ]);
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/posts/error", [
                "error_message" => $error_message
            ]);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/posts/error", [
                "error_message" => $error_message
            ]);
        }
    }
}
