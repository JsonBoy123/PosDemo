@extends('layouts.dbf')

@section('content')

<div class="container">
    <div id="page_title">Customers Graph Report</div><br>
    <div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
	<div class="row">
        <a href="{{route('customers-graph')}}" class="btn btn-primary btn-sm pull-right">Back</a>
		<canvas id="myChart" width="500" height="200"></canvas>
	</div>
</div>
@php 
    $total_arr = [];
  @endphp
  @foreach($customers_id as $custId)
    {{-- {{ dd($customers_id) }} --}}
      <?php $total_arr[] = total($daterange, $location, $custId,'customer'); ?>
  @endforeach
    
<script>

   var emp_val = <?php echo json_encode($total_arr); ?>;
   var customers_name = <?php echo json_encode($customers_name); ?>;


var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {

    // type: 'line',
    type: 'horizontalBar',
    // type: 'doughnut',

    data: {
        labels:customers_name,
        datasets: [{
           
            label: 'Amount',
            backgroundColor:["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd","#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd","#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f"],
            borderColor: "rgba(117,61,41,1)",
            data: emp_val
        }]
    },
    
});
</script>
@endsection
