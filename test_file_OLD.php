<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="Generator" content="EditPlus®">
		<meta name="Author" content="">
		<meta name="Keywords" content="">
		<meta name="Description" content="">
		<title>Document</title>
	</head>
	<body>
<?php
echo"<pre>";
print_r($_GET);
print_r($_REQUEST);
echo"</pre>";/**/
//http://127.0.0.1:8000/offer/gourl/1/18/?&tid={tid}&sub_aff_id={sub_aff_id}
//https://prosquad.workdemo.net/offer/sd
//https://affidemo.affitrace.com/offer/sd
?>
		<form method="get" action="http://127.0.0.1:8000/offer/sd">
			<input type="hidden" name="aff_click_id" value="<?php echo $_REQUEST['tid'];?>">
			<?php
			
			foreach($_REQUEST as $key=>$value){

				if($key != 'aff_click_id'){
					?>
						<input type="" name="<?php echo $key;?>" value="<?php echo $value;?>">
					<?php
				}
			}

			?>
			<input type="submit" value="Submit">
		</form>



<script>

var xhttp = new XMLHttpRequest();
//xhttp.open("POST", "https://prosquad.workdemo.net/offer/sd", true);
//xhttp.open("POST", "http://127.0.0.1:8000/offer/sd", true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("aff_id=15&aff_sub1=replace_it&sub_aff_id=replace_it&aff_sub2=replace_it&aff_sub3=replace_it&aff_sub5=replace_it&aff_sub4=replace_it&source=replace_it&android_id=replace_it&google_aid=replace_it&device_id=11&ip=replace_it&iosidfa=replace_it&offer_name=new+offer+2&offer_id=3&country=IN&os=replace_it&raw_useragent=replace_it&os_ver=replace_it&aff_click_id=d1c20b1c22e9b1d75cd75831c3ed5844&adv_id=3&timestamp=replace_it&useragent=replace_it&deep_link=replace_it");

</script>

	</body>
</html>
