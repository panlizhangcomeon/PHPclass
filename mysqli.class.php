<?php

    class MysqliTest
    {
        public $host = "localhost";
        public $user = "root";
        public $pass = "123456";
        public $dbName;//数据库名
        public $link;//连接
        public $sql;//SQL语句
        public $res;//执行结果
        public $result;//查询结果

        //构造方法
        public function __construct($dbName)
        {
            $this->dbName = $dbName;
            $this->dbConnect();
            $this->setCharset();
        }

        //连接数据库
        public function dbConnect()
        {
            $this->link = mysqli_connect($this->host,$this->user,$this->pass,$this->dbName);
            if ($this->link) {
                echo "连接成功" . "<br>";
            } else {
                echo "连接MYSQL失败" . mysqli_connect_error();
            }
        }

        //设置字符集
        public function setCharset()
        {
            mysqli_query($this->link,"set names utf-8");
        }

        //执行SQL语句
        public function execSql($sql)
        {
            $this->sql = $sql;
            $this->res = mysqli_query($this->link,$this->sql);
            if($this->res){
                echo "执行成功" . "<br>";
                return $this->res;
            }else{
                echo "执行失败:" . mysqli_error($this->link);
                return false;
            }
        }

        //查询结果
        public function fetchResult($sql)
        {
            $this->execSql($sql);
            $this->result = mysqli_fetch_assoc($this->res);
            return $this->result;
        }

    }
