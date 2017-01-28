<?php
include_once "strava_fixed/url.inc.php";
include_once "libstrava.php";
include_once "strava-config.inc.php";

$url="https://www.strava.com/oauth/authorize?client_id=$id_client&redirect_uri=http://velo.famille-guignard.org/mystrava/connect_strava.php&response_type=code&approval_prompt=force&state=mystate&scope=view_private";

header("Location: $url");


?>
