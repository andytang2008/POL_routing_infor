<?php
/**
Get POL number by searching for MMS id.
Running test envirnment: Windows 7  + PHP 5.6.26 or above.
08-2019
by Andy Tang
*/
echo "<html lang=\"en\" >";
echo "<head>";
echo "  <meta charset=\"UTF-8\">";
echo "  <title>Beautiful CSS3 Search form</title>";
echo "	<script src='./jquery.min.js'></script>";
echo "	<link rel=\"stylesheet\" href=\"./style2.css\">";
echo "</head>";
echo "<body>";
echo "<div class='console-container'><span id='text'>";
echo "MMS ID:".$_POST['id']."<br>";
ini_set("memory_limit","360M"); //Setup the maximum file size you will open.

$fp = fopen('mmsid_infor.txt', 'w');
$mmsid=$_POST['id'];

apicall($mmsid, $fp);
fclose($fp);
echo "------------------------------------------<br>";
echo "<br><a href=\"javascript:history.go(-1)\">GO BACK</a>";
echo "</span></div>";
echo "</body>";
echo "</html>";


function apicall($mmsid,$filehandle){
	$ch = curl_init(); //Initiate the Curl
	$baseUrl = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/acq/po-lines?q=mms_id~{mms_id}';  
	$templateParamNames = array('{mms_id}');
	$templateParamValues = array(rawurlencode($mmsid)); //if using urlencode(), you will have the trouble if the user primary id contain space.
    $baseUrl = str_replace($templateParamNames, $templateParamValues, $baseUrl); 
    // echo $baseUrl;
	// echo $mmsid;
	
	$queryParams = array(
		'status' => 'ALL',
			'limit' => '10',
			'offset' => '0',
			'order_by' => 'title',
			'direction' => 'desc',
			'acquisition_method' => 'ALL',
			'apikey' => 'Please put your institution API key here'  //Please put your institution's API key here.If you use Sandbox API key, it will extract data from Sandbox. If you use Production environment key, it will extract data from Production. Ex Libris automatically extracts data from Sandbox or Production according to API key type.
	);
	$url = $baseUrl . "&".http_build_query($queryParams);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');  //Using Get method
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	//echo $url;
	$response = curl_exec($ch);
	curl_close($ch);
	//echo "                   ";
	//echo $response;
	//$xml = simplexml_load_string($response);
	$xml = simplexml_load_string($response);
	//print_r($xml);
foreach($xml->po_line as $po_line)
{
    echo (string)$po_line->number."<br>";  //output all number node value under po_line
}
	
	fwrite($filehandle, $response);
}
?>