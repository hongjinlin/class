<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\QrCode;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ClassModel extends Model
{
    public function replay($openid, $recommend)
    {
        $mWxUser = new WxUserModel($openid);
        $userInfo = $mWxUser->userInfo();

        $data['headimgurl'] = $userInfo->headimgurl;
        $data['nickname'] = $userInfo->nickname;

        $log = new Logger('register by CLICK');
        $log->pushHandler(new StreamHandler(APP_PATH . "/logs/register_fail.log", Logger::ERROR));

        $mUser = new UserModel();
        if (!$mUser->getUserByOpenid($openid)) {

            $data['openid'] = $openid;
            $data['recommend'] = $recommend;

            if (!$mUser->register($data)) {
                $log->error('register fail', array($userInfo->nickname));
            }

            $uid = $mUser->getUid();
            $this->makeUserImg($openid, $userInfo, $uid);

            $mUplad = new UploadModel();
            $uploadData = $mUplad->uploadTempImg( APP_PATH . "/../public/images/user/" . $openid . ".jpeg" );
            $mediaId = $uploadData->media_id;

            $mUser->updateMediaId($mediaId);

        } else {
            $mediaId = $mUser->getMediaId();
            if (!$mediaId) {
                $uid = $mUser->getUid();
                $this->makeUserImg($openid, $userInfo, $uid);

                $mUplad = new UploadModel();
                $uploadData = $mUplad->uploadTempImg( APP_PATH . "/../public/images/user/" . $openid . ".jpeg" );
                $mediaId = $uploadData->media_id;

                if (!$mediaId) {
                    $log->error('register fail mediaId', array($mediaId));
                    return;
                }
                $mUser->updateHeadimgAndNickname($data)
                $mUser->updateMediaId($mediaId);

            }
        }

        $mReply = new ReplyModel();
        return $mReply->replayImg($mediaId);

    }

    public function replayForSubscribe($openid, $recommend)
    {
        $log = new Logger('register by Subscribe');
        $log->pushHandler(new StreamHandler(APP_PATH . "/logs/register_fail.log", Logger::ERROR));
        $mUser = new UserModel();
        if (!$mUser->getUserByOpenid($openid)) {

            $data['openid'] = $openid;
            $data['recommend'] = $recommend;

            if (!$mUser->register($data)) {
                $log->error('register fail', array($userInfo->nickname));
            }
            return 'SB!!';
        } else {
            $mediaId = $mUser->getMediaId();
            if (!$mediaId) {
                return 'SB!!!';
            }
            $mReply = new ReplyModel();
            return $mReply->replayImg($mediaId);
        }

    }

    public function makeUserImg($openid, $userInfo, $uid)
    {

        //头像下载
        $headImgUrl = rtrim($userInfo->headimgurl, '0') . '96';
        $ch = curl_init($headImgUrl);
        $fp = fopen(APP_PATH . '/../public/images/head/' . $openid . '.png', 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        //QR code
        $mQrcode = new QrcodeModel();
        $url = $mQrcode->temporaryUrl($uid);

        $qrCode = new QrCode($url);
        $qrCode->setSize(145);
        $qrCode->writeFile(APP_PATH . '/../public/images/qrcode/' . $openid . '.png');

        Image::configure(array('driver' => 'imagick'));
        $headImg = Image::make(APP_PATH . '/../public/images/head/' . $openid . '.png');
        $img = Image::make(APP_PATH . '/../public/images/class.jpeg');
        $img->text($userInfo->nickname, 160, 75, function($font) {
            $font->file(APP_PATH . '/../public/font/simsun.ttf');
            $font->size(24);
            $font->color('#fdf6e3');
            $font->align('left');
            $font->valign('top');
        });
        $img->insert(APP_PATH . '/../public/images/qrcode/' . $openid . '.png', 'top-left', 70, 782);
        $img->insert($headImg, 'top-left', 40, 38)->save(APP_PATH . '/../public/images/user/' . $openid . '.jpeg');
    }

}