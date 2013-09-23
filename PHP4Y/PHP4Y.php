<?php 
    /**
     * @authror Charles
     * @website charlesjiang.com
     * PHP4Y 框架主文件
     * 文件包含
     * URL解析
     *
     */
    header("Content-type:text/html;charset=utf-8");   //设置编码
    date_default_timezone_set("PRC");                 //设置时区
    session_start();                                  //开启session
    //路径设置
    define('PHP_4Y',rtrim(PHP4Y,'/'));                //框架路径
    define('RUNTIME_PATH','runtime');
    define('APP_ROOT',dirname(PHP_4Y));               //项目根路径
    
    //检查配置文件
    if ( file_exists(APP_ROOT.'/config.php') ) {
        require APP_ROOT.'/config.php';
    }

    //设置包含路径
    $include_path  = get_include_path();                         //原基目录
    $include_path .= PATH_SEPARATOR.PHP_4Y.'/lib';              //框架核心文件目录
    $include_path .= PATH_SEPARATOR.PHP_4Y.'/lib/smarty';       //smarty文件目录
    $include_path .= PATH_SEPARATOR.PHP_4Y.'/functions';        //公用函数目录
    $include_path .= PATH_SEPARATOR.PHP_4Y.'/classes';          //公用类目录
    $include_path .= PATH_SEPARATOR.'./'.APP_NAME.'/controller';     //项目action
    $include_path .= PATH_SEPARATOR.'./'.APP_NAME.'/model';          //项目model
    set_include_path($include_path);
    
    Debug::timeStart();                                         //程序执行开始时间
    //检查是否开始调试模式
    if ( defined("DEBUG") && DEBUG == 1 ) {
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors','On');                         //开启错误信息
        set_error_handler(array('Debug','errorHandler'));       //设置自定义错误处理函数
    } else {
        ini_set('display_errors','Off');
        ini_set('error_log',RUNTIME_PATH.'/error_log.log');      //错误日志
    } 
    
    //自动加载
    function __autoload($className)
    {
        if ( $className == 'Smarty' ) {
            include 'Smarty.class.php';        
        } else {
            include $className.'.php' ;
        }
        //添加调试信息 ：文件加载
        Debug::addMsg("{$className}.php", 1);  
    }
    
    //调入公用函数
    include 'function.php';
    //创建相应目录及文件
    Create::make();
    //显示文件创建信息
    foreach (Create::$msg as $msg){
        dump($msg);
    }
    //调入URL处理类
    Router::URL();
    
    //项目中使用的全局变量
    $path = dirname($_SERVER["SCRIPT_NAME"]);
    $GLOBALS['root'] = $path.'/';
    $GLOBALS['app'] = $_SERVER['SCRIPT_NAME'];
    $GLOBALS['url'] = $GLOBALS['app'].'/'.$_GET['a'];
    $GLOBALS['public'] = $GLOBALS['root'].'public';
    
    //实例化对象
    $action = strtolower($_GET['a']);
    $method = strtolower($_GET['m']);
    $filePath = APP_NAME.'/controller/'.ucfirst($action).'Action.php';
    if ( file_exists($filePath) )
    {
        include($filePath);
        $Action = ucfirst($action)."Action";
        if ( class_exists($Action) ) {
            $Action = new $Action();
        } else {
            Debug::errorHandler("E_WARNING", "{$Action}类不存在！", __FILE__, __LINE__);
        }
        if ( method_exists($Action, $method) ) {
            $Action->$method();
        } else {
            Debug::errorHandler("E_WARNING", "{$method}方法不存在！", __FILE__, __LINE__);
        }
        Debug::addMsg(ucfirst($action).'Action.php', 1);
    } else {
        Debug::errorHandler("E_USER_ERROR", "{$filePath}文件不存在！", __FILE__, __LINE__);
    }
    
    //显示调试信息
    if ( defined("DEBUG") && DEBUG == 1 ) {
        Debug::timeEnd(); //程序执行结束时间
        Debug::displayDebug();//显示调试信息
    }