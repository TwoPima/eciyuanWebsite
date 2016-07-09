<?php
/**
*
* 版权所有：亿次元科技（www.eciyuan.net）
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-06-21
* 版    本：1.0.0
* 功能说明：微信端-清真寺控制器演示。
*
**/
namespace Mobile\Controller;

use Think\Controller;

class MosqueController extends ComController {
    public function index(){
       /*  //城市列表
        $model=M('Region');
        $parent=$model->where("pid=1")->select();//省级
        foreach($parent as $n=> $val){
            $parent[$n]['voo']=$model->where('pid='.$val['id'].'')->limit(5)->select();
        }
        $this->assign('list',$parent); */
        import('ORG.Net.IpLocation');// 导入IpLocation类
        $Ip = new \Org\Net\IpLocation('UTFWry.dat'); 
        $nowIp = get_client_ip();
        $location = $Ip->getlocation($nowIp); // 获取某个IP地址所在的位置
        $info = $location['area'];//转码$info = iconv('gbk','utf-8',$location['country'].$location['area']);$location['country'].$location['area'];
        
        $model=M('Mosque');
        
            $where['type']="2";
            $where['status']="2";
            $parent=$model->where($where)->field("s_province")->distinct(true)->select();//提取城市
            $this->assign('city',$parent);
            
            $map['s_provice']=array('like','%$info%');
            $map['type']="2";
            $map['status']="2";
            $mosqueRe=$model->where($map)->select();//提取清真寺
            $this->assign('mosque',$mosqueRe);
            
        $this->display();
    }
    /* 入驻 */
    public function add(){
        if ($_POST['submitAdd']=="1"){
                 $result = D('Mosque')->create();
                 if ($result){
                    // 验证通过 可以进行其他数据操作
                    $result['password']=password($_POST['password']);
                    $this->qrcode($_POST['name'],D('Mosque')->add($result));
                    
            }else {
                $this->error(D('Mosque')->getError());
            }
               
        }else{
           
                $this->display();
            }
    }
    
    /*生成二维码 */
  //引用地址http://www.xcsoft.cn/public/qrcode
//text:需要生成二维码的数据，默认:http://www.xcsoft.cn
//size:图片每个黑点的像素,默认4
//level:纠错等级,默认L
//padding:图片外围空白大小，默认2
//logo:全地址，默认为空
//完整引用地址:http://www.xcsoft.cn/public/qrcode?text=http://www.xcsoft.cn&size=4&level=L&padding=2&logo=http://www.xcsoft.cn/Public//images/success.png
  public function qrcode($url='http://www.eciyuan.net',$id,$size='4',$level='L',$padding=2,$logo=true){
         $url=I('get.text')?I('get.text'):$url;
        $size=I('get.size')?I('get.size'):$size;
        $level=I('get.level')?I('get.level'):$level;
        $logo=I('get.logo')?I('get.logo'):$logo;
        $padding=I('get.padding')?I('get.padding'):$padding;
        
        $path='./Public/Mobile/qrcode/';//图片输出路径
        mkdir($path);
        $tempTime=time().rand(1,10000000);
        $filename=$path.$tempTime.'qrcode.png';//生成文件名
        
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        
       Vendor('phpqrcode.phpqrcode');
       $object = new \QRcode();
       ob_clean();//清除缓冲区
       $result=$object->png($url,$filename, $errorCorrectionLevel, $matrixPointSize, 2);
       if ($result){
           //将二维码信息保存到数据库
           $model=M('Mosque');
           $where['id']=cookie('mosqueId');
           $data['qrcode']=$filename;
           $saveQrcodeRe=$model->where($where)->save($data);
       }
       $this->assign('filename','/'.$filename);
        header("Content-Type:image/jpg");
        imagepng($filename); 
        $this->display('qrcode');
} 
    /* 通知 */
    public function notice(){
        if ($_POST['submit']=="1"){
            if (empty($_POST['title'])){
                $this->error('标题必须填写');
            }
            if (empty($_POST['content'])){
                $this->error('内容必须填写');
            }
            $model=M('MosqueNotice');
            $data['authorID']=$_POST['mosqueId'];//发布者ID
            $data['title']=$_POST['title'];
            $data['content']=$_POST['content'];
            $data['uploadedby']=cookie('user');//发布者名称
            $data['create_time']=time();
            $data['IP']=get_client_ip();
            $result=$model->add($data);
            if ($result){
                $this->success('发布成功',U('Mosque/index'));
            }
        }else{
             $user =cookie('mosqueId');
            $url = U("login/index");
            if (empty($user)) {
                $this->error('您还没有登陆，请登陆后发布通知',U('Mosque/index'));
            }
            $model=M('Mosque');
            $where['id']=$user;
            $result=$model->where($where)->find();
            $this->assign(mosqueId,$result['id']);
            $this -> display();
        }
    }
    /* 主持个人中心 */
    public function perMosque(){
        $model=M('Mosque');
        
        $whereArt['sid']=$_GET['id'];
        $article = M('MosqueArticle')->where($whereArt)->order('update_time asc')->select();
        $this->assign('article',$article);
        
        $where['id']=$_GET['id'];
        $result=$model->where($where)->find();
        $this->assign(detail,$result);
        
        //判断是游客还是本人
        if (!cookie('mosqueName')){
            
            
            //游客
          
        }else{
            //本人
           
            /*   <if condition="isset($_COOKIES['mosqueName'])"> */
            
            $whereNotice['authorID']=$_GET['id'];
            $notice = M('MosqueNotice')->where($whereNotice)->order('update_time asc')->select();
            $this->assign('notice',$notice);
        }
        $this -> display();
    }
    //单页  资讯
    public function detail(){
        $where['aid'] = $_GET['id'];
        //$model=ucfirst($_GET['type']);
        $article = M('MosqueArticle')->where($where)->find();
        $this->assign('article',$article);
        $this -> display();
    }
    //单页  通知
    public function noticeDetail(){
        $where['aid'] = $_GET['id'];
        //$model=ucfirst($_GET['type']);
        $article = M('MosqueArticle')->where($where)->find();
        $this->assign('article',$article);
        $this -> display();
    }
    //按照城市列出清真寺
    public function cityDetail(){
        $model=M('Mosque');
        
        $this->assign('city',$_GET['city']);
        
        $info=$_GET['city'];
        $map['s_provice']=array('like','%$info%');
        $map['status']="2";
        $mosqueRe=$model->where($map)->select();//提取清真寺
        $this->assign('mosque',$mosqueRe);
        
        $this -> display();
    }
}