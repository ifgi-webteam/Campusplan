function hideAddressBar(){
	if(document.documentElement.scrollHeight<window.outerHeight/window.devicePixelRatio){
		document.documentElement.style.height=(window.outerHeight/window.devicePixelRatio)+'px';
			setTimeout(window.scrollTo(1,1),0);
	}
	window.addEventListener("load",function(){hideAddressBar();});
	window.addEventListener("orientationchange",function(){hideAddressBar();});
}

// enable "starring" pages (adding to cookies)
// the cookies added here will be shown on the favorites page (read and displayed via php)
$( document ).ready(function(){

	// make sure all pages are loaded via JS to enable webapp home screen installation:
	$(function() {
      $('a').click(function() {
        document.location = $(this).attr('href');
        return false;
      });
    });

   	$('#back').click(function(){
		event.preventDefault();
		console.log("back");
		history.back();
	});

	$('#forward').click(function(){
		event.preventDefault();
		console.log("forward");
		history.forward();
	});	    
	
	var cookie        = encodeURIComponent($(location).attr('href')) ;
	var value         = $('span#title').html() ;
	var cookieOptions = { expires: 1000 , path: '/' } ;

	// make star blue if cookie is already set:
	if($.cookie(cookie) != undefined){
		$("a#favorite").addClass("btn-info");
		console.log("cookie found");
	}	

	$('a#favorite').unbind();
	$('a#favorite').click(function(){
		
		// toggle cookie and star image
		if($.removeCookie(cookie, cookieOptions)){
			// "unstar" page
			$("a#favorite").removeClass("btn-info");			
		}else{
			$.cookie(cookie, value, cookieOptions);
			$("a#favorite").addClass("btn-info");
		}			
	});
});