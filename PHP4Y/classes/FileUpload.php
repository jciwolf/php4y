<?php
    /**
     * 文件上传类
     * @author charles
     *
     */
    class FileUpload
    {
        
        //默认允许上传类型
        public $allowType = array(
          "jpg",
          "gif",
          "png",
          "jpeg"      
         );
        //默认允许上传大小
        public $allowSize = 2000000;
        
        /**
         * 设置上传
         * @param array $type
         * @param int   $size
         */
        public function setAllowType($type, $size)
        {
            $this->allowType = $type;
            $this->allowSize = $size;
        }
        
        /**
         * 文件上传
         * @param string $tableName 表单file类型 name
         */
        public function upload($tableName = null)
        {
            if ( !is_null($tableName) ) {
                //设置系统上传大小
                ini_set('upload_max_filesize', $this->conversion($this->allowSize).'M');
                ini_set('post_max_size',($this->conversion($this->allowSize)+2)."M");
                if ( isset($_FILES[$tableName]) ) {
                    $fileName = $_FILES[$tableName]['name'];
                    $arr = explode('.', $fileName);
                    $fileType = $arr[1];
                    if ( !in_array($fileType, $this->allowType) ) {
                        Debug::errorHandler(E_WARNING, "'{$fileType}'该文件类型不被允许！", __FILE__, __LINE__);
                    } else if ( $this->conversion($_FILES[$tableName]['size']) > $this->allowSize ) {
                        Debug::errorHandler(E_WARNING, "该文件大小超过系统设置 {$this->allowSize}", __FILE__, __LINE__);
                    } else {
                        //文件名
                        $name = time().rand(1,100).".".$fileType;
                        $filePath = APP_ROOT.'/public/upload/'.$name;
                        if ( move_uploaded_file($_FILES[$tableName]['tmp_name'], $filePath) ) {
                            return $name;
                        } else {
                            Debug::errorHandler(E_WARNING, "文件上传失败", __FILE__, __LINE__);
                        }
                    }
                }
            }
        }
        /**
         * 单位转换
         */
        public function conversion($byte){
            return round($byte/1024/1024);
        }
    }