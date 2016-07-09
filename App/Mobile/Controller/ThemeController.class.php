<?php
/**
*
* 版权所有：亿次元科技（www.eciyuan.net）
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-06-21
* 版    本：1.0.0
* 功能说明：微信端-前台控制器演示。
*
**/
namespace Mobile\Controller;
use Think\Controller;
use Vendor\Page;
class ThemeController extends ComController {
    public function index(){
    /*    <volist name="list" id="vo">
        <li  style="color:blue;"><b>{$vo.name}</b></li>
        <volist name="vo['voo']" id="sub">
        <li style="color:red;">{$sub.title}</li>
        </volist>
        </volist> */
     $themeCat=M('ThemeCategory');  
    $theme=M('Theme');  
    $parent=$themeCat->select();  
    foreach($parent as $n=> $val){  
        $parent[$n]['voo']=$theme->where('sid='.$val['id'].'')->limit(5)->select();  
    }  
    $this->assign('list',$parent);  
		$this -> display();
    }
  public function detail(){
      $theme=M('Theme');
      $result=$theme->where('aid='.$_GET['id'].'')->find();
      $this->assign('detail',$result);
      //上一篇
      $front=$theme->where("aid<".$_GET['id'])->order('aid desc')->find();
      //下一篇
      $after=$theme->where("aid>".$_GET['id'])->order('aid desc')->find();
      $this->assign('front',$front);//上一条
      $this->assign('after',$after);//下一条
      
      $this -> display();
  }
        //列表
        public function more(){
            $m = M('Theme');
            $wheretype['sid']=$_GET['aid'];
            $wherecatename['id']=$_GET['aid'];
            $titleIcon="fa-sitemap";
            $count      = $m->count();// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出
            
            $list  = $m->order('create_time desc')->where($wheretype)->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('list',$list);
            
            $this->assign('title',M('ThemeCategory')->where($wherecatename)->field('name')->find());
            $this->assign('titleIcon',$titleIcon);
            
            $this->assign('page',$show);// 赋值分页输出
            $this -> display();
        }
}