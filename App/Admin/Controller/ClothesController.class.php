<?php
/**
*
* 版权所有：亿次元科技
* 作    者：马晓成
* 日    期：2016-06-12
* 版    本：1.0.0
* 功能说明：衣服资讯控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
use Vendor\Tree;

class ClothesController extends ComController {

	public function add(){
		
		$category = M('ClothesCategory')->field('id,pid,name')->order('sort asc')->select();
		$tree = new Tree($category);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$category = $tree->get_tree(0,$str,0);
		$this->assign('category',$category);//导航
		
		//企业
		$company = M('Company')->field('id,name')->order('create_time desc')->select();
		$this->assign('company',$company);//导航
		$this -> display();
	}
		
	public function index($sid=0,$p=1){
		
		$sid = intval($sid);
		$p = intval($p)>0?$p:1;
		
		$article = M('Clothes');
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$prefix = C('DB_PREFIX');
		if($sid){
			$where = "{$prefix}Clothes.sid=$sid";
		}else{
			$where = '';
		}
		$count = $article->where($where)->count();
		$list  = $article->field("{$prefix}Clothes.*,{$prefix}Clothes_category.name")->where($where)->order("{$prefix}Clothes.aid desc")->join("{$prefix}Clothes_category ON {$prefix}Clothes_category.id = {$prefix}Clothes.sid")->limit($offset.','.$pagesize)->select();
		
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
			if(M('Clothes')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，文章删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}

	}
	
	public function edit($aid){
		
		$aid = intval($aid);
		$article = M('Clothes')->where('aid='.$aid)->find();
		if($article){
			
			$category = M('ClothesCategory')->field('id,pid,name')->order('sort asc')->select();
			$tree = new Tree($category);
			$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
			$category = $tree->get_tree(0,$str,$article['sid']);
			$this->assign('category',$category);//导航
			
			$this->assign('article',$article);
		/* 	//企业
			$company = M('Company')->field('id,name')->order('create_time desc')->select();
			$this->assign('company',$company);
			//当前企业
			$currentCompany = M('Company')->where('id='.$article['addId'])->find();
			$this->assign('currentCompany',$currentCompany); */
		}else{
			$this->error('参数错误！');
		}
		$this -> display();
	}
	
	public function update($aid=0){
		
		$aid = intval($aid);
		$data['sid'] = isset($_POST['sid'])?intval($_POST['sid']):0;
		$data['addId'] = isset($_POST['addId'])?intval($_POST['addId']):0;
		$data['title'] = isset($_POST['title'])?$_POST['title']:false;
		$data['url'] = isset($_POST['url'])?$_POST['url']:false;
		$data['keywords'] = I('post.keywords','','strip_tags');
		$data['description'] = I('post.description','','strip_tags');
		$data['content'] = isset($_POST['content'])?$_POST['content']:false;
		$data['s_province'] = isset($_POST['s_province'])?trim($_POST['s_province']):'';
		$data['s_county'] = isset($_POST['s_county'])?trim($_POST['s_county']):'';
		$data['s_city'] = isset($_POST['s_city'])?trim($_POST['s_city']):'';
		$data['address'] = isset($_POST['address'])?trim($_POST['address']):'';
		$data['thumbnail'] = I('post.thumbnail','','strip_tags');
		$data['update_time'] = time();
		if (empty($data['url'])) {
				if(!$data['content']){
					$this->error('警告！不是外链的文章内容为必填项目。');
				}
		}
		if(!$data['sid'] or !$data['title']){
			$this->error('警告！文章分类、文章标题为必填项目。');
		}
		if($aid){
			M('Clothes')->data($data)->where('aid='.$aid)->save();
			addlog('编辑文章，AID：'.$aid);
			$this->success('恭喜！文章编辑成功！');
		}else{
			$data['create_time'] = time();
			$aid = M('Clothes')->data($data)->add();
			if($aid){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}
			
		}
	}
}