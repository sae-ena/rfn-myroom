<?php
namespace HelperFunction;

require_once '../admin/dbConnect.php';

function addColumnToTable(string $table , string $columnname ,string $datatype , $length= 0 , bool $isNullable = true){
    global $conn;
    
    try {

        if(columnExists($table,$columnname,$conn)){
            echo "Column  : ".$columnname. " already exists ! \n";
            return;
        }
        $isValidDataType = false;

        $datatypeMaxLengths = [
            "INT" => 11,
            "TINYINT" => 1,
            "SMALLINT" => 5,
            "MEDIUMINT" => 8,
            "BIGINT" => 20,
            "VARCHAR" => 255,
            "TEXT" => 65535,
            "TINYTEXT" => 255,
            "MEDIUMTEXT" => 16777215,
            "LONGTEXT" => 4294967295,
            "CHAR" => 255,
            "DECIMAL" => 65, // Total digits (DECIMAL(65,30) max)
            "FLOAT" => 24,  // Approximate digits of precision
            "DOUBLE" => 53, // Approximate digits of precision
            "DATE" => 10, // Format: YYYY-MM-DD
            "DATETIME" => 19, // Format: YYYY-MM-DD HH:MM:SS
            "TIMESTAMP" => 19, // Format: YYYY-MM-DD HH:MM:SS
            "TIME" => 8, // Format: HH:MM:SS
            "YEAR" => 4 // Format: YYYY
        ];
        
        $datatype = strtoupper($datatype);

        foreach($datatypeMaxLengths as $key => $type){

          
            if($datatype == $key){
                $isValidDataType = true;
                $maxLength = $type;
                

            }
        }

        if(! $isValidDataType){
            echo "Invalid   : ".$datatype. " Datatype.. ! \n";
            return;
        }

        if($isNullable){
            $null = "DEFAULT NULL";
        }else{
            $null = "NOT NULL";
        }
        

        $sql1 = "";
        if(isset($length) && $length > 0){
            // Adding background_color column
            $sql1 = "ALTER TABLE ".$table." ADD COLUMN ".$columnname." ". $datatype ."(".$length.") ".$null.";";
   
        }else{
           
                $sql1 = "ALTER TABLE ".$table." ADD COLUMN ".$columnname." ".$datatype." (". $maxLength .") ".$null.";";
      
        }
  
    $conn->query($sql1);
    if ($conn->error) {
        echo $conn->error;
    }
    echo "Columns '".$columnname."' added successfully! \n";
   
} catch (\Exception $e) {
    // Handling any errors that occur
    echo "Error: " . $e->getMessage();
}
} 
function columnExists($table, $column, $conn) {
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = mysqli_query($conn, $query);
    return (mysqli_num_rows($result) > 0);
}
   

?>