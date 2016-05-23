<?php
/**
*
* 版权所有：e次元科技<qwadmin.qiawei.com>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-21
* 版    本：1.0.0
* 功能说明：前台控制器演示。
*
**/
namespace Home\Controller;
use Think\Controller;
use Vendor\Page;
class IndexController extends ComController {
    public function index(){
        //资讯信息
        $whereNewsIndustry['tag']="industry";
        $whereTrends['tag']="trends";
        $whereSkill['tag']="skill";
        $parentIndustryId=M('Category')->where($whereNewsIndustry)->field('id')->find();
        $parentNewsTrendsId=M('Category')->where($whereTrends)->field('id')->find();
        $parentNewsSkillId=M('Category')->where($whereSkill)->field('id')->find();
        $newsIndustry=M('Article')->where('sid='.$parentIndustryId['id'])->order("aid desc")->limit(5)->select();
        $newsTrends=M('Article')->where('sid='.$parentNewsTrendsId['id'])->order("aid desc")->limit(5)->select();
        $newsSkill=M('Article')->where('sid='.$parentNewsSkillId['id'])->order("aid desc")->limit(5)->select();
        $this->assign('newsIndustry',$newsIndustry);
        $this->assign('trends',$newsTrends);
        $this->assign('skill',$newsSkill);
        //案例成果
        $case=M('Case')->order("aid desc")->limit(6)->select();
        $this->assign('case',$case);
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
    /*  表单反馈*/
    public function feedback(){
        $data['name'] = isset($_POST['name'])?$_POST['name']:false;
        $data['email'] = isset($_POST['email'])?$_POST['email']:false;
        $data['content'] = isset($_POST['content'])?$_POST['content']:false;
        $data['create_time'] = time();
        $aid = M('Feedback')->data($data)->add();
        if($aid){
            addlog('新增文章，AID：'.$aid);
            $this->success('恭喜！文章新增成功！');
        }else{
            $this->error('抱歉，未知错误！');
        }
    }
}