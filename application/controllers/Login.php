<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');
class LoginController extends Controller {
    public function indexAction(){
        if($_SESSION['user_info']){
            $this->redirect("/");
        }else{
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
                // 微信，授权登录
                $this->redirect('/Auth');
            }else{
                // pc,显示登录
                $this->error('Access From Weixin');
            }
        }

       //$this->getView()->assign('user', $_SESSION['user_info']);
    }

}