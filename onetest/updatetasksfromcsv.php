<?php
	
//echo "One.UMD API username:" ;
//$line = trim(fgets(STDIN));
//$username = $line;

$username="superPublisher";
$password="SuperPublisher2016!";

//echo "One.UMD API password:" ;
//$line = trim(fgets(STDIN));
//$password = $line;

$thepath = getcwd();
$csvfile = 'data/scholarships.csv';


//****************************************************************READ IN FILE
$row = 1;
if (($handle = fopen($csvfile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        
        
//**************************************************************** ESCAPE SPECIAL CHARACTERS WITH ADD SLASHES IN TEXT AREAS

$dirtyfield1 = $data[3];
$cleanfield1 = htmlspecialchars($dirtyfield1, ENT_QUOTES);
     

//****************************************************************COMBINE THE ROW COLUMNS WITH CONVERTED IMAGE AND ADD THE NESTED STRUCTURES TO BUILD FULL JSON 
$jsonstring = '[{
    "op":  ". $data[1] . ",
    "path": ". $data[2] . ",
    "value": ". $data[3] . "
  }]';



//****************************************************************DEBUG
//****************************************************************WRITE JSON TO SCREEN BUT THERE YOU WOULD ADD YOUR CURL TO SEND IT TO ONE    

// create curl resource"
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, "https://one.umd.edu/rest-api/secure/tasks/" . $data[0] );

// set the authenication with Basic authentication

curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

// change the request to a Patch instead of a Post
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonstring);

curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);

// $output contains the output string

$output = curl_exec($ch);

// retunr header in an array
function parse_http_head ($str) {

    $result = array();

    // Split into lines
    $lines = explode("\r\n", $str);

    // Handle response line
    $line = explode(' ', array_shift($lines), 3);
    $version = explode('/', $line[0]);
    $result['version'] = (float) $version[1];
    $result['code'] = (int) $line[1];
    $result['text'] = $line[2];

    // Parse headers
    $result['headers'] = array();
    while ($line = trim(array_shift($lines))) {
      list($name, $val) = explode(':', $line, 2);
      $name = strtolower(trim($name)); // Header names are case-insensitive, so convert them all to lower case so we can easily use isset()
      if (isset($result['headers'][$name])) { // Some headers (like Set-Cookie:) may appear more than once, so convert them to an array if necessary
        $result['headers'][$name] = (array) $result['headers'][$name];
        $result['headers'][$name][] = trim($val);
      } else {
        $result['headers'][$name] = trim($val);
      }
    }

    return $result;

  }

$parsed = parse_http_head($output);
$dirtycurrentEtag = $parsed["headers"]["etag"];
$currentEtag = addslashes($dirtycurrentEtag);

 //close curl resource to free up system resources
//curl_close($ch); 


// create curl resource"
$ch2 = curl_init();

// set url
curl_setopt($ch2, CURLOPT_URL, "https://one.umd.edu/rest-api/secure/tasks/" . $data[0] );

// set the HTTP header

curl_setopt($ch2, CURLOPT_HTTPHEADER, array ('Content-Type: application/json', "'If-None-Match:". $currentEtag ."'" , 'Accept: application/hal+json;version=1'));

// set the authenication with Basic authentication

curl_setopt($ch2, CURLOPT_USERPWD, $username . ":" . $password);

// change the request to a Patch instead of a Post
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'PATCH');

//return the transfer as a string
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonstring);

// $output contains the output string

$output = curl_exec($ch2);


// $status contains the HTTP response code
$status = curl_getinfo($ch2, CURLINFO_HTTP_CODE);


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

$updateStatus = $http_codes[$status];


echo "UPDATE RESULTS FOR " . date('D - M/d/Y g:H:i A') . " BATCH" . PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

echo "UPDATE Results for ". $data[0] . " : " . $updateStatus . PHP_EOL;

echo PHP_EOL;
echo "Current Etag: " . PHP_EOL;
echo $currentEtag . PHP_EOL;
echo PHP_EOL;
echo "Operation: " . PHP_EOL;
echo $data[1] . PHP_EOL;
echo PHP_EOL;
echo "Field: " . PHP_EOL;
echo $data[2] . PHP_EOL;
echo PHP_EOL;
echo "Value : " . PHP_EOL;
echo $cleanfield1 . PHP_EOL;
echo PHP_EOL;



// Create a new file for items that didn't import successfully

if ($status > 202) {
	
$failedCurloutput = "Update Results for ". $data[0] . " : " . $status . " ". $updateStatus . "\n" . $jsonstring ;

$logtitle = "Failed Updates Log for " . date('D-MdY gHi A');


$fp = fopen("logs/".$logtitle.".txt", 'a+');


fwrite($fp, $failedCurloutput);
fwrite($fp,"\r\n");
fwrite($fp, "Escaped ETAG: ");
fwrite($fp, $currentEtag);
fwrite($fp,"\r\n");
fwrite($fp,"\r\n");
fwrite($fp, $output);
fwrite($fp,"\r\n");
fwrite($fp,"\r\n");
fwrite($fp, $jsonstring);
fwrite($fp,"\r\n");
fwrite($fp,"\r\n");


//Ask cURL to write the contents to a file

curl_setopt($ch, CURLOPT_FILE, $fp);

fclose($fp); 

}

 //close curl resource to free up system resources
curl_close($ch); 

curl_close($ch2);
//echo $jsonstring;

    }
    fclose($handle);
}    
    
 
    
   ?>
