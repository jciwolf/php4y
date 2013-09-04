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
        } else {
            echo '<pre>';
            var_dump($var);
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
            DB::setTable(strtolower(DB_PREF.$table));
            return DB::getInstance();
        }
    }
    
    
