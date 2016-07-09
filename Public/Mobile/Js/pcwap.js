$(document).ready(function () {
	
	$("#search").click(function(){
		
		if($("input[name='key']").val()==false){
							$("input[name='key']").focus();
							return false
		}
		
	
	});

$("#up").click(function(){

	var num= $("#num").text();

	$("#num").html(parseInt(num) + 1);

	

});

$(".j").click(function(){

	var num= $("#num").text();

	if(num >1){

	$("#num").html(num - 1);

	}

});

$("#reg").click(function(){
	if($("input[name='user']").val()==false){
							$("input[name='user']").focus();
							return false
				}
				if($("input[name='pwd']").val()==false){
							$("input[name='pwd']").focus();
							return false
				}
				if($("input[name='phone']").val()==false){
							$("input[name='phone']").focus();
							return false
				}
				if($("input[name='mail']").val()==false){
							$("input[name='mail']").focus();
							return false
				}
				if($("input[name='qq']").val()==false){
							$("input[name='qq']").focus();
							return false
				}
				if($("input[name='code']").val()==false){
							$("input[name='code']").focus();
							return false
				}
				$.ajax({
					type:'post',
					url:'../reg.php',
					data:{
						user:$("input[name='user']").val(),
						pwd:$("input[name='pwd']").val(),
						phone:$("input[name='phone']").val(),
						mail:$("input[name='mail']").val(),
						qq:$("input[name='qq']").val(),
						code:$("input[name='code']").val(),
					},
					success:function(msg){
						if(msg.status=='1'){
							window.location.href="../login.php";
						}else{
							alert(msg.info);
						}
					},
					
				});				
				
	
});

$("#login").click(function(){
	if($("#user").val()==false){
							$("#user").focus();
							return false
				}
				if($("#pwd").val()==false){
							$("#pwd").focus();
							return false
				}
				
				if($("#code").val()==false){
							$("#code").focus();
							return false
				}
				$.ajax({
					type:'post',
					url:'../login.php',
					data:{
						user:$("#user").val(),
						pwd:$("#pwd").val(),					
						code:$("#code").val(),
					},
					success:function(msg){
						if(msg.status=='1'){
							window.location.href="../usercenter.php";
						}else{
							alert(msg.info);
						}
					},
					
				});				
				
	
});

$("#postrev").click(function(){
				if($("input[name='name']").val()==false){
							$("input[name='name']").focus();
							return false
				}
				if($("input[name='email']").val()==false){
							$("input[name='email']").focus();
							return false
				}
				if($("input[name='tel']").val()==false){
							$("input[name='tel']").focus();
							return false
				}
				if($("#con").val()==false){
							$("#con").focus();
							return false
				}
				
				$.ajax({
					type:'post',
					url:'/index.php?s=/book.php',
					data:{
						name:$("input[name='name']").val(),
						email:$("input[name='email']").val(),
						tel:$("input[name='tel']").val(),
						content:$("#con").val(),
						infoid:$("input[name='infoid']").val(),
					},
					success:function(msg){
						alert(msg.info);
					},
					
				});				
			});
			});