<?php
$dbs = 'mysql';
$host = 'localhost';
$dbname = 'test';
$dns = "$dbs:host=$host;dbname=$dbname";
$user = 'root';
$pass = '123456';
$pdo = new pdo($dns,$user,$pass);
class CurlDouban
{
    //抓取数据
    public static function read()
    {
        global $pdo;
        for ($i = 0;$i<250;$i+=25) {
            $url = "https://movie.douban.com/top250?start=$i&filter=";
            $content = file_get_contents($url);
            $row = curlDouban::pregTitle($content);
            $row2 = curlDouban::pregDesc($content);
            $row3 = curlDouban::pregStar($content);
            $row4 = curlDouban::pregDate($content);
            echo '<pre>';

            foreach ($row as $key => $value) {
                $id = $key + $i +1;
                $pdo->exec("insert into douban(id,title) values($id,'$value')");  //数据库中插入电影名
                //file_put_contents("movie.txt","电影名:".$value.PHP_EOL,FILE_APPEND);
            }

            foreach ($row2 as $key => $value) {
                //file_put_contents("desc.txt","简介：".$value.PHP_EOL,FILE_APPEND);
                $id = $key +$i + 1;
                $pdo->exec("update douban set describ = '$value' where id = '$id'");  //数据库中修改简介
            }

            foreach ($row3 as $key => $value) {
                //file_put_contents("star.txt","评分：".$value.PHP_EOL,FILE_APPEND);
                $id = $key +$i + 1;
                $pdo->exec("update douban set star = '$value' where id = '$id'");  //数据库中修改评分
            }

            foreach ($row4 as $key => $value) {
                $id = $key +$i + 1;
                echo $id.$value."<br>";
                $pdo->exec("update douban set link = '$value' where id = '$id'");  //数据库中修改评分
                //file_put_contents("date.txt",$value.PHP_EOL,FILE_APPEND);
            }

        }
        echo "抓取成功，在数据库 test 的 douban 表中查看";
    }

    //匹配电影名称
    public static function pregTitle($str)
    {
        $pattern1 = '/<a.*?<span class="title">(.*?)<\/span>/s';
        preg_match_all($pattern1,$str,$matches);
        return $matches[1];
    }

    //匹配电影简介
    public static function pregDesc($str)
    {
        $pattern = '/<span class="inq">(.*?)<\/span>/s';
        preg_match_all($pattern,$str,$matches);
        return $matches[1];
    }

    //匹配电影评分
    public static function pregStar($str)
    {
        $pattern2 = '/<span class="rating_num.*?>(.*?)<\/span>/s';
        preg_match_all($pattern2,$str,$matches);
        return $matches[1];
    }

    //匹配电影豆瓣链接
    public static function pregDate($str)
    {
        $pattern3 = '/<div class="pic">.*?<a href=[\'"]([^\'"]*?)[\'"]>/s';
        preg_match_all($pattern3,$str,$matches,PREG_PATTERN_ORDER);
        return $matches[1];
    }

}
CurlDouban::read();


