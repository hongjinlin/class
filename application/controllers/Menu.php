<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');

class MenuController extends Controller {

    public function init() {
        $this->mMenu = new MenuModel();
    }
    public function getAction() {

        $menus = $this->mMenu->getCurrent();
        var_dump($menus);
    }

    public function setAction() {
        $buttons = [
            [
                "type" => "click",
                "name" => "我的海报",
                "key"  => "V1001_MY_PIC"
            ]
        ];
        var_dump($this->mMenu->set($buttons));
    }

}