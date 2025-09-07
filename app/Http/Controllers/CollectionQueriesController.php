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
                'CardCode'=> $item['CardCode'],
                'RefDate'=> $item['RefDate'],
                'DocNum'=> $item['DocNum']
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
        $collection = DB::table('collection_schedules')->where('Branch', 'like', '%'.$user.'%')->get();
        return response()->json([
            'data' => $collection
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
    public function payment_store(request $req){
        DB::table('collection_payments')->insert([
            'MapID' => $req->CardCode,
            'CardName' => $req->CardName,
            'Amount'   => $req->Amount,
            'Remarks'  => $req->Remarks,
            'user_id'  => \Auth::user()->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('collection_schedules')->where('CardCode', $req->CardCode)->update([
            'status' => 'Paid',
            'updated_at' => now()
        ]);
        return response()->json([
            'data' => 'done'
        ]);
    }
}
