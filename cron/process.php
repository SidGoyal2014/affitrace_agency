<?php
ini_set('display_errors', 0);

include_once('classes/clsDatabase.php');

transfer_data();

function transfer_data(){

	global $dbObject;

	$sql = "select 
				OT.id, OT.offer_id, OT.aff_id, OT.aff_click_id, OT.sub2, APC.url 
			FROM
				offer_trackings OT 
					INNER JOIN 
				affiliate_postback_columns APC
					ON
				OT.aff_id = APC.uid
			WHERE
				APC.url != '' 
					AND
				OT.aff_click_id != ''
					AND
				OT.is_transferred = 'no'
			";

	//echo $sql;
	$recordset = $dbObject->query($sql);

	/*echo "<pre>";
	print_r($recordset);
	echo "<pre>";*/

	if($dbObject->numRows($recordset) > 0){

		$str = "";

		while($rec = $dbObject->traverseResultSet($recordset)){

			//pr($obj);
			//$url = str_replace('{tid}', $rec['aff_click_id'], $rec['url']);
			$url = str_replace('{sub2}', $rec['sub2'], $rec['url']);

			//echo "<br>".$url;
			send_curl_get_request($url, $rec['offer_id'], $rec['aff_id']);

            $str = $str.$rec['id'].",";

		}//while


		$str = substr($str, 0, -1);
		//echo "<br>".;

		$sql = "UPDATE offer_trackings SET is_transferred = 'yes', transferred_at=NOW() WHERE id IN(".$str.")";
		$dbObject->query($sql);

		remove_extra_logs();


	}else{

		$str = "No records available for sending data to affiliate";
		save_logs($str, Null, Null);

	}//if

}//transfer_data

function send_curl_get_request($url, $offer_id, $aff_id){

    //$fp = fopen("log.txt", "a");
    
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//$response = curl_exec($ch);


    
    $timeStr = "Sent At: ------".date('j M, Y H:i:s')."---------------";
    
    if(curl_exec($ch) === false){
        $str = "\n".$timeStr.'Curl error: ' . curl_error($ch). $url. "\n";
        
    }else{
	  	$info = curl_getinfo($ch);
	  	$str = "\n".$timeStr.'Took '. $info['total_time']. ' seconds to send a request to '. $url. "\n";
	  	
    }
    
	echo $str;
    /*
	fwrite($fp, $str);
	*/
	
	curl_close($ch);

	save_logs($str, $offer_id, $aff_id);
	
}//send_curl_get_request


function save_logs($str, $offer_id, $aff_id){

	global $dbObject;

	$arrLog = array();
	$arrLog['offer_id'] = $offer_id;
	$arrLog['aff_id'] = $aff_id;
	$arrLog['module'] = 'CRON';
	$arrLog['description'] = $str;
	$arrLog['created_at'] = date('Y-m-d H:i:s');

	$table = 'logs';

	$dbObject->insert($table,$arrLog);

}//save_logs


function remove_extra_logs(){

	global $dbObject;

	$sql ="DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 DAY)";

	$dbObject->query($sql);


}//remove_extra_logs

echo "\n".'Completed';

?>