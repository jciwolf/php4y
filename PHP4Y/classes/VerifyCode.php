<?php
    /**
     * 验证码类
     * @author charles
     *
     */
    class VerifyCode
    {
        
    	private $width;       //图片宽度
    	private $height;      //图片高度
    	private $code ='';    //验证码字符
    	private $codeNum;     //验证码位数
    	private $line;        //干扰线条
    	
    	/**
    	 * 构造函数
    	 * @param int $width
    	 * @param int $height
    	 * @param int $codeNum
    	 */
    	public function __construct($width = 50, $height = 25, $codeNum = 4, $line= true)
    	{
    	    $this->width  = $width;
    	    $this->height = $height;
    	    $this->codeNum= $codeNum;
    	    $this->line   = $line;
    	}
    	
    	/**
    	 * 产生指定位数随机字符字符串
    	 */
    	private function randomStr($num)
    	{
    	    $str = "1a2b3c4d5e6f7g8h9ijkpqrstuvwxyz";
    	    $length = strlen($str);
    	    $code = '';
    	    for ( $i=0; $i<$num; $i++ ) {
    	        $code .= $str[mt_rand(1,$length-1)];
    	    }
    	    $this->code = $code;
    	    //存入session
    	    $_SESSION['verifyCode'] = $code;
    	}

    	/**
    	 * 创建图片
    	 */
    	private function createImg()
    	{
    	    $img  = imagecreatetruecolor($this->width, $this->height);
    	    //设置随机背景色
    	    $red = mt_rand(200, 240);
    	    $green = mt_rand(180, 220);
    	    $blue = mt_rand(170, 220);
    	    $background = imagecolorallocate($img, $red, $green, $blue);
    	    imagefill($img,0,0,$background);
    	    //干扰点颜色
    	    $pix = imagecolorallocate($img, 100, 225, 255);
    	    //文字颜色
    	    $textColor = imagecolorallocate($img, mt_rand(50,220), mt_rand(100,150), 200);
    	    //干扰点位置
    	    for ( $i=0; $i<100; $i++ ) {
    	        imagesetpixel($img, mt_rand(0, $this->width), mt_rand(0, $this->height), $pix);
    	    }
    	    imagestring($img, 5, 7, 5, $this->code, $textColor);
    	    if ( $this->line === true ) {
    	        $lineColor = 255;
    	        for ($i=1; $i<3; $i++) {
    	            imageline($img, 0, mt_rand(1,$this->height), $this->width, mt_rand(1, $this->height), $lineColor);
    	        }
    	    }
    	    imagepng($img);
    	    imagedestroy($img);
    	}
    	
    	/**
    	 * 输出图像
    	 */
    	public function __toString()
    	{
    	    header("Content-type:image/png");
    	    $this->randomStr($this->codeNum);
    	    $this->createImg();
    	}
    }
