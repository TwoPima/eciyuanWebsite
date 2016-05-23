<?php
/**
*
* 版权所有：e次元科技<qwadmin.qiawei.com>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-21
* 版    本：1.0.0
* 功能说明：前台控制器_产品展示。
*
**/
namespace Home\Controller;
use Think\Controller;
use Vendor\Page;
class CaseController extends ComController {
    public function index(){
		$this -> display();
    }
	public function detail(){
	    //提取栏目内容
	    $where['id']=$_GET['sid'];
	    $result=M('Category')->where($where)->select();
	    $this->assign('CateName',$result);
	    //提取文章详情
	    $detail=M('Case')->where("aid=".$_GET['aid'])->select();
	    //上一篇
	    $front=M('Case')->where("aid<".$_GET['aid'])->order('aid desc')->find();
	    //下一篇
	    $after=M('Case')->where("aid>".$_GET['aid'])->order('aid desc')->find();
	    $this->assign('detail',$detail);
	    $this->assign('front',$front);//上一条
	    $this->assign('after',$after);//下一条
	    $this->display();
	}
	//单页
    public function single($aid){
		
		$aid = intval($aid);
		$Case = M('Case')->where('aid='.$aid)->find();
		$this->assign('Case',$Case);
		$this->assign('nav',$aid);
		$this -> display();
    }
	//文章
    public function article($aid){
		
		$aid = intval($aid);
		$Case = M('Case')->where('aid='.$aid)->find();
		$this->assign('Case',$Case);
		$this -> display();
    }
	
	//列表
    public function more(){
		    $model=M('Case');
		    $article_list=$model->order('t ASC')->select();
		    $count =$model->count();
		    $page	=	new \Think\Page($count,10);
	        $showPage = $page->show();
	        $this->assign('article_list',$article_list);
	        $this->assign("page", $showPage);
	        $this->display();
	}
}