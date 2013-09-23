<?php
/**
 * MySQL数据库操作类
 * @author charles
 *
 */
class DB
{
    private $tableName = null;
    public static $conn = null;
    protected $options = array();
    protected $sql = '';
    protected $operations = array(
            'field',
            'from',
            'where',
            'limit',
            'order',
            'select',
            'find'
            );
    
    /**
     * 连接数据库
     */
    private function __construct($tableName)
    {
        $this->tableName = $tableName;
        self::$conn = mysql_connect(DB_HOST.':'.DB_PORT, DB_USER, DB_PASS) or die("连接数据库失败！");
        mysql_select_db(DB_NAME, self::$conn) or die("选择数据库失败！");
        mysql_query("SET NAMES UTF8");
    }
    
    
    public static function getInstance($tableName)
    {
           return new self($tableName);
    }
    /**
     * 连贯操作
     * @param string $function
     * @param array  $args
     */
    public function __call($function, $args)
    {
        if ( in_array($function, $this->operations) ) {
            $this->options[$function] = $args;
            return $this;
        } else {
            echo "操作 {$function} 不存在";
        }
    }
    
    /**
     * 查询单条记录
     * @param $id 查询ID  
     */
    public function find()
    {
        $where = ($this->deWhere()!='') ? ' WHERE '.$this->deWhere() : '';
        $order = ($this->deOrder()!='') ? ' ORDER BY '.$this->deOrder() : '';
        $field = $this->deField();
        $sql = "SELECT {$field} FROM " . $this->tableName . "{$where}" ."{$order}"." LIMIT 1";
        //添加调试SQL信息
        DEBUG::addMsg($sql,2);
        $arr = $this->query($sql);
        if ( empty($arr) ) {
            return false;
        } else {
            return $arr[0];
        }
    }
    
    /**
     * 查询多条记录
     */
    public function select()
    {
        $where = ($this->deWhere()!='') ? ' WHERE '.$this->deWhere() : '';
        $order = ($this->deOrder()!='') ? ' ORDER BY '.$this->deOrder() : '';
        $limit = $this->deLimit();
        $field = $this->deField();
        $sql = "SELECT {$field} FROM " . $this->tableName . "{$where}" ."{$order}"."{$limit}";
        //添加调试SQL信息
        DEBUG::addMsg($sql,2);
        $arr = $this->query($sql);
        return $arr;
    }
    
    /**
     * 添加记录
     * @param array $arr
     */
    public function insert($arr = null)
    {
        
        //默认POST提交表单，将表单值对应数据库字段
        $arr = (is_null($arr)) ? $_POST : $arr;
        $k = array_keys($arr);
        //提取与表中字段一致的元素
        $k = $this->fetchIntersect($k);
        $str1='';
        foreach ($k as $f_k) {
            $str1 .= "`{$f_k}`,";
        }
        $k = array_merge($k);
        $str1 = substr($str1, 0, -1);
        $str2 = '';
        $v = array();
        
        for ($i=0; $i<count($k); $i++) {
            $v[] = $arr[$k[$i]];
        }        
        
        foreach ($v as $f_v) {
            $f_v = mysql_real_escape_string($f_v);
            $str2 .= "'{$f_v}',";
        }
        $str2 = substr($str2, 0, -1);
        $sql = "INSERT INTO `".$this->tableName."`(".$str1.")values(".$str2.")";
        //返回添加后的ID
        if ( mysql_query($sql) ) {
            return mysql_insert_id();
        } else {
            DEBUG::addMsg($sql);
        }
        //添加调试SQL信息
        DEBUG::addMsg($sql,2);
    }
    
    /**
     * 修改记录
     * @param array $arr
     * 
     */
    public function update($arr = null)
    {
        //默认更新POST数据
        $arr = is_null($arr) ? $_POST : $arr;
        $sql = '';
        $k = array_keys($arr);
        $k = $this->fetchIntersect($k);
        $k = array_merge($k);
        $v = array();
        for ($i=0; $i<count($k); $i++) {
            $v[] = $arr[$k[$i]];
        }
        
        $arr = array_combine($k, $v);
        
        foreach ($arr as $k=>$v) {
            $k = mysql_real_escape_string($k);
            $v = mysql_real_escape_string($v);
            $sql .= "`{$k}` = '{$v}',"; 
        }
        $where = $this->deWhere();
        $sql = "UPDATE `".$this->tableName."` SET ".substr($sql, 0, -1)." WHERE {$where}";
        if ( mysql_query($sql) ) {
            return true;
        } else {
            DEBUG::addMsg($sql);
        }
        //添加调试SQL信息
        DEBUG::addMsg($sql,2);
    }
    
    /**
     * 删除记录
     */
    public function delete()
    {
        //DELETE FROM `blog_cate` WHERE (`id`='12') LIMIT 1
        $where = $this->deWhere();
        $sql = "DELETE FROM `".$this->tableName."` WHERE {$where}";
        //添加调试SQL信息
        DEBUG::addMsg($sql,2);
        mysql_query($sql);
        if ( mysql_affected_rows()>0) {
            return true;
        } else {
            DEBUG::addMsg(__FUNCTION__." 数据未更新，影响行数 :".mysql_affected_rows());
        }
       
    }
    
    /**
     * 处理field
     */
    private function deField()
    {
        $str = '';
        if ( isset($this->options['field']) ) {
            if ( is_array($this->options['field'][0]) ) {
                foreach ($this->options['field'][0] as $f) {
                    $str .= "`".$f."`".",";
                }
                //去掉最后一个“,”
                $str = substr($str, 0, -1);
            } else {
                $array = explode(',', $this->options['field'][0]);
                unset($array[count($array)]);//去掉最后一个空格
                foreach ($array as $k) {
                    $str .= "`".$k."`".",";
                }
                //去掉最后一个“,”
                $str = substr($str, 0, -1);
                //mysql_real_escape_string
            }
        } else {
            //未设置字段 ，默认为*
            $str = '*';
        }
        return $str;
    }
    
    /**
     * 处理where
     */
    private function deWhere()
    {
        $pre  = ''; 
        if ( isset($this->options['where']) ) {
            if ( is_array($this->options['where'][0]) ) {
              $gt   = ''; //大于条件
              $lt   = ''; //小于条件
              $like = ''; //匹配条件
              foreach ($this->options['where'][0] as $k => $v){
                  //字符转义
                  $k = mysql_real_escape_string($k);
                  $v = mysql_real_escape_string($v);
                  //查询条件 gt:大于  gte：大于等于 lt：小于 lte:小于等于
                  if ( stripos($v, "gt:") === 0 ) {
                     $arr = explode(':', $v);
                     $gt  = $arr[1];
                     //如果$pre不为空 ， 补上ＡＮＤ
                     if ( $pre != "" ) {
                         $pre .= " AND `{$k}` > {$gt}";
                     } else {
                         $pre .= "`{$k}` > {$gt}";
                     }
                      
                  } else if ( stripos($v, "gte:") === 0 ) {
                      $arr = explode(':', $v);
                      $gt  = $arr[1];
                      //如果$pre不为空 ， 补上ＡＮＤ
                      if ( $pre != "" ) {
                          $pre .= " AND `{$k}` >= {$gt}";
                      } else {
                          $pre .= "`{$k}` >= {$gt}";
                      }
                  } else if ( stripos($v, "lt:") === 0 ) {
                     $arr = explode(':', $v);
                     $lt  = $arr[1];
                   //如果$pre不为空 ， 补上ＡＮＤ
                     if ( $pre != "" ) {
                         $pre .= " AND `{$k}` < {$lt}";
                     } else {
                         $pre .= "`{$k}` < {$lt}";
                     }
                  } else if ( stripos($v, "lte:") === 0 ) {
                      $arr = explode(':', $v);
                      $lt  = $arr[1];
                      //如果$pre不为空 ， 补上ＡＮＤ
                      if ( $pre != "" ) {
                          $pre .= " AND `{$k}` <= {$lt}";
                      } else {
                          $pre .= "`{$k}` <= {$lt}";
                      }
                  } else if ( stripos($v, "like:") === 0 ) {
                     $arr  = explode(':', $v);
                     $like = $arr[1];
                     //如果$pre不为空 ， 补上ＡＮＤ
                     if ( $pre != "" ) {
                         $pre .= " AND `{$k}` like '%{$like}%'";
                     } else {
                         $pre .= "`{$k}` like '%{$like}%'";
                     }
                    
                  } else {
                      //如果$pre不为空 ， 补上ＡＮＤ
                      if ( $pre != "" ) {
                          $pre .= " AND `{$k}` = '{$v}'";
                      } else {
                           $pre .= "`{$k}` = '{$v}'";
                      }
                  }
              }
            } else {
                $pre = $this->options['where'][0];
            }
        } else {
            $pre = '';
        }
        return $pre;
    }
    
    /**
     * 处理ORDER
     */
    private function deOrder()
    {
        $str = '';
        if ( isset($this->options['order']) ) {
            $arr = explode(',', $this->options['order'][0]);
            foreach ($arr as $v) {
                $demo = explode(' ',$v);
                $str .= "`{$demo[0]}` ".strtoupper($demo[1]).",";
            }
            $str = substr($str, 0, -1);
        }
        return $str;
    }
    
    /**
     * 处理limit
     */
    private function deLimit()
    {
        $limit = '';
        if ( isset($this->options['limit']) ) {
            if ( isset($this->options['limit'][1]) ) {
                $limit = " LIMIT ".$this->options['limit'][0].",".$this->options['limit'][1];
            } else {
                $limit = " LIMIT ".$this->options['limit'][0];
            }
        }
        return $limit;
    }
    
    /**
     * 查询
     * @param string $sql
     */
    public function query($sql)
    {
        $res = mysql_query($sql);
        $arr = array();
        $row = '';
        while ($row = mysql_fetch_assoc($res)) {
            $arr[] = $row;
        }
        return $arr;
    }
    
    /**
     * 统计
     */
    public function count()
    {
        $sql = "SELECT COUNT(*) FROM ".$this->tableName;
        DEBUG::addMsg($sql,2);
        $res = mysql_query($sql);
        $num = mysql_fetch_row($res);
        return $num[0];
    }
    
    /**
     * 取表中字段交集
     * @param $arr 传递的数组
     */
    private function fetchIntersect($arr)
    {
        $sql = "SHOW COLUMNS FROM ".$this->tableName;
        DEBUG::addMsg($sql,2);
        $res = mysql_query($sql);
        $field = array();
        while ($row = mysql_fetch_assoc($res)) {
            $field[] = $row['Field'];
        }
        //dump($field);
        //dump($arr);
        return array_intersect($field, $arr);
    }
}