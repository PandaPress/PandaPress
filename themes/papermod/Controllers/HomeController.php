<?php

namespace Panda\Theme\Controllers;

use Panda\Controllers\BaseController;
use Panda\Models\Post;

class HomeController extends BaseController {

    private Post $post;


    public function __construct() {
        parent::__construct();
        $this->post = new Post();
    }
    public function index() {
        return  $this->template_engine->render($this->current_theme_views . "/index.latte", $this->appendMetaData());
    }

    public function home() {
        $posts = $this->post->all(['type' => 'post']);
        return  $this->template_engine->render($this->current_theme_views . "/home.latte", $this->appendMetaData([
            'active_link' => 'home',
            ...$posts
        ]));
    }

    public function about() {
        return  $this->template_engine->render($this->current_theme_views . "/about.latte", $this->appendMetaData([
            'active_link' => 'about'
        ]));
    }

    public function profile() {
        return  $this->template_engine->render($this->current_theme_views . "/profile.latte", $this->appendMetaData([
            'active_link' => 'profile'
        ]));
    }
}
