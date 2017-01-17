<?php
	
//echo "One.UMD API username:" ;
//$line = trim(fgets(STDIN));
//$username = $line;

$username="superPublisher";
$password="SuperPublisher2016!";

//Testing Credentials
//$username="curlpublisher";
//$password="Curlpublisher2016!";

//echo "One.UMD API password:" ;
//$line = trim(fgets(STDIN));s
//$password = $line;

$thepath = getcwd();
$csvfile = 'data/newtasksforimport-dec12-run1.csv';


//****************************************************************READ IN FILE
$row = 1;
if (($handle = fopen($csvfile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        
        
//**************************************************************** ESCAPE SPECIAL CHARACTERS WITH ADD SLASHES IN TEXT AREAS

$dirtyDescription = $data[4];
$cleanDescription = htmlspecialchars($dirtyDescription, ENT_QUOTES);

$dirtyMetadescription = $data[5];
$cleanMetadescription = htmlspecialchars($dirtyMetadescription, ENT_QUOTES);        

//****************************************************************BUILD THE NESTED CATEGORIES STRUCTURE
$category = '"categories": [';
$categorycodes = explode(",", $data[13]	);
$i = 0;
$len=count($categorycodes);
foreach ($categorycodes as &$catvalue) {
	if ($i == 0)
		{
    $category = $category  . '{"categoryId": ' . $catvalue  . '},';
    	} else if ($i == $len -1) {
    
          	$category = $category  . '{"categoryId": ' . $catvalue  . '}'; 
            
            }
            
           $i++;
            
	  
}    
$category = trim($category, ",");
    $category = $category  . '],';
	


//****************************************************************BUILD THE NESTED ROLE STRUCTURE

	
$roles = '"roles": [';
$rolecodes = explode(",", $data[15]	);
$i = 0;
$len=count($rolecodes);
foreach ($rolecodes as &$rolevalue) {
	if ($i == 0)
		{
    $roles = $roles  . '{"roleId": ' . $rolevalue  . '},';
    
    } else if ($i == $len -1) {
	    
	     $roles = $roles  . '{"roleId": ' . $rolevalue  . '}';
	     }
	     
	     $i++;
}    
$roles = trim($roles, ",");
    $roles = $roles  . ']'; 
   
//****************************************************************BUILD THE NESTED MARKET STRUCTURE
$markets = '"markets": [';
$marketcodes = explode(",", $data[14] );
$i = 0;
$len=count($marketcodes);
foreach ($marketcodes as &$marketvalue) {
	if ($i == 0) 
	{ 
    $markets = $markets  . '{"marketId": ' . $marketvalue  . '},';
     } else if ($i == $len -1) {
	      $markets = $markets  . '{"marketId": ' . $marketvalue  . '}';
	      }
	      $i++;
}    
$markets = trim($markets, ",");
    $markets = $markets  . '],';

//****************************************************************NOW LOOP THOUGH ROWS

//****************************************************************CONVERT IMAGE CALLED OUT TO BASE64 DATA
$path = "screenshots/".$data[2].".jpg";
$type = pathinfo($path, PATHINFO_EXTENSION);
$dataimage = file_get_contents($path);
$base64 = base64_encode($dataimage);

//****************************************************************COMBINE THE ROW COLUMNS WITH CONVERTED IMAGE AND ADD THE NESTED STRUCTURES TO BUILD FULL JSON STRING
//$jsonstring = '{"publisherId": ' . $data[0] . ',"contactId": ' . $data[1] . ',"title": "' . $data[2] . '","statType": "USER","uniqueKey": "' . $data[3] . '","description": "' . $cleanDescription . '","metaDescription": "' . $cleanMetadescription . '","taskUrl": "' . $data[7] . '","applicationName": "' . $data[6] . '","status": "' . $data[8] . '","authenticated": "' . $data[9] . '","displayVersion": "' . $data[10] . '","imageSetId": ' . $data[11] . ',"media": [{"screenSize": "DESKTOP","mediaType": "IMAGE", "content": "' . $base64 . '","name": "' . $data[13] . '","formatType": "image/jpeg","caption": "' . $data[2] . '"}],'  . $category . $markets . $roles . "}'";

$jsonstring = '{
    "publisherId": ' . $data[0] . ',
    "contactId": ' . $data[1] . ',
    "title": "' . $data[2] . '",
    "statType": "USER",
    "uniqueKey": "' . $data[3] . '",
    "description": "' . $data[4] . '",
    "metaDescription": "' . $data[5] . '",
    "taskUrl": "' . $data[7] . '",
    "applicationName": null,
    "status": "ACTIVE",
    "authenticated": "NONE",
    "displayVersion": "1.0.0",
    "imageSetId": ' . $data[11] . ',
    "media": [
     {
      "screenSize": "DESKTOP",
      "mediaType": "IMAGE",
      "content": "' . $base64 . '",
      "name": "' . $data[2] . '",
      "formatType": "image/jpeg",
      "caption": "' . $data[2] . '"
     }
     
     ],
    '. $category .'
    '. $markets .'
    '. $roles .'
   
}';



//****************************************************************DEBUG
//****************************************************************WRITE JSON TO SCREEN BUT THERE YOU WOULD ADD YOUR CURL TO SEND IT TO ONE    

// create curl resource
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, "https://one.umd.edu/rest-api/secure/tasks");

// set the HTTP header

curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/hal+json;version=1'));

// set the authenication with Basic authentication

curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

// set the location of the JSON file to post
 curl_setopt($ch, CURLOPT_POST, 1);

//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonstring);


// $output contains the output string

$output = curl_exec($ch);

// $status contains the HTTP response code
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// HTTP Response codes as an array

$http_codes = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => 'Switch Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    425 => 'Unordered Collection',
    426 => 'Upgrade Required',
    449 => 'Retry With',
    450 => 'Blocked by Windows Parental Controls',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',
    509 => 'Bandwidth Limit Exceeded',
    510 => 'Not Extended'
);

$importStatus = $http_codes[$status];


echo "IMPORT RESULTS FOR " . date('D - M/d/Y g:H:i A') . " BATCH" . PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;
echo "Import Results for ". $data[2] . " : " . $importStatus . PHP_EOL;


echo PHP_EOL;
echo "Description : " . PHP_EOL;
echo $cleanDescription . PHP_EOL;
echo PHP_EOL;
echo "Meta Description : " . PHP_EOL;
echo $cleanMetadescription . PHP_EOL;
echo PHP_EOL;



// Create a new file for items that didn't import successfully

if ($status > 202) {
	
$failedCurloutput = "Import Results for ". $data[2] . " : " . $status . " ". $importStatus . "\n" . $jsonstring ;

$logtitle = "Failed Imports Log for " . date('D-MdY gHi A');

$fp = fopen("logs/".$logtitle.".txt", 'a+');

fwrite($fp, $failedCurloutput);
fwrite($fp,"\r\n");
fwrite($fp, $jsonstring);
fwrite($fp,"\r\n");
fwrite($fp,"\r\n");
fwrite($fp, $output);
fwrite($fp,"\r\n");
fwrite($fp,"\r\n");
fwrite($fp,"-----------------------------------------------------------");
fwrite($fp,"\r\n");
fwrite($fp,"\r\n");

//Ask cURL to write the contents to a file

curl_setopt($ch, CURLOPT_FILE, $fp);

fclose($fp); 

}

 //close curl resource to free up system resources
curl_close($ch); 


//echo $jsonstring;

    }
    fclose($handle);
}    
    
 
    
   ?>
