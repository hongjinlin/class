<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

class QrcodeModel extends Model
{
    public function __construct()
    {
        $this->app = load('Loader')->wechat();
        $this->qrcode = $this->app->qrcode;
    }

    public function temporaryUrl($info, $expireSeconds = 7 * 24 * 3600)
    {
        $result = $this->qrcode->temporary($info, $expireSeconds);
        return $result->url;
    }



}