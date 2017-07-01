<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

class MenuModel extends Model
{
    public function __construct()
    {
        $this->app = load('Loader')->wechat();
        $this->menu = $this->app->menu;
    }

    public function getCurrent()
    {
        return $this->menu->current();
    }

    public function getAll()
    {
        return $this->menu->all();
    }

    public function set($buttons)
    {
        return $this->menu->add($buttons);
    }

}