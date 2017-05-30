<?php if (!defined('APP_PATH')) exit('No direct script access allowed');
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class MessageModel extends Model
{
    public function __construct()
    {
        $this->app = load('Loader')->wechat();
    }

    public function response()
    {
        $server = $this->app->server;

        $logger = new Logger('wx_msg');

        $logger->pushHandler(new StreamHandler(__DIR__.'/../wx_msg_' . date('Y-m-d') . '.log', Logger::DEBUG));

        $logger->info('msg', $message);
        
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