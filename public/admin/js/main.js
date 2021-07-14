$(function(){

    /**
     * [绑定关闭遮盖层的事件]
     */
    $("body").on('click','#cover',function(){
        hideCover();
    })

    $("body").on('click','.main-cover',function(){
        return false;
    })
    $("body").on('click','.main-cover .close',function(){
        hideCover();
    })
    /**
     * [开关的事件]
     */
    $('.switch-model').click(function(){
        var p = $(this).children('a').not('.switch-hide');
        var url = p.attr('href');
        $(this).children('a').removeClass('switch-hide');
        p.addClass('switch-hide');
        url += "&id="+$(this).parent('td').parent('tr').attr('data-id');

        ajax.sent({
            url:url,
            data:{},
        },'get');
        return false;
    })
    $('.ajax-get-click').click(function(){
        var url = $(this).attr('data-url');
        ajax.sent({
            url:url,
            data:{},
        },'get');
    })

    $('.copy_text').click(function(){
        var t = $.trim($(this).text());
        copyText(t);
        showError('复制成功');
    })

    $(".fast_ajax").click(function(){
        var text = $.trim($(this).text());
        if($(this).attr('data')){
            return false;
        }
        $(this).attr('data',text);
        var html = '<input type="text" class="fast_ajax_input" value="" />';
        $(this).html(html);
        $(this).children('input')[0].focus();
        $(this).children('input').val(text);
    });

    $('.fast_ajax').bind('keydown',function(e){
        (e.keyCode == 13) && $('.fast_ajax_input')[0].blur();
    })

    $(".fast_ajax").on('blur','.fast_ajax_input',function(){
        var url = $(this).parent('.fast_ajax').attr('data-url');
        ajax.run = function(data){
            $(".fast_ajax[data]").text(data.val);
            $(".fast_ajax[data]").removeAttr('data');
        };
        var fd = new FormData();
        fd.append("data", $(this).val());
        fd.append("id", $(this).parent('td').parent('tr').attr('data-id'));
        ajax.sent({
            url:url,
            data:fd,
        });
    })

    $('.del_ajax').click(function(){
        var url = $(this).attr('data-url');
        var fd = new FormData();
        fd.append("id", $(this).parent('td').parent('tr').attr('data-id'));
        ajax.sent({
            url:url,
            data:fd,
        });
    })

    /**
     * [
     *  1.绑定所有提交按钮
     *  2.完成自动验证
     *  3.自动提交功能
     *  4.自动处理部分返回值
     * ]
     */
    $('input[type="submit"]').click(function(){
        var re = true;
        var form = $(this).parents('form');
        var file = form.find('input[type="file"]');
        file.attr('disabled','disabled');
        var form_data = new FormData(form[0]);
        file.removeAttr('disabled');
        var form_url = $(this).parents('form').attr('action');
        var form_method = $(this).parents('form').attr('method');
        var form_name = $(this).parents('form').attr('name');
        if(!form_url){
            showError("没填action");
            return false;
        }
        form.find('input[handle]').each(function(){
            var handle = $(this).attr('handle');
            var value = $(this).val();
            var name = $(this).attr('target');
            if(handle){
                if(!error.check(handle,value)){
                    re = false;
                    showError(error.get(name));
                    return false;
                }
            }
        })
        if(!re){
            return false;
        }
        if(form_method=='get'){
            var aa = form.serialize();
            var url = form_url=='?'?'':'?';
            window.location.href = form_url+url+aa;
            return false;
        }
        // console.log($(this).parents('form').serializeArray());
        if(ajax.success[form_name]){
            ajax.run = ajax.success[form_name];
        }
        ajax.sent({
            url:form_url,
            data:form_data,
        });
        return false;
    })

    /**
     * [上传文件的事件]
     */
    $('input[type="file"][name="img_url"]').change(function(){
        var _that = $(this);
        var td = _that.parent('a').parents('td');
        // td.find(".label.file-name").show().text($(this).val().split("\\").pop());

        var form_data = new FormData();
        form_data.append('img_url', $(this)[0].files[0]);

        ajax.run = function(data){
            var src = data.url;
            var data_click = td.find('.img-line');
            var name = data_click.attr('name');
            var type = data_click.attr('type');
            var u = data_click.find('.image-choose').children('ul');
            type==1 && u.html('');
            var temp ='<li><div class="close">X</div><img src="'+src+'"><input name="'+name+'" type="hidden" value="'+src+'" /></li>';
            // <input name="'+name+'" type="hidden" value="'+src+'" />
            u.append(temp);
        }
        ajax.sent({
            url:imgUploadUrl(),
            data:form_data,
        });
        return false;
    })

    $('.content-cover').on('click', '.cover-pic-list li', function(){
        var src = $(this).children('img').attr('src');
        var data_click = $("div.img-line[data-click]");
        var u = $("div.img-line[data-click] .image-choose ul");
        var r = true;
        var num = 0;
        u.children('li').children('img').each(function(e){
            if($(this).attr('src')==src){
                r = false;
            }
            num++;
        })
        if(r){
            var name = data_click.attr('name');
            var type = data_click.attr('type');
            type==1 && u.html('');
            var html = '<li><div class="close">X</div><img src="'+src+'"><input name="'+name+'" type="hidden" value="'+src+'" /></li>';
            u.append(html);
            showError("添加成功");
        }else{
            showError("已经存在");
        }
    })

    $('.img-line .image-choose').on('click', 'li .close',function(){
        $(this).parent('li').remove();
    })

    $('.content-cover').on('click','.cover-pic-list',function(){
        return false;
    })
    $('.content-cover').on('click','a',function(){
        imgList();
        return false;
    })

});

$(document).ready(function(){
    // $('.sidebar-list li ul').hide();
    // $('.sidebar-list li ul:first').show();
    // console.log($('.sidebar-list li ul:first'));
    var text = window.location.href;
    // console.log(text);
    var have = 0;
    $('.sub-menu li a').each(function(index, el) {
        if(text.indexOf($(el).attr('href'))!=-1){
            have = 1;
            $(this).parent('li').parent('.sub-menu').parent('li').children('ul:first').show();
            $(el).attr('style','color:#fff;');
            $(el).children('i').attr('style','color:#fff;');
            $(el).parent('li').attr('style','background: #428bca;');
        }
    });

    have || $('.sidebar-list li ul:first').show();

    $('.sidebar-list li a').click(function(){
        if($(this).parent('li').children('ul').css('display')!='none'){
             $(this).parent('li').children('ul').fadeOut();
        }else{
            $('.sidebar-list li ul').hide();
            $(this).parent('li').children('ul').fadeIn();
        }
    })

    heightChange();
})
function switchButton(p){
    var v = p.children('input').val();
    var s = '.switch-on';
    var h = '.switch-off';
    if(v==1){
        var s = '.switch-off';
        var h = '.switch-on';
    }
    p.children(s).css('display','table');
    p.css('background',p.children(s).css('background'));
    p.children(h).hide();
    p.children('input').val(1-v);
}
function imgList(){
    var _that = $('.btn-add-pic.pic-home');
    var url = imgUrl();
    ajax.run = function(data){
        var str = '';
        $.each(data,function(k,v){
            var temp ='<li><img src="'+v+'"></li>';
            str += temp;
        })
        $(".cover-pic-list ul").append(str);
        var temp = Number(_that.attr('page'))+1;
        _that.attr('page',temp);
    }
    var data = new FormData();
    data.append('page',_that.attr('page'));
    // data.append('pagesize',1);
    ajax.close = false;
    ajax.sent({
        url:url,
        data:data
    });
}

function heightChange(){
    var height = document.body.offsetHeight>window.innerHeight?document.body.offsetHeight:window.innerHeight;
    $('.sidebar-wrap').height(height-50).css('background','#f2f2f2');
}
/**
 * [redirect 跳转界面]
 * @param  {[type]} redirect [跳转路径]
 */
function redirect(redirect){
    if(self==parent){
        window.location.href = redirect;
    }else{
        parent.window.location.href = redirect;
    }
}

/**
 * [copyText 浏览器粘贴]
 * @param  {[text]} text [粘贴的文字]
 */
function copyText(text)
{
    var oInput = document.createElement('input');
    oInput.value = text;
    document.body.appendChild(oInput);
    oInput.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    oInput.className = 'oInput';
    oInput.style.display='none';
}
/**
 * [ajaxHideCover ajax隐藏遮盖层]
 */
function ajaxHideCover(){
    if(self==parent){
        hideCover();
    }else{
        parent.hideCover();
    }
}
function ajaxShowCoverLoad(){
    if(self==parent){
        showCoverLoad();
    }else{
        parent.showCoverLoad();
    }
}
/**
 * [ajaxShowError ajax错误提示]
 */
function ajaxShowError(str){
    if(self==parent){
        showError(str);
    }else{
        parent.showError(str);
    }
}

function showCoverPic(e){
    $('.img-line').removeAttr('data-click');
    $(e).parent('.img-line').attr('data-click',1);
    setCoverPicHtml();
    $("#cover").show();
    $("#cover .main-cover").show();
    imgList();
}
/**
 * [showCover 显示遮盖层，iframe]
 * @param  {[type]} that [点击元素]
 * @return {[type]}      [description]
 */
function showCover(that){
    var url = $(that).attr('data-url');
    setCoverHtml();
    $("#cover iframe").attr('src',url);
    $("#cover").show();
    $("#cover .load").show();
    $('body').css('overflow','hidden');//浮层出现时窗口不能滚动设置
    $("#cover iframe")[0].onload = function(){
        $("#cover .main-cover").show();
        $("#cover .load").hide();
    };
}
function showCoverLoad(){
    $("#cover").show();
    $("#cover .load").show();
    $("#cover .main-cover").show();
    $('body').css('overflow','hidden');//浮层出现时窗口不能滚动设置
}

/**
 * [setCoverHtml 设置遮盖层，不能直接加载，这样会在页面中多增iframe]
 */
function setCoverHtml(){
    var html = '<iframe name="info" src=""></iframe>';
    $("#cover .content-cover").html(html);
    return false;
}
/**
 * [setCoverPicHtml 设置遮盖层，不能直接加载，这样会在页面中多增iframe]
 */
function setCoverPicHtml(){
    var html = '<div class="cover-pic-list">\
                    <ul></ul><div>\
                    <a href="javascript:void(0)">加载更多</a></div>\
                </div>';
    var _that = $('.btn-add-pic.pic-home');
    if(_that.attr('page')>1){
        return false;
    }
    $("#cover .content-cover").html(html);
    return false;
}
/**
 * [hideCover 隐藏遮盖层]
 */
function hideCover(){
    $("#cover").hide();
    $("#cover .load").hide();
    $("#cover .main-cover").hide();
    $('body').css('overflow','auto');// 浮层关闭时滚动设置
    // console.log(11);
    // window.location.reload();
}

/**
 * [showError 显示错误]
 * @param  {[type]} str [错误的内容]
 */
function showError(str){
    $(".error-info").text(str).show();
    setTimeout(function(){
        $(".error-info").hide();
    },2000);
}
