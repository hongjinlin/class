<?php if (!defined('APP_PATH')) exit('No direct script access allowed');
use EasyWeChat\Message\Image;
class ReplyModel extends Model
{
    function __construct()
    {

    }

    public function replayImg($mediaId)
    {
        return new Image(['media_id' => $mediaId]);
    }

}