var error = {
	message:'',
	phone:"电话号码填写错误",
	require:"%s必须要填写",
	int:"%s必须为数字",
	email:"邮箱填写错误",
	file:"没有上传文件",
	/**
	 * [check 验证]
	 * @param  {[type]} type [验证类型]
	 * @param  {[type]} str  [验证的数据]
	 * @return {[bool]}      [验证的结果]
	 */
	check:function(type,str){
		var re = false;
		switch(type){
			case "phone":
				var partten = /^1[3,4,5,6,7,8]\d{9}$/;
          		re = partten.test(str);
			break;
			case "require":
			case "file":
				re = (str!='');
			break;
			case "int":
				re = (str === "" || str == null);
				re = !(re ^ isNaN(str));
			break;
			case "email":
			 	var partten = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			 	re = partten.test(str);
			break;
		}
		this.message = type;
		return re;
	},
	get:function(name){
		var str = '验证方式错误';
		var message = this[this.message];
		if(message){
			str = message.replace("%s",name);
		}
		return str;
	}
};

var ajax = {
	close:true,
	data:{},
	success:{},
	run:null,
	sent:function(object, type = 'post'){
		var close = this.close;
		var run = this.run?this.run:null;
		object.type = type;
	    object.processData = false;
	    object.contentType = false;
	    object.dataType = "json";
		object.success = function(data){
	        ajaxHideCover();
			if(data.code!=100){
				showError(data.msg);
				return false;
			}
			if(data.redirect){
        		showError('操作成功');
				redirect(data.redirect);
				return false;
			}
			data = data.data;
			if(run){
				run(data);
			}
			if(close){
        		showError('操作成功');
	            return false;
			}
        	showError('操作成功');
		}
		object.error = function(){
	        ajaxHideCover();
			showError('当前请求错误');
		};
		// console.log(object);
		// return false;
		ajaxShowCoverLoad();
		$.ajax(object);
	}
};

/*
if(this.files && this.files[0]){
	var reader = new FileReader();
	reader.onload = function(evt){
		evt.target.result
	}
	reader.readAsDataURL(this.files[0]);
}
*/