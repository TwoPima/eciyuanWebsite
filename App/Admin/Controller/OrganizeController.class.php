<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2016-01-20
* 版    本：1.0.0
* 功能说明：公益信息-用户控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
class OrganizeController extends ComController {
    public function index(){
		

		$p = isset($_GET['p'])?intval($_GET['p']):'1';
		$field = isset($_GET['field'])?$_GET['field']:'';
		$keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
		$order = isset($_GET['order'])?$_GET['order']:'DESC';
		$where = '';
		
		$prefix = C('DB_PREFIX');
		if($order == 'asc'){
			$order = "{$prefix}Organize.create_time asc";
		}elseif(($order == 'desc')){
			$order = "{$prefix}Organize.create_time desc";
		}else{
			$order = "{$prefix}Organize.organize_id asc";
		}
		if($keyword <>''){
			if($field=='name'){
				$where = "{$prefix}Organize.name LIKE '%$keyword%'";
			}
			if($field=='phone'){
				$where = "{$prefix}Organize.phone LIKE '%$keyword%'";
			}
			if($field=='qq'){
				$where = "{$prefix}Organize.qq LIKE '%$keyword%'";
			}
			if($field=='email'){
				$where = "{$prefix}Organize.email LIKE '%$keyword%'";
			}
		}
		
		
		$user = M('Organize');
		$pagesize = 10;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count = $user->count();
		
		$list  = $user->order($order)->where($where)->limit($offset.','.$pagesize)->select();
		
		
		//$user->getLastSql();
		$page	=	new \Think\Page($count,$pagesize); 
		$page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);
		$this -> display();
    }
	
	public function del(){
		
		$uids = isset($_REQUEST['uids'])?$_REQUEST['uids']:false;
		//uid为1的禁止删除
		if($uids==1 or !$uids){
			$this->error('参数错误！');
		}
		if(is_array($uids)) 
		{
			foreach($uids as $k=>$v){
				if($v==1){//uid为1的禁止删除
					unset($uids[$k]);
				}
				$uids[$k] = intval($v);
			}
			if(!$uids){
				$this->error('参数错误！');
				$uids = implode(',',$uids);
			}
		}

		$map['organize_id']  = array('in',$uids);
		if(M('Organize')->where($map)->delete()){
			M('auth_group_access')->where($map)->delete();
			addlog('删除会员UID：'.$uids);
			$this->success('恭喜，用户删除成功！');
		}else{
			$this->error('参数错误！');
		}
	}
	
	public function edit(){
		
		$uid = isset($_GET['uid'])?intval($_GET['uid']):false;
		if($uid){	
			$prefix = C('DB_PREFIX');
			$user = M('Organize');
			$Organize  = $user->where("{$prefix}organize.organize_id=$uid")->find();

		}else{
			$this->error('参数错误！');
		}
		
		$this->assign('Organize',$Organize);
		$this -> display();
	}
	
	public function update($ajax=''){
		if($ajax=='yes'){
			$uid = I('get.uid',0,'intval');
			$gid = I('get.gid',0,'intval');
			M('auth_group_access')->data(array('group_id'=>$gid))->where("uid='$uid'")->save();
			die('1');
		}
		
		$uid = isset($_POST['uid'])?intval($_POST['uid']):false;
		$user = isset($_POST['name'])?htmlspecialchars($_POST['name'], ENT_QUOTES):'';
		$head = I('post.head','','strip_tags');
		if($head<>'') {
			$data['head'] = $head;
		}
		$data['mobile'] = isset($_POST['mobile'])?trim($_POST['mobile']):'';
		$data['linkman'] = isset($_POST['linkman'])?trim($_POST['linkman']):'';
		$data['nature'] = isset($_POST['nature'])?trim($_POST['nature']):'';
		$data['contact'] = isset($_POST['contact'])?trim($_POST['contact']):'';
		$data['qq'] = isset($_POST['qq'])?trim($_POST['qq']):'';
		$data['email'] = isset($_POST['email'])?trim($_POST['email']):'';
		$data['create_time'] = time();
		$data['name'] = $user;
		if(!$uid){
			if($user==''){
				$this->error('用户名称不能为空！');
			}
			if(M('Organize')->where("name='$user}'")->count()){
				$this->error('用户名已被占用！');
			}
			$uid = M('Organize')->data($data)->add();
			addlog('新增会员，会员UID：'.$uid);
		}else{
			M('Organize')->data($data)->where("organize_id=$uid")->save();
			addlog('编辑会员信息，会员UID：'.$uid);
		}
		$this->success('操作成功！');
	}
	
	
	public function add(){

		$usergroup = M('auth_group')->field('id,title')->select();
		$this->assign('usergroup',$usergroup);
		$this -> display();
	}
}