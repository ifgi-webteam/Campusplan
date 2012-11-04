<?php 
	require_once("functions.php");
	getHead("ULB Katalog");
?>

<body> 

<div data-role="page" class="type-interior" id="page">		

	<?php getHeader("ULB Katalog", "home"); ?>

	<div data-role="content">
		
		<div class="content-primary">  
			<iframe src="http://www.ulb.uni-muenster.de/ULB/katalog" width="100%" height="100%" style="min-height: 500px" frameborder="no">
            <p>Ihr Browser untersützt offenbar keine iFrames. <a href="http://www.ulb.uni-muenster.de/ULB/katalog">Direkt zum ULB Katalog</a>.</p>
        </iframe> 
		</div><!--/content-primary -->		
		
		<?php getMenu(); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->

<?php getFoot(); ?>
