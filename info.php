<?php require_once("head.php"); ?>

<body> 

<div data-role="page" class="type-interior">

	<?php getHeader("Info", "home"); ?>

	<div data-role="content">
		
		<div class="content-primary">

			
			<ul data-role="listview" data-inset="true" data-divider-theme="a">
				<li data-role="list-divider">Campus Plan</li>
				<li data-icon="info">Web App Version 1.0 beta</li>
				<li><a href="mailto:campusplan-support@uni-muenster.de?&amp;subject=Campus%20Plan%20Web%20App%20Feedback">Feedback</a></li>
				</li>
			</ul>

			<h2>Copyright Hinweise</h2>
			
			<p>Kartendaten &copy; <a href="http://openstreetmap.org">OpenStreetMap</a>. Die daraus erstellten Kacheln werden von <a href="http://www.mapquest.com/">MapQuest</a> zur Verfügung gestellt. Daten und Kacheln stehen unter einer <a href="http://creativecommons.org/licenses/by-sa/2.0/">Creative Commons Lizenz</a>. Die Navigation für Radfahrer, Fußgänger und Autos wird von <a href="http://cloudmade.com/">Cloudmade</a> zur Verfügung gestellt. <!-- Busverbindungen werden von den <a href="https://www.stadtwerke-muenster.de/">Stadtwerken Münster</a> zur Verfügung gestellt. --></p>

			<p>Die Icons stammen von <a href="http://iconmonstr.com/">Iconmonstr</a> (Creative Commons Lizenz) und <a href="http://thenounproject.com/">The Noun Project</a> (Public Domain).</p>
			
			<a href="http://www.uni-muenster.de"><img src="images/wwu-full.png" class="logoimg" /></a>
			<a href="http://ifgi.uni-muenster.de"><img src="images/ifgi.png" class="logoimg" /></a>

						
				
		</div><!--/content-primary -->		
		
		<?php getMenu("info.php"); ?> 	
		
	</div><!-- /content -->		
</div><!-- /page -->

</body>
</html>