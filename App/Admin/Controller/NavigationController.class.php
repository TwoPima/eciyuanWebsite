<?php
/**
*
* 版权所有：e次元科技<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-20
* 版    本：1.0.0
* 功能说明：产品功能控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
use Vendor\Tree;

class NavigationController extends ComController {

	public function add(){
		
		$categoryNavigation = M('CategoryNavigation')->field('id,pid,name')->order('o asc')->select();
		$tree = new Tree($categoryNavigation);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$categoryNavigation = $tree->get_tree(0,$str,0);
		$this->assign('categoryNavigation',$categoryNavigation);//导航
		$this -> display();
	}
		
	public function index($sid=0,$p=1){
		
		$sid = intval($sid);
		$p = intval($p)>0?$p:1;
		
		$Navigation = M('Navigation');
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$prefix = C('DB_PREFIX');
		if($sid){
			$where = "{$prefix}Navigation.sid=$sid";
		}else{
			$where = '';
		}
		$count = $Navigation->where($where)->count();
		$list  = $Navigation->field("{$prefix}Navigation.*,{$prefix}category_navigation.name")->where($where)->order("{$prefix}navigation.aid desc")->join("{$prefix}category_navigation ON {$prefix}category_navigation.id = {$prefix}navigation.sid")->limit($offset.','.$pagesize)->select();
		
		$page	=	new \Think\Page($count,$pagesize); 
		$page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);
		$this -> display();
	}
	
	public function del(){
		
		$aids = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($aids){
			if(is_array($aids)){
				$aids = implode(',',$aids);
				$map['aid']  = array('in',$aids);
			}else{
				$map = 'aid='.$aids;
			}
			if(M('Navigation')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}

	}
	
	public function edit($aid){
		
		$aid = intval($aid);
		$Navigation = M('Navigation')->where('aid='.$aid)->find();
		if($Navigation){
			
			$categoryNavigation = M('categoryNavigation')->field('id,pid,name')->order('o asc')->select();
			$tree = new Tree($categoryNavigation);
			$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
			$categoryNavigation = $tree->get_tree(0,$str,$Navigation['sid']);
			$this->assign('categoryNavigation',$categoryNavigation);//导航
			
			$this->assign('Navigation',$Navigation);
		}else{
			$this->error('参数错误！');
		}
		$this -> display();
	}
	
	public function update($aid=0){
		
		$aid = intval($aid);
		$data['sid'] = isset($_POST['sid'])?intval($_POST['sid']):0;
		$data['title'] = isset($_POST['title'])?$_POST['title']:false;
		$data['keywords'] = I('post.keywords','','strip_tags');
		$data['description'] = I('post.description','','strip_tags');
		$data['content'] = isset($_POST['content'])?$_POST['content']:false;
		$data['thumbnail'] = I('post.thumbnail','','strip_tags');
		$data['t'] = time();
		if(!$data['sid'] or !$data['title'] or !$data['content']){
			$this->error('警告！分类、标题及内容为必填项目。');
		}
		if($aid){
			M('Navigation')->data($data)->where('aid='.$aid)->save();
			addlog('编辑信息，AID：'.$aid);
			$this->success('恭喜！编辑成功！');
		}else{
			$aid = M('Navigation')->data($data)->add();
			if($aid){
				addlog('新增内容，AID：'.$aid);
				$this->success('恭喜！新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}
			
		}
	}
}