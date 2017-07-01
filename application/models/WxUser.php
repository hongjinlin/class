<?php  if ( ! defined('APP_PATH')) exit('No direct script access allowed');

class WxUserModel extends Model
{
    public function __construct($openid)
    {
        $this->openid = $openid;
        $this->app = load('Loader')->wechat();
        $this->userService = $this->app->user;
    }

    public function userInfo()
    {
        return $this->userService->get($this->openid);
    }




}