<?php
// prompts for user input of username and password


echo "One.UMD API username:" ;
$line = trim(fgets(STDIN));
$username = $line;

echo "One.UMD API password:" ;
$line = trim(fgets(STDIN));
$password = $line;

echo "Task CSV File location (e.g. newtasks.csv)" ;
$line = trim(fgets(STDIN));
$filename = $line;

//echo "JSON filename (with extension)" ;
//$line = trim(fgets(STDIN));
//$filename = $line;

//$json = file_get_contents($filename);

// php function to convert csv to json format
function csvToJson($fname) {
    // open csv file
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    
    //read csv headers
    $key = fgetcsv($fp,"1024",",");
    
    // parse csv rows into array
    $json = array();
        while ($row = fgetcsv($fp,"1024",",")) {
        $json[] = array_combine($key, $row);
    }
    
    // release file handle
    fclose($fp);
    
    // encode array to json
    return json_encode($json);
}


print_(csvToJson($filename);



//$jsonIterator = new RecursiveIteratorIterator(
//    new RecursiveArrayIterator(json_decode($json, TRUE)),
//    RecursiveIteratorIterator::SELF_FIRST);

// foreach ($jsonIterator as $key => $val) {
//    if(is_array($val)) {
//        echo "$key:\n";
//    } else {
//        echo "$key => $val\n";
//    }
//}


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
 curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// $output contains the output string

$output = curl_exec($ch);

// $status contains the HTTP response code
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo $status ;

//close curl resource to free up system resources
curl_close($ch);

?>
