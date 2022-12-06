<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Dompdf\Dompdf;
use Dompdf\Options;
class SapRportsController extends Controller
{
    

    public function mssqlcon(){
        return \Auth::user()->dbselection->connection;
    }
    public function seriesname(request $req){
        if($req){
            $q = DB::connection($this->mssqlcon())->select("SELECT distinct SeriesName from nnm1");
            if($req->q == 'sapprogtbl' ){
            $q = DB::connection($this->mssqlcon())->select("SELECT U_Branch1 FROM DBO.[@PROGTBL]");
            }
        }
        return  response()->json($q);
    }
    public function incoming_crb_generate(request $req){
        function remapBranch($b){
                return  DB::table('branches')->where('name', $b)->pluck('seriesname')->first();
        }
        $getB = remapBranch($req->branch);
        $getD = $req->date;
        $table = \DB::connection($this->mssqlcon())
        ->select(DB::raw("/* SELECT FROM [dbo].[ORCT] z1 */ 
        DECLARE @Det AS VARCHAR(50)  
        SET @Det = '$getD'   
        /* SELECT FROM [dbo].[OUSR] z2 */  
        DECLARE @Branch AS VARCHAR(50)  
        /* WHERE z2.U_ORCT IS NOT NULL */  
        SET @Branch = '$getB'       
        SELECT	a.docnum as [DocNum],    
                a.docdate as [Date],    
                isnull(a.cardname,a.U_recvFrom) as [Name],      
                a.comments as [Particulars],    
                a.counterref as [OR#],    
                case when a.DocType = 'C'   and a.CashSum = 0.00 
                    then a.CheckSum   
                    else CASE WHEN a.CashSum = 0.00 THEN A.NoDocSum ELSE A.CashSum END
                end as [Amount],
                case when a.DocType = 'C' then  (a.CashSum + a.TrsfrSum - a.NoDocSum) else null end as [PaymtAcct],    
                case when (a.DocType = 'C' and  a.NoDocSum <> 0) then a.NoDocSum else null end as [Interest], 
                case when a.DocType = 'A' then a.NoDocSum else null end as [otherBranch_Acct], 
                case when a.DocType = 'V' then a.NoDocSum else null end as [Supplier], 
                case when a.TrsfrSum = 0 then null else a.TrsfrSum end as [Rebate]    
                
                FROM ORCT A   
                INNER JOIN NNM1 B ON A.Series = B.Series   
                INNER JOIN OACT C ON A.CashAcct = C.AcctCode   
                LEFT JOIN OACT D ON A.TrsfrAcct = D.AcctCode      
                
                where a.canceled = 'N' 
                    and a.docdate = @Det 
                    and b.seriesname = @Branch   
                    and a.Comments not in ('CASH SALES')  
                    AND (a.TrsfrSum = 0   AND C.segment_0 IN('11130','11150') OR  a.TrsfrSum <> 0   
                    AND D.segment_0 IN('41310') 
                    AND C.segment_0 IN('11130','11150') )  
                    AND (a.TrsfrSum = 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null   
                    AND W0.U_ORCT = @Branch group by w0.U_ORCT) OR a.TrsfrSum <> 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null 
                    AND W0.U_ORCT = @Branch group by w0.U_ORCT)  AND D.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null AND W0.U_ORCT = @Branch group by w0.U_ORCT) ) 
                    
                ORDER BY A.counterref ASC"));
        $amount = 0;
        $PaymtAcct = 0;
        $Interest = 0;
        $otherBranch_Acct = 0;
        $Rebate = 0;
        foreach($table as $query){
           $amount += $query->Amount;
           $PaymtAcct += $query->PaymtAcct;
           $Interest += $query->Interest;
           $otherBranch_Acct += $query->otherBranch_Acct;
           $Rebate += $query->Rebate;
        }
        $total = ['Particulars'=> 'TOTAL',
                  'Amount'=> $amount,
                  'PaymtAcct'=> $PaymtAcct,
                  'Interest'=> $Interest,
                  'otherBranch_Acct'=> $otherBranch_Acct,
                  'Rebate'=> $Rebate];
        array_push($table ,  $total);
        return $table;
    }
    public function preview(request $req){
    function remapBranch($b){
            return  DB::table('branches')->where('name', $b)->pluck('seriesname')->first();
    }
    $branch = $req->branch;
    $getB = remapBranch($req->branch);
    $getD = $req->date;
    $table = \DB::connection($this->mssqlcon())
    ->select(DB::raw("/* SELECT FROM [dbo].[ORCT] z1 */ 
    DECLARE @Det AS VARCHAR(50)  
    SET @Det = '$getD'   
    /* SELECT FROM [dbo].[OUSR] z2 */  
    DECLARE @Branch AS VARCHAR(50)  
    /* WHERE z2.U_ORCT IS NOT NULL */  
    SET @Branch = '$getB'       
    SELECT	a.docnum as [DocNum],    
            a.docdate as [Date],    
            isnull(a.cardname,a.U_recvFrom) as [Name],      
            a.comments as [Particulars],    
            a.counterref as [OR],    
            case when a.DocType = 'C'   and a.CashSum = 0.00 
                then a.CheckSum   
                else CASE WHEN a.CashSum = 0.00 THEN A.NoDocSum ELSE A.CashSum END
            end as [Amount],
            case when a.DocType = 'C' then  (a.CashSum + a.TrsfrSum - a.NoDocSum) else null end as [PaymtAcct],    
            case when (a.DocType = 'C' and  a.NoDocSum <> 0) then a.NoDocSum else null end as [Interest], 
            case when a.DocType = 'A' then a.NoDocSum else null end as [otherBranch_Acct], 
            case when a.DocType = 'V' then a.NoDocSum else null end as [Supplier], 
            case when a.TrsfrSum = 0 then null else a.TrsfrSum end as [Rebate]    
            
            FROM ORCT A   
            INNER JOIN NNM1 B ON A.Series = B.Series   
            INNER JOIN OACT C ON A.CashAcct = C.AcctCode   
            LEFT JOIN OACT D ON A.TrsfrAcct = D.AcctCode      
            
            where a.canceled = 'N' 
                and a.docdate = @Det 
                and b.seriesname = @Branch   
                and a.Comments not in ('CASH SALES')  
                AND (a.TrsfrSum = 0   AND C.segment_0 IN('11130','11150') OR  a.TrsfrSum <> 0   
                AND D.segment_0 IN('41310') 
                AND C.segment_0 IN('11130','11150') )  
                AND (a.TrsfrSum = 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null   
                AND W0.U_ORCT = @Branch group by w0.U_ORCT) OR a.TrsfrSum <> 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null 
                AND W0.U_ORCT = @Branch group by w0.U_ORCT)  AND D.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null AND W0.U_ORCT = @Branch group by w0.U_ORCT) ) 
                
            ORDER BY A.counterref ASC"));
    $amount = 0;
    $PaymtAcct = 0;
    $Interest = 0;
    $otherBranch_Acct = 0;
    $Rebate = 0;
    foreach($table as $query){
       $amount += $query->Amount;
       $PaymtAcct += $query->PaymtAcct;
       $Interest += $query->Interest;
       $otherBranch_Acct += $query->otherBranch_Acct;
       $Rebate += $query->Rebate;
    }
    $total = ['Particulars'=> 'TOTAL',
              'Amount'=> $amount,
              'PaymtAcct'=> $PaymtAcct,
              'Interest'=> $Interest,
              'otherBranch_Acct'=> $otherBranch_Acct,
              'Rebate'=> $Rebate];
        array_push($table ,  $total);
        return view('sap_reportsprint.incoming.incoming_qpld', compact('table','getD', 'branch' ));
    }

    //Query Searching of Vehicles parts
    public function searchofvehicleparts(request $req){
        try {
            ## QUERIES FOR ITEMS
            function items($items, $server){
                if($items == 'type'){
                       $q = \DB::connection($server)
                             ->select(DB::raw("select DISTINCT U_vRMExpTyp from [@VRMPARTROWS]"));
                             return response()->json($q);
                }
                if($items == 'parts'){
                       $q = \DB::connection($server)
                             ->select(DB::raw("select name from [@VRMPARTS]"));
                             return response()->json($q);
                }
            }
            ## QUERIES GENERATIONS
            function queries($part, $type, $server){
                $q = \DB::connection($server)
                                   ->select(DB::raw(" DECLARE @Name AS VARCHAR(50)
                                    SET @Name = '$part'
                                    DECLARE @ExpTyp AS VARCHAR(50)
                                    SET @ExpTyp = '$type'
                                    SELECT 
                                    A.Name AS [CATEGORY]
                                    ,B.U_vRMName AS [VEHICLE PART]
                                    ,B.U_vRMBdyTyp AS [BODY TYPE]
                                    ,B.U_vRMExpTyp AS [EXPENSE TYPE]  
                                    ,B.U_vLSpan AS [LIFE SPAN]
                                    FROM [@VRMPARTS] A INNER JOIN [@VRMPARTROWS] B ON A.Code = B.Code
                                    WHERE B.U_vRMExpTyp = @ExpTyp AND A.Name = @Name
                                    ORDER BY 2,3 ASC"));
                return response()->json($q);
            }
            ## PRINTING PRINT PREVIEW 
            function printpreview($part,$type, $server){
                $q = \DB::connection($server)
                                   ->select(DB::raw(" DECLARE @Name AS VARCHAR(50)
                                    SET @Name = '$part'
                                    DECLARE @ExpTyp AS VARCHAR(50)
                                    SET @ExpTyp = '$type'
                                    SELECT 
                                    A.Name AS [CATEGORY]
                                    ,B.U_vRMName AS [VEHICLEPART]
                                    ,B.U_vRMBdyTyp AS [BODYTYPE]
                                    ,B.U_vRMExpTyp AS [EXPENSETYPE]  
                                    ,B.U_vLSpan AS [LIFESPAN]
                                    FROM [@VRMPARTS] A INNER JOIN [@VRMPARTROWS] B ON A.Code = B.Code
                                    WHERE B.U_vRMExpTyp = @ExpTyp AND A.Name = @Name
                                    ORDER BY 2,3 ASC"));
 
                
                return view('sap_reportsprint.vehicleparts.search_for_vehicleparts', compact('q'));
            }
            $server = $this->mssqlcon();
            if($req->q == 'items'){
                
                return items($req->req, $server);
            }
            if($req->q == 'queries'){
                return  queries($req->part, $req->type, $server);
            }
            if($req->q == 'printing'){
                return printpreview($req->part, $req->type, $server);
            }
           return 'Stevefox Linux';
        }catch(Exception $e){
            return "something wrong";
        }
    }
 

    //Invoice Query Series Revised
    public function invoicequeryseriesrevised(request $req){
          
       try{
 
            function concept($branch, $dateFrom, $dateTo, $params, $server){
                $get = \DB::connection($server)->unprepared("
                DECLARE @DateFrom AS smalldatetime
                DECLARE @DateTo AS smalldatetime
                DECLARE @Series AS VARCHAR(50)
                
                SET @DateFrom='$dateFrom'
                SET @DateTo='$dateTo'
                SET @Series = '$branch'
                
                SELECT	T0.NumAtCard AS [INVOICE],
                        max(T0.DocDate) AS [DOCUMENTDATE], 
                        max(T0.DocNum) AS [DOCUMENTNUMBER], 
                        max(T0.CardName) AS [NAMEOFCUSTOMER], 
                        sum(T1.LineTotal) AS [INVOICEAMOUNT],
                        CASE	WHEN max(T0.docstatus) = 'O' and MAX(T0.Printed) = 'N' THEN 'OPEN'
                                WHEN MAX(T0.DocStatus) = 'O' and MAX(T0.Printed) = 'Y' THEN 'OPEN - PRINTED'
                                WHEN MAX(T0.DocStatus) = 'C' THEN 'CLOSED'
                        End AS STATUS,
                        max(t6.PymntGroup) as [PaymentTerms], 
                        --MAX(isnull(T3.DocNum,'')) AS  ARCM,
                        MAX(T0.U_ClosedType) AS CLOSEDTYPE
                        INTO #TEMP 
                        FROM OINV T0 
                        INNER JOIN INV1 T1 ON T0.[DocEntry] = T1.[DocEntry] 
                        left join RIN1 T2 on T2.[BaseEntry] = T1.Docentry and T2.[BaseLine] = T1.[LineNum] and T2.[BaseType] =13 
                        left JOIN ORIN T3 ON T2.[DocEntry] = T3.[DocEntry]
                        inner join NNM1 t4 on t0.Series = t4.Series
                        LEFT JOIN [@PROGTBL] t5 ON LEFT(t4.SeriesName,4) = t5.Name
                        INNER JOIN OCTG T6 ON T0.GroupNum = t6.GroupNum
                        WHERE t4.SeriesName = @Series and t0.DocDate between @DateFrom and @DateTo
                        and t1.ItemCode <> 'DIFC2' and t1.LineTotal <> 0 AND T0.U_ClosedType  in ('WRNG','CANC','-')
                        --AND (isnull(T3.DocNum,'')) = 0
                        GROUP BY t0.NumAtCard, t0.DocDate,t0.DocNum, t0.CardName
                ORDER BY T0.NumAtCard");
                
                $q = \DB::connection($server)
                ->select("SELECT * FROM #TEMP A
                        where (isnull(a.[DOCUMENTNUMBER],'')) = 0 and  A.CLOSEDTYPE = '-'
                        UNION ALL
                        SELECT * FROM #TEMP A
                        where (isnull(a.[DOCUMENTNUMBER],'')) <> 0 and  A.CLOSEDTYPE = '-'
                        UNION ALL
                        SELECT * FROM #TEMP A
                        where (isnull(a.[DOCUMENTNUMBER],'')) = 0 and  A.CLOSEDTYPE <> '-'
                        DROP TABLE #TEMP");
                    if($params == 'queries'){
                            return $q;
                    }
                    if($params == 'printing'){
                        return view('sap_reportsprint.invoicequeryseriesrevised.invoice_query_series_revised', compact('q','dateFrom','dateTo'));
                    }
  
            }

                if($req->q){
                        $dateFrom = $req->datefrom;
                        $dateTo = $req->dateto;
                        $seriesName = $req->series;
                        $params = $req->q;
                        $server = $this->mssqlcon();
                        return concept($seriesName,$dateFrom,$dateTo, $params, $server);
                }
                }catch(Exception $e){
                        return response()->json('something wrong');
                }
            
                    return "Stevefox Linux Pogi";
                
                }
 
    //Martketing AR Invoice Query
    public function marketingarinvoicequery(request $req){
        try{
               function concept($branch, $dateFrom, $dateTo, $params, $server){
                $q = \DB::connection($server)
                ->select("
                DECLARE @DateFrom AS smalldatetime
                DECLARE @DateTo AS smalldatetime
                DECLARE @Seriesname AS VARCHAR(50)
                    SET @DateFrom='$dateFrom'
                    SET @DateTo='$dateTo'
                    SET @Seriesname ='$branch'
                    SELECT	
                            a.CardName AS NAME,
                            f.Block + ' ' + isnull(f.City, 'No City') + ' ' + province.Name AS [ADDRESS],
                            h.FirmName as BRAND, G.frgnName as [PRODUCTCATEGORY],
                            G.ItemName AS [MODEL],
                            E.Cellular AS [CONTACTNO]
                    from OINV A 
                    INNER JOIN INV1 B ON A.DocEntry = B.DocEntry
                    inner join nnm1 c on c.series = a.series
                    LEFT JOIN [@PROGTBL] d ON LEFT(c.SeriesName,4) = d.Name
                    inner join OCRD e on a.CardCode = e.CardCode
                    inner join CRD1 f on a.CardCode = f.CardCode
                    INNER JOIN OITM G ON B.ItemCode = G.ItemCode
                    INNER JOIN OMRC H ON G.FirmCode = h.FirmCode
                    INNER JOIN OCST province ON province.Code = f.State
                    WHERE @Seriesname = C.SeriesName AND a.DocDate between @DateFrom AND @DateTo and b.ItemCode <> 'DIFC2'
                    and a.ObjType = '13' and a.U_ClosedType not in ('CANC', 'WRNG')
                    FOR BROWSE");
 
                    if($params == 'queries'){
                            return $q;
                    }
                    if($params == 'printing'){
                        return view('sap_reportsprint.marketingarinvoicequery.marketing_ar_invoice_query', compact('q','dateFrom','dateTo'));
                    }
            }
                if($req->q){
                        $dateFrom = $req->datefrom;
                        $dateTo = $req->dateto;
                        $seriesName = $req->series;
                        $params = $req->q;
                        $server = $this->mssqlcon();
                        return concept($seriesName,$dateFrom,$dateTo, $params, $server);
                }
                }catch(Exception $e){
                        return response()->json('something wrong');
                }
                    return "Stevefox Linux Pogi";
    }
 
    //Summary of Customer DepositApplied
    public function summaryofcustomerdepositapplied(request $req){   
        try{
            function concept($branch, $dateFrom, $dateTo, $params, $server){
                $q = \DB::connection($server)
                ->select(" 
                        DECLARE @DateFrom AS smalldatetime
                        DECLARE @DateTo AS smalldatetime
                        DECLARE @Branch AS VARCHAR(50)
                        SET @DateFrom='$dateFrom'
                        SET @DateTo='$dateTo'
                        SET @Branch ='$branch'
                        SELECT	t4.CardName AS [CUSTOMERNAME],
                        T0.RefDate as [DATE],
                        T0.Number AS [JENUMBER],
                        T0.TransCode AS [TRANSCODE],
                        T0.Ref2 AS [REF2],
                        T0.SysTotal AS [AMOUNT]
                        FROM OJDT T0 
                        inner join JDT1 T1 on T0.TransID = T1.TransID
                        inner join OACT T2 on T2.AcctCode = T1.contraact
                        left join [@QUOTA] T3 on T2.Segment_2 = T3.U_Segment
                        inner join OCRD T4 on T1.ShortName = T4.CardCode
                        WHERE T2.Segment_0 in ('21510','11210') and t0.RefDate between @DateFrom and @DateTo
                        and @Branch = t3.U_Branch and T0.TransType = '30'
                        ORDER BY T0.RefDate");
 
                 if($params == 'queries'){
                         return $q;
                 }
                 if($params == 'printing'){
                     return view('sap_reportsprint.summaryofcustomerdepositapplied.summary_of_customerdeposit', compact('q','dateFrom','dateTo'));
                 }
         }
             if($req->q){
                     $dateFrom = $req->datefrom;
                     $dateTo = $req->dateto;
                     $seriesName = $req->series;
                     $params = $req->q;
                     $server = $this->mssqlcon();
                     return concept($seriesName,$dateFrom,$dateTo, $params, $server);
             }
             }catch(Exception $e){
                     return response()->json('something wrong');
             }
                 return "Stevefox Linux Pogi";
        
    }

    //Adjustments Sales Discount
    public function adjustmentsalesdiscount(request $req){
        try{
            function concept($branch, $dateFrom, $dateTo, $params, $server){
            ##SELECT U_Branch1 FROM DBO.[@PROGTBL];
            $q = \DB::connection($server)
            ->select(" 
                    DECLARE @DateFrom AS smalldatetime
                    DECLARE @DateTo AS smalldatetime
                    DECLARE @Branch AS VARCHAR(50)
                    SET @DateFrom='$dateFrom'
                    SET @DateTo='$dateTo'
                    SET @Branch ='$branch'
                    SELECT  c.CardName AS [CUSTOMERNAME],
                    a.RefDate AS [DATE],
                    a.Number AS [JENUMBER],
                    a.TransCode AS [TRANSCODE],
                    a.Ref2,
                    b.credit - b.Debit AS AMOUNT
                    from dbo.ojdt a
                        inner join dbo.nnm1 a1 on a1.series = a.series
                        inner join dbo.jdt1 b on b.transid = a.transid
                        inner join dbo.ocrd c on c.cardcode = b.shortname 
                        --inner join dbo.oact c1 on c1.acctcode = b.Account
                        left join OACT d1 on d1.AcctCode = b.ContraAct --f1
                        left join [@PROGTBL] E ON A1.SeriesName = E.U_Series1
                            
                        where a.TransCode = 'BEG' and d1.AcctCode is null
                        and a.RefDate between @DateFrom and @DateTo
                        and e.U_Branch1 = @Branch
                    union all
                    SELECT c.CardName AS [CUSTOMERNAME],
                    a.RefDate AS [DATE],
                    a.Number AS [JENUMBER],
                    a.TransCode AS [TRANSCODE],
                    a.Ref2,
                    b.credit - b.Debit AS AMOUNT
                    from dbo.ojdt a
                        inner join dbo.nnm1 a1 on a1.series = a.series
                        inner join dbo.jdt1 b on b.transid = a.transid
                        inner join dbo.ocrd c on c.cardcode = b.shortname 
                        --inner join dbo.oact c1 on c1.acctcode = b.Account
                        left join OACT d1 on d1.AcctCode = b.ContraAct --f1
                        left join [@PROGTBL] E ON A1.SeriesName = E.U_Series1
                        where ISNULL(a.TransCode,'') IN('ASD','') and d1.segment_0 = '41310' 
                        and a.TransType = N'30'
                        and a.RefDate between @DateFrom and @DateTo
                        and e.U_Branch1 = @Branch          
                    union all	
                    SELECT  c.CardName AS [CUSTOMERNAME],
                    a.RefDate AS [DATE],
                    a.Number AS [JENUMBER],
                    a.TransCode AS [TRANSCODE],
                    a.Ref2,
                    b.credit - b.Debit AS AMOUNT

                    from dbo.ojdt a
                        inner join dbo.nnm1 a1 on a1.series = a.series
                        inner join dbo.jdt1 b on b.transid = a.transid
                        inner join dbo.ocrd c on c.cardcode = b.shortname 
                        --inner join dbo.oact c1 on c1.acctcode = b.Account
                        left join OACT d1 on d1.AcctCode = b.ContraAct --f1
                        left join [@PROGTBL] E ON A1.SeriesName = E.U_Series1

                        where a.TransType = N'30' AND isnull(a.TransCode,'') IN('OPT','') and d1.segment_0 = '41250'  --and a.TransCode = 'OPT'
                        and a.RefDate between @DateFrom and @DateTo
                        and e.U_Branch1 = @Branch");
                 if($params == 'queries'){
                         return $q;
                 }
                 if($params == 'printing'){
                     return view('sap_reportsprint.adjustmentsalesdiscounts.adjustment_sales_discounts', compact('q','dateFrom','dateTo'));
                 }
         }
             if($req->q){
                     $dateFrom = $req->datefrom;
                     $dateTo = $req->dateto;
                     $seriesName = $req->series;
                     $params = $req->q;
                     $server = $this->mssqlcon();
                     return concept($seriesName,$dateFrom,$dateTo, $params, $server);
             }
             }catch(Exception $e){
                     return response()->json('something wrong');
             }
                 return "Stevefox Linux Pogi";

    }
     //Recomputed Account
     public function recomputedaccount(request $req){
        try{
            function concept($branch, $dateFrom, $dateTo, $params, $server){
                    ##SELECT U_Branch1 FROM DBO.[@PROGTBL];
                    $q = \DB::connection($server)
                    ->select("  DECLARE @From AS smalldatetime
                                DECLARE @To AS smalldatetime
                                DECLARE @branch AS VARCHAR(50)
                                SET @From='$dateFrom'
                                SET @To='$dateTo'
                                SET @branch ='$branch'

                                select distinct a.DocNum,a.CardName,c.NumAtCard as [Invoice], 
                                a.CounterRef as [OR], c.TaxDate as [DateofOR], c.U_Coll as [CollectorCode], e.U_Branch1 as [Branch]
                                from ORCT a
                                inner join RCT2 b on b.DocNum = a.DocNum
                                inner join OINV c on c.DocEntry = b.DocEntry and c.ObjType = N'13'
                                left join NNM1 d on d.Series = a.Series
                                left join [@PROGTBL] e on e.U_Series1 = d.SeriesName
                                where a.DocDate between @From and @To 
                                and a.U_Status = '9' and e.U_Branch1 = @branch for browse");
 
                 if($params == 'queries'){
                         return $q;
                 }
                 if($params == 'printing'){
                     return view('sap_reportsprint.recomputedaccount.recomputed_account', compact('q','dateFrom','dateTo'));
                 }
         }
             if($req->q){
                     $dateFrom = $req->datefrom;
                     $dateTo = $req->dateto;
                     $seriesName = $req->series;
                     $params = $req->q;
                     $server = $this->mssqlcon();
                     return concept($seriesName,$dateFrom,$dateTo, $params,$server);
             }
             }catch(Exception $e){
                     return response()->json('something wrong');
             }
                 return "Stevefox Linux Pogi";

        
    }
 
    //Ar Invoice Open Balance 
    public function arinvoiceopenbalance(request $req){
                try{
                    function concept($branch, $dateFrom, $dateTo, $params, $server){
                            ##SELECT U_Branch1 FROM DBO.[@PROGTBL];
                            $q = \DB::connection($server)
                            ->select("SELECT	
                            DISTINCT
                            T3.[U_Branch1] AS Branch,
                            T0.[DocDate],
                            T0.NumAtCard AS [Invoice], 
                            T0.DocNum AS [DocumentNo], 
                            T0.CardName as [CustomerName],
                            (t0.DocTotal - T0.PaidToDate) AS [OpenBalanceAmt]
                            FROM OINV t0
                            INNER JOIN  INV1 T1 on T1.docentry = T0.docentry
                            INNER JOIN  NNM1 T2 on T2.series = T0.series
                            LEFT JOIN [@PROGTBL] T3 ON LEFT(T2.SeriesName,4) = T3.Name
                            WHERE T0.[DocStatus] = 'O' and T3.U_Branch1 = '$branch' 
                            AND T0.GroupNum IN ('-1', '30', '57')
                            ORDER BY T0.[DocDate]
                            FOR BROWSE");
                         if($params == 'queries'){
                                 return $q;
                         }
                         if($params == 'printing'){
                             return view('sap_reportsprint.arinvoiceopenbalance.ar_invoice_open_balance', compact('q','dateFrom','dateTo'));
                         }
                 }
                     if($req->q){
                             $dateFrom = $req->datefrom;
                             $dateTo = $req->dateto;
                             $seriesName = $req->series;
                             $params = $req->q;
                             $server = $this->mssqlcon();
                             return concept($seriesName,$dateFrom,$dateTo, $params, $server);
                     }
                     }catch(Exception $e){
                             return response()->json('something wrong');
                     }
                         return "Stevefox Linux Pogi";
    }
    //Incoming Payment Customer Deposit
    public function incomingpaymentcustomerdeposit(request $req){
                ##SELECT from nnm1 for branch
                try{
                    function concept($branch, $params, $server){
                        $q = \DB::connection($server)
                        ->select("SELECT	
                        DISTINCT
                        T3.[U_Branch1] AS Branch,
                        T0.[DocDate],
                        T0.NumAtCard AS [Invoice], 
                        T0.DocNum AS [DocumentNo], 
                        T0.CardName as [CustomerName],
                        (t0.DocTotal - T0.PaidToDate) AS [OpenBalanceAmt]
                        FROM OINV t0
                        INNER JOIN  INV1 T1 on T1.docentry = T0.docentry
                        INNER JOIN  NNM1 T2 on T2.series = T0.series
                        LEFT JOIN [@PROGTBL] T3 ON LEFT(T2.SeriesName,4) = T3.Name
                        WHERE T0.[DocStatus] = 'O' and T3.U_Branch1 = '$branch' 
                        AND T0.GroupNum IN ('-1', '30', '57')
                        ORDER BY T0.[DocDate]
                        FOR BROWSE");
                         if($params == 'queries'){
                                 return $q;
                         }
                         if($params == 'printing'){
                             return view('sap_reportsprint.inncomingpaymentdeposit.incoming_payment_deposit', compact('q'));
                         }
                 }
                     if($req->q){
                             $seriesName = $req->series;
                             $params = $req->q;
                             $server = $this->mssqlcon();
                             return concept($seriesName, $params, $server);
                     }
                     }catch(Exception $e){
                             return response()->json('something wrong');
                     }
                         return "Stevefox Linux Pogi";
    }
 
    //Incoming Payment open Balamce
    public function incomingpaymentopenbalance(request $req){
             
                ##SELECT from nnm1 for branch
                try{
                    function concept($branch, $params, $server){
                        $q = \DB::connection($server)
                        ->select("SELECT 
                         T1.[SeriesName],
                         T0.[Series],
                         T0.[DocDate], 
                         T0.[DocNum],
                         T0.[CardName],
                         T0.[CounterRef],
                           T0.[OpenBal] FROM ORCT T0 INNER JOIN NNM1 T1 ON  T0.Series =  T1.Series WHERE T0.[OpenBal] <> 0.00 AND T1.SeriesName = '$branch' ORDER BY T0.[DocNum]");
            
                         if($params == 'queries'){
                                 return $q;
                         }
                         if($params == 'printing'){
                             return view('sap_reportsprint.incomingpaymentopenbalance.incoming_payment_openbalance', compact('q'));
                         }
                      }
                     if($req->q){
                             $seriesName = $req->series;
                             $params = $req->q;
                             $server = $this->mssqlcon();
                             return concept($seriesName, $params, $server);
                     }
                     }catch(Exception $e){
                             return response()->json('something wrong');
                     }
                         return "Stevefox Linux Pogi";
    }
  

}
