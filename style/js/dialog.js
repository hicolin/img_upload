/**
 * layer 弹出框的封装
 */

var dialog = {

    // 错误弹出层
    error:function(message){
        layer.open({
            content:message,
            icon:2,
            title:"错误提示"
        })
    },

    // 成功弹出层
    success:function(message,url){
        layer.open({
            content:message,
            icon:1,
            yes:function(){
                location.href = url;
            }
        })
    },

    // 消息弹出层
    message:function(message){
        layer.msg(message,{anim:0,time:1500})
    },

    // 消息跳转层
    messageTo:function(message,url){
        layer.msg(message,{anim:0,time:2000},function(){
            location.href = url;
        })
    }
};