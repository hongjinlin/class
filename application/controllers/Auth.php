<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');
class AuthController extends Controller {
    public function indexAction(){
        $mAuth = new WxAuthModel();
        if($_GET['code']){
            $userInfo = $mAuth->userInfo();
            if(!$userInfo['openid']){
                var_dump($userInfo);
                return;
            }
            $_SESSION['user_info']['openid'] = $userInfo['openid'];
            $mUser = new UserModel();

            return;
        }
        $response = $mAuth->response('snsapi_userinfo');
        $response->send();
    }

}