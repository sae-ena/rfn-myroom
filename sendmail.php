<?php
// Define the PHP code that we want to encrypt
$plain_code = '<?php 
$host = "mysql-29620eed-kidssujal-9bd8.j.aivencloud.com"; 
$username = "avnadmin";
$password = "AVNS_1N9Dr_M5lJZIRxcd8gj"; 
$dbname = "rf_db";
$portNo = 18250;

$conn = new mysqli($host, $username, $password, $dbname,$portNo);
?>

    ';

// Define a secret key and IV (Initialization Vector) for encryption
$encryption_key = 'my_secret_key785765';  // A secret key to encrypt the data
$iv = '1234567890123456';  // 16-byte IV (for AES-128)

// Encrypt the PHP code using AES-128-CBC
$encrypted_code = openssl_encrypt($plain_code, 'aes-128-cbc', $encryption_key, 0, $iv);

// Output the encrypted code to store (for example, in a database or file)
echo "Encrypted Code: " . $encrypted_code . "\n";

// For testing purposes, you can save this encrypted string into a file or database
// This step is just for demonstration purposes
// file_put_contents('encrypted_code.txt', $encrypted_code);  // Save the encrypted code into a file**************

// Retrieve the encrypted code from the file (or from a database)
// $encrypted_code = file_get_contents('encrypted_code.txt');  // Read the encrypted code from the file*******************

// Define the decryption key and IV (must match the encryption process)
$decryption_key = 'my_secret_key785765';  // The same secret key used for encryption
$iv = '1234567890123456';  // The same IV used during encryption

// Decrypt the encrypted code
$decrypted_code = openssl_decrypt($encrypted_code, 'aes-128-cbc', $decryption_key, 0, $iv);

// Check if decryption was successful
if ($decrypted_code === false) {
    die('Decryption failed');
}

// Output the decrypted code (just for verification)
echo "Decrypted Code: " . $decrypted_code . "\n";

// Execute the decrypted PHP code using eval()
eval('?>' .$decrypted_code);  // This will execute the decrypted PHP code



$result= $conn->query('Select * From users');
var_dump($result->fetch_assoc());



if(false){


$plain_code = '<?php echo "This is the decrypted code!"; ?>';  // The PHP code you want to encrypt

// Encryption parameters
$encryption_key = 'secret_key';  // The secret key for encryption
$iv = '1234567890123456';  // Initialization Vector (16 bytes for AES-128)

// $encrypted_code = openssl_encrypt($plain_code, 'aes-128-cbc', $encryption_key, 0, $iv);

// You can store $encrypted_code securely (e.g., in a file or database)
// echo $encrypted_code;  // For demonstration, print the encrypted code
//QzruKPDcEY8bdJs9EeB4qUytuykI4utw7K1SwUPzJWiihHQiwVdG2HRPZvg1NhjW
// die;
// Encrypted sensitive code (validation logic, etc.)
$encrypted_code = 'This is the decrypted code!';

// Decrypt and execute the code
// $decrypted_code = openssl_decrypt($encrypted_code, 'aes-128-cbc', 'secret_key', 0, '1234567890123456');


// Decryption parameters
$decryption_key = 'QzruKPDcEY8bdJs9EeB4qUytuykI4utw7K1SwUPzJWiihHQiwVdG2HRPZvg1NhjW';  // The secret key for decryption (must match encryption key)
$iv = '1234567890123456';  // Initialization Vector (16 bytes for AES-128)

$decrypted_code = openssl_decrypt($encrypted_code, 'aes-128-cbc', $decryption_key, 0, $iv);

if ($decrypted_code === false) {
    die('Decryption failed');
}

// Decrypt the code and evaluate it
eval($decrypted_code);  
$mac = exec('getmac');

echo $mac;
die;
}?>