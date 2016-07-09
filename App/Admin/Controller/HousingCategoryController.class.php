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

class HousingCategoryController extends ComController {

	public function index(){
	
		
		$category = M('HousingCategory')->order('sort asc')->select();
		$category = $this->getMenu($category);
		$this->assign('category',$category);
		$this -> display();
	}
	
	public function del(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		if($id){
			$data['id'] = $id;
			$category = M('HousingCategory');
			if($category->where('pid='.$id)->count()){
				die('2');//存在子类，严禁删除。
			}else{
				$category->where('id='.$id)->delete();
				addlog('删除分类，ID：'.$id);
			}
			die('1');
		}else{
			die('0');
		}

	}
	
	public function edit(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		$currentcategory = M('HousingCategory')->where('id='.$id)->find();
		$this->assign('currentcategory',$currentcategory);

		$category = M('HousingCategory')->field('id,pid,name')->order('sort asc')->select();
		$tree = new Tree($category);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$category = $tree->get_tree(0,$str, $currentcategory['pid']);
		
		$this->assign('category',$category);
		$this -> display();
	}
	
	public function add(){
		
		$pid = isset($_GET['pid'])?intval($_GET['pid']):0;
		$category = M('HousingCategory')->field('id,pid,name,link,tag')->order('sort asc')->select();
		$tree = new Tree($category);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$category = $tree->get_tree(0,$str, $pid);
		
		$this->assign('category',$category);
		$this -> display();
	}
	
	public function update($act=null){
		if($act=='order'){
			$id = I('post.id',0,'intval');
			if(!$id){
				die('0');
			}
			$o = I('post.o',0,'intval');
			M('HousingCategory')->data(array('sort'=>$o))->where("id='{$id}'")->save();
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
			if(M('HousingCategory')->data($data)->where('id='.$id)->save()){
				addlog('文章分类修改，ID：'.$id.'，名称：'.$data['name']);
				die('1');
			}
		}else{
			$id = M('HousingCategory')->data($data)->add();
			if($id){
				addlog('新增分类，ID：'.$id.'，名称：'.$data['name']);
				die('1');
			}
		}
		die('0');
	}
}