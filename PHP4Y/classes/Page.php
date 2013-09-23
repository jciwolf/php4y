<?php
    /**
     * 分页类
     * @author charles
     *
     */
    class Page 
    {
        
        private $sum;        //总记录
        private $listNum;    //每页显示数量
        private $pageNum;   //总页数
        private $page;       //当前页
        private $limit;      //限制范围
        private $url;        //当前请求URL
        private $step;       //数字分页长度
        //配置项
        public  $config = array(
              'sum'=>'总数据',
              'prev'=>'上一页',
              'next'=>'下一页',
              'first'=>'首页',
              'last'=>'尾页'   
        );
        
        
        /**
         * 构造函数
         * @param int $sum        //总记录
         * @param int $listNum    //每页显示条目
         */
        public function __construct($sum, $listNum = 10, $step = 8)
        {
            $this->sum = $sum;
            $this->listNum = $listNum;
            $this->url = $GLOBALS['url'].'/'.$_GET['m']."/page/";
            $this->page = isset($_GET['page']) ? $_GET['page'] : 1;
            $this->limit = (($this->page-1)*$this->listNum).','.$this->listNum;
            $this->pageNum = ceil($this->sum / $this->listNum);
            $this->step = $step;
        }
        
        /**
         * __get
         */
        public function __get($var)
        {
            if ( $var == 'limit' ) {
                 return $this->limit;
            }            
        }
        
        /**
         * 开始数据
         */
        private function dataStart()
        {
            if ( $this->sum ==0 ) {
                return;
            } else {
                return ($this->page - 1) * $this->listNum + 1;
            }
        }
        
        /**
         * 结束数据
         */
        private function dataEnd()
        {
            return min($this->page*$this->listNum,$this->sum);
        }
        
        /**
         * 首页
         */
        private function first()
        {
            $str = '';
            if ( $this->page != 1 ) {
                $str .= '<li><a href="'.$this->url.'1">'.$this->config['first'].'</a></li>';
            }
            return $str;
        }
        
        /**
         * 尾页
         */
        private function last()
        {
            $str = '';
            if ( $this->page != $this->pageNum ) {
                $str .= '<li><a href="'.$this->url.$this->pageNum.'">'.$this->config['last'].'</a></li>';
            }
            return $str;
        }
        
        /**
         * 上一页
         */
        private function prev()
        {
            $str = '';
            if ( $this->page != 1 ) {
                $str = '<li><a href="'.$this->url.($this->page - 1).'">'.$this->config['prev'].'</a></li>';
            }
            return $str;
        }
        
        /**
         * 下一页
         */
        private function next()
        {
            $str = '';
            if ( $this->page != $this->pageNum ) {
                $str = '<li><a href="'.$this->url.($this->page + 1).'">'.$this->config['next'].'</a></li>';
            }
            return $str;
        }
        
        /**
         * 数字分页
         */
        private function number()
        {
            $str = '';
            $num = floor($this->step / 2);
            for ($i=$num; $i>=1; $i--) {
                $page = $this->page-$i;
                if ($page < 1) {
                    continue;
                }
                $str .= '<li><a href="'.$this->url.$page.'">'.$page.'</a></li>';
            }
            $str .= '<li class="active"><a href="javascript:void(0)">'.$this->page.'</a></li>';
            for ($i=1; $i<=$num; $i++) {
                $page = $this->page+$i;
                if ($page > $this->pageNum) {
                    break;
                } else {
                    $str .= '<li><a href="'.$this->url.$page.'">'.$page.'</a></li>';
                }
            }
            return $str;
        }
        /**
         * 分页格式
         */
        public function showPage()
        {
            $page  = '';
           // $page .= $this->first();
            $page .= $this->prev();
            $page .= $this->number();
            $page .= $this->next();
           // $page .= $this->last();
            return $page;
        }
        
        
        
    }