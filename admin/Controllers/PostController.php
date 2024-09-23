<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;
use Latte\Exception\CompileException;
use MongoDB\BSON\ObjectId;


class PostController extends BaseController {


    public function __construct() {
        parent::__construct();
    }

    private function posts($params) {

        // $page = isset($params['page']) ? $params['page'] : 1;
        // $limit = isset($params['limit']) ? $params['limit'] : 25;
        // $skip = ($page - 1) * $limit;

        $tag = isset($params['tag']) ? $params['tag'] : null;

        $type = isset($params['type']) ? $params['type'] : null;

        global $pandadb;

        $pipeline = [
            [
                '$addFields' => [
                    "cid" => ['$toObjectId' => '$category']
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'categories',
                    'localField' => 'cid',
                    'foreignField' => '_id',
                    'as' => 'category_obj'
                ]
            ]
        ];

        // tag filter all documents that have the tag
        // the documents could be posts or pages
        if ($tag) {
            array_unshift($pipeline, ['$match' => ['tags' => ['$in' => [$tag]]]]);
        }

        // type filter all documents that are pages
        if ($type === 'page') {
            array_unshift($pipeline, ['$match' => ['type' => 'page']]);
        }

        // type filter all documents that are posts
        if ($type === 'post') {
            array_unshift($pipeline, ['$match' => [
                '$or' => [
                    ['type' => 'post'],
                    ['type' => ['$exists' => false]]
                ]
            ]]);
        }

        // !TODO pagination
        $documents = $pandadb->selectCollection("posts")->aggregate($pipeline);

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
                "category" => $document['category_obj'][0],
                "tags" => iterator_to_array($document["tags"]),
                "type" => isset($document["type"]) ? $document["type"] : "post"
            ];
            array_push($posts, $post);
        }

        return $posts;
    }


    public function index() {
        $posts = $this->posts(['type' => 'post']);

        return $this->template_engine->render("$this->views/posts/index.latte", $this->getFullDataForTemplate([
            "posts" => $posts
        ]));
    }

    public function pages() {
        $posts = $this->posts(['type' => 'page']);

        return $this->template_engine->render("$this->views/posts/pages.latte", $this->getFullDataForTemplate([
            "posts" => $posts
        ]));
    }

    public function compose() {
        global $pandadb;
        return $this->template_engine->render("$this->views/posts/compose.latte", $this->getFullDataForTemplate([
            "categories" => $pandadb->selectCollection("categories")->find()
        ]));
    }

    // check if slug is unique
    public function save() {
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

            $result = $pandadb->selectCollection("posts")->insertOne([
                "title" => $title,
                "slug" => $slug,
                "status" => $status,
                "author" => $author,
                "content" => $content,
                "tags" => $tags,
                "type" => $type,
                "category" => $category, // !TODO: should save the entire category object here
                "updated_at" => time(),
                "created_at" => time()
            ]);

            if ($result->getInsertedCount() > 0) {
                return $router->simpleRedirect("/admin/success", [
                    "success_message" => "Post saved successfully"
                ]);
            } else {
                return $router->simpleRedirect("/admin/error", [
                    "error_message" => "Post creation failed"
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
        global $pandadb;
        global $router;

        try {
            $id = $_POST["_id"];

            $post = $pandadb->selectCollection("posts")->findOne(["_id" => new ObjectId($id)]);

            if ($post['status'] === "published") {
                $pandadb->selectCollection("posts")->updateOne(
                    ["_id" => new ObjectId($id)],
                    ['$set' => ['status' => 'deleted']]
                );
                return $router->simpleRedirect("/admin/posts");
            } else {
                $pandadb->selectCollection("posts")->deleteOne(
                    ["_id" => new ObjectId($id)]
                );

                return $router->simpleRedirect("/admin/success", [
                    "success_message" => "Post deleted successfully"
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

    public function update($id) {
        global $pandadb;
        global $router;

        try {

            $post = $pandadb->selectCollection("posts")->findOne(["_id" => new ObjectId($id)]);
            $categories = $pandadb->selectCollection("categories")->find()->toArray();
            $category = $pandadb->selectCollection("categories")->findOne(["_id" => new ObjectId($post['category'])]);

            return $this->template_engine->render("$this->views/posts/update.latte", $this->getFullDataForTemplate([
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

        return $this->template_engine->render("$this->views/posts/tags.latte", $this->getFullDataForTemplate([
            "tags" => $tags
        ]));
    }

    public function tag($tag) {
        $posts = $this->posts(['tag' => $tag]);

        return $this->template_engine->render("$this->views/posts/tag.latte", $this->getFullDataForTemplate([
            "posts" => $posts,
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
        } catch (Exception | \Exception $e) {
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
