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
                    return new Image(['media_id' => 'H0778zNDRpZq9kWmnI4ZnY8WnLPpd8xq_KOqLCET_NUEzaXUJ95gLo0Ie4R-vxkk']);
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