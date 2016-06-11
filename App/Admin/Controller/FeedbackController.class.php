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

class FeedbackController extends ComController {

	public function add(){
		$whereType['type']="1";
		$categoryFeedback = M('Category')->where($whereType)->field('id,pid,name')->order('o asc')->select();
		$tree = new Tree($categoryFeedback);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$categoryFeedback = $tree->get_tree(0,$str,0);
		$this->assign('category',$categoryFeedback);//导航
		$this -> display();
	}
		
	public function index(){
		
		$Feedback = M('Feedback');
		$pagesize = 20;#每页数量
		$count = $Feedback->count();
		$list  = $Feedback->order("t desc")->select();
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
			if(M('Feedback')->where($map)->delete()){
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
		$Feedback = M('Feedback')->where('aid='.$aid)->find();
		if($Feedback){
			$this->assign('Feedback',$Feedback);
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
			M('Feedback')->data($data)->where('aid='.$aid)->save();
			addlog('编辑信息，AID：'.$aid);
			$this->success('恭喜！编辑成功！');
		}else{
			$aid = M('Feedback')->data($data)->add();
			if($aid){
				addlog('新增内容，AID：'.$aid);
				$this->success('恭喜！新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}
			
		}
	}
	//发送邮件显示
	public function sendEmail($aid){
	    $aid = intval($aid);
	    $Feedback = M('Feedback')->where('aid='.$aid)->find();
	    if($Feedback){
	        $this->assign('Feedback',$Feedback);
	    }else{
	        $this->error('参数错误！');
	    }
	    $this -> display();
	}
	//发送邮件
	public  function email(){
//$this->think_send_mail($result['member_id']);	    
	    $aid = intval($aid);
	    $data['feedbackId'] = isset($_POST['feedbackId'])?intval($_POST['feedbackId']):0;
	    $data['title'] = isset($_POST['title'])?$_POST['title']:false;
	    $data['recipientId'] = isset($_POST['recipientId'])?$_POST['recipientId']:false;
	    $data['content'] = isset($_POST['content'])?$_POST['content']:false;
	    $data['remark'] = isset($_POST['remark'])?$_POST['remark']:false;
	    $data['create_time'] = time();
	    //292334666@qq.com
	    $config = C('THINK_EMAIL');
	    vendor('phpmailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
	    $mail             = new PHPMailer(); //PHPMailer对象
	    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	    $mail->IsSMTP();  // 设定使用SMTP服务
	    $mail->SMTPDebug  = 1;                     // 关闭SMTP调试功能
	    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
	    $mail->SMTPSecure = 'ssl';                 // 使用安全协议
	    $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
	    $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
	    $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
	    $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
	    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
	    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
	    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
	    $mail->AddReplyTo($replyEmail, $replyName);
	   
	    $mail->Subject    = "宁夏亿次元科技";
	    $mail->Body = $data['content'];//邮件内容
	    //$mail->MsgHTML($body);
	    $mail->AddAddress($data['recipientId']);
	    if(!$mail->Send()) {
	        //echo "发送失败: " . $mail->ErrorInfo;
	        $this->error('发送失败，请重新操作！');
	    } else {
	        M('Email')->add($data);
	        addlog('发送邮件，AID：'.$aid);
	        $this->success('发送邮件成功！');
	    }
	}
}