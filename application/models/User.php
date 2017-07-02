<?php  if ( ! defined('APP_PATH')) exit('No direct script access allowed');

class UserModel extends Model
{
    public function __construct(){
        $this->dbr = load('Loader')->database('dbr');
        $this->dbw = load('Loader')->database('dbw');
    }

    public function register($data){
        if (!$data['openid']) {
            return false;
        }

        $config = load('Config')->get('config');

        $actId = $config['activity_id'];

        $this->_actId = $actId;

    	$this->dbw->set('openid', $data['openid']);
    	$this->dbw->set('nickname', base64_encode($data['nickname']));
    	$this->dbw->set('headimg', $data['headimgurl']);
        $this->dbw->set('recommend', (int)$data['recommend']);
        $this->dbw->set('media_id', $data['media_id']);
    	$this->dbw->set('activity_id', $actId);
    	$this->dbw->set('regtime', time());
    	$this->dbw->from('user');

    	$insertId = $this->dbw->insert();

    	if($insertId){
    	    $this->_uid = $this->dbw->insert_id();
    		return true;
    	}else {
    		return false;
    	}

    }

    public function getUserById($id){

    	$this->dbr->where('id', (int)$id);
    	$this->dbr->from('user');

    	$this->_userInfo = $this->dbr->get()->row_array();
    	$this->_uid = $this->_userInfo['id'];
    	$this->_openid = $this->_userInfo['openid'];

    	return $this->_userInfo;
    }

    public function getUserByOpenid($openid){

        $this->dbr->where('openid', $openid);
        $this->dbr->where('activity_id', $this->_actId);

        $this->dbr->from('user');

        $this->_userInfo = $this->dbr->get()->row_array();
        $this->_uid = $this->_userInfo['id'];
        $this->_openid = $this->_userInfo['openid'];

        return $this->_userInfo;
    }

    public function getUserInfo(){
        return $this->_userInfo;
    }

    public function getMediaId() {
        return $this->_userInfo['media_id'];
    }

    public function isUserExist($openid) {
        if ($this->getUserByOpenid($openid)) {
            return true;
        }
        return false;
    }

    public function getUid() {
        return $this->_uid;
    }

    public function updateMediaId($mediaId) {
        if (!$mediaId) {
            return false;
        }
        $this->dbw->set('media_id', $mediaId);
        $this->dbw->where('id', $this->_uid);
        $this->dbw->from('user');

        $this->dbw->update();
    }



}