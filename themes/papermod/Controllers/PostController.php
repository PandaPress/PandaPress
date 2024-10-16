<?php

namespace Panda\Theme\Controllers;

use Panda\Controllers\BaseController;
use Panda\Models\Post;

class PostController extends BaseController {

    private Post $post;
    public function __construct() {
        parent::__construct();
        $this->post = new Post();
    }

    public function show($slug) {
        $post = $this->post->findBySlug($slug);
        return $this->template_engine->render($this->current_theme_views . "/post.latte", ["post" => $post]);
    }

    public function archive() {

        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query,  $query);

        $params = [
            "page" => $query["page"] ?? 1,
            "ppp" => $query["ppp"] ?? 25, // posts per page
            "type" => $query["type"] ?? "post",
        ];

        $posts = $this->post->all($params);

        // generate the proper data structure for the archive page
        // [
        //     "2024" => [
        //         "months" => [
        //             "01" => [
        //                 {// some post 1 },
        //                 {// some post 2 },
        //                 {// some post 3 },
        //             ]
        //         ]
        //     ]
        // ]
        $_posts = [];
        foreach ($posts['posts'] as $post) {
            $created_at = $post['created_at'];

            $year = date('Y', $created_at);

            $month = date('m', $created_at);
            $dateObj   = \DateTime::createFromFormat('!m', $month);
            $month = $dateObj->format('F'); // January, February, etc.

            if (!isset($_posts[$year])) {
                $_posts[$year] = [];
            }
            if (!isset($_posts[$year]['months'][$month])) {
                $_posts[$year]['months'][$month] = [];
            }
            $_posts[$year]['months'][$month][] = $post;
        }



        return $this->template_engine->render(
            $this->current_theme_views . "/archive.latte",
            $this->appendMetaData(
                [
                    "active_link" => "archive",
                    "posts" => $_posts,
                    "totalCount" => $posts['totalCount']
                ]
            )
        );
    }
}
