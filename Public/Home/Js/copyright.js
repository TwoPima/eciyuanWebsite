
document.body.oncopy = function ()
{
    setTimeout(
        function ()
        {
            var text = clipboardData.getData("text");
            if (text)
            { 
                text = text + "\r\n原文出自宁夏亿次元科技："+location.href;
                clipboardData.setData("text", text);
            }
        },200
    )
}
window.onload = function()
{
    this.focus();
}