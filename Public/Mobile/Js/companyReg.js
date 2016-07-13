//企业用户注册，验证脚本

$(function () {

    $("#email").focus(function () {
        $(this).parent().removeClass().addClass("wrap inputfocus");
    });
    $("#email").blur(function () {
        $(this).parent().removeClass().addClass("wrap");
    });
    $(":input[type='password']").focus(function () {
        $(this).parent().removeClass().addClass("wrap inputfocus");
    });
    $(":input[type='password']").blur(function () {
        $(this).parent().removeClass().addClass("wrap");
    });
    $("#nickname").focus(function () {
        $(this).parent().removeClass().addClass("wrap inputfocus");
    });
    $("#nickname").blur(function () {
        $(this).parent().removeClass().addClass("wrap");
    });

    $("#code").focus(function () {
        $(this).parent().removeClass().addClass("wrap inputfocus");

        var code = $.trim($("#code").val());
        if (code == "") {
            $("#codemsg").removeClass().addClass("msg wrong");
            $("#codemsg").html("<i></i>请输入验证码");
        }
        else {
            $("#codemsg").removeClass();
            $("#codemsg").html("");
        }
    });
    $("#code").blur(function () {
        $(this).parent().removeClass().addClass("wrap");

        var code = $.trim($("#code").val());
        if (code == "") {
            $("#codemsg").removeClass().addClass("msg wrong");
            $("#codemsg").html("<i></i>请输入验证码");
        }
        else {
            $.ajax({
                url: "/User/IsRegistCode?ran=" + Math.random,
                datatype: "text",
                type: "post",
                data: { Ucode: code },
                success: function (data) {
                    if (data == "0") {
                        $("#codemsg").removeClass().addClass("msg right");
                        $("#codemsg").html("<i></i>");
                    }
                    else if (data == "1") {
                        $("#codemsg").removeClass().addClass("msg wrong");
                        $("#codemsg").html("<i></i>验证码不一致！");
                        refreshcode();
                    }
                    else {
                        $("#codemsg").removeClass().addClass("msg wrong");
                        $("#codemsg").html("<i></i>验证码已过期，请重新输入！");
                        refreshcode();
                    }
                }
            });
        }
    });

    jQuery.validator.addMethod("stringMinLength", function (value, element, param) {
        var length = value.length;
        for (var i = 0; i < value.length; i++) {
            if (value.charCodeAt(i) > 127) {
                length++;
            }
        }
        return this.optional(element) || (length >= param);
    }, $.validator.format("长度不能小于{0}!"));

    jQuery.validator.addMethod("stringMaxLength", function (value, element, param) {
        var length = value.length;
        for (var i = 0; i < value.length; i++) {
            if (value.charCodeAt(i) > 127) {
                length++;
            }
        }
        return this.optional(element) || (length <= param);
    }, $.validator.format("长度不能大于{0}!"));

    jQuery.validator.addMethod("isNickName", function (value, element) {
        var reg = /^([0-9a-zA-Z\u4e00-\u9fa5\_\-]*)$/;
        return this.optional(element) || (reg.test(value));
    }, "用户昵称不能包含特殊字符");

    $("#registForm").validate({
        errorElement: "p",
        rules: {
            email: {
                required: true,
                maxlength: 30,
                email: true,
                remote: {
                    type: "post",
                    url: "/User/CheckEmail?t="+new Date(),
                    data: {
                        email: function () {
                            return $.trim($("#email").val());
                        }
                    },
                    cache:false,
                    dataType: "text",
                    dataFilter: function (data, type) {
                        if (data == "True") {
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                }
            },
            password: {
                required: true,
                minlength: 6,
                maxlength: 18
            },
            confirm_pass: {
                required: true,
                minlength: 6,
                maxlength: 18,
                equalTo: "#password"
            },
            street: {
                required: true
            },
            nickname: {
                required: true,
                stringMinLength: 4,
                stringMaxLength: 20,
                isNickName: true,
                remote: {
                    type: "post",
                    url: "/User/IsExitNickName",
                    data: {
                        nickname: function () {
                            return $.trim($("#nickname").val());
                        }
                    },
                    cache: false,
                    dataType: "text",
                    dataFilter: function (data, type) {
                        if (data == "True")
                            return false;
                        else
                            return true;
                    }
                }
            }
        },
        messages: {
            email: {
                required: "<i></i>请输入邮箱地址",
                maxlength: jQuery.format("<i></i>邮箱长度不能大于{0}个字符!"),
                email: "<i></i>请输入有效的邮箱地址!",
                remote: "<i></i>此邮箱已被注册!"
            },
            password: {
                required: "<i></i> 请输入密码",
                minlength: jQuery.format("<i></i>密码长度不能少于{0}个字符!"),
                maxlength: jQuery.format("<i></i>密码长度不能大于{0}个字符!")
            },
            street: {
                required: "<i></i>请选择省-市-区-街道"
            },
            confirm_pass: {
                required: "<i></i>请输入确认密码",
                minlength: jQuery.format("<i></i>确认密码长度不能少于{0}个字符!"),
                maxlength: jQuery.format("<i></i>确认密码长度不能大于{0}个字符!"),
                equalTo: "<i></i>两次输入密码不一致!"
            },
            nickname: {
                required: "<i></i>请企业输入昵称",
                stringMinLength: jQuery.format("<i></i>企业昵称长度不能少于{0}个字符!"),
                stringMaxLength: jQuery.format("<i></i>企业昵称长度不能大于{0}个字符!"),
                isNickName: '<i></i>昵称仅支持中英文,数字和“_”“-”',
                remote: "<i></i>此昵称已被注册或者包含敏感字符!"
            }
        },
        errorPlacement: function (error, element) {
            element.parent().next().remove();
            error.removeClass().addClass("msg wrong");
            error.appendTo(element.parent().parent());
        },
        success: function (p) {
            p.removeClass().addClass("msg right").html("<i></i>");
        },
        focusInvalid: false,
        onkeyup: false
    });


    $("#province").change(function () {
        var name = $("#province option:selected").text();
        var id = $("#province option:selected").val();
        if (id != 0) {
            if (id == 1 || id == 2 || id == 3 || id == 4) {
                $("#city").empty();
                $("#city").html("<option selected=\"selected\" value=\"" + id + "\">" + name + "</option>");
                $.get("/User/GetAreasByCityId", { cityid: id }, function (data) {
                    $("#area").empty();
                    $("#area").html(data);
                    $("#street").empty();
                    $("#street").html("<option selected=\"selected\" value=\"\">请选择街道</option>");
                });
            }
            else {
                $.get("/User/GetCitysByProvinceId", { provinceid: id }, function (data) {
                    $("#city").empty();
                    $("#area").empty();
                    $("#street").empty();
                    $("#city").html(data);
                    $("#area").html("<option selected=\"selected\" value=\"0\">请选择城区</option>");
                    $("#street").html("<option selected=\"selected\" value=\"\">请选择街道</option>");
                });
            }
        }
        else {
            $("#city").empty();
            $("#area").empty();
            $("#street").empty();
            $("#city").html("<option selected=\"selected\" value=\"0\">请选择城市</option>");
            $("#area").html("<option selected=\"selected\" value=\"0\">请选择城区</option>");
            $("#street").html("<option selected=\"selected\" value=\"\">请选择街道</option>");
        }
    });

    $("#city").change(function () {
        var cityid = $("#city option:selected").val();
        if (cityid != 0) {
            $.get("/User/GetAreasByCityId", { cityid: cityid }, function (data) {
                $("#area").empty();
                $("#area").html(data);
                $("#street").empty();
                $("#street").html("<option selected=\"selected\" value=\"\">请选择街道</option>");
            });
        }
        else {
            $("#area").empty();
            $("#street").empty();
            $("#area").html("<option selected=\"selected\" value=\"0\">请选择城区</option>");
            $("#street").html("<option selected=\"selected\" value=\"\">请选择街道</option>");
        }
    });

    $("#area").change(function () {
        var areaid = $("#area option:selected").val();
        if (areaid != 0) {
            $.get("/User/GetStreetsByAearId", { areaid: areaid }, function (data) {
                $("#street").empty();
                $("#street").html(data);
            });
        }
        else {
            $("#street").empty();
            $("#street").html("<option selected=\"selected\" value=\"\">请选择街道</option>");
        }
    });


});

function refreshcode() {
    $("#codeimage").attr("src", "/User/CaptchaImage?time=" + (new Date()).getMilliseconds());
}
//检查邮箱
function chkMail() {
    var issu;
    $.ajax({
    async:false,
        type: "get",
        url: "/User/CheckEmail",
        data: { email: $("#email").val() },
        datatype: "text",
        success: function (data) {
            if (data == 'True') {

                $("#email").parent().next().removeClass().addClass("msg wrong");
                $("#email").parent().next().html("<i></i>邮箱已注册");
                issu = false;
            }
            else {
                issu = true;
            }
        }
    });
    return issu;
}
function chkNickName() {
    var issu;
    $.ajax({
        async:false,
        type: "get",
        url: "/User/IsExitNickName",
        data: { nickname: $("#nickname").val() },
        datatype: "text",
        success: function (data) {
            if (data == 'True') {
                $("#nickname").parent().next().removeClass().addClass("msg wrong");
                $("#nickname").parent().next().html("<i></i>此昵称已被注册或者包含敏感字符!");
                issu = false;
            }
            else {
                issu = true;
            }
        }
    });
    return issu;
}
function dosubmit() {
    if ($("#registForm").valid()) {

        var code = $.trim($("#code").val());
        if (code == "") {
            $("#codemsg").removeClass().addClass("msg wrong");
            $("#codemsg").html("<i></i>请输入验证码");
        }
        else {

            if ($("#agreement").is(":checked")) {
                $("#agreementmsg").removeClass("msg wrong");
                $("#agreementmsg").html("");

                $.ajax({
                    url: "/User/IsRegistCode?ran=" + Math.random,
                    datatype: "text",
                    type: "post",
                    data: { Ucode: code },
                    success: function (data) {
                        if (data == "0") {
                            $("#codemsg").removeClass().addClass("msg right");
                            $("#codemsg").html("<i></i>");
                            var resultmail = chkMail();
                            
                            if (resultmail) {
                                var result = chkNickName();
                                if (result) {
                                    $("#registForm").submit();
                                }
                            }
                        }
                        else if (data == "1") {
                            refreshcode();
                            $("#codemsg").removeClass().addClass("msg wrong");
                            $("#codemsg").html("<i></i>验证码不一致！");
                        }
                        else {
                            refreshcode();
                            $("#codemsg").removeClass().addClass("msg wrong");
                            $("#codemsg").html("<i></i>验证码已过期，请重新输入！");
                        }
                    }
                });
            }
            else {
                $("#agreementmsg").removeClass("msg right").addClass("msg wrong");
                $("#agreementmsg").html("<i></i>同意新途网络服务协议才能注册");
            }
        }
    }
}