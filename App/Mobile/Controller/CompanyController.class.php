<?php
/**
*
* 版权所有：亿次元科技（www.eciyuan.net）
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-06-21
* 版    本：1.0.0
* 功能说明：微信端-企业控制器演示。
*
**/
namespace Mobile\Controller;

use Think\Controller;
use Mobile\Com\Wechat;
use Mobile\Com\WechatAuth;

class CompanyController extends ComController {
    public function index(){
        import('ORG.Net.IpLocation');// 导入IpLocation类
        $Ip = new \Org\Net\IpLocation('UTFWry.dat'); 
        $nowIp = get_client_ip();
        $location = $Ip->getlocation($nowIp); // 获取某个IP地址所在的位置
        $info = $location['area'];//转码$info = iconv('gbk','utf-8',$location['country'].$location['area']);$location['country'].$location['area'];
        
        $model=M('Company');
        
            $where['type']="2";
            $where['status']="2";
            $parent=$model->where($where)->field("s_province")->distinct(true)->select();//提取城市
            $this->assign('city',$parent);
            
            $map['s_provice']=array('like','%$info%');
            $map['type']="2";
            $map['status']="2";
            $CompanyRe=$model->where($map)->select();//提取清真寺
            $this->assign('Company',$CompanyRe);
            
        $this->display();
    }
    /* 入驻 */
    public function add(){
        if ($_POST['submitAdd']=="1"){
                 $result = D('Company')->create();
                 if ($result){
                    // 验证通过 可以进行其他数据操作
                    //$name=$_POST['name'];
                    $result['name']=$_POST['name'];
                    $result['password']=password($_POST['password']);
                    $result['license'] = I('post.license','','strip_tags');
                    $result['qingzhen'] = I('post.qingzhen','','strip_tags');
                    $id=D('Company')->add($result);
                   /*  cookie('CompanyName',$name);
                    cookie('CompanyId',$id); */
                    if ($id) {
                    	$this->success('恭喜您，入驻成功！请耐心等待审核！',U('Life/index'));
                    }else{
                    	$this->error('入驻资料有错误，请返回重新核对资料！');
                    }
            }else {
                $this->error(D('Company')->getError());
            }
               
        }else{
           
                $this->display();
            }
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
            $model=M('CompanyNotice');
            $data['authorID']=cookie('CompanyId');//发布者ID	
            $data['title']=$_POST['title'];
            $data['content']=$_POST['content'];
            $data['uploadedby']=cookie('CompanyName');//发布者名称
            $data['create_time']=time();
            $data['update_time']=time();
            $data['IP']=get_client_ip();
            $result=$model->add($data);
            if ($result){
                $this->success('发布成功',U('Company/index'));
            }
        }else{
             $user =cookie('CompanyId');
            if (empty($user)) {
                $this->error('您还没有登陆，请登陆后发布通知',U('Login/index'));
            }
            $model=M('Company');
            $where['id']=$user;
            $result=$model->where($where)->find();
            $this->assign(CompanyId,$result['id']);
            $this -> display();
        }
    }
    /* 主持个人中心 */
    public function perCompany(){
        if (!cookie('?CompanyId')) {
        	//登录跳转
        	  $whereArt['cateid']=cookie('CompanyId');//资讯ID
        	  $where['id']=cookie('CompanyId');//基本信息ID
        	  //我的通知
        	  $whereNotice['authorID']=cookie('CompanyId');
        	  $notice = M('CompanyNotice')->where($whereNotice)->order('update_time asc')->select();
        	  $this->assign('notice',$notice);
        }else {
        	//列出信息跳转过来  游客
        	$whereArt['cateid']=$_GET['id'];//资讯ID
        	$where['id']=$_GET['id'];//基本信息ID
        }
        //相关资讯
    	$model=M('Company');
        $article = M('CompanyArticle')->where($whereArt)->order('update_time asc')->select();
        $this->assign('article',$article);
    	//基本信息
        $result=$model->where($where)->find();
        $this->assign(detail,$result);
            
        $this -> display();
    }
    //单页  资讯
    public function detail(){
        $where['aid'] = $_GET['id'];
        //$model=ucfirst($_GET['type']);
        $article = M('CompanyArticle')->where($where)->find();
        $this->assign('article',$article);
        $this -> display();
    }
    //单页  通知
    public function noticeDetail(){
        $where['aid'] = $_GET['id'];
        //$model=ucfirst($_GET['type']);
        $article = M('CompanyArticle')->where($where)->find();
        $this->assign('article',$article);
        $this -> display();
    }
    //按照城市列出清真寺
    public function cityDetail(){
        $model=M('Company');
        
        $this->assign('city',$_GET['city']);
        
        $info=$_GET['city'];
        $map['s_provice']=array('like','%$info%');
        $map['status']="2";
        $CompanyRe=$model->where($map)->select();//提取清真寺
        $this->assign('Company',$CompanyRe);
        
        $this -> display();
    }
}