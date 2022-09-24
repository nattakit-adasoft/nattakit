<?php
$serverName = "147.50.143.126,33433"; //serverName\instanceName, portNumber (default is 1433)
$connectionInfo = array( "Database"=>"AdaAccPos5.0BigCProd", "UID"=>"sa", "PWD"=>"GvFhk@61");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}
?>