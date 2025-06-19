<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use DB;
use App\SalesQueries;
use App\SalesEmployee;
use App\ProductMaintenance;
class SalesEmployeeController extends Controller
{


    
   public function sync(){
        
        DB::table('sales_queries')->truncate();
        DB::table('product_maintenances')->truncate();
        DB::table('sales_employees')->truncate();
        function importQuery($data){
                $new = new SalesQueries();
                $new->DocNum = $data->DocNum;
                $new->NumAtCard = $data->NumAtCard;
                $new->DocDate = $data->DocDate;
                $new->Day = $data->Day;
                $new->Year = $data->Year;
                $new->Month = $data->Month;
                $new->Branch = $data->Branch;
                $new->Brand = $data->Brand;
                $new->Supplier = $data->Supplier;
                $new->Amt = $data->Amt;
                $new->Salesman = $data->Salesman;
                $new->PromoName = $data->PromoName;
                $new->DateHired = $data->DateHired;
                $new->SlpName = $data->SlpName;
                $new->ItemCode = $data->ItemCode;
                $new->ItemName = $data->ItemName;
                $new->Quota = $data->Quota;
                $new->BQuota = $data->BQuota;
                $new->Position = $data->Position;
                $new->save();
        }
        function importEmployee($data){
            $new = new SalesEmployee();
            $new->employee_id = $data->Salesman;
            $new->employee = $data->PromoName;
            $new->datehired = $data->DateHired;
            $new->position = $data->Position;
            $new->brand = $data->Brand;
            $new->save();
        }
        function importSmi($data){
            $product = ProductMaintenance::updateOrCreate([
                'brand' => $data->Brand,
                'model' => $data->ItemName,
            ]);
            $product->product_bonus = 100;
            $product->save();
            
        }
    $client = new Client(['timeout' => 300]);
    $response = $client->get('http://192.168.1.19:7771/api/branches/public/salesemployee', [
        'headers' => ['Accept' => 'application/json'],
    ]);
    $body = $response->getBody();
    $data = json_decode($body);
    foreach($data as $data2){
        importQuery($data2);
    }
    $getData = DB::table('sales_queries')
            ->select('PromoName', DB::raw('MIN(Branch) as Branch'), DB::raw('MIN(Position) as Position'), DB::raw('MIN(DateHired) as DateHired'), DB::raw('MIN(Brand) as Brand'), DB::raw('MIN(Salesman) as Salesman'))
            ->groupBy('PromoName')
            ->get();
    $getItemName = DB::table('sales_queries')
            ->select('ItemName', DB::raw('MIN(Brand) as Brand') )
            ->groupBy('ItemName')
            ->get();
    
    foreach($getData as $insert){
        importEmployee($insert);
    }
    foreach($getItemName as $insert){
        importSmi($insert);
    }

    return 'sync';


    return $data;
   }
   public function index(request $req){
        return DB::table('sales_employees')->where('employee_id', 'DEVAN-CNE-52-01')->get();
   }
   public function employee_get(request $req){
    return DB::table('sales_employees')->where('Salesman', 'DEVAN-CNE-52-01')->get();
}
   
}
