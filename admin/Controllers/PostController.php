<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;
use Latte\Exception\CompileException;
use MongoDB\BSON\ObjectId;
use Panda\Models\Post;

class PostController extends BaseController {

    private Post $post;

    public function __construct() {
        parent::__construct();
        $this->post = new Post();
    }

    public function index() {
        $posts = $this->post->all(['type' => 'post']);
        return $this->template_engine->render("$this->views/posts/index.latte", $this->appendUserData([
            "posts" => $posts['posts'],
            "totalCount" => $posts['totalCount']
        ]));
    }

    public function pages() {
        $posts = $this->post->all(['type' => 'page']);
        return $this->template_engine->render("$this->views/posts/pages.latte", $this->appendUserData([
            "posts" => $posts['posts'],
            "totalCount" => $posts['totalCount']
        ]));
    }

    public function compose() {
        global $pandadb;
        return $this->template_engine->render("$this->views/posts/compose.latte", $this->appendUserData([
            "categories" => $pandadb->selectCollection("categories")->find()
        ]));
    }

    // check if slug is unique
    public function save() {
        global $router;

        try {
            $title = $_POST["title"];
            $slug = $_POST["slug"];
            $content = $_POST["editor"];
            $status = $_POST["status"]; // "draft" or "published" or "deleted"
            $author = isset($_POST["author"]) ? $_POST["author"] : "admin";

            $_tags = $_POST["tags"];
            $tags = isset($_tags) && strlen($_tags) > 0 ? explode(',', $_tags) : [];

            $category = isset($_POST["category"]) ? $_POST["category"] : "uncategorized";
            $type = isset($_POST["type"]) ? $_POST["type"] : "post";

            $requiredFields = ['title', 'slug', 'editor'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                    return $router->simpleRedirect("/admin/posts/compose", [
                        "error_message" => "There are missing fields",
                        "post_form_data" => $_POST
                    ]);
                }
            }

            $result = $this->post->create([
                "title" => $title,
                "slug" => $slug,
                "status" => $status,
                "author" => $author,
                "content" => $content,
                "tags" => $tags,
                "type" => $type,
                "category" => $category
            ]);

            if ($result["success"]) {
                return $router->simpleRedirect("/admin/success", [
                    "success_message" => "Post saved successfully"
                ]);
            } else {
                return $router->simpleRedirect("/admin/error", [
                    "error_message" => $result["message"]
                ]);
            }
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



    public function delete() {
        global $router;

        try {
            $id = $_POST["_id"];

            $post = $this->post->findById($id);

            if ($post['status'] === "published") {
                $this->post->softDeleteById($id);
                return $router->simpleRedirect("/admin/posts");
            } else if ($post['status'] === "deleted") {
                $this->post->hardDeleteById($id);

                return $router->simpleRedirect("/admin/success", [
                    "success_message" => "Post deleted successfully"
                ]);
            } else {
                return $router->simpleRedirect("/admin/error", [
                    "error_message" => "Post is not in a valid status"
                ]);
            }
        } catch (Exception | \Throwable $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        }
    }

    // ! this is not update action, it just renders the update form
    public function update($id) {
        global $pandadb;
        global $router;

        try {

            $post = $pandadb->selectCollection("posts")->findOne(["_id" => new ObjectId($id)]);
            $categories = $pandadb->selectCollection("categories")->find()->toArray();
            $category = $pandadb->selectCollection("categories")->findOne(["_id" => new ObjectId($post['category'])]);

            return $this->template_engine->render("$this->views/posts/update.latte", $this->appendUserData([
                "post" => $post,
                "categories" => $categories,
                "current_category" => $category,
                "tags" => iterator_to_array($post['tags'])
            ]));
        } catch (CompileException | \Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        }
    }

    public function upsave() {
        global $pandadb;
        global $router;

        if (!isset($_POST["id"])) {
            return $router->simpleRedirect("/admin/error", [
                "error_message" => "Post id is missing"
            ]);
        }

        try {
            $id = $_POST["id"];
            $title = $_POST["title"];
            $slug = $_POST["slug"];
            $content = $_POST["editor"];
            $status = $_POST["status"]; // "draft" or "published" or "deleted"
            $author = isset($_POST["author"]) ? $_POST["author"] : "admin";

            $_tags = $_POST["tags"];
            $tags = isset($_tags) && strlen($_tags) > 0 ? explode(',', $_tags) : [];

            $category = $_POST["category"];

            $pandadb->selectCollection("posts")->updateOne(
                ["_id" => new ObjectId($id)],
                [
                    '$set' => [
                        'title' => $title,
                        'slug' => $slug,
                        'content' => $content,
                        'status' => $status,
                        'author' => $author,
                        'tags' => $tags,
                        'category' => $category,
                        'updated_at' => time()
                    ]
                ]
            );

            return $router->simpleRedirect("/admin/success", [
                "success_message" => "Post updated successfully"
            ]);
        } catch (CompileException | \Exception $e) {
            $error_message = $e->getMessage();
            return $router->simpleRedirect("/admin/error", [
                "error_message" => $error_message
            ]);
        }
    }


    public function tags() {


        global $pandadb;

        $cursor = $pandadb->posts->aggregate([
            ['$unwind' => '$tags'],
            ['$group' => ['_id' => '$tags']],
            ['$sort' => ['_id' => 1]]
        ]);

        $tags = [];
        foreach ($cursor as $doc) {
            $tags[] = $doc['_id'];
        }

        return $this->template_engine->render("$this->views/posts/tags.latte", $this->appendUserData([
            "tags" => $tags
        ]));
    }

    public function tag($tag) {
        $posts = $this->post->all(['tag' => $tag]);

        return $this->template_engine->render("$this->views/posts/tag.latte", $this->appendUserData([
            "posts" => $posts['posts'],
            "totalCount" => $posts['totalCount'],
            "tag" => $tag
        ]));
    }


    // remove a tag from all posts and pages
    public function removeTag4All() {
        global $pandadb;
        global $router;

        $tag = $_POST["tag"];

        try {
            $pandadb->posts->updateMany(
                ['tags' => $tag],
                ['$pull' => ['tags' => $tag]]
            );

            echo json_encode([
                "code" => 0,
                "success" => true,
                "data" => null,
                'message' => "tag is removed from all posts and pages"
            ]);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            echo json_encode([
                "code" => -1,
                "success" => false,
                "data" => null,
                'message' => $error_message
            ]);
        }
    }

    // !TODO: the two functions below are never used yet
    // remove a tag from a post or page
    public function removeTag4One() {
        global $pandadb;
        global $router;

        $tag = $_POST["tag"];
        $id = $_POST["id"];

        try {
            $pandadb->posts->updateOne(
                ['_id' => new ObjectId($id)],
                ['$pull' => ['tags' => $tag]]
            );

            echo json_encode([
                "code" => 0,
                "success" => true,
                "data" => null,
                'message' => "tag is removed from the post"
            ]);
        } catch (Exception | \Throwable $e) {
            $error_message = $e->getMessage();
            echo json_encode([
                "code" => -1,
                "success" => false,
                "data" => null,
                'message' => $error_message
            ]);
        }
    }
}
