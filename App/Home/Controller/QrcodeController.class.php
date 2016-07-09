<?php
/**
*
* 版权所有：亿次元科技<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-21
* 版    本：1.0.0
* 功能说明：二维码生成控制器。
*
**/
namespace Home\Controller;
use Think\Controller;
class QrcodeController extends ComController {
    public  function index(){
        //import('@.Org.QRcode');//thinkphp 
    }

/* 
 * 浏览器输出二维码
 * <? 
include "phpqrcode/phpqrcode.php"; 
$value="http://www.jb51.net"; 
$errorCorrectionLevel = "L"; 
$matrixPointSize = "4"; 
QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize); 
exit; 
?> */
    /*
     *  文件输出二维码
     *   include('phpqrcode/phpqrcode.php'); 
// 二维码数据 
$data = 'http://www.jb51.net'; 
// 生成的文件名 
$filename = '1111.png'; 
// 纠错级别：L、M、Q、H 
$errorCorrectionLevel = 'L'; 
// 点的大小：1到10 
$matrixPointSize = 4; 
QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
*/
    /* 
     * 生成中间带logo的二维码
     * 
     * 
     * include('phpqrcode/phpqrcode.php'); 
$value='http://www.jb51.net'; 
$errorCorrectionLevel = 'L'; 
$matrixPointSize = 6; 
QRcode::png($value, 'xiangyang.png', $errorCorrectionLevel, $matrixPointSize, 2); 
echo "QR code generated"."<br />"; 
$logo = 'logo.png'; 
$QR = 'xiangyang.png';
if($logo !== FALSE) 
{
$QR = imagecreatefromstring(file_get_contents($QR)); 
$logo = imagecreatefromstring(file_get_contents($logo)); 
$QR_width = imagesx($QR); 
$QR_height = imagesy($QR); 
$logo_width = imagesx($logo); 
$logo_height = imagesy($logo); 
$logo_qr_width = $QR_width / 5; 
$scale = $logo_width / $logo_qr_width; 
$logo_qr_height = $logo_height / $scale; 
$from_width = ($QR_width - $logo_qr_width) / 2; 
imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height); 
} 
imagepng($QR,'xiangyanglog.png');  */
    /* <?php
微信api接口处理函数
  
  //天气查询函数
  function _getWeather($keyword){
        $cityname = trim(substr($keyword,6,strlen($keyword)-6));
        $url = "http://api.map.baidu.com/telematics/v2/weather?location={$cityname}&ak=1a3cde429f38434f1811a75e1a90310c";
        $fa=file_get_contents($url);
        $f=simplexml_load_string($fa);
        $city=$f->currentCity;
        $da1=$f->results->result[0]->date;
        $da2=$f->results->result[1]->date;
        $da3=$f->results->result[2]->date;        
        $w1=$f->results->result[0]->weather;
        $w2=$f->results->result[1]->weather;
        $w3=$f->results->result[2]->weather;        
        $p1=$f->results->result[0]->wind;
        $p2=$f->results->result[1]->wind;
        $p3=$f->results->result[2]->wind;
        $q1=$f->results->result[0]->temperature;
        $q2=$f->results->result[1]->temperature;
        $q3=$f->results->result[2]->temperature;
        $d1=$cityname.$da1.$w1.$p1.$q1;
        $d2=$cityname.$da2.$w2.$p2.$q2;
        $d3=$cityname.$da3.$w3.$p3.$q3;
        $msg =<<<str
         $d1
         $d2
         $d3
str;
        return $msg;
      }
  
  
      
  //翻译函数
  function _fanyi($keyword){
      $keyword = trim(substr($keyword,6,strlen($keyword)-6));
      $tranurl="http://openapi.baidu.com/public/2.0/bmt/translate?client_id=9peNkh97N6B9GGj9zBke9tGQ&q={$keyword}&from=auto&to=auto";//百度翻译地址
      $transtr=file_get_contents($tranurl);//读入文件
      $transon=json_decode($transtr);//json解析
      //print_r($transon);
      $contentStr = $transon->trans_result[0]->dst;//读取翻译内容
      return $contentStr;
      }
      
  
  //快递查询函数
  function _getDindan($keyword){
        $keyword = trim(substr($keyword,6,strlen($keyword)-6));
          $status=array('0'=>'查询出错','1'=>'暂无记录','2'=>'在途中','3'=>'派送中','4'=>'已签收','5'=>'拒收','6'=>'疑难件','7'=>'退回');//构建快递状态数组
        $kuaidiurl="http://www.aikuaidi.cn/rest/?key=ff4735a30a7a4e5a8637146fd0e7cec9&order={$keyword}&id=shentong&show=xml";//快递地址
        $kuaidistr=file_get_contents($kuaidiurl);//读入文件
        $kuaidiobj=simplexml_load_string($kuaidistr);//xml解析
        $kuaidistatus = $kuaidiobj->Status;//获取快递状态
        $kuaistr=strval($kuaidistatus);//对象转换为字符串
        $contentStr0 =$status[$kuaistr];//根据数组返回
        foreach ($kuaidiobj->Data->Order as $a)
         {    
         foreach ($a->Time as $b)
           {
            foreach ($a->Content as $c)
            {$m.="{$b}{$c}";}
            }
         }
        //遍历获取快递时间和事件
        $contentStr="你的快递单号{$keyword}{$contentStr0}{$m}";
        return $contentStr;
      }
      
     //小黄鸡函数
     function _xiaohuangji($keyword){
         $keyword = trim(substr($keyword,1,strlen($keyword)-1));
         $strurl="http://sandbox.api.simsimi.com/request.p?key=e0f1c913-fe3a-40ad-904f-5467677a38b7&lc=ch&text='{$keyword}'";
         $fa=file_get_contents($strurl);
         $strjson=json_decode($fa);
         $contentStr = $strjson->response;
         return $contentStr;
         }
 ?>
复制代码
 */
}