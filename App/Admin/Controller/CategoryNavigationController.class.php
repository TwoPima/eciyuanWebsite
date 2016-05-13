<?php
/**
*
* 版权所有：e次元科技<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-20
* 版    本：1.0.0
* 功能说明：导航菜单分类控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
use Vendor\Tree;

class CategoryNavigationController extends ComController {

	public function index(){
	
		
		$CategoryNavigation = M('CategoryProduct')->field('id,pid,name,o')->order('o asc')->select();
		$CategoryProduct = $this->getMenu($CategoryProduct);
		$this->assign('CategoryProduct',$CategoryProduct);
		$this -> display();
	}
	
	public function del(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		if($id){
			$data['id'] = $id;
			$CategoryProduct = M('CategoryProduct');
			if($CategoryProduct->where('pid='.$id)->count()){
				die('2');//存在子类，严禁删除。
			}else{
				$CategoryProduct->where('id='.$id)->delete();
				addlog('删除分类，ID：'.$id);
			}
			die('1');
		}else{
			die('0');
		}

	}
	
	public function edit(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		$currentCategoryProduct = M('CategoryProduct')->where('id='.$id)->find();
		$this->assign('currentCategoryProduct',$currentCategoryProduct);

		$CategoryProduct = M('CategoryProduct')->field('id,pid,name')->order('o asc')->select();
		$tree = new Tree($CategoryProduct);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$CategoryNavigation = $tree->get_tree(0,$str, $currentCategoryNavigation['pid']);
		
		$this->assign('CategoryNavigation',$CategoryNavigation);
		$this -> display();
	}
	
	public function add(){
		
		$pid = isset($_GET['pid'])?intval($_GET['pid']):0;
		$CategoryNavigation = M('CategoryNavigation')->field('id,pid,name')->order('o asc')->select();
		$tree = new Tree($CategoryNavigation);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$CategoryNavigation = $tree->get_tree(0,$str, $pid);
		
		$this->assign('CategoryNavigation',$CategoryNavigation);
		$this -> display();
	}
	
	public function update($act=null){
		if($act=='order'){
			$id = I('post.id',0,'intval');
			if(!$id){
				die('0');
			}
			$o = I('post.o',0,'intval');
			M('CategoryNavigation')->data(array('o'=>$o))->where("id='{$id}'")->save();
			addlog('分类修改排序，ID：'.$id);
			die('1');
		}		$id = isset($_POST['id'])?intval($_POST['id']):false;
		$data['pid'] = isset($_POST['pid'])?intval($_POST['pid']):0;
		$data['name'] = isset($_POST['name'])?trim($_POST['name']):false;
		$data['keywords'] = isset($_POST['keywords'])?strip_tags(trim($_POST['keywords'])):'';
		$data['description'] = isset($_POST['description'])?strip_tags(trim($_POST['description'])):'';
		$data['o'] = isset($_POST['o'])?intval($_POST['o']):0;
		if(!$data['name']){
			die('0');
		}
		if($id){
			if(M('CategoryNavigation')->data($data)->where('id='.$id)->save()){
				addlog('分类修改，ID：'.$id.'，名称：'.$name);
				die('1');
			}
		}else{
			$id = M('CategoryNavigation')->data($data)->add();
			if($id){
				addlog('新增分类，ID：'.$id.'，名称：'.$data['name']);
				die('1');
			}
		}
		die('0');
	}
}