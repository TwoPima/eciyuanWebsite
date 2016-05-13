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

class ProductController extends ComController {

	public function add(){
		
		$categoryProduct = M('CategoryProduct')->field('id,pid,name')->order('o asc')->select();
		$tree = new Tree($categoryProduct);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$categoryProduct = $tree->get_tree(0,$str,0);
		$this->assign('categoryProduct',$categoryProduct);//导航
		$this -> display();
	}
		
	public function index($sid=0,$p=1){
		
		$sid = intval($sid);
		$p = intval($p)>0?$p:1;
		
		$Product = M('Product');
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$prefix = C('DB_PREFIX');
		if($sid){
			$where = "{$prefix}Product.sid=$sid";
		}else{
			$where = '';
		}
		$count = $Product->where($where)->count();
		$list  = $Product->field("{$prefix}Product.*,{$prefix}category_product.name")->where($where)->order("{$prefix}Product.aid desc")->join("{$prefix}category_product ON {$prefix}category_product.id = {$prefix}Product.sid")->limit($offset.','.$pagesize)->select();
		
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
			if(M('Product')->where($map)->delete()){
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
		$Product = M('Product')->where('aid='.$aid)->find();
		if($Product){
			
			$categoryProduct = M('categoryProduct')->field('id,pid,name')->order('o asc')->select();
			$tree = new Tree($categoryProduct);
			$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
			$categoryProduct = $tree->get_tree(0,$str,$Product['sid']);
			$this->assign('categoryProduct',$categoryProduct);//导航
			
			$this->assign('Product',$Product);
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
			M('Product')->data($data)->where('aid='.$aid)->save();
			addlog('编辑信息，AID：'.$aid);
			$this->success('恭喜！编辑成功！');
		}else{
			$aid = M('Product')->data($data)->add();
			if($aid){
				addlog('新增内容，AID：'.$aid);
				$this->success('恭喜！新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}
			
		}
	}
}