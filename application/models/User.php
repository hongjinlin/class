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

        $config = load('Config');

        $actId = $config->get('activity_id');

    	$this->dbw->set('openid', $data['openid']);
    	$this->dbw->set('nickname', base64_encode($data['nickname']));
    	$this->dbw->set('headimg', $data['headimgurl']);
        $this->dbw->set('recommend', $data['recommend']);
        $this->dbw->set('media_id', $data['media_id']);
    	$this->dbw->set('activity_id', $actId);
    	$this->dbw->set('regtime', time());
    	$this->dbw->from('user');

    	$insertId = $this->dbw->insert();

    	if($insertId){
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



}