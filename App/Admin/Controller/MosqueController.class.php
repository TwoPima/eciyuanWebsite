<?php
/**
*
* 版权所有：亿次元科技
* 作    者：马晓成
* 日    期：2016-06-12
* 版    本：1.0.0
* 功能说明：清真寺控制器
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
class MosqueController extends ComController {
    public function index(){
		
		$p = isset($_GET['p'])?intval($_GET['p']):'1';
		$order = isset($_GET['order'])?$_GET['order']:'DESC';
		$field = isset($_GET['field'])?$_GET['field']:'';
		$keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
		$order = isset($_GET['order'])?$_GET['order']:'DESC';
		$where = '';
		
		$prefix = C('DB_PREFIX');
		if($order == 'asc'){
			$order = "{$prefix}Mosque.create_time asc";
		}elseif(($order == 'desc')){
			$order = "{$prefix}Mosque.create_time desc";
		}else{
			$order = "{$prefix}Mosque.id asc";
		}
		if($keyword <>''){
			if($field=='name'){
				$where = "{$prefix}Mosque.name LIKE '%$keyword%'";
			}
			if($field=='phone'){
				$where = "{$prefix}Mosque.phone LIKE '%$keyword%'";
			}
			if($field=='linkman'){
				$where = "{$prefix}Mosque.qq LIKE '%$keyword%'";
			}
			if($field=='email'){
				$where = "{$prefix}Mosque.email LIKE '%$keyword%'";
			}
		}
		
		
		$user = M('Mosque');
		$pagesize = 10;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count = $user->count();
		
		$list  = $user->field("{$prefix}Mosque.*")->order($order)->where($where)->limit($offset.','.$pagesize)->select();
		
		
		//$user->getLastSql();
		$page	=	new \Think\Page($count,$pagesize); 
		$page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);
		$group = M('auth_group')->field('id,title')->select();
		$this->assign('group',$group);
		$this -> display();
    }
	
	public function del(){
		
		$uids = isset($_REQUEST['ids'])?$_REQUEST['ids']:false;
		//uid无效的
		if(!$uids){
			$this->error('参数错误！');
		}
		if(is_array($uids)) 
		{
			foreach($uids as $k=>$v){
				$uids[$k] = intval($v);
			}
			if(!$uids){
				$this->error('参数错误！');
				$uids = implode(',',$uids);
			}
		}

		$map['id']  = array('in',$uids);
		if(M('Mosque')->where($map)->delete()){
			$this->success('恭喜，用户删除成功！');
		}else{
			$this->error('参数错误！');
		}
	}
	
	public function edit(){
		
		$uid = isset($_GET['id'])?intval($_GET['id']):false;
		if($uid){	
			$user = M('Mosque');
			$where['id']=$uid;
			$Mosque  = $user->where($where)->find();
			if ($Mosque['type']=="2"){
			    $this->assign('type',2);//为清真寺
			}
		}else{
			$this->error('参数错误！');
		}
		
		$this->assign('Mosque',$Mosque);
		$this -> display();
	}
	
	public function update($ajax=''){
		if($ajax=='yes'){
			$uid = I('get.id',0,'intval');
			$gid = I('get.id',0,'intval');
			die('1');
		}
		$uid = isset($_POST['uid'])?intval($_POST['uid']):false;
		$user = isset($_POST['name'])?htmlspecialchars($_POST['name'], ENT_QUOTES):'';
		$head = I('post.head','','strip_tags');
		$qrcode = I('post.qrcode','','strip_tags');
		if($head<>'') {
			$data['head'] = $head;
		}
		if($qrcode<>'') {
			$data['qrcode'] = $qrcode;
		}
		$data['phone'] = isset($_POST['phone'])?trim($_POST['phone']):'';
		$data['linkman'] = isset($_POST['linkman'])?trim($_POST['linkman']):'';
		$data['qq'] = isset($_POST['qq'])?trim($_POST['qq']):'';
		$data['email'] = isset($_POST['email'])?trim($_POST['email']):'';
		$data['s_province'] = isset($_POST['s_province'])?trim($_POST['s_province']):'';
		$data['s_county'] = isset($_POST['s_county'])?trim($_POST['s_county']):'';
		$data['s_city'] = isset($_POST['s_city'])?trim($_POST['s_city']):'';
		$data['address'] = isset($_POST['address'])?trim($_POST['address']):'';
		$data['update_time'] = time();
		$data['name'] = $user;
		$password = isset($_POST['password'])?trim($_POST['password']):false;
		if($password) {
		    $data['password'] = password($password);
		}
		if(!$uid){
			if($user==''){
				$this->error('用户名称不能为空！');
			}
			if($password==''){
				$this->error('密码不能为空！');
			}
			if(M('Mosque')->where("name='$user}'")->count()){
				$this->error('用户名已被占用！');
			}
			
			$data['create_time'] = time();
			$uid = M('Mosque')->data($data)->add();
			addlog('新增会员，会员UID：'.$uid);
		}else{
			M('Mosque')->data($data)->where("id=$uid")->save();
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