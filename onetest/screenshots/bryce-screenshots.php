<?php
	




$thepath = getcwd();
$csvfile = 'newTasksfromTwitter-run1.csv';


//****************************************************************READ IN FILE
$row = 1;
if (($handle = fopen($csvfile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        


//Screenshot Machine Credentials
// For my free account only

//$url = $data[10];
$url = $data[7];
$creds = "bc3e63";
$secret = "MakeamericaGreatagain2016";
$hash = md5($url.$secret);

//****************************************************************CONVERT IMAGE CALLED OUT TO BASE64 DATA
//$path = $data[17];
//$type = pathinfo($path, PATHINFO_EXTENSION);
//$dataimage = file_get_contents($path);
//$base64 = base64_encode($dataimage);

// create curl resource
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, "http://api.screenshotmachine.com/?key=".$creds."&size=X&hash=".$hash."&url=".$url);

// Create a new file

$imagetitle = trim($data[2]);

$fp = fopen($imagetitle.".jpg", 'w');

//Ask cURL to write the contents to a file

curl_setopt($ch, CURLOPT_FILE, $fp);

curl_exec($ch);


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
echo "Import Results for ". $data[10] . " : " . $importStatus . PHP_EOL;
echo "Created ". $imagetitle . ".jpg". " in the same directory" . PHP_EOL;

echo PHP_EOL;
echo PHP_EOL;


//echo $data[2] . " " . "Imported" . PHP_EOL;

 //close curl resource to free up system resources
curl_close($ch);  
fclose($fp);
    }
    fclose($handle);
}    
    
 
    
   ?>
