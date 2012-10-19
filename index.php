<?php 
	require_once("head.php");
	$fb_title = $_GET["fb_title"];
	$fb_desc  = $_GET["fb_desc"]; 
	$fb_uri   = $_GET["fb_uri"]; 
?>

<body> 

<div data-role="page" class="type-interior">		

	<?php getHeader("WWU Campus Plan", "home"); ?>

	<div data-role="content">
		
		<div class="content-primary">

		<a href="uni-a-z.php"><img src="images/front-az.png" class="frontimg" /></a><a href="mensen.php"><img src="images/front-mensen.png" class="frontimg" /></a><a href="ulb.php"><img src="images/front-ulb.png" class="frontimg" /></a><a href="karte.php"><img src="images/front-karte.png" class="frontimg-s" /></a><a href="wohnheime.php"><img src="images/front-wohnheime.png" class="frontimg-s" /></a><a href="fachbereiche.php"><img src="images/front-fachbereiche.png" class="frontimg-s" /></a><a href="info.php"><img src="images/front-info.png" class="frontimg-s" /></a>

		<a href="http://www.uni-muenster.de"><img src="images/wwu-full.png" class="logoimg" /></a>
		<a href="http://ifgi.uni-muenster.de"><img src="images/ifgi.png" class="logoimg" /></a>
		</div><!--/content-primary -->		
		
		<?php getMenu(""); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->

</body>
</html>
