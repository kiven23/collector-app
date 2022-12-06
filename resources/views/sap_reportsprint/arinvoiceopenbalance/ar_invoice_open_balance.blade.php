
<button onclick="PrintElem()"style="float: right; color: blue">  <strong>PRINT ME &#x2399;</strong></button>
<div id="printMe">
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8"><link type="text/css" rel="stylesheet" href="resources/sheet.css" >
<style type="text/css">.ritz .waffle a { color: inherit; }.ritz .waffle .s0{background-color:#ffffff;text-align:left;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s1{background-color:#ffffff;text-align:left;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}</style><div   class="ritz grid-container" dir="ltr"><table class="waffle" cellspacing="0" cellpadding="0"><thead><tr><th class="row-header freezebar-origin-ltr"></th><th id="0C0" style="width:159px;" class="column-headers-background"></th><th id="0C1" style="width:100px;" class="column-headers-background"></th><th id="0C2" style="width:184px;" class="column-headers-background"></th><th id="0C3" style="width:125px;" class="column-headers-background"></th><th id="0C4" style="width:100px;" class="column-headers-background"></th><th id="0C5" style="width:130px;" class="column-headers-background"></th><th id="0C6" style="width:104px;" class="column-headers-background"></th><th id="0C7" style="width:135px;" class="column-headers-background"></th><th id="0C8" style="width:116px;" class="column-headers-background"></th><th id="0C9" style="width:120px;" class="column-headers-background"></th><th id="0C10" style="width:100px;" class="column-headers-background"></th></tr></thead><tbody><tr style="height: 20px"><th id="0R0" style="height: 20px;" class="row-headers-background"><div class="row-header-wrapper" style="line-height: 20px"></div>
</th>
<td class="s0" dir="ltr">AR-INVOICE OPEN BALANCE</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td class="s0" dir="ltr">DATE: {{  date("Y/m/d") }}</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr style="height: 20px">
<th id="0R1" style="height: 20px;" class="row-headers-background">
<div class="row-header-wrapper" style="line-height: 20px"></div>
</th>
<td class="s0" dir="ltr"></td>
<td></td><td></td><td></td><td></td><td></td>
<td></td><td></td><td></td><td></td><td></td>
</tr>

<tr style="height: 20px"><th id="0R2" style="height: 20px;" class="row-headers-background">
<div class="row-header-wrapper" style="line-height: 20px">
</div>
</th>
<td class="s0" dir="ltr" style="width: 15px;">BRANCH</td>
<td class="s0" dir="ltr">DOC DATE</td>
<td class="s0" dir="ltr">INVOICE</td>
<td class="s0" dir="ltr">DOCUMENT NO</td>
<td class="s0" dir="ltr">CUSTOMER NO</td>
<td class="s0" dir="ltr">CUSTOMER NAME</td>
<td class="s0" dir="ltr">OPEN BALANCE</td>
</tr><tr style="height: 20px">
<th id="0R3" style="height: 20px;" class="row-headers-background">
<div class="row-header-wrapper" style="line-height: 20px">
</div></th>
@foreach ($q as $data)
<tr>
<td class="s1" dir="ltr"> </td>
<td class="s1" dir="ltr"><strong>{{$data->Branch}}</strong></td>
 
<td class="s1" dir="ltr"><strong>{{$data->DocDate}}</strong></td>
<td class="s1" dir="ltr"><strong>{{$data->Invoice}}</strong></td>
<td class="s1" dir="ltr"><strong>{{$data->DocumentNo}}</strong></td>
<td class="s1" dir="ltr"><strong>{{$data->CustomerName}}</strong></td>
<td class="s1" dir="ltr"><strong>{{$data->OpenBalanceAmt}}</strong></td>
</tr>
@endforeach
</tr><tr style="height: 20px">
<th id="0R4" style="height: 20px;" class="row-headers-background">
<div class="row-header-wrapper" style="line-height: 20px">
</div></th><td></td><td></td><td></td><td class="s0" dir="ltr">
</td><td></td><td></td><td></td><td>

</td><td></td><td></td><td></td></tr></tbody></table></div>
<br>
<br>
<p style="
    background-color: #ffffff;
    text-align: left;
    font-weight: bold;
    color: #000000;
    font-family: 'Arial';
    font-size: 10pt;
    vertical-align: bottom;
    white-space: nowrap;
    direction: ltr;
    padding: 2px 3px 2px 3px;
" >Printed by WEBPORTAL V1 SYSTEM</p>
</div>

 <script>
 function PrintElem()
        {
            window.open('', '', 'left=0,top=0,width=100%,height=1000px,toolbar=0,scrollbars=0,status=0');
            var printContents = document.getElementById('printMe').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            return true;
        }
</script>
 