<?php  if ( ! defined('APP_PATH')) exit('No direct script access allowed');
/**
|---------------------------------------------------------------
| Define timezone
|---------------------------------------------------------------
*/
if(!defined('TIMEZONE')) define('TIMEZONE','Asia/Shanghai');
date_default_timezone_set(TIMEZONE);

/*
|---------------------------------------------------------------------------
| Debug Modes
|---------------------------------------------------------------------------
|
| These modes are used when write log
|
*/
define('YEY_ERROR',   1);
define('YEY_WARNING', 2);
define('YEY_PARSE',   4);
define('YEY_NOTICE',  8);
define('YEY_DEBUG',  16);
define('YEY_LOG',    32);


if(!class_exists('Redis') && class_exists('ZRedis')){
    class Redis extends ZRedis{}
}

/**
|---------------------------------------------------------------
| load方法，加载类
|---------------------------------------------------------------
*/
function load($filename){
    if ( $filename == 'Config' ) {
        return Yaf_Registry::get('config');
    } elseif ($filename == 'Debug') {
        return Yaf_Registry::get('debug');
    } elseif ($filename == 'Input') {
        return Yaf_Registry::get('input');
    }else if($filename == 'Loader'){
        return Yaf_Registry::get('loader');
    }
    Yaf_Loader::import($filename);
}

// ---------------------------------------------------------------------------------------------------------------------------
/**
 * 异常类
 */
class ZException extends Exception{}

// ---------------------------------------------------------------------------------------------------------------------------
/**
 * Controller - 控制器接口
 *
 * 所有控制器请继承该接口
 *
 * @package     YEY
 * @subpackage  YEY.libraries
 * @author      YEY Dev Team
 * @category    Singleton
 */
class ZController extends Yaf_Controller_Abstract
{
    public function init(){
        if($_SESSION['user_info']){

            $_SESSION['user_info']['password'] = null;

            $this->getView()->assign('user', $_SESSION['user_info']);
            
        }
    }

    protected function success($data, $url = ''){
        $msg = array(
            'msg' => $data,
            'url' => $url,
            'tpl' => $this->_error_tpl
        );

        throw new Yaf_Exception(json_encode($msg), 1001);
    }
    protected function error($data) {
        $msg = array(
            'msg' =>$data,
            'tpl' => $this->_error_tpl
        );
        $data = json_encode($msg);
        throw new Yaf_Exception($data, 1010);
    }

    protected function appInit(){
        // if($_GET['wx_app_id'] && in_array($_GET['wx_app_id'], $_SESSION['wx_apps'])){
        //     $_SESSION['act_wx_app_id'] = $_GET['wx_app_id'];
        // }
        // $apps = SWxAppModel::getInstance()->load();

        // $this->getView()->assign('apps', $apps);
        // $this->getView()->assign('wx_app_id', $_SESSION['act_wx_app_id']);

        // $this->_app = SWxAppModel::getInstance()->get($_SESSION['act_wx_app_id']);

        // if(!$this->_app){
        //     $this->error('PController.Common.InvalidAppID');
        // }
    }

    protected function errorTpl($tpl){
        $this->_error_tpl = $tpl;
    }
}

class Controller extends ZController {

    public function echoJson($code, $msg = null, $data = array()){

        $codeData = load('Config')->load('code')->get('code');

        if(!$msg){

            $msg = $this->codeData[$code];

        }

        echo json_encode(compact('code', 'msg', 'data'));

        return;

    }

}

class Model{
    public function getData(){
        return $this->_data;
    }

    public function getError(){
        return $this->_error;
    }

    protected function _cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

// ---------------------------------------------------------------------------------------------------------------------------
//CI database库重用
function show_error($errstr)
{
    load('Debug')->log($errstr, YEY_ERROR);

}

function log_message($level, $info)
{
    load('Debug')->log($info, YEY_NOTICE);
}

class ZDatabase
{
    static public function factory($name)
    {
        static $instance = array();
        if (!isset($instance[$name])) {
            define('APPPATH', APP_PATH . '/');
            define('BASEPATH', dirname(__FILE__) . '/');
            require_once('database/DB.php');
            $db = load('Config')->get('database');
            $instance[$name] = DB($db[$name]);
        }
        return $instance[$name];
    }
}

class ZWeChat
{
    static public function factory() {
        $options = load('Config')->load('wechat')->get('wechat');
        return new \EasyWeChat\Foundation\Application($options);
    }
}

// ---------------------------------------------------------------------------------------------------------------------------
/**
 * Model - 模型接口
 *
 * 所有模型请继承该接口
 *
 * @package     YEY
 * @subpackage  YEY.libraries
 * @author      YEY Dev Team
 * @category    Singleton
 */
class Single extends Model
{
    public function __construct()
    {
        $this->init();
    }

    static public function getInstance()
    {
        static $instance = array();
        $className = get_called_class();
        if(!isset($instance[$className])){
            $instance[$className] = new $className();
        }
        return $instance[$className];
    }

    /**
     * 初始化
     */
    public function init()
    {
        //nothing todo
    }
}

// ---------------------------------------------------------------------------------------------------------------------------
/**
 * Config Class - for php 5.4
 *
 * @package     YEY
 * @subpackage  YEY.libraries
 * @author      YEY Dev Team
 * @category    Config
 */

class ZConfig{
    /**
     * Config map
     *
     * @access  private
     * @var     array
     */
    private $_config = array();

    /**
     * Array Class
     *
     * @access  private
     * @var     object
     */
    private $_array;

    /**
     * The Map Of Loaded Config
     *
     * @access  private
     * @var     array
     */
    private $_map = array();

    /**
     * Constructor
     *
     * Write debug information
     */
    public function __construct() {
        load('Debug')->log('Config Class Initialized.', YEY_DEBUG);
        $this->load('config');
    }

    /**
     * Get config information
     *
     * @access  public
     * @param   string|array  $item   default ''  the name of config
     * @return  mixed
     */
    public function get($item = '', $return = false) {
        if (is_array($item)) {
            foreach ($item as $v)
                if (isset($this->_config[$v])) $r[] = $this->_config[$v];
            return is_array($r) ? $r : $return;
        }

        $item = strtolower($item);

        foreach (array(':', '->') as $split) {
            if (false !== strpos($item, $split)) {
                $r = explode($split, $item);
                $conf = $this->get(array_shift($r));
                foreach ($r as $v) {
                    $conf = $conf[$v];
                }
                return $conf;
            }
        }

        if (is_string($item) && isset($this->_config[$item])) return $this->_config[$item];
        elseif ($item == '') return $this->_config;
        else return $return;
    }

    /**
     * Load config files
     *
     * @access  public
     * @param   string  $file   the name of a config file
     * @return  object
     */
    public function load($file) {
        if (in_array($file, $this->_map)) {
            load('Debug')->log('Config file ('.$file.'.php) already loaded, Second attempt ignored.', YEY_NOTICE);
            return $this;
        }

        $_config = array();

        if (file_exists(APP_PATH.'/config/'.$file.'.php')) {
            require_once(APP_PATH.'/config/'.$file.'.php');
            if (isset($$file) && is_array($$file)){
                $_config[$file] = is_array($_config[$file]) ? array_merge($_config[$file], $$file) : $$file;
            }
            unset($$file);
            load('Debug')->log('Config file [System config -> '.$file.'.php] be loaded.', YEY_DEBUG);
        } else {
            load('Debug')->log('Config file ['.$file.'.php] fail.', YEY_WARNING);
        }

        $this->_map[] = $file;

        if($_config) {
            $this->_config = array_merge($this->_config, $_config);
        }

        return $this;
    }

    public function set($config, $value = null) {
        if(is_array($config)) {
            $this->_config = array_merge($this->_config, $config);
        } else {
            $this->_config[$config] = $value;
        }
    }
}

// ---------------------------------------------------------------------------------------------------------------------------
/**
 * Debug Class - for php 5.4
 *
 * @package     YEY
 * @subpackage  YEY.libraries
 * @author      YEY Dev Team
 * @category    Debug
 */
class ZDebug{
    /**
     * date format
     *
     * @access  private
     * @var     string
     */
    private $_date_fmt;

    /**
     * message list
     *
     * @access  private
     * @var     array
     */
    private $_msg;

    /**
     * message id
     *
     * @access  private
     * @var     int
     */
    private $_id;

    /**
     * @var int 打印日志堆栈时,打印的第N层参数
     */
    public static $_argsnum = 1;

    /**
     * define the date_format
     */
    public function __construct() {
        $this->_date_fmt = 'Y-m-d H:i:s';
        $this->levels = array(
            YEY_ERROR   => 'ERROR  ',
            YEY_WARNING => 'WARNING',
            YEY_PARSE   => 'PARSE  ',
            YEY_NOTICE  => 'NOTICE ',
            YEY_DEBUG   => 'DEBUG  ',
            YEY_LOG     => 'LOG    ',
        );
        $this->_id = 0;
    }

    /**
     * Write debug information
     *
     * @access  public
     * @param   string  $msg    the message of debug information
     * @return  boolean
     */
    public function log($msg, $level = YEY_ERROR){

        // remove in gpm

        if(!(RUNLEVELS & $level)) return false;

        $level = isset($this->levels[$level]) ? $this->levels[$level] : $level;

        // 添加调试信息
//        $backtrace = debug_backtrace();
        if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);    //此参数，必须保证 php 5.3.6+
        }
        else
        {
            $backtrace = debug_backtrace();
        }

        $i = 0;
        foreach($backtrace as $row) {
            $args = '';
            if($i < self::$_argsnum)    //只记录前self::$_argsnum次调用的参数
            {
                 $args = print_r($value['args'], TRUE);
            }

            $this->_msg[] = sprintf('%03d', ++$this->_id).' --> '.date($this->_date_fmt). " --> {$level} --> {$row['file']} - {$row['line']} - {$row['function']} - {$row['class']} - {$args} \n";
        }

        if(is_array($msg)){
            foreach($msg as $key => $base ) {
                $this->_msg[] = sprintf('%03d', ++$this->_id).' --> '.date($this->_date_fmt). " --> {$level} --> {$base}";
            }
        }else{
            $this->_msg[] = sprintf('%03d', ++$this->_id).' --> '.date($this->_date_fmt). " --> {$level} --> {$msg}";
        }
        $this->_msg[] = sprintf('%03d', ++$this->_id).' --> '.date($this->_date_fmt). " --> {$level} --> -----------------------------------------------------------------------";
        
        return true;
    }

    /**
     * Ouput Debug Information
     *
     * @access  public
     * @param   string  $msg
     * @return  void
     */
    public function output($msg, $function = 'print_r'){
        if(function_exists($function)) $function($msg);
        else print_r($function);
        die;
    }

    /**
     * Point debug information
     *
     * @access  public
     * @param   string  $msg    the message of debug information
     * @return  boolean
     */
    public function point($msg, $save = false){
        // remove in gpm
        return true;


        if(!is_string($msg)) $msg = var_export($msg, true);
        $message = date($this->_date_fmt) . " --> ----------------------- point ----------------------------\n";
        $message .= date($this->_date_fmt). ' --> '.$msg."\n";
        $message .= date($this->_date_fmt) . " --> ----------------------------------------------------------\n";
        $this->_msg[] = $message;
        return true;
    }

    public function flush(){
        $this->_save(false);
        $this->_msg = array();
    }

    public function save(){
        $this->_save();
    }

    /**
     * Save log Message to file
     *
     * @access  private
     * @param   string  $path   the path of log file.
     * @param   string  $file   the name of log file.
     * @param   string  $msg    the message of log information.
     * @return  boolean
     */
    private function _save($end = true){

        if(count($this->_msg) == 0) return ;


        $file = date('Y-m-d').'.php';
        if ( $end ) {
            $this->log('Debug End...', YEY_DEBUG);
            $msg = "\n================================================================================\n";
        } else {
            $msg = '';
        }

        $msg .= implode($this->_msg, "\n");

        if ( $end ) {
            $msg .= "\n================================================================================\n";
        }

        $real_log_path=APP_PATH.'/logs/core/' . date('Y-m-d');

        if (! file_exists($real_log_path))
        {
            mkdir($real_log_path, 0777, true);
        }
        file_put_contents($real_log_path . '/' . $file, join($this->_msg, "") . "\n", FILE_APPEND | LOCK_EX);
        return true;
    }

    /**
     * Destruct
     *
     * Save debug info
     */
    public function __destruct(){
        $this->_save();
    }
}

// ---------------------------------------------------------------------------------------------------------------------------
/**
 * Input Class - for php 5.0
 *
 * @package     YEY
 * @subpackage  YEY.libraries
 * @author      YEY Dev Team
 * @category    Input
 */
class ZInput{
    /**
     * IP Address
     *
     * @access  private
     * @var     string
     */
    private $_ipAddress;

    /**
     * User Agent
     *
     * @access  private
     * @var     string
     */
    private $_userAgent;

    /**
     * Controller
     *
     * Write Debug Information
     */
    public function __construct() {
        $this->_ipAddress = false;
        $this->_userAgent = false;
        load('Debug')->log('Input Class Initialized.', YEY_DEBUG);
    }

    /**
     * Online IP
     *
     * @access  public
     * @return  string
     */
    public function ip() {
        if ($this->_ipAddress !== false) return $this->_ipAddress;
        if ($this->server('HTTP_X_FORWARDED_FOR') && strcasecmp($this->server('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = $this->server('HTTP_X_FORWARDED_FOR');
        } elseif ($this->server('HTTP_CLIENT_IP') && strcasecmp($this->server('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = $this->server('HTTP_CLIENT_IP');
        } elseif ($this->server('REMOTE_ADDR') && strcasecmp($this->server('REMOTE_ADDR'), 'unknown')) {
            $onlineip = $this->server('REMOTE_ADDR');
        }

        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $this->_ipAddress = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
        return $this->_ipAddress;
    }

    /**
     * User Agent
     *
     * @access  public
     * @return  string
     */
    public function userAgent() {
        if (false !== $this->_userAgent) {
            return $this->_userAgent;
        }

        $this->_userAgent = $this->server('HTTP_USER_AGENT');

        return $this->_userAgent;
    }

    /**
     * Get a Item From Server Array
     *
     * @access  public
     * @param   string          $index
     * @return  string|array
     */
    public function server($index = '', $return = false) {
        $r = $this->_fetchArray($_SERVER, $index) ? $this->_fetchArray($_SERVER, $index) : getenv($index);
        return $r ? $r : $return;
    }

    /**
     * Foreach Item From Array
     *
     * @access  public
     * @param   string  $array
     * @param   string  $index
     * @return  string
     */
    private function _fetchArray($array, $index = '') {
        if ($index == '') return $array;
        if (isset($array[$index])) {
            return $array[$index];
        } else {
            return false;
        }
    }
}

// ---------------------------------------------------------------------------------------------------------------------------
/**
 * YEY_Driver Class - for php 5.0
 *
 * @package     YEY
 * @subpackage  YEY.Database
 * @author      YEY Dev Team
 * @category    Driver
 */
class IRedis extends Redis{
    protected $host;
    protected $prefix;
    protected $port;
    protected $serialize;

    static private $config = null;
    static private $instance = array();

    // 实例化时，直接连接Redis
    public function __construct($params = array())
    {
        load('Debug')->log('Redis Class Initialized.', YEY_DEBUG);
        $this->host                 = '127.0.0.1';
        $this->prefix               = '';
        $this->port                 = '6379';
        $this->serialize            = '';
        $this->password             = '';
        $serializeArray             = array(
            //'igbinary' => Redis::SERIALIZER_IGBINARY,
            'php' => Redis::SERIALIZER_PHP,
            'none' => Redis::SERIALIZER_NONE,
        );

        if(is_array($params)){
            foreach($params as $key => $v){
                $this->$key = $v;
            }
        }

        parent::__construct();

        if ( $params['pconnect'] ) {
            $this->pconnect($this->host, $this->port, 0, $params['handle']);
        } else {
            $this->connect($this->host, $this->port);
        }

        if($this->password){
            $this->auth($this->password);
        }

        if($this->prefix){
            $this->setOption(Redis::OPT_PREFIX, "{$this->prefix}::");
        }

        if($this->serialize){
            $this->setOption(Redis::OPT_SERIALIZER, $serializeArray[$this->serialize]);
        }
    }

    static public function factory($name)
    {

        if (!self::$config){
            self::$config = load('Config')->load('redis')->get('redis');
        }

        if (!isset(self::$config[$name])){
            load('Debug')->log('Load Redis Config Section '.$name.' No-exists.', YEY_ERROR);
            return null;
        }


        if (!isset(self::$instance[$name])){
            self::$instance[$name] = new self(self::$config[$name]);
        }

        return self::$instance[$name];
    }


    /**
     * 取key，不带前辍
     */
    public function nKeys($key = '*') {
        $r = $this->keys($key);
        foreach($r as &$val) {
            $val = str_replace("{$this->prefix}::", '', $val);
        }
        return $r;
    }
}

/**
 * Loader Class - for php 5.4
 *
 * @package     YEY
 * @subpackage  YEY.libraries
 * @author      YEY Dev Team
 * @category    Loader
 */
class ZLoader{
    public function __construct(){
        $this->_error = load('Errors');
    }

    /**
     * Load Redis Class
     *
     * @access  public
     * @param   arary   $param
     * @return  mixed
     */
    public function redis($name) {
        return IRedis::factory($name);
    }

    /**
     * Load Database Class
     *
     * @access  public
     * @param   arary   $param
     * @param   boolean $return
     * @return  mixed
     */
    public function database($name) {
        return ZDatabase::factory($name);
    }

    /**
     * Load EasyWeChat Class
     *
     */
    public function wechat(){
        return ZWeChat::factory();
    }

    /**
     * 设置dbconfig
     *
     * @access  public
     * @param   array
     * @return  void
     */
    public function redisConfig($config){
        if ( ! is_array($config) ) return ;
        foreach ($config as $key => $v) {
            $handle = ucfirst($v['handle']);
            $v['handle'] = "rdr{$handle}";
            $this->_redisConfig["rdr{$handle}"] = $v;
            $v['handle'] = "rdw{$handle}";
            $this->_redisConfig["rdw{$handle}"] = $v;
        }
        load('Config')->set('redis', $this->_redisConfig);
    }

}
