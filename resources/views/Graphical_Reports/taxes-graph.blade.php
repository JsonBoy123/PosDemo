@extends('layouts.dbf')

@section('content')

<div class="container">
  <div id="page_title">Taxes Graph Report</div><br>
  <div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
  <div class="row">
    <a href="{{route('taxGraphReport')}}" class="btn btn-primary btn-sm pull-right">Back</a>
    <canvas id="myChart" width="560" height="200"></canvas>
 </div>
</div>
@php 
$total_arr = [];
@endphp
@foreach($tax as $Tax)
<?php $total_arr[] = floatval(total($daterange, $location, $Tax, 'taxe')); ?>
@endforeach 

<script>

 var tax_lable = <?php echo json_encode($tax); ?>;
 var tax_val = <?php echo json_encode($total_arr); ?>;
 console.log(tax_val)
 
 var ctx = document.getElementById("myChart").getContext('2d');
 var myChart = new Chart(ctx, {
    //type: 'line',
    //type: 'horizontalBar',
    //type: 'Bar',
    //type: 'pie',
    type: 'doughnut',

    data: {
      labels: tax_lable,
      
      datasets: [{
        
        backgroundColor: [
        "#2ecc71","#3498db","#95a5a6","#9b59b6","#f1c40f","#e74c3c","#34495e","#2ecc71","#3498db","#95a5a6","#9b59b6","#f1c40f","#e74c3c","#34495e","#2ecc71","#3498db","#95a5a6","#9b59b6","#f1c40f","#e74c3c","#34495e"
        ],

        data: tax_val
     }]
  }
});
</script>
@endsection
