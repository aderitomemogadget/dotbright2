<?php
$path =  dirname( __FILE__ ).'/../../core/';
include $path.'core_files.php';

global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	

if(isset($_POST['imei'])) {
    $selectedImei = $_POST['imei'];

    $rs = new DataObj();
    $sql = 'SELECT marca, modelo FROM telemoveis_emp WHERE imei='.$selectedImei.' ';
    $rs->db_open($db_link,$DB_name,$sql);

    if(!$rs->EOF()) {
        $marca = $rs->db_data['marca'];
        $modelo = $rs->db_data['modelo'];

        $response = array('marca' => $marca, 'modelo' => $modelo);
        echo json_encode($response);
    }
    else {
        // Handle case where no record is found, if necessary
        echo json_encode(array('error' => 'Record not found'));
    }

    $rs->db_close();
}
?>