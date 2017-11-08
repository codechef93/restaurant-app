<?php 

    /**
    * @author Appsthing
    * @version 4
    */

    error_reporting(0);

    include 'server_checker_packages/server_db_connection_checker.php';

    $server_requirements = [];

    $laravel_version = '7.30.4';

    $matched = '<i class="fas fa-check-circle text-success"></i>';
    $missed = '<i class="fas fa-times-circle text-danger"></i>';

    $laravel_requirement_list = [
        '7.30.4' => [
            'php' => '7.2.5',
            'mcrypt' => false,
            'openssl' => true,
            'pdo' => true,
            'mbstring' => true,
            'tokenizer' => true,
            'xml' => true,
            'ctype' => true,
            'json' => true,
            'bcmath' => true,
            'mod_rewrite' => true,
            'file_info' => true,
            'zip' => true,
            'gd' => true,
            'obs' => '',
            'mysqli' => true,
            'proc_open' => true,
            'symlink' => true
        ]
    ];
    
    $server_requirements['php_version'] = version_compare(PHP_VERSION, $laravel_requirement_list[$laravel_version]['php'], '>=');

    $server_requirements['openssl_enabled'] = extension_loaded("openssl");

    $server_requirements['mbstring_enabled'] = extension_loaded("mbstring");

    $server_requirements['tokenizer_enabled'] = extension_loaded("tokenizer");

    $server_requirements['pdo_enabled'] = defined('PDO::ATTR_DRIVER_NAME');
 
    $server_requirements['xml_enabled'] = extension_loaded("xml");

    $server_requirements['ctype_enabled'] = extension_loaded("ctype");

    $server_requirements['json_enabled'] = extension_loaded("json");

    $server_requirements['mcrypt_enabled'] = extension_loaded("mcrypt_encrypt");

    $server_requirements['bcmath_enabled'] = extension_loaded("bcmath");

    $server_requirements['file_info_enabled'] = extension_loaded("fileinfo");

    $server_requirements['zip_enabled'] = extension_loaded("zip");

    $server_requirements['gd_enabled'] = extension_loaded("gd");

    $server_requirements['mysqli_enabled'] = extension_loaded("mysqli");

    $server_requirements['symlink_enabled'] = is_link('symlink_check');

    $proc_open_enabled = true;

    if(function_exists('ini_get')){
        $disabled_functions = ini_get('disable_functions');
        if ($disabled_functions != ''){
            $disabled_function_array = explode(',', $disabled_functions);
            if(in_array('proc_open', $disabled_function_array) || !function_exists('proc_open')){
                $proc_open_enabled = false;
            }
        }
    }

    $server_requirements['proc_open_enabled'] = $proc_open_enabled;

    chmod("../storage", 0775);
    $storage_folder_writable = is_writable('../storage');

    chmod("../bootstrap/cache", 0775);
    $boostrap_cache_folder_writable = is_writable('../bootstrap/cache');

    $storage_symbolic_link_exists = is_link('storage');
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Appsthing Server Requirement Checker</title>

        <link rel="icon" type="image/png" sizes="32x32" href="images/favicon_32_32.png">
        <link rel="apple-touch-icon" href="images/logo_apple_touch_icon.png"/>  
        <link rel="stylesheet" href="css/font.css">
        <link rel="stylesheet" href="css/labels.css">
        <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="plugins/fontawesome/all.css">
        <link rel="stylesheet" href="css/server_checker.css">
    </head>
    <body>
        <div class="container-fluid pl-0 pr-0">
            <div class="content mt-5 pl-3 pr-3">

                <div class="d-flex justify-content-center mb-4">
                    <span class="text-headline">Server Requirements Checker & Installation Helper</span>
                </div>
                <div class="d-flex justify-content-center mb-4">
                    <span class="">This script will check if your server meets the requirements for running Appsthing POS application</span>
                </div>

                <hr>

                <div class="d-flex justify-content-center text-title mb-3 text-secondary">General Requirements</div>
                
                <div class="d-flex flex-column mb-4">
                    <div class="mb-1">
                        <div class="d-flex flex-column">
                            <p class="d-flex justify-content-center mb-0">
                                <span><?php echo $server_requirements['php_version'] ? $matched : $missed; ?>&nbsp; PHP Version >= <?php echo $laravel_requirement_list[$laravel_version]['php'] ?></span>
                            </p>
                            <div class="d-flex justify-content-center">
                                <p class=""><p>Current Version <?php echo PHP_VERSION; ?></p></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['bcmath']) { ?>
                            <p><?php echo $server_requirements['bcmath_enabled'] ? $matched : $missed; ?>&nbsp;BCmath PHP Extension</p>
                        <?php } ?>
                    </div>
                    
                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['openssl']) { ?>
                            <p><?php echo $server_requirements['openssl_enabled'] ? $matched : $missed; ?>&nbsp;OpenSSL PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['file_info']) { ?>
                            <p><?php echo $server_requirements['file_info_enabled'] ? $matched : $missed; ?>&nbsp;Fileinfo PHP Extension</p>
                        <?php } ?>
                    </div>
                    
                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['pdo']) { ?>
                            <p><?php echo $server_requirements['pdo_enabled'] ? $matched : $missed; ?>&nbsp;PDO PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['mbstring']) { ?>
                            <p><?php echo $server_requirements['mbstring_enabled'] ? $matched : $missed; ?>&nbsp;Mbstring PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['tokenizer']) { ?>
                            <p><?php echo $server_requirements['tokenizer_enabled'] ? $matched : $missed; ?>&nbsp;Tokenizer PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['xml']) { ?>
                            <p><?php echo $server_requirements['xml_enabled'] ? $matched : $missed; ?>&nbsp;XML PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['ctype']) { ?>
                            <p><?php echo $server_requirements['ctype_enabled'] ? $matched : $missed; ?>&nbsp;Ctype PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['json']) { ?>
                            <p><?php echo $server_requirements['json_enabled'] ? $matched : $missed; ?>&nbsp;JSON PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['mcrypt']) { ?>
                            <p><?php echo $server_requirements['mcrypt_enabled'] ? $matched : $missed; ?>&nbsp;Mcrypt PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['mysqli']) { ?>
                            <p><?php echo $server_requirements['mysqli_enabled'] ? $matched : $missed; ?>&nbsp;MySQLi PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['zip']) { ?>
                            <p><?php echo $server_requirements['zip_enabled'] ? $matched : $missed; ?>&nbsp;Zip PHP Extension</p>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-center mb-1">
                        <?php if ($laravel_requirement_list[$laravel_version]['gd']) { ?>
                            <p><?php echo $server_requirements['gd_enabled'] ? $matched : $missed; ?>&nbsp;GD PHP Extension</p>
                        <?php } ?>
                    </div>

                    <?php if ($laravel_requirement_list[$laravel_version]['proc_open']) { ?>
                    <div class="d-flex justify-content-center text-center mb-1">
                        <div class="d-flex flex-column">
                            <div>
                                <p class="<?php echo ($server_requirements['proc_open_enabled'] == false)?'mb-0':''; ?>"><?php echo $server_requirements['proc_open_enabled'] ? $matched : $missed; ?>&nbsp;<span class='text-info'>proc_open</span> Function</p>
                            </div>
                            <?php if ($server_requirements['proc_open_enabled'] == false) { ?>
                                <div class="text-secondary">
                                    <p class=""><i class="fas fa-info-circle"></i> Please enable <span class='text-info'>proc_open</span> function on your server</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if ($laravel_requirement_list[$laravel_version]['symlink']) { ?>
                    <div class="d-flex justify-content-center text-center mb-1">
                        <div class="d-flex flex-column">
                            <div>
                                <p class="<?php echo ($server_requirements['symlink_enabled'] == false)?'mb-0':''; ?>"><?php echo $server_requirements['symlink_enabled'] ? $matched : $missed; ?>&nbsp;<span class='text-info'>symlink</span> Function</p>
                            </div>
                            <?php if ($server_requirements['symlink_enabled'] == false) { ?>
                                <div class="text-secondary">
                                    <p class=""><i class="fas fa-info-circle"></i> Please enable <span class='text-info'>symlink</span> on your server</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>

                </div>

                <div class="d-flex justify-content-center mb-3"><i class="fas fa-arrow-down fa-2x"></i></div>

                <hr>

                <div class="d-flex justify-content-center text-title mb-3 text-secondary">Database Connection</div>

                <div class="d-flex flex-column mb-4">
                    <div class="mb-1">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-center mb-0">
                                <p><?php echo $db_connection ? $matched : $missed; ?>&nbsp; Database Connection Status</p>
                            </div>
                            <?php if($db_connection == false){ ?>
                                <div class="d-flex justify-content-center">
                                    <p class="">Please update <span class='text-info'>.env</span> file with the database details</p>
                                </div>
                                <?php if($db_connection_error != ''){ ?>
                                    <?php if($db_connection_value_missing_error == true){?>
                                        <div class="d-flex justify-content-center">
                                            <p class=""><i class="fas fa-info-circle"></i> Some of the database details in <span class='text-info'>.env</span> file is not set</p>
                                        </div>
                                    <?php } ?>
                                    <div class="d-flex justify-content-center">
                                        <p class="text-danger">DB Error: (<?php echo $db_connection_error_no ?>) <?php echo $db_connection_error; ?></p>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php if($db_connection && $db_table_exists == false){ ?>
                                <div class="d-flex justify-content-center">
                                    <p class=""><span class="badge badge-danger">Important</span> Please import the database tables</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mb-3"><i class="fas fa-arrow-down fa-2x"></i></div>
                
                <hr>

                <div class="d-flex justify-content-center text-title mb-3 text-secondary">App .env File Requirements</div>

                <div class="d-flex flex-column mb-4">
                    <?php if($env_error == true){ ?>
                        <div class="mb-1">
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-center mb-0">
                                    <p class="mb-0"><?php echo $env_error ? $missed : $matched; ?>&nbsp; Unable to Read .env File</p>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <p class=""><p>Please check <span class='text-info'>.env</span> file exists in the root directory</p></p>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <div class="mb-1">
                            <p class="d-flex justify-content-center mb-0">
                                <span><?php echo $app_environment == 'production' ? $matched : $missed; ?></span>&nbsp; Current App Enviroment &nbsp;<span class='text-info'><?php echo $app_environment; ?></span>
                            </p>
                            <?php if($app_environment != 'production'){ ?>
                                <div class="d-flex justify-content-center">
                                    <p class="">Keep the APP_ENV as <span class='text-info'>production</span>, if you are running the app in live domain</p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex flex-column">
                                <p class="d-flex justify-content-center mb-0">
                                    <span><?php echo $app_debug == 'false' ? $matched : $missed; ?></span>&nbsp; Current App Debug &nbsp;<span class='text-info'><?php echo $app_debug; ?></span>
                                </p>
                                <?php if($app_debug == 'true'){ ?>
                                    <div class="d-flex justify-content-center">
                                        <p class="">Keep the APP_DEBUG as <span class='text-info'>false</span>, if you are running the app in live domain</p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if($app_key == ''){ ?>
                            <div class="d-flex justify-content-center mb-1">
                                <p class="mb-0"><?php echo $app_key == '' ? $missed : $matched; ?>&nbsp; Please set the APP KEY</p>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>

                <div class="d-flex justify-content-center mb-3"><i class="fas fa-arrow-down fa-2x"></i></div>

                <hr>

                <div class="d-flex justify-content-center text-title mb-3 text-secondary">App Folder Permission Requirements</div>

                <div class="d-flex flex-column mb-5">
                    <div class="d-flex justify-content-center mb-1">
                        <p><?php echo $storage_folder_writable ? $matched : $missed; ?>&nbsp;<span class='text-info'>storage</span> Folder is Writable (775 Permission)</p>
                    </div>
                    <div class="d-flex justify-content-center mb-1">
                        <p><?php echo $boostrap_cache_folder_writable ? $matched : $missed; ?>&nbsp; <span class='text-info'>bootstrap/cache</span> Folder is Writable (775 Permission)</p>
                    </div>
                    <div class="mb-1">
                        <div class="d-flex flex-column">
                            <p class="d-flex justify-content-center mb-0">
                                <span><?php echo $storage_symbolic_link_exists ? $matched : $missed; ?>&nbsp; Storage Folder Symbolic Link Exists</span>
                            </p>
                            <?php if($storage_symbolic_link_exists == false){ ?>
                                <div class="d-flex justify-content-center">
                                    <p class="">Create a symbolic link by running this link: &lt;your-app-link&gt;/execute_create_storage_link</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <?php if($db_connection && $db_table_exists == true){ ?>

                    <div class="d-flex justify-content-center mb-3"><i class="fas fa-arrow-down fa-2x"></i></div>

                    <hr>

                    <div class="d-flex justify-content-center text-title mb-3 text-secondary">Activate Purchase Code</div>
                
                    <?php if($product_activated == false){ ?>
                    <div class="d-flex justify-content-center mb-1">
                        <div class="form-row col-md-4 mb-2">
                        <div class="input-group mb-3">
                                <input type="text" id="pcode" class="form-control form-control-custom" placeholder="Please enter your purchase code" autocomplete="off">
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn btn-primary" id="activate_pcode">Activate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="d-flex justify-content-center mb-3">
                        <span id="activation_error"></span>
                    </p>
                    <p class="d-flex justify-content-center mb-3">
                        <span><?php echo $missed ?>&nbsp; Purchase code is not activated</span>
                    </p>
                    <p class="d-flex justify-content-center mb-3">
                        <span><i class="fas fa-info-circle"></i> &nbsp; Please don't activate Purchase Code from local system</span>
                    </p>
                    <?php }else{ ?>
                        <p class="d-flex justify-content-center mb-3">
                            <span><?php echo $matched ?>&nbsp; Purchase code is activated</span>
                        </p>
                    <?php } ?>
                <?php } ?>

                <div class="d-flex justify-content-center mb-0">
                    <small class="text-muted">Version 4</small>
                </div>
                <div class="d-flex justify-content-center mb-0">
                    <small class="text-muted">Laravel Version <?php echo $laravel_version; ?></small>
                </div>
                <div class="d-flex justify-content-center mb-5 pb-2">
                    Powered by&nbsp;<a href="https://www.appsthing.com">appsthing.com</a>
                </div>

            </div>
        </div>
        <script src="plugins/jquery/jquery-3.5.1.min.js"></script>
        <script>
            var activating_btn_label = "<i class='fa fa-circle-notch fa-spin'></i> Activating..";
            var activate_default_btn_label = "Activate";

            $(document).on('click', '#activate_pcode', function(e){
                e.stopImmediatePropagation();
                $("#activation_error").html('');
                var pcode = $("#pcode").val().trim();
                if(pcode != ''){
                    var confirm = window.confirm("Please make sure you are activating your purchase code from your final production domain.\nAre you sure you want to continue?\nPress OK to Proceed");
                    if(confirm == true){
                        $("#activate_pcode").html(activating_btn_label).attr("disabled", true);
                        $.ajax({
                            type: "POST",
                            url: 'server_checker_packages/activate_purchase_code.php',
                            data: {
                                'purchase_code' : pcode
                            },
                            dataType: 'json',
                            success: function(response) {
                                if(response.status_code == 200){
                                    window.location.reload();
                                }else{
                                    var msg = (response.msg != null)?response.msg:'Error occurred while activating!'
                                    $("#activation_error").html(msg).addClass("text-danger");
                                    $("#activate_pcode").html(activate_default_btn_label).attr("disabled", false);
                                }
                            }
                        });
                    }
                }
            });
        </script>
    </body>
</html>