<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

class MessageModel extends Model
{
    private $_openid;

    public function __construct()
    {
        $this->app = load('Loader')->wechat();
    }

    public function response()
    {
        $server = $this->app->server;

        $server->setMessageHandler(function ($message) {
             $this->_openid = $message->FromUserName; // 用户的 openid
            if (strcmp($message->MsgType, 'text') === 0) {
                if (strcmp($message->Content, '上课') === 0) {
                    $mClass = new ClassModel();
                    return $mClass->replay();
                }
            } elseif (strcmp($message->MsgType, 'event') === 0 && strcmp($message->Event, 'CLICK') === 0) {
                if (strcmp($message->EventKey, 'V1001_MY_PIC') === 0) {
                    $mClass = new ClassModel();
                    return $mClass->replay();
                }
            }
            return "SB弯弯";
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
        return $this->_openid;
    }

}