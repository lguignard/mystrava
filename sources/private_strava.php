<?php
include_once "strava_fixed/url.inc.php";
include_once "libstrava.php";
include_once "strava-config.inc.php";

if( $_GET['code'] && ! $_COOKIE["mystrava_token"]) {
	$code = $_GET['code'];	// Get authorized code from HTTP response
}
else {
	$code = null;
}

$obj = new mystrava;

// Debug section
$obj->settesting($mydebug);
$obj->setdebug($mytesting);

$obj->setclient_id($id_client);


if(! $_COOKIE["mystrava_token"]) {
	if( $code == null ) {
		$url="https://www.strava.com/oauth/authorize?client_id=$id_client&redirect_uri=http://velo.famille-guignard.org/mystrava/private_strava.php&response_type=code&approval_prompt=force&state=mystate&scope=view_private";
		header("Location: $url");
	}
	else {
		$obj->seturl($token_url);
		$obj->get_access_token($secret_client,$code);
		setcookie("mystrava_token",$obj->getaccess_token(),time()+60*60*24*120,"/mystrava/");
	}
}
else {
	$obj->setaccess_token($_COOKIE["mystrava_token"]);
}

	$year = date("Y");
	$date = mktime(0,0,0,1,1,$year);
	$premieran = mktime(0,0,0,1,1,date("Y"));

	$dist_totale = 0;
	$elev_tot = 0;
	$move = 0;
	$altitude_max = 0;
	$commute_nb = 0;
	$lastdate = "";
	$sup = array();


	$obj->seturl($activities_url);
	$obj->setper_page(200);
	$obj->setafter($premieran);
	$obj->run_request();
	$response = $obj->getresult();

	$json = json_decode($response);

		// Analyse des résultats - partie statistique
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
					if($key2 == "start_date") {
						//echo "$activ / ";
						$lastdate = $activ;
					}
				}
			}
		}


	// Exploitation des résultats : affichage
	$dist_totale = $dist_totale/1000;
	$move = $move/3600;
	echo "<h2>Données strava pour l'année $year</h2>";
	echo "Date de la dernière activité traitée : $lastdate<br />";
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
