function hideAddressBar(){
  window.scrollTo(0, 1);
}
 
window.onload = hideAddressBar;
window.onresize = hideAddressBar;
window.onorientationchange = hideAddressBar;

// enable "starring" pages (adding to cookies)
// the cookies added here will be shown on the favorites page (read and displayed via php)
$( document ).ready(function(){

	// make sure all pages are loaded via JS to enable webapp home screen installation:
	$(function() {
      $('a.internal').click(function() {
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
		$("button#favorite").addClass("btn-info");		
	}	

	$('button#favorite').unbind();
	$('button#favorite').click(function(){
		
		// toggle cookie and star image
		if($.removeCookie(cookie, cookieOptions)){
			// "unstar" page
			$("button#favorite").removeClass("btn-info");			
		}else{
			$.cookie(cookie, value, cookieOptions);
			$("button#favorite").addClass("btn-info");
		}			
	});
});