// 공용 javascript
function setPng24(obj) {
	obj.width=obj.height=1;
	obj.className=obj.className.replace(/\bpng24\b/i,'');
	obj.style.filter ="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ obj.src +"',sizingMethod='image');"
	obj.src='';
	return '';
}

function clear_date(a, b)
{
	$('#'+a).val('');
	$('#'+b).val('');
}

function toggleMenu(sublink) {
	 if(document.getElementById(sublink).style.visibility == 'hidden') {
		  document.getElementById(sublink).style.visibility = 'visible';
		  //document.getElementById(sublink).style.zIndex= '100000';
	 } else  {
		  document.getElementById(sublink).style.visibility = 'hidden';
		  //document.getElementById(sublink).style.zIndex= '1';
	 }
}


function getPageSize(){
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}


	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}

//플로팅이미지 top 이동

 function fix(){ //기본3단
 var a=document.body.scrollTop+450
 var b=document.body.scrollLeft+190
 bar.style.top = a
 bar.style.left = b
 }

 function fix2(){  //2단
 var a=document.body.scrollTop+450
 var b=document.body.scrollLeft+760
 bar.style.top = a
 bar.style.left = b
 }

 function fix_top(){ //기본3단 - 초대장보내기
 var a=document.body.scrollTop
 var b=document.body.scrollLeft+190
 bar1.style.top = a
 bar1.style.left = b
 }

 function fix2_top(){  //2단 - 초대장보내기
 var a=document.body.scrollTop
 var b=document.body.scrollLeft+760
 bar1.style.top = a
 bar1.style.left = b
 }

function openWindowFrameless(url,winname,x,y){
win = window.open( "" ,winname, "fullscreen=1,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width="+x+",height="+y+";");

var cx = Math.ceil( (window.screen.width - x) / 2 );
var cy = Math.ceil( (window.screen.height - y) / 2 );

win.resizeTo( Math.ceil( x ) , Math.ceil(y) );
win.moveTo ( Math.ceil( cx ) , Math.ceil( cy ) )

win.document.location.replace(url);
}

function view_detail_image(id, adv_id)
{
    window.open('/ads/image_view_popup/?image_group_id='+id+'&adv_id='+adv_id, 'detail_image_popup', 'scrollbars=yes,toolbar=no,resizable=no,width=450,height=600,left=0,top=0');
}

function view_inspect_popup(id)
{
    window.open('/admin/inspect/inspect_view_popup/?inspect_id='+id, 'detail_image_popup', 'scrollbars=yes,toolbar=no,resizable=no,width=450,height=600,left=0,top=0');
}