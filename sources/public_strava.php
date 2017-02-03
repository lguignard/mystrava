	<?php

	include_once "strava_fixed/url.inc.php";
	include_once "libstrava.php";
	include_once "strava-config.inc.php";

	//$id_client="1280";

	if(count($_GET) >=1 && $mydebug!=0)
		var_dump($_GET);

	$year = date("Y");
	$premieran = mktime(0,0,0,1,1,$year);

	$obj_public = new mystrava();
	$obj_public->settesting($mytesting);	// Set the object debug mode 
	$obj_public->setdebug($mydebug);

	$obj_public->settoken($public_token);
	$obj_public->seturl($activities_url);
	$obj_public->setper_page(200);
	$obj_public->setafter($premieran);
	$obj_public->run_request();
	$response = $obj_public->getresult();

	$json = json_decode($response);

	$dist_totale = 0;
	$elev_tot = 0;
	$move = 0;
	$altitude_max = 0;
	$commute_nb = 0;
	$sup = array();

	//echo "Nombre d'enregistrements : ".count ($json)."<br />";
	//print_r($json);

		foreach($json as $key1 => $object_act) {
			foreach($object_act as $key2 => $activ) {
				if(! is_object($activ)) {
					if($key2 == "distance") {
						$dist_totale = $dist_totale+$activ;
						$km = round($activ/1000,2);
						
						if($km<90)
							$index="inférieure(s) à 90";
						else if($km>=90 and $km<100)
							$index="entre 90 et 100";
						else if($km>=100 and $km<150)
							$index="entre 100 et 150";
						else if($km>=150 and $km<200)
							$index="entre 150 et 200";
						else if($km>=200)
							$index="inférieure(s) à 200";
						else
							$index="autres";

						if( isset($sup["$index"]) )
							$sup["$index"] = $sup["$index"]+1;
						else
							$sup["$index"] = 1;

						//echo "$km - ".$sup["$index"]." - ".$object_act->start_date."<br />";
					}
					if($key2 == "total_elevation_gain")
						$elev_tot = $elev_tot+$activ;
					if($key2 == "moving_time")
						$move = $move+$activ;
					if($key2 == "elev_high") {
						if($activ > $altitude_max)
							$altitude_max = $activ;
					}
					if($key2 == "commute") {
						if($activ == 1) {
							$commute_nb++;
						}
					}
				}
			}
		}

	$dist_totale = $dist_totale/1000;
	$move = $move/3600;
	echo "<h2>Données publiques de l'année $year</h2>";
	echo "Nombre de sorties : ".count($json)."<br />";
	echo "Distance totale : ".round($dist_totale,2)."km<br />";
	echo "Dénivelé positif : ".$elev_tot."m<br />";
	echo "Temps de déplacement : ".round($move,2)."h<br />";
	echo "Altitude maximum de l'année : ".$altitude_max."m<br />";
	echo "Nombre de commute(s) : ".$commute_nb."</br>";
	arsort($sup);
	$keys = array_keys($sup);
	foreach ($keys as $dist_key) {
		$kvalue = $sup["$dist_key"];
		$percent = round($sup["$dist_key"]*100/count($json));
		echo "Nombre de sorties ".$dist_key."km : ".$sup["$dist_key"]." ($percent % des sorties)</br>";
	}

	?>
