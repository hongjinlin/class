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

        // Create a basic QR code
        $qrCode = new QrCode($url);
        $qrCode->setSize(145);

        // Save it to a file
        $qrCode->writeFile(APP_PATH . '/../public/images/qrcode.png');

        Image::configure(array('driver' => 'imagick'));
        $img = Image::make(APP_PATH . '/../public/images/class.jpeg');
        $img->text('中国人，哇哈哈', 160, 75, function($font) {
            $font->file(APP_PATH . '/../public/font/simsun.ttf');
            $font->size(24);
            $font->color('#fdf6e3');
            $font->align('left');
            $font->valign('top');
        });
        $img->insert(APP_PATH . '/../public/images/qrcode.png', 'top-left', 70, 782)->save(APP_PATH . '/../public/images/test.jpeg');

    }

}