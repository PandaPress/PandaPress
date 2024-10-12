<?php

namespace Panda\Models;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;

class Post {

    private string $collection_name = "posts";
    private Collection $collection;

    public function __construct() {
        global $pandadb;
        $this->collection = $pandadb->selectCollection($this->collection_name);
    }

    public function all($params) {

        // $page = isset($params['page']) ? $params['page'] : 1;
        // $limit = isset($params['limit']) ? $params['limit'] : 25;
        // $skip = ($page - 1) * $limit;

        $tag = isset($params['tag']) ? $params['tag'] : null;

        $type = isset($params['type']) ? $params['type'] : null;



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
        $documents = $this->collection->aggregate($pipeline);

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

    public function findById(string $id): array|object|null {
        return $this->collection->findOne(["_id" => new ObjectId($id)]);
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
