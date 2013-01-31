function hideAddressBar(){
	if(document.documentElement.scrollHeight<window.outerHeight/window.devicePixelRatio){
		document.documentElement.style.height=(window.outerHeight/window.devicePixelRatio)+'px';
			setTimeout(window.scrollTo(1,1),0);
	}
	window.addEventListener("load",function(){hideAddressBar();});
	window.addEventListener("orientationchange",function(){hideAddressBar();});
}

// enable "starring" pages (adding to cookies)
$(function(event){
	// make star blue if cookie is already set:
	if($.cookie('bookmark-'+$('title').html()) == $(location).attr('href')){
		$("span#favorite").addClass("activecookie");
	}


	$('span#favorite').unbind();
	$('span#favorite').click(function(){
		var cookie = 'bookmark-'+$('title').html();
		var value = $(location).attr('href');

		// toggle cookie and star image
		if($.removeCookie(cookie)){
			// "unstar" page
			$("span#favorite").removeClass("activecookie");
			console.log('cookie entfernt');
		}else{
			$.cookie(cookie, value, { expires: 1000 });
			$("span#favorite").addClass("activecookie");
			console.log('cookie hinzugefügt');
		}			
	});
});