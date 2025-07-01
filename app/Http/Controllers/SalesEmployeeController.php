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
use App\User;
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
       return \Auth::user()->branch_id;
        return DB::table('sales_employees')->where('employee_id', 'HAIER-CNE-52-01')->paginate(request()->get('per_page', 10));;
   }
   public function employee_get(request $req){
    return DB::table('sales_employees')->where('Salesman', 'HAIER-CNE-52-01')->get();
   }
   public function generator_master(request $req){
    
      
     
   
      ## DATE MASTER
      $startDate = $req->start_date;
      $endDate =  $req->end_date;
      ### GRAPH 
      if($req->identify == 'xyz'){
            $months = Carbon::parse($startDate)->diffInMonths(Carbon::parse($endDate)) +1;
            $dataMaster = SalesEmployee::whereHas('salesQueries', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('DocDate', [$startDate, $endDate]);
            })
            ->with(['salesQueries' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('DocDate', [$startDate, $endDate]);
            }])
            ->where('branch', $req->branch)
            ->get();
      
      }else{
            #OVER KILL DATE
            $dataMaster = SalesEmployee::with('salesQueries')->get();
             
            $months = Carbon::parse($startDate)->diffInMonths(Carbon::parse($endDate)) +1;
            $dataMaster = SalesEmployee::whereHas('salesQueries', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('DocDate', [$startDate, $endDate]);
            })
            ->with(['salesQueries' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('DocDate', [$startDate, $endDate]);
            }])
            ->when($req->branch !== 'ALL', function ($query) use ($req) {
                return $query->where('branch', $req->branch);
            }) 
            ->get();
      }
    $reports = [];
    if($req->q == 0){
        foreach($dataMaster as $overkill){
            $salesQueries = $overkill->salesQueries ?? collect(); 
            $reports[] = ['branch'=> $overkill->branch,
                          'employee'=> $overkill->employee, 
                          'datehired'=>  $overkill->datehired,
                          'brand'=>  $overkill->brand,
                          'qouta'=> $this->get_quota($salesQueries)  * $months  ,
                          'sale_qouta'=> $this->get_sale_quota($salesQueries),
                          'sales_performance'=> $this->get_sales_performance($this->get_sale_quota($salesQueries),$this->get_quota($salesQueries) * $months) ,
                          'peformance_assessment'=> $this->get_sales_performance_assessment( $this->get_sale_quota($salesQueries), $this->get_quota($salesQueries) * $months),
                          'recommendation'=> $this->get_recommendation($this->get_sale_quota($salesQueries), $this->get_quota($salesQueries)  * $months)
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
                          'qouta'=> $this->get_quota($salesQueries)  * $months  ,
                          'sale_qouta'=> $this->get_sale_quota($salesQueries),
                          'sales_performance'=> $this->get_sales_performance($this->get_sale_quota($salesQueries),$this->get_quota($salesQueries) ) ,
                          'product_bonus_total'=> $this->get_product_bonus($salesQueries),
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
                'r' => '#',
                'branch' =>'#',
                'employee'=> 'GRAND TOTAL',
                'datehired'=> '#',
                'brand'=> '#',
                'qouta'=> array_sum($qoutaTotal),
                'sale_qouta'=> array_sum($sale_qoutaTotal) ,
                'sales_performance'=> $this->get_sales_performance( array_sum($sale_qoutaTotal) ,array_sum($qoutaTotal))  ,
                'product_bonus_total'=> array_sum($product_bonus_total),
                'recommendation'=> '#',
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
                'r' => '#',
                'branch' =>'#',
                'employee'=> 'GRAND TOTAL',
                'datehired'=> '#',
                'brand'=> '#',
                'qouta'=> array_sum($qoutaTotal),
                'sale_qouta'=> array_sum($sale_qoutaTotal) ,
                'sales_performance'=> $this->get_sales_performance( array_sum($sale_qoutaTotal) ,array_sum($qoutaTotal) )  ,
                'peformance_assessment'=>  $this->get_sales_performance_assessment( array_sum($sale_qoutaTotal) ,array_sum($qoutaTotal)),
                'recommendation'=> '#',
                'dateto'=> $startDate . ' - ' . $endDate
            ];
        }
    
    ######## MATIC SA GRAPH #####
    if($req->identify == 'xyz'){
        return $this->get_sales_performance( array_sum($sale_qoutaTotal) ,array_sum($qoutaTotal) );
    }else{
        $response = $this->generateReports(  $finalData, $req->q);
    
    ######## REPORTS GENERATION HERE #########################
        

            if (isset($response['download_url'])) {
                $downloadUrl = $response['download_url']; 
                $filename = $response['filename'];
            } else {
                Log::error('Download URL not found', ['response' => $response]);
                return response()->json([
                    'status' => 'error',
                    'message' => $response['message'] ?? 'Unexpected error during report generation.'
                ], 500);
            }
        ###->>next data here ->> na steven
    ####### END REPORTS GENERATION ##########################
    $data = ['period' => $startDate . ' - ' . $endDate, 'reports' => $finalData, 'download_link'=> $downloadUrl, 'filename'=> $filename ];
    return response()->json($data);
}
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
   private function generateReports($reports,$type){
    
    $client = new Client(['timeout' => 300000]);
    $response = $client->post('http://192.168.200.11:8004/api/reports/crystal/sales/generator?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiYWRtaW4iLCJleHAiOjIwNTc3MjQ3NDd9.0F5ZFHigMNt732EHIFd7azram_PWHIC5RGkkz8wqEz8&type='.$type, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($reports),
    ]);
    $body = $response->getBody()->getContents();
    return json_decode($body, true);
   }
    #PRO CODER  
         #SOLVER QUOTA COLUMN
       private  function get_quota($master){
            $sum = [];
            foreach($master as $q){
                  $sum[] = $q->Quota;
            }
            return  $sum[0];
          }
          #SOLVER SALES AMNT COLUMN
          private  function get_sale_quota($master){
            $total = [];
            foreach ($master as $q) {
                    $total[] = floatval($q->Amt);  
            }
            return  array_sum($total);
          }
          #SOLVER SALES PERFOMANCE
          private    function get_sales_performance($amt,$quota){
            if ($quota == 0) {
                return '0.00%'; // Or 'N/A' kung gusto mo ipakita na walang quota
            }
            if ($amt == 0) {
                return '0.00%'; // Or 'N/A' kung gusto mo ipakita na walang quota
            }
            return round(($amt / $quota) * 100, 2) . '%';
          }
          private   function calculate($c){
            
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
          private   function get_sales_performance_assessment($amt,$quota){
            if ($quota == 0) {
                return '0.00%'; // Or 'N/A' kung gusto mo ipakita na walang quota
            }
            if ($amt == 0) {
                return '0.00%'; // Or 'N/A' kung gusto mo ipakita na walang quota
            }
            $performance = $this->calculate(round($amt / $quota * 100));
            return  $performance;
          }
          #SOLVER RECOMMENDATION
          private   function get_recommendation($amt, $quota){
            $c = round($amt / $quota * 100);
            if ($c < 40) {
                return 'FOR REPLACEMENT';
            } elseif ($c >= 40 && $c <= 70) {
                return 'WRITTEN MEMO + TRAINING';
            }else{
                return '';
            }
          }
          private   function searchItemBonus($item){
            $amount = DB::table('product_maintenances')->where('model', $item)->pluck('product_bonus')->first();
            return floatval($amount);
          }
          private   function get_product_bonus($master){
            
            $sum = [];
            foreach($master as $q){
                  $sum[] = $this->searchItemBonus($q->ItemName);
            }
            return  array_sum($sum);
          }
   private function graph($branch, $controllerInstance) {
    $startDate = Carbon::parse('2025-01-01');
    $endDate = Carbon::now();
    $quarters = [];
    $current = $startDate->copy();

    while ($current->lte($endDate)) {
        $start = $current->copy();
        $end = $start->copy()->addMonths(3)->subDay();
        if ($end->gt($endDate)) {
            $end = $endDate->copy();
        }

        $dd = new Request([
            'type' => 0,
            'start_date' =>  $start->toDateString(),
            'end_date' =>  $end->toDateString(),
            'branch' => $branch,
            'identify' => 'xyz'
        ]);

        $quarters[] = [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'label' => 'Q' . ceil($start->month / 3) . ' ' . $start->year,
            'branch' => $branch,
            'solver' =>  $controllerInstance->generator_master($dd) // ✅ now this works!
        ];

        $current = $current->copy()->addMonths(3);
    }

    return $quarters;
  }
  private function perbranchgraph($branch){
 
    return [ $this->graph($branch, $this)];
      
  }
  public function dashboardgraph(Request $req) {
    function generateGraph($logs, $branch) {
        $client = new Client(['timeout' => 9999999]);
    
        $response = $client->post('http://192.168.200.11:8004/api/generate/global', [
            'headers' => ['Accept' => 'application/json'],
            'json' => [  // not 'body' or 'form_params'
                'branch' => $branch,
                'data' => $logs  // remove json_encode — Guzzle will do it
            ]
        ]);
    
        $body = $response->getBody()->getContents();
        return json_decode($body, true);
    }
    
     $branch = DB::table('sales_queries')
    ->select('Branch')
    ->distinct()
    ->orderBy('Branch')
    ->take(80)->get();
      
 
    if($req->all == 0){
        foreach ($branch as $b) {
            $d[] = $this->perbranchgraph($b->Branch, $this); // pass $this as controller instance
        }
        foreach($d as $i){
           $info [] = generateGraph($i, $i[0][0]['branch']);
        }
         
         foreach($info as $d){
            $data[] = DB::table('branches')
                     ->where('name', $d['branch'])
                     ->update(["graph"=> $d['download_url'] ]);
             
         }
         return $data;

    }
    
    if($req->all == 1){
        foreach ($branch as $b) {
            $d[] = $this->graph($b->Branch, $this); // pass $this as controller instance
        }
        return generateGraph($d,'ALL');
    }
     
   } 
   public function createuser(request $req){

      $user = DB::table('sales_queries')
    ->select('PromoName', DB::raw('MIN(Branch) as Branch'))
    ->groupBy('PromoName')
    ->orderBy('PromoName')
     ->get();
    function checkbranch($branch){
         return DB::table('branches')->where('name', $branch)->pluck('id')->first();
    }


    foreach($user as $index=> $dd){
        $xxpo = explode(' ',$dd->PromoName);
        $users_pass[] = ['count'=> count($xxpo), 'name'=>$dd->PromoName ]  	;

        if(count($xxpo) == 2){
            $datas [] = ['first_name' => $xxpo[0],
                         'last_name' =>$xxpo[1] ,
                         'fullname'=>$dd->PromoName ,
                         'email'=> $xxpo[1].$index.'_'.$xxpo[0].'@salesedge.addessa.com',
                         'branch_id'=> checkbranch($dd->Branch),
                         'password'=> '123$qweR'];
        }
        if(count($xxpo) == 3){
            $datas [] = ['first_name' => $xxpo[0].' '.$xxpo[1],
                         'last_name' =>$xxpo[2],
                         'fullname'=>$dd->PromoName ,
                         'email'=> $xxpo[0].$index.'_'.$xxpo[2].'@salesedge.addessa.com',
                         'branch_id'=> checkbranch($dd->Branch),
                         'password'=> '123$qweR'];
        }
        if(count($xxpo) == 4){
            $datas [] = ['first_name' => $xxpo[0].' '.$xxpo[1],
                         'last_name' =>$xxpo[3] , 'fullname'=>$dd->PromoName,
                         'fullname'=>$dd->PromoName ,
                         'email'=> $xxpo[0].$index.'_'.$xxpo[3].'@salesedge.addessa.com',
                         'branch_id'=> checkbranch($dd->Branch),
                         'password'=> '123$qweR'];
        }

    }
    
    // if ($req->password) { 
    //     $password = bcrypt($req->password);
    //   } else { $password = bcrypt('123$qweR'); }
     foreach( $datas as $index => $d){
        $ddd[$index] = 'ok';
        $user = new User;
        $user->first_name = $d['first_name'];
        $user->last_name = $d['last_name'];
    	$user->branch_id = $d['branch_id'];
        $user->fullname = $d['fullname'];
    	$user->email = $d['email'];
    	$user->password = bcrypt($d['password']);
    	$user->save();
        $user->roles()->sync([83]);  
     }
    	 return $ddd;
     
        // foreach($req){

        // }
   }

 
}
