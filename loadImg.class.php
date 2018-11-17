<?php

    class LoadImg
    {
        public $fileName;
        public $allowSize;
        public $allowType;
        public $source; //要裁剪的图片路径
        public $width; // 裁剪后的宽度
        public $height; // 裁剪后的搞度
        public $target; //裁剪后的图片保存路径

        //构造函数
        public function __construct($fileName,$allowSize)
        {
            $this->fileName = $fileName;
            $this->allowSize = $allowSize;
        }

        //设置裁剪图片信息
        public function setCrop($source, $width, $height, $target){
            $this->source = $source;
            $this->width = $width;
            $this->height = $height;
            $this->target = $target;
        }

        //上传图片大小
        public function allowSize()
        {
            if ($_FILES[$this->fileName]['size'] > $this->allowSize) {
                echo "上传文件过大" . "<br>";
                return false;
            } else {
                return true;
            }
        }

        //上传图片类型
        public function allowType()
        {
            if ($_FILES[$this->fileName]['type'] != "image/gif"
                && $_FILES[$this->fileName]['type'] != "image/jpg"
                && $_FILES[$this->fileName]['type'] != "image/png"
                && $_FILES[$this->fileName]['type'] != "image/jpeg") {
                echo "上传文件类型不是图片" . "<br>";
                return false;
            }
            return true;
        }

        //判断文件上传是否成功
        public function isSuccess()
        {
            if ($this->allowSize() == true && $this->allowType() == true && $_FILES[$this->fileName]['error'] == 0) {
                echo "上传文件成功" . "<br>";
                return true;
            } else {
                echo "上传文件失败";
                return false;
            }
        }

        //保存上传文件
        public function saveImg(){
            if (file_exists("upload/" . $_FILES[$this->fileName]['name'])) {
                echo "文件已经存在";
            } else {
                move_uploaded_file($_FILES[$this->fileName]['tmp_name'],"upload/" . $_FILES[$this->fileName]['name']);
                echo "文件保存在:upload/" . $_FILES[$this->fileName]['name'];
            }
        }

        //裁剪图片
        public function cropImg()
        {
            $source = $this->source;
            $width = $this->width;
            $height = $this->height;
            $target =$this->target;

            if (!file_exists($source)) {
                return false;
            }

            switch (exif_imagetype($source)) {  //内置函数获取图片类型
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($source); //从文件中载入图像，返回文件资源
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($source);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($source);
                    break;
            }

            if (!isset($image)) {
                return false;
            }

            //获取图像尺寸信息
            $target_w = $width;
            $target_h = $height;
            $source_w = imagesx($image); //获取图像宽度
            $source_h = imagesy($image); //获取图像高度

            //计算裁剪宽度和高度
            $judge = (($source_w / $source_h) > ($target_w / $target_h));
            $resize_w = $judge ? ($source_w * $target_h) / $source_h : $target_w;
            $resize_h = !$judge ? ($source_h * $target_w) / $source_w : $target_h;
            $start_x = $judge ? ($resize_w - $target_w) / 2 : 0;
            $start_y = !$judge ? ($resize_h - $target_h) / 2 : 0;

            //绘制居中缩放图像
            $resize_img = imagecreatetruecolor($resize_w, $resize_h); //创建一副空白图像
            imagecopyresampled($resize_img, $image, 0, 0, 0, 0, $resize_w, $resize_h, $source_w, $source_h); //图片缩放函数
            $target_img = imagecreatetruecolor($target_w, $target_h);
            imagecopy($target_img, $resize_img, 0, 0, $start_x, $start_y, $resize_w, $resize_h); //图片裁剪函数

            //将图片保存至于文件
            if (!file_exists(dirname($target))) { //判断目录不存在
                mkdir(dirname($target));  //创建目录
            }

            switch (exif_imagetype($source)) {
                case IMAGETYPE_JPEG:
                    imagejpeg($target_img,$target); //输出图像并保存在target目录下
                    break;
                case IMAGETYPE_GIF:
                    imagegif($target_img,$target);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($target_img,$target);
                    break;
                }

            return boolval(file_exists($target));
            }

    }