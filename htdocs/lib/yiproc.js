/* coding: utf-8
标题：一些常用 javascript 类
依赖：import prototype.js;
最后修正：2008-03-12
作者：TurboY (Yi Huawei)
说明：由于有全局变量，所以建议放到页面后面载入。
*/
/* 显示进度时屏幕变成半透明 */
var maskScreenClass = Class.create();
maskScreenClass.prototype = {
	initialize: function() {
		if (!$('progressDiv')) {
			var div = document.createElement('div');
			div.id='progressDiv';
			div.style.position='absolute';
			div.style.left='0px';
			div.style.top='0px';
			div.style.width='100%';
			div.style.height='100%';
			div.style.backgroundColor='#fff';
			div.style.filter='Alpha(Opacity=80)';
			div.style.opacity='0.8';
			div.style.display='none';
			document.body.appendChild(div);
		}
		this.maskdiv=$('progressDiv');
	},
	show: function() {
		this.maskdiv.show();
		this.maskdiv.style.left=document.documentElement.scrollLeft+'px';
		this.maskdiv.style.top=document.documentElement.scrollTop+'px';
	},
	hide: function() {
		this.maskdiv.hide();
	}
};
//var maskscreen=new maskScreenClass();

/* 进度条类，显示进度时屏幕变成半透明 */
var progressClass = Class.create();
progressClass.prototype = {
	initialize: function() {
		if (!$('progressDivImg')) {
			this.maskdiv=new maskScreenClass();
			var img = document.createElement('img');
			img.id='progressDivImg';
			img.src='images/loading.gif';
			img.alt='Loading...';
			img.style.display='none';
			img.style.zIndex='100';
			document.body.appendChild(img);
		}
		this.gif=$('progressDivImg');
	},
	show: function() {
		this.gif.style.position="absolute";
		this.maskdiv.show();
		this.gif.show();
		this.gif.style.left=parseInt(document.documentElement.scrollLeft+(document.documentElement.clientWidth-this.gif.width)/2,10)+"px";
		this.gif.style.top=parseInt(document.documentElement.scrollTop+(document.documentElement.clientHeight-this.gif.height)/2,10)+"px";
		//showObj(this.gif);
	},
	hide: function() {
		this.maskdiv.hide();
		this.gif.hide();
	}
};
//var progress=new progressClass();

/* 修改BODY元素的透明度 */
function bodymask(b) {
	var div=document.body;
	if (b) {
		div.style.backgroundColor='#fff';
		div.style.backgroundImage='none';
		div.style.filter='Alpha(Opacity=10)';
		div.style.opacity='0.1';
	} else {
		div.style.backgroundColor='';
		div.style.backgroundImage='';
		div.style.filter='';
		div.style.opacity='';
	}
}

/* 调试时显示当前对象的坐标 */
function showObj(obj) {
	var s='id='+obj.id
		+ ', left='+obj.left
		+ ', top='+obj.top
		+ ', width='+obj.width
		+', height='+obj.height
		+ ', style.left='+obj.style.left
		+ ', style.top='+obj.style.top
		+ ', style.width='+obj.style.width
		+', style.height='+obj.style.height
		+ "\n"
		+', document.documentElement.clientWidth='+document.documentElement.clientWidth
		+', document.documentElement.clientHeight='+document.documentElement.clientHeight
		+', document.documentElement.scrollLeft='+document.documentElement.scrollLeft
		+', document.documentElement.scrollTop='+document.documentElement.scrollTop
		+', document.documentElement.scrollWidth='+document.documentElement.scrollWidth
		+', document.documentElement.scrollHeight='+document.documentElement.scrollHeight;
	window.alert(s);
}

/* 设置表格行在鼠标经过时变色, 参数tbody对象ID */
function setTableColor(obj) {
	var Color='';
	if (typeof(ColorTrHover)=='undefined') { //全局变量，色彩
		Color='#efe';
	} else {
		Color=ColorTrHover;
	}
	var tb=$(obj);
	for (var i=0; i<tb.rows.length; i++) {
		tb.rows[i].onmouseover= function() {
			this.style.backgroundColor= Color;
		}
		tb.rows[i].onmouseout= function() {
			this.style.backgroundColor= '';
		}
	}
}


/* 一个自定义的消息框 */
function showMessage(message) {
	if (!$('DivShowMessage')) {
		this.div=document.createElement('div');
		this.div.id='DivShowMessage';
		this.div.style.backgroundColor='#f0f0f0';
		this.div.style.width='400px';
		this.div.style.height='252px';
		this.div.style.display='none';
		this.title=document.createElement('h1');
		this.title.style.backgroundColor='#0000ff';
		this.title.style.color='#ffffff';
		this.div.appendChild(this.title);
		this.content=document.createElement('div');
		this.content.style.margin='2px';
		this.div.appendChild(this.content);
		document.body.appendChild(this.div);
	}
	this.div.style.position="absolute";
	this.div.style.left=document.documentElement.scrollLeft+parseInt((document.documentElement.scrollWidth-400)/2)+"px";
	this.div.style.top=document.documentElement.scrollTop+parseInt((document.documentElement.scrollHeight-252)/2)+"px";
	this.title.innerHTML='Message';
	this.content.innerHTML=message;
	this.div.style.display='block';
}


