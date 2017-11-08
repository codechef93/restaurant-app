<?php 

    /**
    * @author Appsthing
    * @version 2
    */
    
    error_reporting(0);

    $env_data = array();
    $env_error = false;
    $db_connection = false;
    $db_connection_error = '';
    $db_connection_error_no = '';
    $db_connection_value_missing_error = false;
    $db_table_exists = false;
    $product_activated = false;
    
    $env_path = (isset($level) && $level == 'include_db_connection')?"../../.env":"../.env";
    $handle = fopen($env_path, "r");
    
    if($handle) {
        while (($line = fgets($handle)) !== false) {
        if( strpos($line,"=") !== false) {
                $var = explode("=",$line);
                $env_data[trim($var[0])] = trim($var[1]);
            }
        }
        fclose($handle);
    } else {
        $env_error = true;
    }
    
    if(count($env_data)>0){
        $app_environment = $env_data['APP_ENV'];
        $app_debug = $env_data['APP_DEBUG'];
        $app_key = $env_data['APP_KEY'];
        
        $db_host = $env_data['DB_HOST'];
        $db_port = $env_data['DB_PORT'];
        $db_name = $env_data['DB_DATABASE'];
        $db_username = $env_data['DB_USERNAME'];
        $db_password = $env_data['DB_PASSWORD'];

        $db_array = [$db_host, $db_username, $db_password, $db_name, $db_port];
        
        $conn = new mysqli($db_host, $db_username, $db_password, $db_name, $db_port);
        
        if ($conn->connect_errno) {
            $db_connection = false;
            $db_connection_error = $conn->connect_error;
            $db_connection_error_no = $conn->connect_errno;
            if(count(array_filter($db_array)) != count($db_array)){
                $db_connection_value_missing_error = true;
            }
        }else if(mysqli_get_host_info($conn) != ''){
            $db_connection = true;
            $GLOBALS['conn'] = $conn;
            $sql = "SHOW TABLES FROM `$db_name`";
            $result = $conn->query($sql);
            if (!$result) {
                $db_table_exists = false;
            }else{
                if($result->num_rows >0){
                    $db_table_exists = true;
                }else{
                    $db_table_exists = false;
                }
            }
        }

        if($db_connection == true){
            $activation_code_checker_query = "SELECT activation_code FROM app_activation";
            $activation_code_checker_query_result = $conn->query($activation_code_checker_query);
            if($activation_code_checker_query_result != false){
                $row = $activation_code_checker_query_result->fetch_assoc();
                if(isset($row['activation_code']) && $row['activation_code'] != ''){
                    $product_activated = true;
                }
            }
        }
    }