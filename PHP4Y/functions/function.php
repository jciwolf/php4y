<?php 
    /**
     * 公用函数
     * @author Charles
     */
    
    
    /**
     * 调试打印
     * @param string $str
     */
    function dump($var)
    {
        if ( is_string($var) ) {
            echo $var .'<br>';
        } else if ( is_array($var) ) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        } else {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
    
    }
    
    /**
     * 实例化模型
     */
    function M($table = '')
    {
        //默认实例化当前模型类名对应表
        if ( $table == '' ) {
            DEBUG::addMsg("找不到对应表！");
        } else {
            return DB::getInstance(strtolower(DB_PREF.$table));
        }
    }
    
    /**
     * 重定向
     */
    function redirect($url)
    {
        
        header("Location:{$url}");
        return;
    }
    
    /**
     * 去标签
     * @param string $str
     * @return string
     */
    function dropTags($str)
    {
        $string = strip_tags(strip_tags($str));
        if ( !get_magic_quotes_gpc() ) {
            $str = addslashes($str);
        }
        
        $string = preg_replace ('/\n/is', '', $string);
        $string = preg_replace ('/ |　/is', '', $string);
        $string = preg_replace ('/&nbsp;/is', '', $string);
        return $string;
    }
    
    /**
     * 字符串截取
     */
    function cutStr($string, $sublen, $ext='...')
    {
        $string = cutHtml($string);
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $t_string);
        if( count($t_string[0]) - 0 > $sublen ) {
            $string = join('', array_slice($t_string[0], 0, $sublen)).$ext;
        } else {
           $string = join('', array_slice($t_string[0], 0, $sublen));
        }
        return $string;
    }