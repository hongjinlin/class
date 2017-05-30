<?php  if ( ! defined('APP_PATH')) exit('No direct script access allowed');

class UserModel extends Model
{
    public function __construct(){
        $this->dbr = load('Loader')->database('dbr');
        $this->dbw = load('Loader')->database('dbw');
    }

    public function register($data){

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    	if(!$email || !$password){
    		return false;
    	}

    	if($this->isemailExist($email)){

    		return false;

    	}

        $config = load('Config')->load('config');

        $hashAlgorithm = $config['hash_algorithm'];
        $hashOptionCost = $config['hash_option_cost']

    	$passwordHash = password_hash($password, $hashAlgorithm, $hashOptionCost);

        if($passwordHash === false){
            $this->_error = 'Password Hash Failed';
            return false;
        }

    	$this->dbw->set('email', $email);
    	$this->dbw->set('password', $passwordHash);
    	$this->dbw->set('regtime', time());
    	$this->dbw->from('user');

    	$insertId = $this->dbw->insert();

    	if($insertId){
    		return true;
    	}else {
    		return false;
    	}

    }

    public function login($email, $password){

    	if(!$email || !$password){
    		return false;
    	}

    	if(!$this->getUserByName($email)){
            return false;
        }

        if(password_verify($password, $this->_userInfo['password']) !== false){
            return true;
        }

        return false;
    }

    public function getUserByName($email){
    	if($this->_userInfo){
    		return $this->_userInfo;
    	}

    	$this->dbr->where('email', $this->_cleanInput($email));
    	$this->dbr->from('user');

    	$this->_userInfo = $this->dbr->get()->row_array();
    	$this->_uid = $this->_userInfo['id'];

    	return $this->_userInfo;
    }

    public function getUserById($id){
    	if($this->_userInfo){
    		return $this->_userInfo;
    	}

    	$this->dbr->where('id', $this->_cleanInput($id));
    	$this->dbr->from('user');

    	$this->_userInfo = $this->dbr->get()->row_array();
    	$this->_uid = $this->_userInfo['id'];

    	return $this->_userInfo;
    }

    public function getUserInfo(){
    	if($this->_userInfo){
    		return $this->_userInfo;
    	}

    	return $this->getUserById($this->_uid);
    }

    public function isemailExist($email){
    	if(!$email){
    		return false;
    	}

    	$rzt = $this->getUserByName($email);

    	if($rzt){
    		return true;
    	}else{
    		return false;
    	}

    }


}