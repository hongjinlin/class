<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Controller {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/ys/index/index/index/name/root 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {

        if($_SESSION['user_info']){

            // $this->getView()->assign('user', $_SESSION['user_info']);

            // $this->getView()->assign('apps', $_SESSION['apps']);

            // $sCache = SCacheModel::getInstance();

            // $apps = $sCache->get('apps');

            // $this->getView()->assign('apps_info', $apps);

        }else{

            $this->redirect("/Login");

        }

	}

	public function testAction(){
    
		echo 'tet';

	}
}
