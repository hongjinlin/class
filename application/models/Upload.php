<?php if (!defined('APP_PATH')) exit('No direct script access allowed');

class UploadModel extends Model
{
    private static $temporary;
    private static $material;
    function __construct()
    {
        $this->app = load('Loader')->wechat();
        self::$temporary = $this->app->material_temporary;
        self::$material = $this->app->material;
    }

    public function uploadTempImg($path)
    {
        return self::$temporary->uploadImage($path);
    }

}