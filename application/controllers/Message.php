<?php if ( ! defined('APP_PATH')) exit('No direct script access allowed');

use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\QrCode;

class MessageController extends Controller {
    public function indexAction() {
        $mMessage = new MessageModel();
        $mMessage->response();
    }

    public function qrcodeAction() {

        $mQrcode = new QrcodeModel();
        $url = $mQrcode->temporaryUrl(666);
        //头像下载
        $ch = curl_init('http://wx.qlogo.cn/mmopen/ajNVdqHZLLBxCoiavJwicGjlDlDweOicjrHUGibapel0ibtIiciaM1OOCZSZhZVVIgua9lwHdQ6bmasgpXia2oztbFt4LQ/96');
        $fp = fopen(APP_PATH . '/../public/images/head.png', 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        //QR code
        $qrCode = new QrCode($url);
        $qrCode->setSize(145);
        $qrCode->writeFile(APP_PATH . '/../public/images/qrcode.png');

        Image::configure(array('driver' => 'imagick'));
        $testImg = Image::make(APP_PATH . '/../public/images/head.png');
        $img = Image::make(APP_PATH . '/../public/images/class.jpeg');
        $img->text('中国人，哇哈哈', 160, 75, function($font) {
            $font->file(APP_PATH . '/../public/font/simsun.ttf');
            $font->size(24);
            $font->color('#fdf6e3');
            $font->align('left');
            $font->valign('top');
        });
        $img->insert(APP_PATH . '/../public/images/qrcode.png', 'top-left', 70, 782);
        $img->insert($testImg, 'top-left', 40, 38)->save(APP_PATH . '/../public/images/test.jpeg');

    }

}