<?php
	/* Original source http://wiki.openstreetmap.org/wiki/ProxySimplePHP
	*	Modified to use directory structure matching the OSM urls and retries on a failure
	*/
 
    $ttl = 604800; //cache timeout in seconds - 1 week
 
    $x = intval($_GET['x']);
    $y = intval($_GET['y']);
    $z = intval($_GET['z']);
    if (isset($_GET['r'])) {
		$r = strip_tags($_GET['r']);
	} else {
		$r = 'mapnik';
	}
 
    switch ($r) {
      case 'mapnik':
        $r = 'mapnik';
        break;
 
      case 'osma':
      default:
        $r = 'osma';
        break;
    }
 
    $file = "tiles/$r/$z/$x/$y.png";
    $img = null;
    $tries = 0;
    if (!is_file($file) || filemtime($file) < time()-(86400*30)) {
		do {
			$server = array();
			switch ($r) {
				case 'mapnik':
					$server[] = 'a.tile.openstreetmap.org';
					$server[] = 'b.tile.openstreetmap.org';
					$server[] = 'c.tile.openstreetmap.org';
 
					$url = 'http://'.$server[array_rand($server)];
					$url .= "/".$z."/".$x."/".$y.".png";
					break;
 
				case 'osma':
				default:
					$server[] = 'a.tah.openstreetmap.org';
					$server[] = 'b.tah.openstreetmap.org';
					$server[] = 'c.tah.openstreetmap.org';
 
					$url = 'http://'.$server[array_rand($server)].'/Tiles/tile.php';
					$url .= "/".$z."/".$x."/".$y.".png";
					break;
			}
 
			@mkdir(dirname($file), 0755, true);
 
			$img = file_get_contents($url);
 
			if ($img) {
				$fp = fopen($file, "w");
				fwrite($fp, $img);
				fclose($fp);
			}
 
			if ($tries++ > 5) exit();	// Give up after five tries
		} while (!$img); 	// If curl has returned a broken file, then try downloading again
	} else {
		$img = file_get_contents($file);
	}
 
    $exp_gmt = gmdate("D, d M Y H:i:s", time() + $ttl * 60) ." GMT";
    $mod_gmt = gmdate("D, d M Y H:i:s", filemtime($file)) ." GMT";
    header("Expires: " . $exp_gmt);
    header("Last-Modified: " . $mod_gmt);
    header("Cache-Control: public, max-age=" . $ttl * 60);
    // for MSIE 5 -- really?
    // header("Cache-Control: pre-check=" . $ttl * 60, FALSE);
    header ('Content-Type: image/png');
    //readfile($file);
    echo $img;

?>