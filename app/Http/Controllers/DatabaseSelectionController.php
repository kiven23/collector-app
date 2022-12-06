<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exec\DatabaseTester;
use DB;

 
class DatabaseSelectionController extends Controller
{

  private $db = null;

  public function __construct(){
    $this->db = new DatabaseTester();
  }
  public function testDB(Request $req){
      $exec = ['server'=> $req['server'], 
               'dbname'=> $req['dbname'], 
               'uid'=> $req['username'],
               'pwd'=> $req['password']
              ];
      return  $this->db->testdatabase($exec);
  }
  public function connections(){
    $user = DB::table('users')->where('id', \Auth::user()->id)->pluck('sqldb')->first();
    $db = DB::table('database_selections')->get();
      if($user){
        $active = $user;
      }else{
        $active =  DB::table('database_selections')->pluck('id')->first();
      }
     
    
    $dblist = ['databases' => $db, 'connection' => $active];
    return response()->json($dblist);
  }
  public function update(request $req){
    
    try {
      
      DB::table('users')->where('id', \Auth::user()->id)->update([
        'sqldb'=> $req->id
      ]);
      $re = 'ok';
    }catch(Exception $e){
      $re = $e;
    }
    return response()->json($re);
 
    
  }

  //DATABASE CONFIGURATION
  public function createDB(request $req){
   $req->validate([
      'dbname' => ['required'],
      'host' => ['required'],
      'port'=> ['required'],
      'username' => ['required'],
      'password' => ['required'],
      'connection' => ['required'],
    ]);
    DB::beginTransaction();
    try {
      $result = $this->db->testdatabase([
        'server'=> $req->host, 
        'dbname'=> $req->dbname, 
        'uid'=> $req->username,
        'pwd'=> $req->password
      ]);
      if($result["success"]) {
        DB::table('custom_db')->insert([
          'dbname'=> $req->dbname,
          'server'=> $req->host,
          'port'=> $req->port,
          'username'=> $req->username,
          'password'=> $req->password,
          'connection'=> $req->connection,
          'entryname'=> md5($req->host.$req->dbname)
        ]);
        DB::table('database_selections')->insert([
            'dbname'=> $req->dbname .' - '.$req->host,
            'connection'=> md5($req->host.$req->dbname),

        ]);
        $msg = ['msg'=> 'Database successfully created.'];
        DB::commit();
      } else {
        $msg = ['msg'=> $result["message"]];
      }
    }catch(\Exception $e){
      DB::rollback();
      $msg = ['msg'=> $e];
    }
    return response()->json($msg);
  }
  public function updateDB(request $req){
    $validatedData = $req->validate([
      'dbname' => ['required'],
      'host' => ['required'],
      'port'=> ['required'],
      'username' => ['required'],
      'password' => ['required'],
      'connection' => ['required'],
    ]);
    DB::beginTransaction();
    try {
      $result = $this->db->testdatabase([
        'server'=> $req->host, 
        'dbname'=> $req->dbname, 
        'uid'=> $req->username,
        'pwd'=> $req->password
      ]);
      if($result["success"]) {
            DB::table('custom_db')->where('id', $req->id)->update([
              'dbname' => $req->dbname,
              'server' => $req->host,
              'port' => $req->port,
              'username' => $req->username,
              'password' => $req->password,
              'connection' => $req->connection,
            ]);
            DB::table('database_selections')->where('connection', $req->entry)->update([
              'dbname'=> $req->dbname.' - '.$req->host
            ]);
           $msg = ['msg'=> 'Database successfully updated.'];
           DB::commit();
           
      } else {
        $msg = ['msg'=> $result["message"]];
      }
    }catch(\Exception $e){
      DB::rollback();
      $msg = ['msg'=> $e];
    }
    return response()->json($msg);
    
  }
  public function fetchDB(){
    foreach(DB::table('custom_db')->get() as $db){
        $database[] = ['id'=> $db->id,
                       'host'=> $db->server, 
                       'dbname'=> $db->dbname,
                       'username'=> $db->username,
                       'password'=> "****",
                       'connection'=> $db->connection,
                       'port'=> $db->port, 
                       'entry'=> $db->entryname
                       ];
    }
    return response()->json($database);
  }
  public function deleteDB(request $req){
    try{
      DB::table('custom_db')->where('id', $req->id)->delete();
      DB::table('database_selections')->where('connection', $req->entryname)->delete();
      $msg = ['msg'=> 'Database successfully deleted.'];
    }catch(Exception $e){
      $msg = ['msg'=> $e];
    }
    return response()->json($msg);
      
  }

  
}
 