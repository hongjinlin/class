<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');
class LogoutController extends Controller {
    public function indexAction(){
        $_SESSION = array();
        session_destroy();
        echo 'Login Out Success !';
    }
}
