<?php
/**
*
* 版权所有：e次元科技<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-20
* 版    本：1.0.0
* 功能说明：标签控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
use Vendor\Tree;

class TagController extends ComController {

	public function index(){
		$category = M('Tag')->order('sort asc')->select();
		$this->assign('category',$category);
		$this -> display();
	}
	
	public function del(){
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		if($id){
			$data['tagid'] = $id;
			if(M('TagArticleMap')->where('tagid='.$id)->count()){
			     M('TagArticleMap')->where('tagid='.$id)->delete();//存在索引删除
			}
			M('Tag')->where('tagid='.$id)->delete();
			addlog('删除标签，ID：'.$id);
			die('1');
		}else{
			die('0');
		}

	}
	
	public function edit(){
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		$currentcategory = M('Tag')->where('tagid='.$id)->find();
		$this->assign('currentcategory',$currentcategory);
		$this -> display();
	}
	
	public function add(){
		$this -> display();
	}
	
	public function update($act=null){
		if($act=='sort'){
			$id = I('post.id',0,'intval');
			if(!$id){
				die('0');
			}
			$o = I('post.o',0,'intval');
			M('Tag')->data(array('sort'=>$o))->where("tagid='{$id}'")->save();
			addlog('标签修改排序，ID：'.$id);
			die('1');
		}
		$data=M('Tag')->create();
		if(!$data['tagname']){
			die('0');
		}
		if($id){
			if(M('Tag')->data($data)->where('tagid='.$id)->save()){
				addlog('标签修改，ID：'.$id.'，名称：'.$data['tagname']);
				die('1');
			}
		}else{
			$id = M('Tag')->data($data)->add();
			if($id){
				addlog('新增标签，ID：'.$id.'，名称：'.$data['tagname']);
			}
		}
		die('0');
	}
}	