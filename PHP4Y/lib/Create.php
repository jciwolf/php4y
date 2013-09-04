<?php 
    /**
     * 目录结构创建类
     * @author Charles
     *
     */
    class Create
    {
    
        public static $msg = array(); //操作信息
    
        public static function make()
        {
          //配置文件
          if ( !file_exists(APP_ROOT.'config.php') ) {
           $cfg = <<<exo
<?php
/**
* 系统配置文件
* @author   Charles 
* @website  www.charlesjiang.com
*/
define('DEBUG' , 1);             //调试模式
define('DB_HOST' , '127.0.0.1'); //数据库配置信息
define('DB_PORT' , '3306');      //数据库端口
define('DB_NAME' , '');          //数据库名
define('DB_USER' , 'root');      //用户名
define('DB_PASS' , 'root');      //用户密码
define('DB_PREF' , 'php4y_');    //表前缀
//Smarty配置
define('SMARTY_L_DELIMITER' , '<{');    //Smarty模板变量左分割符
define('SMARTY_R_DELIMITER' , '}>');    //Smarty模板变量右分割符
define('SMARTY_CACHE' , false);         //开启模板缓存
define('SMARTY_CACHE_TIME' , 60*60);    //缓存时间        
exo;
          //创建配置文件
          self::makeFile('config.php', $cfg);
       }
       
        //运行时目录
        if ( !file_exists(RUNTIME_PATH) ) {
           self::makeDir(RUNTIME_PATH);
           self::makeDir(RUNTIME_PATH.'/cache');
           self::makeDir(RUNTIME_PATH.'/cache/'.APP_NAME);
           self::makeDir(RUNTIME_PATH.'/compile/');
           self::makeDir(RUNTIME_PATH.'/compile/'.APP_NAME);
        } 
        
        //公用资源文件
        if( !file_exists(APP_ROOT.'/public') ) {
           self::makeDir(APP_ROOT.'/public');//资源目录
           self::makeDir(APP_ROOT.'/public/'.APP_NAME);
           self::makeDir(APP_ROOT.'/public/res');//公共资源目录
           self::makeDir(APP_ROOT.'/public/'.APP_NAME.'/js');//js目录
           self::makeDir(APP_ROOT.'/public/'.APP_NAME.'/images');//image目录
           self::makeDir(APP_ROOT.'/public/'.APP_NAME.'/css');//css目录
           self::makeDir(APP_ROOT.'/public/upload');//upload目录
           //写入提示信息
           self::makeFile(APP_ROOT.'/public/res/readme.txt', '这里存放公共使用文件资源，例如：jquery.js');
        }
       /**
        * 创建目录结构
        */
        if ( !file_exists(APP_NAME) ) {
           //创建应用目录及MVC目录
           self::makeDir(APP_NAME);
           self::makeDir(APP_NAME.'/controller'); //控制器目录
           self::makeDir(APP_NAME.'/model'); //模型目录
           self::makeDir(APP_NAME.'/view'); //视图目录
           self::makeDir(APP_NAME.'/common');//公用类 ，函数目录


          //创建IndexAction
           $ind = <<<ind
<?php
    class IndexAction extends Action
    {
        public function index()
        {
            /**
            * 默认index方法
            */
           echo "<font style='font-size:14px;'>欢迎使用PHP4Y开源免费框架  ~_~</font>&nbsp;&nbsp;";
           echo "<a href='http://weibo.com/cameljq' target='_blank'>欢迎关注我的新浪微博.</a><br>";
        }    
    }
ind;
            self::makeFile(APP_NAME.'/controller/IndexAction.php', $ind);
        }
        
    }
    
    
    /**
     * 创建文件
     * @param string $fileName 文件名
     * @param string $data 文件内容
     */
    private static function makeFile($fileName,  $data)
    {
         if( !file_exists($fileName) ) {
             if ( file_put_contents($fileName, $data) ) {
                self::$msg[] = "<font class='success'>文件---{$fileName}---创建成功!</font>";
             } else {
                self::$msg[] = "<font class='success'>文件***{$fileName}***创建失败!</font>";
             }
          }
    }
    
    /**
     * 创建目录，赋予最大权限
     * @param string $dirName
     */
    private static function makeDir($dirName)
    {
         if( !file_exists($dirName) ) {
             if ( mkdir($dirName, "0777") ) {
                self::$msg[] = "<font class='success'>文件夹---{$dirName}---创建成功!</font>";
             } else {
                self::$msg[] = "<font class='success'>文件夹***{$dirName}***创建失败!</font>";
             }
         } 
     
    }
}