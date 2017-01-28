<?php


// Classe permettant la réalisation de requetes vers le site Strava
class mystrava {

	//**** PUBLIC ****
	// Constructeur
	public function __construct() {
		$this->url = "";/*{{{*/
		$this->tocken = "";
		$this->result = "";
		$this->client_id = "";
		$this->redirect_uri = "";
		$this->response_type = "";
		$this->result = "";
		$this->options = "";
		$this->per_page = "";
		$this->page = "";
		$this->before = "";
		$this->after = "";
		$this->state = "";
		$this->approval_prompt = "";
		$this->client_secret = "";
		$this->access_token = "";

		$this->testing = 0;
		$this->debug = 0;
	}/*}}}*/

	// Destructeur
	public function __destruct() {
	}

	// Getter et Setter
/*{{{*/
	// Getter testing - Variable utilisée pour afficher au lien d'exécuter
	public function gettesting() {
		return $this->testing;
	}

	// Setter testing - Variable utilisée pour afficher au lien d'exécuter
	public function settesting($tst) {
		$this->testing = $tst;
	}

	// Getter testing - Variable utilisée pour afficher le résultat des commandes
	public function getdebug() {
		return $this->debug;
	}

	// Setter debug - Variable utilisée pour afficher le résultat des commandes
	public function setdebug($dbg) {
		$this->debug = $dbg;
	}

	// Getter result
	public function getresult() {
		return $this->result;
	}

	// Setter result
	public function setresult($res) {
		$this->result = $res;
	}

	// Getter token
	public function gettoken() {
		return $this->token;
	}
	// Setter token
	public function settoken($tok) {
		$this->token = $tok;
	}

	// Getter url
	public function geturl() {
		return $this->url;
	}
	// Setter url
	public function seturl($ur) {
		$this->url = $ur;
	}

	// Getter client_id
	public function getclient_id() {
		return $this->client_id;
	}
	// Setter client_id
	public function setclient_id($clt_id) {
		$this->client_id = $clt_id;
	}

	// Getter redirect_uri
	public function getredirect_uri() {
		return $this->redirect_uri;
	}
	// Setter redirect_uri
	public function setredirect_uri($uri) {
		$this->redirect_uri = $uri;
	}

	// Getter response_type
	public function getresponse_type() {
		return $this->response_type;
	}
	// Setter response_type
	public function setresponse_type($resp_typ) {
		$this->response_type = $resp_typ;
	}

	// Getter approval_prompt
	public function getapproval_prompt() {
		return $this->approval_prompt;
	}
	// Setter approval_prompt
	public function setapproval_prompt($prt) {
		$this->approval_prompt = $prt;
	}

	// Getter per_page
	public function getper_page() {
		return $this->per_page;
	}
	// Setter per_page
	public function setper_page($pp) {
		$this->per_page = $pp;
	}

	// Getter page
	public function getpage() {
		return $this->page;
	}
	// Setter page
	public function setpage($p) {
		$this->page = $p;
	}

	// Getter after
	public function getafter() {
		return $this->after;
	}
	// Setter after
	public function setafter($aft) {
		$this->after = $aft;
	}

	// Getter before
	public function getbefore() {
		return $this->before;
	}
	// Setter before
	public function setbefore($befo) {
		$this->before = $befo;
	}

	// Getter state
	public function getstate() {
		return $this->state;
	}
	// Setter state
	public function setstate($st) {
		$this->state = $st;
	}

	// Getter scope
	public function getscope() {
		return $this->scope;
	}
	// Setter scope
	public function setscope($sco) {
		$this->scope = $sco;
	}

	// Getter client_secret
	public function getclient_secret() {
		return $this->client_secret;
	}
	// Setter client_secret
	public function setclient_secret($secret) {
		$this->client_secret = $secret;
	}

	// Getter access_token
	public function getaccess_token() {
		return $this->access_token;
	}
	// Setter access_token
	public function setaccess_token($atk) {
		$this->access_token = $atk;
	}



/*}}}*/

	// Fonction permettant de réaliser l'appel au site Strava
 	public function run_request() {/*{{{*/
		if($this->url != "") {
			$this->build_options();

			if($this->testing != 0) {
				echo "Test URL appelée : ";
				echo $this->url.$this->options;
				echo "<br />";
			}
			else {
				$this->result = file_get_contents($this->url."".$this->options);
				if($this->debug != 0) {
					echo "Debug URL appelée : ";
					echo $this->url.$this->options;
					echo "<br />";
					print_r($this->result);
					echo "<br />";
				}
			}
		}
		else {
			$this->result = "Développeur, vous devez initialiser au minimum l'URL avant de faire appel à l'objet...";
			if($this->debug != 0 or $this->testing != 0) {
				echo "<br />".$this->result."<br />";
			}
		}
	}/*}}}*/

	//**** PROTECTED ****


	//**** PRIVATE ****

	// Fonction pour obtenir access_token après authentification et autorisation de l'application
	public function get_access_token($secret, $code) { /*{{{*/
		$this->setclient_secret($secret);
		$params = array();

		if($this->client_id != "") {
			// Les variables sont correctement positionnées et on peut procéder à l'éxecution
			$params["client_id"] = $this->getclient_id();
			$params["client_secret"] = $this->getclient_secret();
			$params["code"] = "$code";
			$query = http_build_query ($params);	// Build Http query using params
			// Create Http context details
			$contextData = array (
				'method' => 'POST',
				'header' => "Connection: close\r\n".
				"Content-Length: ".strlen($query)."\r\n",
				'content'=> $query );
			// Create context resource for our request
			$context = stream_context_create (array ( 'http' => $contextData ));
			// Read page rendered as result of your POST request
			$result =  file_get_contents ( $this->url, false, $context);
			$json = json_decode($result);
			$this->access_token = $json->access_token;
			if($this->testing!=0 and $this->debug!=0) {
				print_r($json);
				echo "Token : ".$this->access_token;
			}
		}
		else {
			// Ces variables doivent impérativement être définies
			// Peut-être sera-t-il nécessaire de définir des valeurs de retour...
			return false;
		}
	}
	/*}}}*/

	// Fonction d'ajout des caractères spéciaux pour la requête HTTP
	private function add_speccar() { /*{{{*/
		if($this->options != "")
			$this->options = $this->options."&";
		else
			$this->options = $this->options."?";
	} /*}}}*/

	// Fonction permettant de construire les options de la requête Strava
	private function build_options() { /*{{{*/
	
		if($this->token!="" or $this->access_token!="") {
			$this->add_speccar();
			if($this->debug!=0) {
				echo "Token : ".$this->token." Access_token : ".$this->access_token."<br />";
			}
			if($this->access_token != "")	// S'il y a identification privée alors je l'utilise
				$this->options = $this->options."access_token=".$this->access_token;
			else	// Sinon utilisation du profil public
				$this->options = $this->options."access_token=".$this->token;
		}
		if($this->client_id !=  ""){
			$this->add_speccar();
			$this->options = $this->options."client_id=$this->client_id";
		}
		if($this->redirect_uri != ""){
			$this->add_speccar();
			$this->options = $this->options."redirect_uri=$this->redirect_uri";
		}
		if($this->response_type != ""){
			$this->add_speccar();
			$this->options = $this->options."response_type=$this->response_type";
		}
		if($this->approval_prompt != ""){
			$this->add_speccar();
			$this->options = $this->options."approval_prompt=$this->approval_prompt";
		}
		if($this->per_page !=  ""){
			$this->add_speccar();
			$this->options = $this->options."per_page=$this->per_page";
		}
		if($this->page !=  "") {
			$this->add_speccar();
			$this->options = $this->options."page=$this->page";
		}
		if($this->after != ""){
			$this->add_speccar();
			$this->options = $this->options."after=$this->after";
		}
		if($this->before!= ""){
			$this->add_speccar();
			$this->options = $this->options."before=$this->before";
		}
		if($this->state!=""){
			$this->add_speccar();
			$this->options = $this->options."state=$this->state";
		}
		if($this->scope!=""){
			$this->add_speccar();
			$this->options = $this->options."scope=$this->scope";
		}

	} /*}}}*/

	// Paramètres optionnels
	private $client_id;
	private $redirect_uri;
	private $response_type;
	private $approval_prompt;
	private $per_page;
	private $after;
	private $before;
	private $client_secret;

	// Paramètres obligatoires
	private $token;
	private $url;
	
	// Résultats
	private $result;
	private $options;
	private $access_token;
	


	// Variables de développement
	private $testing;	// 0 : execution, 1: test (echo)

}


?>
