<?php
class CheckPlugin extends Yaf_Plugin_Abstract{
    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $respose) {
        $class = $request->getControllerName();
        $method = $request->getActionName();

         // 如果不是登录界面，检测用户有效性,排除合服脚本
        if( php_sapi_name() == "cli") return true;

        $config = load('Config')->load('config')->get('config');
        if (in_array($class, $config['white_controller'])) {
            return true;
        }

        /*

        $log = array();
        $log['username'] = $_SESSION['username'];
        $log['gid'] = $_SESSION['gid'];
        $log['time'] = time();
        $log['ip'] = load('Input')->ip();
        $lang = load('Config')->load('language')->get('language');
        $methods = $lang['method'];
        $action = $methods[$class][$method];
        if (! $action)
            $action = $class . '->' . $method;
        $log['action'] = $action;
        $params = '';
        $gets = $_GET;
        unset($gets['c']);
        unset($gets['m']);
        if (is_array($gets) && count($gets) > 0) {
            $params .= 'GET = { ';
            foreach ($gets as $key => $value) {
                $params .= $key . ':' . $value . ',';
            }
            $params .= '};';
        }
        $posts = $_POST;
        if (is_array($posts) && count($posts) > 0 ) {
            $params .= 'POST = { ';
            foreach ($posts as $key => $value) {
                $params .= $key . ':' . $value . ',';
            }
            $params .= '};';
        }
        $log['others'] = $params;

        $mLog = new Gm_MLogModel();
        $mLog->add($log);
        */
    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $respose) {
        $class = $request->getControllerName();
        $method = $request->getActionName();
        $config = load('Config')->get('config');

         // 如果不是登录界面，检测用户有效性,排除合服脚本
        if( php_sapi_name() == "cli") return true;

        Yaf_Registry::set('static_url', $config['static_url']);
        Yaf_Registry::set('controller', $class);
        Yaf_Registry::set('action', $method);

        //if ($class == 'Index' || $class == 'Login' || $class == 'Imgcode' || $class == 'Api' ) {
        if (in_array($class, $config['white_controller'])) {
            return true;
        }else{
            if (! $_SESSION['user_info'] ) {
                header("Location:/");
                exit();
            }
        }

        /*
        if ( $class != 'Index' && $class != 'Login' &&  $class != 'Imgcode') {
            if (! $_SESSION['uid'] ) {
                header("Location:/");
                exit();
            }
        }
        */

        /*
        // 得到用户组被开放的权限
        $enableActions = $_SESSION['enable_action'];
        $pass = false;

        // 判断是否可进入
        if ($enableActions[$class]) {
            if (is_array($enableActions[$class])) {
                if ($enableActions[$class][$method] == true) {
                    $pass = true;
                }
            } else if ($enableActions[$class] == true) {
                $pass = true;
            }
        }

        if ( $class == 'Jump' ) {
            $pass = true;
        }

        if ( !$pass) {
            echo $class;
            echo '<br />';
            echo $method;
            echo '<br />';
            echo 'permission deny';
            exit;
        }
        */

    }
}

if(!function_exists('html_select')){
    function html_select($name, $options, $checked = null, $_val = null, $_key = null){
        $output = "<select name=\"{$name}\">\n";
        $output .= html_options($options,$checked, $_val, $_key);
        $output .= "</select>\n";
        return $output;
    }
}

if(!function_exists('html_selects')){
    function html_selects($name, $options, $checked = null, $_val = null, $_key = null){
        return html_select($name, $options, $checked, $_val, $_key);
    }
}

if(!function_exists('html_options')){
    function html_options($options,$checked = null, $_val = null, $_key = null){
        if(!is_array($options)) return ;
        $output = '';
        foreach($options as $key => $val){
            if( $_key !== null && isset($val[$_key]) ) $key = $val[$_key];
            if( $_val !== null && isset($val[$_val]) ) $val = $val[$_val];
            $select = '';
            if(is_array($checked)){
                if(in_array($key,$checked)) $select = ' selected';
            }else{
                if($key == $checked) $select = ' selected';
            }
            $output .= "<option value=\"{$key}\" label=\"{$val}\"{$select}>{$val}</option>\n";
        }
        return $output;
    }
}

if(!function_exists('html_radio')){
    function html_radio($name, $options, $checked = null, $space = '&nbsp;', $_val = null, $_key = null){
        return _html_option_output($name, $options, $checked, $space, 'radio', $_val, $_key);
    }
}

if(!function_exists('html_radios')){
    function html_radios($name, $options, $checked = null, $space = '&nbsp;', $_val = null, $_key = null){
        return html_radio($name, $options, $checked, $space, $_val, $_key);
    }
}

if(!function_exists('html_checkbox')){
    function html_checkbox($name, $options, $checked = null, $space = '&nbsp;', $_val = null, $_key = null){
        return _html_option_output($name, $options, $checked, $space, 'checkbox', $_val, $_key);
    }
}

if(!function_exists('html_checkboxs')){
    function html_checkboxs($name, $options, $checked = null, $space = '&nbsp;', $_val = null, $_key = null){
        return html_checkbox($name, $options, $checked, $space, $_val, $_key);
    }
}

if(!function_exists('_html_option_output')){
    function _html_option_output($name, $options, $checked = null,$space = '&nbsp;',$tags = 'radio', $_val = null, $_key = null){
        if(!is_array($options)) return ;
        $output = '';
        foreach($options as $key => $val){
            if ($_key !== null && isset($val[$_key])) $key =  $val[$_key];
            if ($_val !== null && isset($val[$_val])) $val = $val[$_val];
            if($key == $checked) $select = ' checked';
            else $select = '';
            $output .= "<label name=\"{$name}\"><input type=\"{$tags}\" name=\"{$name}\" value=\"{$key}\" label=\"{$val}\"{$select} />{$val}{$space}\n</label>";
        }
        return $output;
    }
}
