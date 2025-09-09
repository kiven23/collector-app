<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use GuzzleHttp\Client;
class CollectionQueriesController extends Controller
{
    public function sync(request $req){
        // $collection = DB::connection('sqlsrv2')->table(DB::raw("(
        //     SELECT * FROM CC1_APPT_IRDetailed
        //     UNION ALL
        //     SELECT * FROM CC1_MIA_IRDetailed
        //     UNION ALL
        //     SELECT * FROM CC1_ETO_IRDetailed
        //     UNION ALL
        //     SELECT * FROM CC1_PAN_IRDetailed
        // ) as ir_detailed"))
        // ->where('OverDueAmt', '>', 0)
        // ->get();
        
        // return $collection;
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://192.168.1.19:7771/api/branches/public/irinvoice', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                // kung may authentication, dito mo ilagay e.g. 'Authorization' => 'Bearer token'
            ]);
    
            $statusCode = $response->getStatusCode(); // 200 kung ok
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            foreach($data as $item){
                DB::table('collection_queries')->updateOrInsert(
                 
                    [   'CardCode' => $item['CardCode'],
                        'CardName'      => $item['CardName'],
                        'OverDueAmt'    => $item['OverDueAmt'],
                        'Branch'        => $item['Branch'],
                        'CollectorCode' => $item['CollectorCode'],
                        'CollectorName' => $item['CollectorName'],
                        'RefDate'=> $item['RefDate'],
                        'DocNum'=> $item['DocNum'],
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]
                );
            }
            return response()->json([
                'status' => $statusCode,
                'data'   => 'done'
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function sync_schedule(request $req){
        

       $collection =  DB::table('collection_queries')->get();
         foreach($collection as $item){
            $data = [
                'CardCode'=> $item->CardCode,
                'RefDate'=> $item->RefDate,
                'DocNum'=> $item->DocNum
            ];
            $string = json_encode($data);
            // Step 2: Hash it with SHA256
            $hash = hash('sha256', $string);
            DB::table('collection_schedules')->updateOrInsert(
                    [
                        'CardCode'      => $item->CardCode,
                        'CardName'      => $item->CardName,
                        'OverDueAmt'    => $item->OverDueAmt,
                        'Branch'        => $item->Branch,
                        'CollectorCode' => $item->CollectorCode,
                        'CollectorName' => $item->CollectorName,
                        'MapID'         => $hash,
                        'status'        => 'Pending',
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]
            );
         }
    }

    public function schedule_index(request $req){
        $user = \Auth::user()->branch->name;
        $perPage = $req->get('per_page', 10); // Default 10 items per page
        $page = $req->get('page', 1); // Default page 1
        $search = $req->get('search', ''); // Search parameter

        $query = DB::table('collection_schedules')
                ->where('Branch', 'like', '%'.$user.'%')
                ->where(function ($query) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', \Auth::id());
                }) ->Where('status','Pending');

        // Apply search filter if provided
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('CardName', 'like', '%'.$search.'%')
                  ->orWhere('CardCode', 'like', '%'.$search.'%')
                  ->orWhere('Branch', 'like', '%'.$search.'%');
            });
        }

        $collection = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $collection->items(),
            'current_page' => $collection->currentPage(),
            'last_page' => $collection->lastPage(),
            'per_page' => $collection->perPage(),
            'total' => $collection->total()
        ]);
    }
    public function schedule_set(request $req){
 
        foreach($req->data as $item){
            DB::table('collection_schedules')->where('id', $item)->update([
                'status' => 'Scheduled',
                'user_id' => \Auth::user()->id,
                'updated_at' => now()
            ]);
        }
        return response()->json([
            'data' => 'done'
        ]);
    }
    private function getMapInfo($mapid, $pluck){
        return DB::table('collection_schedules')->where('MapID',$mapid)->pluck($pluck);
    }
    
    public function payment_store(request $req){
         
         
        function checkAmount($mapID,$newAmt){
            // Get the current OverDueAmt for the given MapID
            $checkOldAmount = DB::table('collection_schedules')
                ->where('MapID', $mapID)
                ->value('OverDueAmt'); // mas safe kaysa pluck()->first()
            // Handle case kung walang nahanap
            if ($checkOldAmount === null) {
                throw new \Exception("MapID not found.");
            }
            // Ensure numeric values
            $old = floatval($checkOldAmount);
            $new = floatval($newAmt);
            // Compute remaining balance
            $sum = $old - $new;
            // Prevent negative balance
            if ($sum < 0) {
                throw new \Exception("Payment amount exceeds overdue amount.");
            }
            return $sum;
        }
        DB::table('collection_payments')->insert([
            'MapID' => $req->MapID,
            'CustomerName' => $this->getMapInfo($req->MapID, 'CardName')->first(),
            'CollectedAmount' => $req->CollectedAmount	,
            'Remarks'  => 'COLLECTED BY THE SYSTEM',
            'CollectedBy'  => \Auth::user()->id,
            'signature'  => $req->Signature,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('collection_schedules')
            ->where('MapID', $req->MapID)
            ->update([
            'status' => 'Collected',
            'arrived' => true,
            'OverDueAmt'=> checkAmount($req->MapID, $req->CollectedAmount),
            'updated_at' => now()
        ]);
        $returnData =  $this->getPaymentCollection($req->MapID);
        return response()->json([
            'data' =>  $returnData
        ]);
    }

    public function collected_payments_index(request $req){
        try {
            $user = \Auth::user();
            $branchFilter = '';

            if ($user && $user->branch) {
                $branchName = $user->branch->name;
                $branchFilter = $branchName;
                \Log::info('User branch:', ['user_id' => $user->id, 'branch' => $branchName]);
            } else {
                \Log::info('No authenticated user or no branch, showing all payments');
            }

            $perPage = $req->get('per_page', 10);
            $page = $req->get('page', 1);

            $query = DB::table('collection_payments')
                ->join('collection_schedules', 'collection_payments.MapID', '=', 'collection_schedules.MapID')
                ->whereIn('collection_schedules.status', ['Collected', 'Posted'])
                ->select(
                    'collection_payments.id',
                    'collection_payments.MapID',
                    'collection_payments.CustomerName',
                    'collection_payments.CollectedAmount',
                    'collection_payments.Remarks',
                    'collection_payments.signature',
                    'collection_payments.created_at',
                    'collection_payments.updated_at',
                    'collection_schedules.CardCode',
                    'collection_schedules.CardName',
                    'collection_schedules.Branch',
                    'collection_schedules.status',
                    'collection_schedules.OverDueAmt'
                )
                ->orderBy('collection_payments.created_at', 'desc');

            // Apply branch filter only if user has a branch
            if (!empty($branchFilter)) {
                $query->where('collection_schedules.Branch', 'like', '%'.$branchFilter.'%');
            }

            $payments = $query->paginate($perPage, ['*'], 'page', $page);

            \Log::info('Collected payments query result:', [
                'total' => $payments->total(),
                'count' => $payments->count(),
                'data_count' => count($payments->items())
            ]);

            return response()->json([
                'data' => $payments->items(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in collected_payments_index:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function gpstrack(request $req){
        $data = $req->data;
        DB::table('collection_trackings')->insert([
            'MapID'=>  $data['mapid'],
            'Latitude'=>   $data['Latitude'],
            'Longitude'=>   $data['Longitude'],
        ]);
        DB::table('collection_schedules')->where('MapID',  $data['mapid'])->update([
            'arrived'=>  true,
        ]);
        return response()->json([
            'data' => 'ok'
        ]);
    }
    public function getTrack(request $req){

        $data = DB::table('collection_trackings')->where('mapid', $req->mapid)->get();
        return response()->json($data);
    }

    public function scheduled_today(request $req){
        try {
            $user = \Auth::user();
            $branchFilter = '';

            if ($user && $user->branch) {
                $branchName = $user->branch->name;
                $branchFilter = $branchName;
            }

            $query = DB::table('collection_schedules')
                ->where('status', 'Scheduled')
                ->where(function ($query) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', \Auth::id());
                });

            // Apply branch filter only if user has a branch
            if (!empty($branchFilter)) {
                $query->where('Branch', 'like', '%'.$branchFilter.'%');
            }

            $scheduledCustomers = $query->get();

            return response()->json([
                'data' => $scheduledCustomers
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in scheduled_today:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function kpis(request $req){
            try {
                $user = \Auth::user();
                $branchFilter = '';
    
                if ($user && $user->branch) {
                    $branchName = $user->branch->name;
                    $branchFilter = $branchName;
                }
    
                $query = DB::table('collection_schedules');
    
                // Apply branch filter only if user has a branch
                if (!empty($branchFilter)) {
                    $query->where('Branch', 'like', '%'.$branchFilter.'%');
                }
    
                $totalAccounts = $query->count();
    
                $overdueAccounts = $query->where('OverDueAmt', '>', 0)->count();
    
                $amountToCollect = $query->where('OverDueAmt', '>', 0)->sum('OverDueAmt');
    
                return response()->json([
                    'total_accounts' => $totalAccounts,
                    'overdue_accounts' => $overdueAccounts,
                    'amount_to_collect' => $amountToCollect
                ]);
            } catch (\Exception $e) {
                \Log::error('Error in kpis:', ['error' => $e->getMessage()]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
     
       private function getPaymentCollection($mapid){
            $user = \Auth::user();
            $branchName = $user->branch->name;
            $returnData = DB::table('collection_payments')
                ->where('MapID', $mapid)
                ->first();
            $firstname = DB::table('users')
                    ->where('id', $this->getMapInfo($mapid, 'user_id')->first())
                    ->pluck('first_name')
                    ->first();
            $lastname = DB::table('users')
                    ->where('id', $this->getMapInfo($mapid, 'user_id')->first())
                    ->pluck('last_name')
                    ->first();
            $returnData->collectedby = $lastname.', '.$firstname;
            $formattedId = str_pad($returnData->id, 6, '0', STR_PAD_LEFT);
            $code = $branchName . '-' . $formattedId;
            $returnData->ornumber = $code;
            return $returnData;
       }
       public function collectedPayment(request $req){
        return response()->json($this->getPaymentCollection($req->mapid));
        }
    }

