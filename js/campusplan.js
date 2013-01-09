function hideAddressBar(){
  		if(document.documentElement.scrollHeight<window.outerHeight/window.devicePixelRatio){
    		document.documentElement.style.height=(window.outerHeight/window.devicePixelRatio)+'px';
  			setTimeout(window.scrollTo(1,1),0);
		}
		window.addEventListener("load",function(){hideAddressBar();});
		window.addEventListener("orientationchange",function(){hideAddressBar();});
	}

	// enable "starring" pages (adding to cookies)
	jQuery('[data-role="page"]').live('pageinit', function(){
    	$('img#star').unbind();
    	$('img#star').click(function(){
			var cookie = 'bookmark-'+$('title').html();
			var value = $(location).attr('href');

			// toggle cookie and star image
			if($.removeCookie(cookie)){
				// "unstar" page
				$("#star").attr("src","img/favorite.png");
				console.log('cookie entfernt');
			}else{
				$.cookie(cookie, value, { expires: 1000 });
				$("#star").attr("src","img/favorite-active.png");
				console.log('cookie hinzugefügt');
			}			
		});
	});