<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

	public function _init(){

		//load core
		Yaf_Loader::import('ZApplication.php');
		
		//create core library and set registry
		Yaf_Registry::set('debug', new ZDebug());
		Yaf_Registry::set('config', new ZConfig());
		Yaf_Registry::set('input', new ZInput());
		Yaf_Registry::set('loader', new ZLoader());

		load('Config')->load('database');

	}

    public function _initManager(Yaf_Dispatcher $displatcher) {
		$checkPlugin = new CheckPlugin();
		$displatcher->registerPlugin($checkPlugin);
	}
	
	public function _initRouter(Yaf_Dispatcher $displatcher) {
		$router = $displatcher->getRouter();
		$route = new Yaf_Route_Simple("l", "c", "m");
		$router->addRoute("name", $route);
	}
}
