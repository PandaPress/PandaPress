<?php

namespace Panda\Admin\Controllers;



class SessionController extends BaseController {

    public function __construct() {
        parent::__construct();
    }


    public function clear() {
        $session_keys = $_POST['session_keys'];
        unset_session_keys($session_keys);
        header('Content-Type: application/json');
        echo json_encode([
            "code" => 0,
            "success" => true,
            "data" => null,
            'message' => "Session data is cleared"
        ]);
    }
}
