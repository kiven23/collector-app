<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use DB;
use App\SalesQueries;
use App\SalesEmployee;
use App\ProductMaintenance;
use Carbon\Carbon;
use Excel;

class SalesEmployeeController extends Controller
{


    
   public function sync(){
        
        #DB::table('sales_queries')->truncate();
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
            $new->branch = $data->Branch;
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
    // $client = new Client(['timeout' => 9999999]);
    // $response = $client->get('http://192.168.1.19:7771/api/branches/public/salesemployee', [
      
    //     'headers' => ['Accept' => 'application/json'],
    // ]);
    // $body = $response->getBody();
    // $data = json_decode($body);
    // foreach($data as $data2){
    //     importQuery($data2);
    // }
    // return "d";
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
        return DB::table('sales_employees')->where('employee_id', 'HAIER-CNE-52-01')->paginate(request()->get('per_page', 10));;
   }
   public function employee_get(request $req){
    return DB::table('sales_employees')->where('Salesman', 'HAIER-CNE-52-01')->get();
   }
   public function generator_master(request $req){
    
     

    #PRO CODER  
         #SOLVER QUOTA COLUMN
          function get_quota($master){
            $sum = [];
            foreach($master as $q){
               
         
                  $sum[] = $q->Quota;
       
            }
            return  $sum[0];
          }
          #SOLVER SALES AMNT COLUMN
          function get_sale_quota($master){
            $total = [];
            foreach ($master as $q) {
                    $total[] = floatval($q->Amt);  
            }
            return  array_sum($total);
          }
          #SOLVER SALES PERFOMANCE
          function get_sales_performance($amt,$quota){
           return round($amt / $quota * 100,2) . '%';
          }
          function calculate($c){
            
            if ($c > 100) {
                return 'EXCELLENT';
            } elseif ($c >= 90 && $c <= 100) {
                return 'VERY GOOD';
            } elseif ($c >= 80 && $c < 90) {
                return 'GOOD';
            } elseif ($c >= 70 && $c < 80) {
                return 'FAIR';
            } else {
                return 'POOR';
            }
           }
          #SOLVER PERFORMANCE ASSESSMENT
          function get_sales_performance_assessment($amt,$quota){
               
            $performance = calculate(round($amt / $quota * 100));
            return  $performance;
          }
          #SOLVER RECOMMENDATION
          function get_recommendation($amt, $quota){
            $c = round($amt / $quota * 100);
            if ($c < 40) {
                return 'FOR REPLACEMENT';
            } elseif ($c >= 40 && $c <= 70) {
                return 'WRITTEN MEMO + TRAINING';
            }else{
                return '';
            }
          }
          function searchItemBonus($item){
            $amount = DB::table('product_maintenances')->where('model', $item)->pluck('product_bonus')->first();
            return floatval($amount);
          }
          function get_product_bonus($master){
            
            $sum = [];
            foreach($master as $q){
               
         
                  $sum[] = searchItemBonus($q->ItemName);
       
            }
            return  array_sum($sum);
          }
  
      #OVER KILL DATE
      $dataMaster = SalesEmployee::with('salesQueries')->get();
        $startDate = $req->start_date;
        $endDate =  $req->end_date;
        $months = Carbon::parse($startDate)->diffInMonths(Carbon::parse($endDate)) +1;
        $dataMaster = SalesEmployee::whereHas('salesQueries', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('DocDate', [$startDate, $endDate]);
        })
        ->with(['salesQueries' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('DocDate', [$startDate, $endDate]);
        }])
        //->where('employee_id', 'MIDEA-CZM-69-01')
          //->where('brand', 'ASAHI')
        ->where('branch', $req->branch)
        ->get();
    $reports = [];
    if($req->q == 0){
        foreach($dataMaster as $overkill){
            $salesQueries = $overkill->salesQueries ?? collect(); 
            $reports[] = ['branch'=> $overkill->branch,
                          'employee'=> $overkill->employee, 
                          'datehired'=>  $overkill->datehired,
                          'brand'=>  $overkill->brand,
                          'qouta'=> get_quota($salesQueries)  * $months  ,
                          'sale_qouta'=> get_sale_quota($salesQueries),
                          'sales_performance'=> get_sales_performance(get_sale_quota($salesQueries),get_quota($salesQueries) * $months) ,
                          'peformance_assessment'=> get_sales_performance_assessment( get_sale_quota($salesQueries), get_quota($salesQueries) * $months),
                          'recommendation'=> get_recommendation( get_sale_quota($salesQueries), get_quota($salesQueries)  * $months)
                        ];
            #SOLVER EXECUTION
        }
    }
     
    if($req->q == 1){
        foreach($dataMaster as $overkill){
            
            $salesQueries = $overkill->salesQueries ?? collect(); 
        
            $reports[] = ['branch'=> $overkill->branch,
                        'employee'=> $overkill->employee, 
                        'datehired'=>  $overkill->datehired,
                        'brand'=>  $overkill->brand,
                        'qouta'=> get_quota($salesQueries)  * $months  ,
                        'sale_qouta'=> get_sale_quota($salesQueries),
                        'sales_performance'=> get_sales_performance(get_sale_quota($salesQueries),get_quota($salesQueries) ) ,
                        'product_bonus_total'=> get_product_bonus($salesQueries),
                        ];
            #SOLVER EXECUTION
        }
    }
    usort($reports, function ($a, $b) {
        $perfA = floatval(str_replace('%', '', $a['sales_performance']));
        $perfB = floatval(str_replace('%', '', $b['sales_performance']));
        return $perfB <=> $perfA;
    });
    $qoutaTotal = [];
    $sale_qoutaTotal = [];
    $sales_performanceTotal = [];
    $product_bonus_total = [];
    $finalData = [];
        if($req->q == 1){
            foreach($reports as $index=> $final){
                $finalData [] = [
                    'r' => $index+1,
                    'branch' =>$final['branch'],
                    'employee'=> $final['employee'],
                    'datehired'=> $final['datehired'],
                    'brand'=> $final['brand'],
                    'qouta'=> $final['qouta'],
                    'sale_qouta'=> $final['sale_qouta'],
                    'sales_performance'=> $final['sales_performance'],
                    'product_bonus_total'=> $final['product_bonus_total'],
                    'dateto'=> $startDate . ' - ' . $endDate
                ];
                $qoutaTotal[] = $final['qouta']; 
                $sale_qoutaTotal[] = $final['sale_qouta'];
                $sales_performanceTotal[] = $final['sales_performance'];
                $product_bonus_total[] = $final['product_bonus_total'];
            }
       
            $finalData[] = [
                'r' => '',
                'branch' =>'',
                'employee'=> 'GRAND TOTAL',
                'datehired'=> '',
                'brand'=> '',
                'qouta'=> array_sum($qoutaTotal),
                'sale_qouta'=> array_sum($sale_qoutaTotal) ,
                'sales_performance'=> get_sales_performance( array_sum($sale_qoutaTotal) ,array_sum($qoutaTotal))  ,
                'product_bonus_total'=> array_sum($product_bonus_total),
                'recommendation'=> '',
                'dateto'=> $startDate . ' - ' . $endDate
            ];
        }else{
            foreach($reports as $index=> $final){
                $finalData [] = [
                    'r' => $index+1,
                    'branch' =>$final['branch'],
                    'employee'=> $final['employee'],
                    'datehired'=> $final['datehired'],
                    'brand'=> $final['brand'],
                    'qouta'=> $final['qouta'],
                    'sale_qouta'=> $final['sale_qouta'],
                    'sales_performance'=> $final['sales_performance'],
                    'peformance_assessment'=> $final['peformance_assessment'],
                    'recommendation'=> $final['recommendation'],
                    'dateto'=> $startDate . ' - ' . $endDate
                ];
                $qoutaTotal[] = $final['qouta']; 
                $sale_qoutaTotal[] = $final['sale_qouta'];
                $sales_performanceTotal[] = $final['sales_performance'];
                
            }
       
            $finalData[] = [
                'r' => '',
                'branch' =>'',
                'employee'=> 'GRAND TOTAL',
                'datehired'=> '',
                'brand'=> '',
                'qouta'=> array_sum($qoutaTotal),
                'sale_qouta'=> array_sum($sale_qoutaTotal) ,
                'sales_performance'=> get_sales_performance( array_sum($sale_qoutaTotal) ,array_sum($qoutaTotal) )  ,
                'peformance_assessment'=>  get_sales_performance_assessment( array_sum($sale_qoutaTotal) ,array_sum($qoutaTotal)),
                'recommendation'=> '',
                'dateto'=> $startDate . ' - ' . $endDate
            ];
        }
    
    $data = ['period' => $startDate . ' - ' . $endDate, 'reports' => $finalData];
    return response()->json($data);
    
   }
   public function Branch(request $req){
        return DB::table('sales_queries')
        ->select('Branch')
        ->distinct()
        ->orderBy('Branch')
        ->get();
   }
   public function ItemMaintenance(request $req){
   return ProductMaintenance::all();
   }
   public function upload(request $req){

    $csv_path = $req->file('file')->getRealPath();


     ProductMaintenance::truncate();
      Excel::load($csv_path, function($reader) {
         
         foreach($reader->toArray() as $csv){
             $new = new ProductMaintenance;
             $new->brand = $csv['brand'];
             $new->model =  $csv['model'];
             $new->product_bonus = $csv['product_bonus'];
             $new->save();   
         }
       
      });
      return 'sync';
   
   }
   public function salelist(request $req){
     function getData($data){
        return DB::table('sales_queries')->where('Salesman', $data);
     }
     return getData($req->data)->select('DocDate','ItemCode','ItemName','Brand','Supplier','Amt')->get();
   }
   public function generateReports(request $req){
    return $req;
   }
}
