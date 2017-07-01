<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\QrCode;

class ClassModel extends Model
{
    public function replay($openid)
    {
        $this->makeUserImg($openid);

        $mUplad = new UploadModel();
        $uploadData = $mUplad->uploadTempImg( APP_PATH . "/../public/images/test.jpeg" );
        $mediaId = $uploadData->media_id;

        $mReply = new ReplyModel();
        return $mReply->replayImg($mediaId);


    }

    public function makeUserImg($openid)
    {
        $mWxUser = new WxUserModel($openid);
        $userInfo = $mWxUser->userInfo();
        $nickname = json_decode(preg_replace("#(\\\ue[0-9a-f]{3}|\\\ud83e|\\\udd14)#ie", "", json_encode($userInfo->nickname)));

        //头像下载
        $headImgUrl = rtrim($userInfo->headimgurl, '0') . '96';
        $ch = curl_init($headImgUrl);
        $fp = fopen(APP_PATH . '/../public/images/head.png', 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $mQrcode = new QrcodeModel();
        $url = $mQrcode->temporaryUrl(666);

        //QR code
        $qrCode = new QrCode($url);
        $qrCode->setSize(145);
        $qrCode->writeFile(APP_PATH . '/../public/images/qrcode.png');

        Image::configure(array('driver' => 'imagick'));
        $headImg = Image::make(APP_PATH . '/../public/images/head.png');
        $img = Image::make(APP_PATH . '/../public/images/class.jpeg');
        $img->text($nickname, 160, 75, function($font) {
            $font->file(APP_PATH . '/../public/font/simsun.ttf');
            $font->size(24);
            $font->color('#fdf6e3');
            $font->align('left');
            $font->valign('top');
        });
        $img->insert(APP_PATH . '/../public/images/qrcode.png', 'top-left', 70, 782);
        $img->insert($headImg, 'top-left', 40, 38)->save(APP_PATH . '/../public/images/test.jpeg');
    }

}