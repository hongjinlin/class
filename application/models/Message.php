<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

class MessageModel extends Model
{
    public function __construct()
    {
        $this->app = load('Loader')->wechat();
    }

    public function response()
    {
        $server = $this->app->server;
        $server->setMessageHandler(function ($message) {
            // $message->FromUserName // 用户的 openid
            // $message->MsgType // 消息类型：event, text....
            return "您好！欢迎关注我!";
        });
        $response = $server->serve();
        $response->send();
    }

    public function userInfo()
    {
        return $this->app->oauth->user()->getOriginal();
    }

    public function getOpenid()
    {

    }

}