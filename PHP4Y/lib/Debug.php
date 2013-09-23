<?php 
    /**
     * Debug类
     * @author Charles
     *
     */
    class Debug
    {
        public static $info = array(); //基本信息
        public static $sql  = array(); //执行sql信息
        public static $file = array(); //包含文件信息
        public static $timeStart; //开始运行时间
        public static $timeEnd; //结束运行时间
        public static $type = array(
          E_WARNING => '运行警告',
          E_NOTICE => '运行提醒',
          E_STRICT => '编码警告',
          E_USER_ERROR => '自定义错误',
          E_USER_WARNING => '自定义警告',
          E_USER_NOTICE => '自定义提醒',
          'Other'=>'未知错误类型'
        );

        /**
         * 添加调试信息
         * @param string $msg
         * @param int    $type
         * 0：错误信息  1：包含的文件  2：执行的sql语句
         */
        public static function addMsg($msg, $type = 0)
        {
          switch ($type) {
            case 0 :
                 self::$info[] = $msg;
                 break;
            case 1 :
                 self::$file[] = $msg;
                 break;
            case 2 :
                 self::$sql[] = $msg;
                 break;
          }
        }

        /**
         * 自定义错误处理
         * @param string errno   错误级别
         * @param string errstr  错误信息
         * @param string errfile 错误文件
         * @param string errline 错误行号
         */
        public static function errorHandler($errno, $errstr, $errfile, $errline)
        {
            if ( !isset(self::$type[$errno]) ) {
                $errno = 'Other';
            }
            $str  = '<font color="red">';
            $str .= '<b>'.self::$type[$errno]."</b>在 {$errfile} 第 {$errline} 行";
            $str .= $errstr;
            $str .= '</font>';
            self::addMsg($str);
        }

        /**
         * 显示错误信息
         */
        public static function displayDebug()
        {

            $html  = '<ul style="position:relative;bottom:0;bottom:-100px;font-size:13px;list-style:none;border:1px dashed #555;width:600px;padding:20px;">';
            $html .= '<li style="font-size:14px;font-weight:bold">·执行时间: <font color="red">' . self::timeCount() .'</font> 毫秒<li>';
            $html .= '<li style="font-size:14px;font-weight:bold;margin-top:10px;">·加载文件</li>';
            //输出包含文件信息
            if ( count(self::$file) > 0 ) {
                foreach (self::$file as $fileName) {
                    $html.= "<li style='margin-top:5px;color:#4575A9'> &nbsp;&nbsp;--{$fileName} </li>";
                }
            }
            $html .= '<li style="font-size:14px;font-weight:bold;margin-top:10px;">·基本信息</li>';
            //输出基本信息
            if ( count(self::$info) > 0 ) {
                foreach (self::$info as $info) {
                    $html.= "<li style='margin-top:5px;'> &nbsp;&nbsp;--{$info} </li>";
                }
            }
            //输出执行sql
            $html .= '<li style="font-size:14px;font-weight:bold;margin-top:10px;">·SQL信息</li>';
            if ( count(self::$sql) > 0 ) {
                foreach (self::$sql as $s) {
                    $html.= "<li style='margin-top:5px;color:green'> &nbsp;&nbsp;--{$s} </li>";
                }
            }
            $html .= '</ul>';
            echo $html;

        }

        /**
         * 计算错误执行程序时间，开始时间
         */
        public static function timeStart()
        {
            self::$timeStart = microtime(true);
        }

        /**
         * 计算错误执行程序时间，结束时间
         */
        public static function timeEnd()
        {
            self::$timeEnd = microtime(true);
        }

        /**
         * 计算错误执行程序时间
         */
        public static function timeCount()
        {
            return round((self::$timeEnd - self::$timeStart) , 3); //保留3位小数
        }
    }