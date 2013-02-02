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
$( document ).ready(function(){ // TODO: we might have to change this with ajaxify

	
    // Prepare History object à la history.js:

    var History = window.History; // Note: We are using a capital H instead of a lower h
    if ( !History.enabled ) {
         // History.js is disabled for this browser.
         // This is because we can optionally choose to support HTML4 browsers or not.
        return false;
    }

   	$('#back').click(function(){
		event.preventDefault();
		console.log("back");
		History.back();
	});

	$('#forward').click(function(){
		event.preventDefault();
		console.log("forward");
		History.forward();
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