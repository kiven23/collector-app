<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use GuzzleHttp\Client;
class CollectionQueriesController extends Controller
{
    public function index(request $req){
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
    
            return response()->json([
                'status' => $statusCode,
                'data'   => $data
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
