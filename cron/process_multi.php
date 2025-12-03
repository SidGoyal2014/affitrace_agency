<?php
ini_set('display_errors', 0);

include_once('classes/clsDatabase.php');



transfer_data();

function transfer_data(){

	global $dbObject;

	$sql = "select 
				OT.id, OT.sub2, APC.url 
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
		$arrUrls = array();

		while($rec = $dbObject->traverseResultSet($recordset)){

			//pr($obj);
			$arrUrls[] = str_replace('{sub2}', $rec['sub2'], $rec['url']);

			$str = $str.$rec['id'].",";

		}//while

        /*
    	echo"<pre>";
    	print_r($arrUrls);
    	echo"</pre>";
    	*/



	    // Set the batch size based on your optimization needs
	    $batchSize = 50;
	    $urlBatches = array_chunk($arrUrls, $batchSize);

	    foreach ($urlBatches as $batch) {
	        sendCurlRequests($batch);
	        echo "<br>Sleeping for half second ...". date('Y-m-d H:i:s');
	        // Introduce a delay if needed to avoid overwhelming the server or to comply with rate limits
	        usleep(500000); // Sleep for 0.5 seconds (adjust as needed)
	        echo "<br>Woke up again ...". date('Y-m-d H:i:s');
	    }

		$str = substr($str, 0, -1);
		echo "<br>".$sql = "UPDATE offer_trackings SET is_transferred = 'yes', transferred_at=NOW() WHERE id IN(".$str.")";
		$dbObject->query($sql);


	}//if


}//transfer_data



// Function to send cURL requests in batches
function sendCurlRequests($batch) {

    $multiHandler = curl_multi_init();
    $handles = [];

    foreach ($batch as $affiliateUrl) {
        
        $ch = curl_init();
        // Set cURL options for each request
        // ...
        
        curl_setopt($ch, CURLOPT_URL, $affiliateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // ...
        curl_multi_add_handle($multiHandler, $ch);
        $handles[] = $ch;
    }

    do {
        $status = curl_multi_exec($multiHandler, $running);

        echo "<br>----------".date('Y-m-d H:i:s')."---------------".$status;

    } while ($status === CURLM_CALL_MULTI_PERFORM || $running);



	if ($status !== CURLM_OK) {
	    echo "cURL multi-exec error: " . curl_multi_strerror($status);
	}


    foreach ($handles as $ch) {

        $response = curl_multi_getcontent($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_multi_remove_handle($multiHandler, $ch);
        curl_close($ch);
    }

    curl_multi_close($multiHandler);

}//sendCurlRequests





echo '<br>Completed';

?>