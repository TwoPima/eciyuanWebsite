<?php
/**
*
* 版权所有：亿次元科技<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-21
* 版    本：1.0.0
* 功能说明：前台文章控制器。
*
**/
namespace Mobile\Controller;
use Think\Controller;
use Vendor\Page;
class LifeController extends ComController {
    //首页资讯详细
    public function index(){
        import('ORG.Net.IpLocation');// 导入IpLocation类
        $Ip = new \Org\Net\IpLocation('UTFWry.dat');
        $nowIp = get_client_ip();
        $location = $Ip->getlocation($nowIp); // 获取某个IP地址所在的位置
        $info = $location['area'];//转码$info = iconv('gbk','utf-8',$location['country'].$location['area']);$location['country'].$location['area'];
        
        $where['status']="2";
        
        $parentFood=M('Food')->where($where)->field("s_province")->distinct(true)->select();//提取城市
        $this->assign('FoodCity',$parentFood);
        
        $parentClothes=M('Clothes')->where($where)->field("s_province")->distinct(true)->select();//提取城市
        $this->assign('ClothesCity',$parentClothes);
        
        $parentHousing=M('Housing')->where($where)->field("s_province")->distinct(true)->select();//提取城市
        $this->assign('HousingCity',$parentHousing);
        
        $parentCarry=M('Carry')->where($where)->field("s_province")->distinct(true)->select();//提取城市
        $this->assign('CarryCity',$parentCarry);
        
        /* 查询详细资讯条件 */
        $map['s_provice']=array('like','%$info%');
        $map['status']="2";
        
        //提服装
        $resultClothes=M('Clothes')->where($map)->order('create_time desc')->limit(6)->select();
        $this->assign('clothes',$resultClothes);
        //美食
        $resultFood=M('Food')->where($map)->order('create_time desc')->limit(6)->select();
        $this->assign('food',$resultFood);
        //房产
        $resultHousing=M('Housing')->where($map)->order('create_time desc')->limit(6)->select();
        $this->assign('housing',$resultHousing);
        //行
        $resultCarry=M('Carry')->where($map)->order('create_time desc')->limit(6)->select();
        $this->assign('carry',$resultCarry);
        
		
		$this -> display();
    }
	
	//单页
    public function detail(){
		$where1['aid'] = $_GET['id'];
		$model=ucfirst($_GET['type']);
		$resultArt= M($model)->where($where1)->find();
		//上一篇
		$front=M($model)->where("aid<".$_GET['id'])->order('aid desc')->find();
		//下一篇
		$after=M($model)->where("aid>".$_GET['id'])->order('aid desc')->find();
		$this->assign('artDetail',$resultArt);
		$this->assign('front',$front);//上一条
		$this->assign('after',$after);//下一条
		$this->display();
    }
	//列表
    public function more(){
        if ($_GET['type']=="housing"){
            $m = M('Housing');
          //  $wheretype['tag']="housing";
            $title="房地产";
            $titleIcon="fa-building";
        }elseif ($_GET['type']=="food"){
            $m = M('Food');
           // $wheretype['tag']="food";
            $titleIcon="fa-inbox";
            $title="美食";
        }elseif ($_GET['type']=="carry"){
            $m = M('Carry');
          //  $wheretype['tag']="carry";
            $title="旅行";
            $titleIcon="fa-car";
        }else{
            $m = M('Clothes');
           // $wheretype['tag']="clothes";
            $title="服饰";
            $titleIcon="fa-sitemap";
        }
		$count      = $m->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       = $Page->show();// 分页显示输出
		$list  = $m->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);	
		$this->assign('title',$title);	
		$this->assign('titleIcon',$titleIcon);	
		$this->assign('page',$show);// 赋值分页输出
		$this -> display();
    }
    //搜索部分
    public function search(){
       
      $p = isset($_GET['p'])?intval($_GET['p']):'1';
		$field = isset($_GET['field'])?$_GET['field']:'';
		$keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
		$order = isset($_GET['order'])?$_GET['order']:'DESC';
		$where = '';
		
		$prefix = C('DB_PREFIX');
		if($order == 'asc'){
			$order = "{$prefix}member.t asc";
		}elseif(($order == 'desc')){
			$order = "{$prefix}member.t desc";
		}else{
			$order = "{$prefix}member.uid asc";
		}
		if($keyword <>''){
			if($field=='user'){
				$where = "{$prefix}member.user LIKE '%$keyword%'";
			}
			if($field=='phone'){
				$where = "{$prefix}member.phone LIKE '%$keyword%'";
			}
			if($field=='qq'){
				$where = "{$prefix}member.qq LIKE '%$keyword%'";
			}
			if($field=='email'){
				$where = "{$prefix}member.email LIKE '%$keyword%'";
			}
		}
		
		
		$user = M('member');
		$pagesize = 10;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count = $user->count();
		
		$list  = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title")->order($order)->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")->where($where)->limit($offset.','.$pagesize)->select();
		
		
		//$user->getLastSql();
		$page	=	new \Think\Page($count,$pagesize); 
		$page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);
		$group = M('auth_group')->field('id,title')->select();
		$this->assign('group',$group);
		$this -> display();
    }
    //按照城市列出清真寺
    public function cityDetail(){
        if ($_GET['type']=="housing"){
            $model = M('Housing');
            $title="房地产";
            $titleIcon="fa-building";
        }elseif ($_GET['type']=="food"){
            $model = M('Food');
            // $wheretype['tag']="food";
            $titleIcon="fa-inbox";
            $title="美食";
        }elseif ($_GET['type']=="carry"){
            $model = M('Carry');
            //  $wheretype['tag']="carry";
            $title="旅行";
            $titleIcon="fa-car";
        }else{
            $model = M('Clothes');
            // $wheretype['tag']="clothes";
            $title="服饰";
            $titleIcon="fa-sitemap";
        }
    
      
        $info=$_GET['city'];
        $map['s_provice']=array('like','%$info%');
        $map['status']="2";
        
        $count      = $model->where($map)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        
        $re=$model->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();//提取
        $this->assign('detail',$re);
        $this->assign('city',$_GET['city']);
        $this->assign('titleIcon',$titleIcon);
        $this->assign('page',$show);// 赋值分页输出
    
        $this -> display();
    }	 
}