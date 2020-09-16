<?php
require_once 'config.php';
  
try {
    $adapter->authenticate();
    $token = $adapter->getAccessToken();
    $db = new DB();
    if($db->is_table_empty()) {
        $db->update_access_token(json_encode($token));
        echo "Access token inserted successfully.";
    }
}
catch( Exception $e ){
    echo $e->getMessage() ;
}