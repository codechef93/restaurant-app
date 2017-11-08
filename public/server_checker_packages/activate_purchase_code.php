<?php 

    /**
    * @author Appsthing
    * @version 2
    */
    
    error_reporting(0);

    $level = 'include_db_connection';

    include 'server_db_connection_checker.php';

    $failure_response = [
        'status' => false,
        'msg' => 'Activation Failed!',
        'status_code' => 400
    ];

    if (isset($_POST['purchase_code']) && $_POST['purchase_code'] != '' ) {
        
        $purchase_code = trim($_POST['purchase_code']);
        $chost = trim($_SERVER['HTTP_HOST']);
        $cip = trim($_SERVER['SERVER_ADDR']);
        $post_data = array(
            'purchase_code' => $purchase_code,
            'chost' => $chost,
            'ip' => $cip
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://www.appsthing.com/api/activate_product',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_FOLLOWLOCATION => true
        ));
        
        $output = curl_exec($ch);
        $activation_output = json_decode($output, true);
        if($activation_output != null && $activation_output['status_code'] == 200){
            if(isset($activation_output['data']['activation_code']) && $activation_output['data']['activation_code'] != ''){
                
                $activation_code = $activation_output['data']['activation_code'];
                $created_date = date('Y-m-d H:i:s');
                
                $activation_code_delete_sql = "DELETE FROM app_activation";
                if(mysqli_query($conn, $activation_code_delete_sql)){

                    $activation_code_sql = "INSERT INTO app_activation (activation_code, created_at, updated_at) VALUES ('$activation_code', '$created_date', '$created_date')";
                    
                    if (mysqli_query($conn, $activation_code_sql)) {
                        $response = [
                            'status' => $activation_output['status'],
                            'msg' => $activation_output['msg'],
                            'status_code' => $activation_output['status_code']
                        ];
                    }else{
                        $response = $failure_response;
                    }
                }else{
                    $response = $failure_response;
                }
            }else{
                $response = $failure_response;
            }
        }else{
            $response = [
                'status' => $activation_output['status'],
                'msg' => $activation_output['msg'],
                'status_code' => 400
            ];
        }

    } else {
        $response = $failure_response;
    }

    mysqli_close($conn);
    echo json_encode($response);