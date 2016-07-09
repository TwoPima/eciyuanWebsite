<?php
/**
*
* 版权所有：亿次元<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-20
* 版    本：1.0.0
* 功能说明：分类控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
use Vendor\Tree;

class FoodCategoryController extends ComController {

	public function index(){
		$FoodCategory = M('FoodCategory')->order('sort asc')->select();
		$FoodCategory = $this->getMenu($FoodCategory);
		$this->assign('category',$FoodCategory);
		$this -> display();
	}
	
	public function del(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		if($id){
			$data['id'] = $id;
			$FoodCategory = M('FoodCategory');
			if($FoodCategory->where('pid='.$id)->count()){
				die('2');//存在子类，严禁删除。
			}else{
				$FoodCategory->where('id='.$id)->delete();
				addlog('删除分类，ID：'.$id);
			}
			die('1');
		}else{
			die('0');
		}

	}
	
	public function edit(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		$currentFoodCategory = M('FoodCategory')->where('id='.$id)->find();
		$this->assign('currentcategory',$currentFoodCategory);

		$FoodCategory = M('FoodCategory')->field('id,pid,name')->order('sort asc')->select();
		$tree = new Tree($FoodCategory);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$FoodCategory = $tree->get_tree(0,$str, $currentFoodCategory['pid']);
		
		$this->assign('category',$FoodCategory);
		$this -> display();
	}
	
	public function add(){
		
		$pid = isset($_GET['pid'])?intval($_GET['pid']):0;
		$FoodCategory = M('FoodCategory')->field('id,pid,name,link,tag')->order('sort asc')->select();
		$tree = new Tree($FoodCategory);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$FoodCategory = $tree->get_tree(0,$str, $pid);
		
		$this->assign('category',$FoodCategory);
		$this -> display();
	}
	
	public function update($act=null){
		if($act=='order'){
			$id = I('post.id',0,'intval');
			if(!$id){
				die('0');
			}
			$o = I('post.o',0,'intval');
			M('FoodCategory')->data(array('o'=>$o))->where("id='{$id}'")->save();
			addlog('分类修改排序，ID：'.$id);
			die('1');
		}
		$id = isset($_POST['id'])?intval($_POST['id']):false;
		$data['pid'] = isset($_POST['pid'])?intval($_POST['pid']):0;
		$data['name'] = isset($_POST['name'])?trim($_POST['name']):false;
		$data['keywords'] = isset($_POST['keywords'])?strip_tags(trim($_POST['keywords'])):'';
		$data['description'] = isset($_POST['description'])?strip_tags(trim($_POST['description'])):'';
		$data['link'] = isset($_POST['link'])?trim($_POST['link']):'';
		$data['tag'] = isset($_POST['tag'])?trim($_POST['tag']):'';
		$data['sort'] = isset($_POST['sort'])?intval($_POST['sort']):0;
		if(!$data['name']){
			die('0');
		}
		if($id){
			if(M('FoodCategory')->data($data)->where('id='.$id)->save()){
				addlog('文章分类修改，ID：'.$id.'，名称：'.$data['name']);
				die('1');
			}
		}else{
			$id = M('FoodCategory')->data($data)->add();
			if($id){
				addlog('新增分类，ID：'.$id.'，名称：'.$data['name']);
				die('1');
			}
		}
		die('0');
	}
}