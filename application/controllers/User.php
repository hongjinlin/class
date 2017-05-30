<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');
class UserController extends Controller {

    // public function indexAction(){

    //     if($_SESSION['user_info']){

    //         $this->redirect("/");

    //     }else{

    //         if(!$_POST['username'] && !$_POST['password']){

    //         }

    //     }

    //    //$this->getView()->assign('user', $_SESSION['user_info']);
    // }

    public function registerAction(){

        Yaf_Dispatcher::getInstance()->disableView();

        if(!$_POST['username'] || !$_POST['password']){

            $this->echoJson(1006);

            return;

        }

        $mUser = new UserModel();

        $rzt = $mUser->register($_POST['username'], $_POST['password']);

        if($rzt){

            $this->echoJson(1000);

            return;

        }

        $this->echoJson(2000);

    }

    public function loginAction(){

        Yaf_Dispatcher::getInstance()->disableView();

        if($_SESSION['user_info']){

            $this->echoJson(1011);

            return;
            
        }

        if(!$_POST['username'] || !$_POST['password']){

            $this->echoJson(1006);

            return;

        }

        $mUser = new UserModel();

        $rzt = $mUser->login($_POST['username'], $_POST['password']);

        if($rzt){

            $_SESSION['user_info'] = $mUser->getUserInfo();

            $this->echoJson(1000);

            return;

        }

        $this->echoJson(2000);

    }

    public function checkUsernameAction(){

        Yaf_Dispatcher::getInstance()->disableView();

        if(!$_POST['username']){

            $this->echoJson(1006);

            return;

        }



    }

}