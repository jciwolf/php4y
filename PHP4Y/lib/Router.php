<?php 
/**
 * 路由类
 * 将 index.php?a=index&m=index 转为 index.php/index/index
 * @author Charles
 *
 */
class Router
{
    /**
     * url处理
     */
    public static function URL()
    {
         //pathinfo 请求
         if ( isset($_SERVER['PATH_INFO']) ) {
              $ext = explode('/', $_SERVER['PATH_INFO']);
              array_shift($ext); //移除空格
              $action = !empty($ext[0]) ? $ext[0] : 'index'; //默认操作index模块
              array_shift($ext);
              $method = !empty($ext[0]) ? $ext[0] : 'index'; //默认操作index方法
              array_shift($ext);
              //处理其他参数,存入$GET数组
              for ($i=0; $i<count($ext); $i=$i+2) {
                  $_GET[$ext[$i]] = $ext[$i+1];    
              }
              //将参数负值到$GET数组
              $_GET['a'] = $action;
              $_GET['m'] = $method;
          
         } else {
              $_GET['a'] = isset($_GET['a']) ? $_GET['a'] : 'index'; //默认操作index模块
              $_GET['m'] = isset($_GET['m']) ? $_GET['m'] : 'index'; //默认index方法
              //参数处理
              if ( !empty($_SERVER['QUERY_STRING']) ) {
                   //去除 $_GET['a'] 和 $_GET['m']
                   unset($_GET['a']);
                   unset($_GET['m']);
                   //组合GET参数
                   $ext = http_build_query($_GET);
                   $url = $_SESSION['SCRIPT_NAME']."/{$a}/{$m}".str_repeat(array('&','='),'/',$ext);
                   header("Location:".$url);
              }
             
         }
    } 
}