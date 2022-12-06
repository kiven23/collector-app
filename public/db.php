<?php
$server = $_POST['server'];
$dbName = $_POST['dbname'];
$uid = $_POST['username'];
$pwd = $_POST['password'];
try {
  $conn = new PDO("sqlsrv:server=$server; database = $dbName", $uid, $pwd);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $msg = ['msg'=> 'Connected successfully'];
}catch(PDOException $e){
  $msg = ['msg'=> "Connection failed: " . $e->getMessage()];
}
echo json_encode($msg);
 
    // $query = "SELECT * FROM ocrd";
    // $conn->query($query);  
    // var_dump($conn->errorCode());  
    // echo "\n";  
    // var_dump($conn->errorInfo());
  
//   $mysqli = new mysqli("localhost","root","crawling23","webportal_8");
//   if ($mysqli -> connect_errno) {
//     echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
//     exit();
//   }
//   $myArray = [];
//   $sql = "SELECT * FROM custom_db";
//   $result = $mysqli -> query($sql);
//   while($row = $result->fetch_assoc()) {
//     $myArray[] = $row;
//   }
//   $database_list = json_encode($database_list);
//   $mysqli -> close();
  
// $database_list =  [
//     ['dbname'=> 'APPLIANTECH',
//     'server'=> '192.168.1.248',
//     'port'=> '1433',
//     'username'=> 'webportal',
//     'password'=> '124$qweR',
//     'connection'=> 'sqlsrv'], 
//     ['dbname'=> 'ReportsFinance',
//     'server'=> '192.168.1.13',
//     'port'=> '1433',
//     'username'=> 'sapprog105',
//     'password'=> '124$qweR',
//     'connection'=> 'sqlsrv'], 
//     ['dbname'=> 'APPLIANTECH',
//     'server'=> '192.168.1.248',
//     'port'=> '1433',
//     'username'=> 'webportal',
//     'password'=> '124$qweR',
//     'connection'=> 'sqlsrv'], 
//     ];

?>