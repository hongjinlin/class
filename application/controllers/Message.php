<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');
class MessageController extends Controller {
    public function indexAction(){
        $mMessage = new MessageModel();
        $mMessage->response();
    }

}