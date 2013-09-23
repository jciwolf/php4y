<?php 
    /**
     * Action基类
     * 所有Action都必须继承此类
     * @author Charles
     *
     */
    class Action extends Smarty
    {
        
         /**
          * 配置Smarty
          */
         public function __construct()
         {
             $this->template_dir = APP_NAME.'/view';  //模板目录
             $this->compile_dir = APP_ROOT.'/runtime/compile/'.APP_NAME ; //编译目录
             $this->debugging = false;  //调试
            @$this->caching = SMARTY_CACHE;   //开启缓存
             $this->cache_dir = APP_ROOT.'/runtime/cache/'.APP_NAME;
            @$this->cache_lifetime = SMARTY_CACHE_TIME;
            @$this->left_delimiter = SMARTY_L_DELIMITER;   //模板文件中使用的“左”分隔符号
            @$this->right_delimiter = SMARTY_R_DELIMITER;   //模板文件中使用的“右”分隔符号
             
             /**
              * 分配变量
              * 模板文件使用 <{$js}>-------找到该项目js文件夹
              * 模板文件使用 <{$images}>---找到该项目images文件夹
              * 模板文件使用 <{$css}>------找到该项目css文件夹
              * 模板文件使用 <{$upload}>---找到项目upload文件夹
              * 模板文件使用 <{$public}>---找到项目public文件夹
              * 模板文件使用 <{$app}>------获取操作的action
              */
             $this->assign("root" , $GLOBALS['root']);
             $this->assign("app" , $GLOBALS['app']);
             $this->assign("url" , $GLOBALS['url']);
             $this->assign("js" , $GLOBALS['public'].'/'.APP_NAME.'/js');
             $this->assign("images" , $GLOBALS['public'].'/'.APP_NAME.'/images');
             $this->assign("css" , $GLOBALS['public'].'/'.APP_NAME.'/css');
             $this->assign("upload" , $GLOBALS['public'].'/upload');
             $this->assign("res" , $GLOBALS['public'].'/res');
             $this->assign("public" , $GLOBALS['public']);
             parent::__construct();
         }
         //重载display方法
        function display($resource_name, $cache_id = null, $compile_id = null)
        {
            Debug::addMsg("使用模板：{$resource_name}");
            $this->fetch($resource_name.".html", $cache_id, $compile_id, true);
        }
        
        /**
         * 错误跳转
         * @param $url  跳转URL
         * @param $msg  提示信息
         * @param $time 等待时间
         */
        public function error($msg, $url='', $time=2)
        {
            if ( $url=='' ) {
               $url = $GLOBALS['url'];
            }

            $this->assign('retime',$time);
            $this->assign('reurl',$url);
            $this->assign('remsg',$msg);
            $this->display('public/error');
        }
        
        /**
         * 错误跳转
         */
        public function success($msg, $url='', $time=2)
        {
            if ( $url=='' ) {
                $url = $GLOBALS['url'];
            }
            
            $this->assign('retime',$time);
            $this->assign('reurl',$url);
            $this->assign('remsg',$msg);
            $this->display('public/success');
        }
    }