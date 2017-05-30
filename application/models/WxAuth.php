<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

class WxAuthModel extends Model
{
    public function __construct()
    {
        $this->app = load('Loader')->wechat();
    }

    public function response($scope)
    {
        return $this->app->oauth->scopes([$scope])->redirect();
    }

    public function userInfo()
    {
        return $this->app->oauth->user()->getOriginal();
    }

    public function getOpenid()
    {

    }

}