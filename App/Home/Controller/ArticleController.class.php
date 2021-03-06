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
namespace Home\Controller;
use Think\Controller;
use Vendor\Page;
class ArticleController extends ComController {
    //首页资讯详细
    public function index(){
        //提取栏目内容
        $where['id']=$_GET['sid'];
        $result=M('Category')->where($where)->select();
        $this->assign('CateName',$result);
        
		$aid = intval($_GET['id']);
		$article = M('Article')->where('aid='.$aid)->select();
		$this->assign('detail',$article);
		$this -> display();
    }
	
	//单页
    public function single($aid){
		
		$aid = intval($aid);
		$article = M('article')->where('aid='.$aid)->find();
		$this->assign('article',$article);
		$this->assign('nav',$aid);
		$this -> display();
    }
	//尾部的处理
    public function footerDetail($word){
		$where['tag']=$_GET['word'];
		$cate=M('Category')->where($where)->field("id")->find();
		$article = M('Article')->where("sid='{$cate['id']}'")->find();
		
		//提取栏目内容
		$whereCate['id']=$cate['id'];
		$result=M('Category')->where($whereCate)->select();
		$this->assign('CateName',$result);
		
		$this->assign('detail',$article);
		$this -> display('index');
    }
	//文章
    public function article($aid){
		
		$aid = intval($aid);
		$article = M('article')->where('aid='.$aid)->find();
		$sort = M('asort')->field('name,id')->where("id='{$article['sid']}'")->find();
		$this->assign('article',$article);
		$this->assign('sort',$sort);
		$this -> display();
    }
	
	//列表
    public function articlelist($sid='',$p=1){
		$sid = intval($sid);
		$p = intval($p)>=1?$p:1;
		$sort = M('asort')->field('name,id')->where("id='$sid'")->find();
		if(!$sort) {
			$this -> error('参数错误！');
		}
		$sorts = M('asort')->field('id')->where("id='$sid' or pid='$sid'")->select();
		$sids = array();
		foreach($sorts as $k=>$v){
			$sids[] = $v['id'];
		}
		$sids = implode(',',$sids);

		$m = M('article');
		$pagesize = 2;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count = $m->where("sid in($sids)")->count();
		$list  = $m->field('aid,title,description,thumbnail,t')->where("sid in($sids)")->order("aid desc")->limit($offset.','.$pagesize)->select();
		//echo $m->getlastsql();
		$params = array(
			'total_rows'=>$count, #(必须)
			'method'    =>'html', #(必须)
			'parameter' =>"/list-{$sid}-?.html",  #(必须)
			'now_page'  =>$p,  #(必须)
			'list_rows' =>$pagesize, #(可选) 默认为15
		);
		$page = new Page($params);
		$this->assign('list',$list);	
		$this->assign('page',$page->show(1));
		$this->assign('sort',$sort);
		$this->assign('p',$p);
		$this->assign('n',$count);

		$this -> display();
    }
}