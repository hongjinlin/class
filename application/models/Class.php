<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

class ClassModel extends Model
{
    public function replay()
    {
        $mUplad = new UploadModel();
        $uploadData = $mUplad->uploadTempImg( APP_PATH . "/../public/images/class.jpeg" );
        $mediaId = $uploadData->media_id;

        $mReply = new ReplyModel();
        return $mReply->replayImg();


    }

}