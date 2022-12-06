<?php
namespace App\Exec;
use PDO;
class DatabaseTester{
   function testdatabase($req){
                    $server = $req['server'];
                    $dbName = $req['dbname'];
                    $uid = $req['uid'];
                    $pwd = $req['pwd'];
                    try {
                        $conn = new PDO("sqlsrv:server=$server; database = $dbName", $uid, $pwd);
                      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                      return ["success" => true, "message" => "Connected successfully"]; 
                     }catch(\PDOException $e){
                      return ["success" => false, "message" => "Connection failed: " . $e->getMessage()];
                    }
        }
}
 
?>