<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2016-01-17
* 版    本：1.0.0
* 功能说明：后台登录控制器。
*
**/

namespace Mobile\Controller;
use Common\Controller\BaseController;
use Think\Auth;
class LoginController extends BaseController {
    public function index(){
		/* if(!empty($user)){
		    $this -> success('您已经登录,正在跳转到主页',U("Mosque/perMosque"));
		} */
		$this -> display();
    }
    public function login(){
		$verify = isset($_POST['verify'])?trim($_POST['verify']):'';
		if (!$this->check_verify($verify,'login')) {
			$this -> error('验证码错误！',U("Login/index"));
		}

		$username = isset($_POST['name'])?trim($_POST['name']):'';
		$password = isset($_POST['password'])?password(trim($_POST['password'])):'';
		$remember = isset($_POST['remember'])?$_POST['remember']:0;
		if ($username=='') {
			$this -> error('登录名不能为空！',U("Login/index"));
		} elseif ($password=='') {
			$this -> error('密码必须！',U("Login/index"));
		}

			$model = M("Mosque");
			$where['name']=$username;
			$where['mobile']=$username;
			$where['email']=$username;
			$where['_logic'] = 'OR';
			$map['_complex'] = $where;
			$map['password']  = $password;
			$user = $model->where($map)->find();
			$mosqueName=$user['name'];
			$mosqueId=$user['id'];
		if($user) {
			if($remember){
				cookie('mosqueName',$mosqueName,3600*24*365);//记住我
				cookie('mosqueId',$mosqueId,3600*24*365);
			}else{
				cookie('mosqueName',$mosqueName);
				cookie('mosqueId',$mosqueId);
			}
			if($user){
				addlog('登录成功。',$mosqueId);			
				$url=U('Mosque/perMosque');
				header("Location: $url");
				exit(0);
			}
		}else{
			addlog('登录失败。',$username);
			$this -> error('登录失败，请重试！',U("Login/index"));
		}
    }
	
	public function verify() {
	    ob_clean();
		$config = array(
		'fontSize' => 14, // 验证码字体大小
		'length' => 4, // 验证码位数
		'useNoise' => false, // 关闭验证码杂点
		'imageW'=>100,
		'imageH'=>30,
		);
		$verify = new \Think\Verify($config);
		$verify -> entry('login');
	}
	
	function check_verify($code, $id = '') {
		$verify = new \Think\Verify();
		return $verify -> check($code, $id);
	}
	public function checkLogin() {
		if (isset($_COOKIE[$this->loginMarked])) {
			$cookie = $_COOKIE[$this->loginMarked];
		} else {
			$this->redirect("Public/index");
		}
		return TRUE;
	}
	public function dealLogin($email,$password){
		//正常操作登陆
		
	}
	public function loginOut() {
		  cookie('mosqueName',null);
		  cookie('mosqueId',null);
        $this->success('退出成功',U("Mosque/index"));
	}
	public function getUserInfo($email,$password){
		//得到用户信息（验证）
		$member=M('Member');
		$where['email']=$email;
		$where['password']=md5($password);
		$result=$member->where($where)->select();
		return $result;
	}
	public function saveUserInfo($id){
		//保存登录信息
		$member=M('Member');
		$where['member_id']=$id;
		$data['last_login_time']=time();
		$data['OnlineTF']="1";
		$data['getIp']=get_client_ip();
		$result=$member->where($where)->save($data);
		return $result;
	}
	public function emailCheck(){
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);
		$code = trim($_POST['code']);
	
		if ($_SESSION['verify']!==md5($code)) {
			echo json_encode(1);
			return;
		}
		$where['email']=$email;
		$where['password']=md5($password);
		$m=M('member');
		$find =$m->where($where)->select();
		if($find){
			$re=2;
		}else{
			$re=3;
		}
		echo json_encode($re);
	}
}