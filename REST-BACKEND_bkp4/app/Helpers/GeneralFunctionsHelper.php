<?php

	use Illuminate\Support\Facades\Http;

	function pr($data){
		echo'<pre>';
		print_r($data);
		echo'</pre>';

	}//pr


	function showVar($var){
	
		return isset($var)?$var:'';
	
	}//showVar


	function loginPageUrl($strUsername=""){

		$username = "";

		if($strUsername == ""){

			if(!is_null(session('username')) && !empty(session('username'))){
				$username = session('username');

			}elseif(!is_null(request('eai')) && !empty(request('eai'))){
				$username = request('eai');

			}

			/*
			echo "<br>EAI: ". request('eai')."---------".is_null(request('eai'));
			echo "<br>SESSION: ".session('username')."---------".is_null(session('username'));


			if (!empty(request('eai')) || !empty(session('username'))) {

			    echo "<br>At least one of 'EAI: ".request('eai')."' or 'SESSION: ".session('username')."' is not empty";
			}
			*/


		}else{

			$username = $strUsername;
		}

		//$loginPageUrl = loginPageUrl(session('username'));

		return 'login/'.$username;

	}//loginPageUrl


	function dynamic_username($strUsername=""){

		$username = "";

		if($strUsername == ""){

			if(!is_null(session('username')) && !empty(session('username'))){
				$username = session('username');

			}elseif(!is_null(request('eai')) && !empty(request('eai'))){
				$username = request('eai');

			}

		}else{

			$username = $strUsername;
		}

		return $username;

	}//dynamic_username


?>