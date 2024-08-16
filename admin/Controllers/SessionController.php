<?php

namespace Panda\Admin\Controllers;



class SessionController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }


    public function clear() {
        $session_keys = $_POST['session_keys'];
        unset_session_keys($session_keys);
        return json_encode([
            'result' => "Session data is cleared"
        ]);
    }
}