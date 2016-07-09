var login = {};
login.validate_login=function(URL){
	var email = $.trim($("#emailInput").val());
	var password = $.trim($("#passInput").val());
	/*var code = $.trim($("#code").val());*/
	if(email.length<1){
		$("#login_error").html("邮箱不能为空");
		return false;
	}else{
		$("#login_error").html("");
	}
	
	if(password.length<1){
		$("#login_error").html("密码不能为空");
		return false;
	}else{
		$("#login_error").html("");
	}
	
	/*if(code.length<1){
		$("#login_error").html("验证码不能为空");
		return false;
	}else{
		$("#login_error").html("");
	}*/
	
		$.ajax({
	        type: "post",
	        dataType: "JSON",
	        async:false,
	        url: URL+"/emailCheck",
	        data:{"email":email,"password":password},
	        success: function (data) {//data就是返回的那个字符串
	        	if (data==2) {
	        		$("#login_error").html("");
	            }else if(data==3){
	            	$("#login_error").html("邮箱或密码错误");
	            }
	        }
	    });
	
		var result =  $("#login_error").html();
		if(result.length<1){
			return true;
		}else{
			return false;
		}
};
login.onsubmit = function(URL){
	var result = login.validate_login(URL);
	return result;
};