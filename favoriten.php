<?php 
	require_once("functions.php");
	dontCache();
	getHead();
?>

<body> 

<div class="container">
	<div class="row-fluid">
		<?php getFavorites(); ?>			
	</div>
</div>


<?php

getFoot();

function getFavorites(){
	
	?>
	<h1>Favoriten</h1>
	<div class="btn-group btn-group-vertical">
	<?php
	
	foreach ($_COOKIE as $url => $title) {
		if(strpos($url, 'http') === 0){
			$url = urldecode($url);
			$url = str_replace('orgdetails_php', 'orgdetails.php', $url);
			$url = str_replace('_uni-muenster_de', '.uni-muenster.de', $url);			
			echo '<a class="btn btn-large btn-stacked" href="'.$url.'">'.$title.'</a>';		
		}
	} 					
	
	?>
	</div>
	<div class="lead" style="margin-top: 15px">Du kannst Seiten zu den Favoriten hinzufügen, indem Du den <a class="btn btn-small" style="width:59px"><i class="icon-star"></i> Merken</a> Button auf der jeweiligen Seite antippst.</div>

<?php } ?>