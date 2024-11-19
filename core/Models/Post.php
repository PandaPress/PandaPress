<?php

namespace Panda\Models;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;



class Post {

    private string $collection_name = "posts";
    private Collection $collection;
    private Collection $category_collection;

    public function __construct() {
        global $pandadb;
        $this->collection = $pandadb->selectCollection($this->collection_name);
        $this->category_collection = $pandadb->selectCollection("categories");
    }

    public function all($params = []): array {

        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 25;
        $skip = ($page - 1) * $limit;

        $tag = isset($params['tag']) ? $params['tag'] : null;

        $type = isset($params['type']) ? $params['type'] : null;


        $pipeline = [
            [
                '$addFields' => [
                    "category_id" => [
                        '$cond' => [
                            'if' => ['$eq' => [['$type' => '$category'], 'objectId']],
                            'then' => ['$toString' => '$category'],
                            'else' => '$category'
                        ]
                    ]
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'categories',
                    'let' => ['category_id' => '$category_id'],
                    'pipeline' => [
                        [
                            '$match' => [
                                '$expr' => [
                                    '$or' => [
                                        ['$eq' => ['$_id', '$$category_id']],
                                        ['$eq' => [['$toString' => '$_id'], '$$category_id']]
                                    ]
                                ]
                            ]
                        ]
                    ],
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

        // Add pagination stages
        $countPipeline = $pipeline; // php array is copy-on-write, don't worry 
        $countPipeline[] = ['$count' => 'total'];

        $pipeline[] = ['$skip' => $skip];
        $pipeline[] = ['$limit' => $limit];

        $documents = $this->collection->aggregate($pipeline);
        $totalCountResult = $this->collection->aggregate($countPipeline)->toArray();
        $totalCount = $totalCountResult[0]['total'] ?? 0;

        $posts = [];

        foreach ($documents as $document) {
            $post = [
                "_id" => (string) $document["_id"],
                "title" => $document["title"],
                "slug" => $document["slug"],
                // !TODO: https://github.com/PandaPress/PandaPress/issues/1
                "content" => htmlspecialchars(strip_tags($document["content"]), ENT_QUOTES, 'UTF-8'),
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

        return [
            "posts" => $posts,
            "totalCount" => $totalCount
        ];
    }

    public function findById(string $id): array|object|null {
        return $this->collection->findOne(["_id" => new ObjectId($id)]);
    }

    public function findBySlug(string $slug): array|object|null {
        // 1. Find the post by slug
        $post = $this->collection->findOne(["slug" => $slug]);

        if ($post) {
            // 2. Convert MongoDB ID to string for API compatibility
            $post['_id'] = (string) $post['_id'];

            // 3. Look up the category from categories collection
            if (isset($post['category'])) {
                // Ensure we're comparing with integer 0, not string "0"
                if ($post['category'] === 0 || $post['category'] === "0") {
                    $post['category'] = $this->category_collection->findOne(['_id' => 0]);
                } else {
                    try {
                        $categoryId = new ObjectId($post['category']);
                        $post['category'] = $this->category_collection->findOne(["_id" => $categoryId]);
                    } catch (\Exception $e) {
                        // If category ID is invalid, set category to null
                        global $logger;
                        $logger->error("Error looking up category: " . $e->getMessage());
                        $post['category'] = null;
                    }
                }
            }
        }
        return $post;
    }

    public function findByCategory(string $category): array {
        return iterator_to_array($this->collection->find(["category" => $category]));
    }

    public function findByTag(string $tag): array {
        return iterator_to_array($this->collection->find(["tags" => ['$in' => [$tag]]]));
    }

    public function create(array $data): array {
        try {
            $result = $this->collection->insertOne([
                "title" => $data["title"],
                "slug" => $data["slug"],
                "status" => $data["status"],
                "author" => $data["author"],
                "content" => $data["content"],
                "tags" => $data["tags"],
                "type" => $data["type"],
                "category" => $data["category"], // !TODO: should save the entire category object here
                "updated_at" => time(),
                "created_at" => time()
            ]);

            return [
                "success" => $result->getInsertedCount() > 0,
                "message" => "Post created successfully"
            ];
        } catch (\Throwable $th) {
            global $logger;
            $logger->error("Error creating post: " . $th->getMessage());
            return [
                "success" => false,
                "message" => "Error creating post"
            ];
        }
    }

    public function softDeleteById(string $id): void {
        $this->collection->updateOne(
            ["_id" => new ObjectId($id)],
            ['$set' => ['status' => 'deleted']]
        );
    }

    public function hardDeleteById(string $id): void {
        $this->collection->deleteOne(["_id" => new ObjectId($id)]);
    }
}
