@extends('layouts.dbf')

@section('content')

<div class="container">
	<div id="page_title">Transations graph report </div><br>
  	<div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
	<div class="row">
        <a href="{{route('transGraphReport')}}" class="btn btn-primary btn-sm pull-right">Back</a>
		<canvas id="myChart" width="500" height="200"></canvas>
	</div>
</div>
@php 
    $total_amt = [];
  @endphp
  @foreach($date_label as $Payt)
      <?php $total_amt[] = floatval(total($daterange, $location, $Payt, 'transactions')); ?>
  @endforeach 
<script>
	
	var date_lable = <?php echo json_encode($date_label); ?>;
    var date_val = <?php echo json_encode($total_amt); ?>;

	var ctx = document.getElementById('myChart').getContext('2d');
	var chart = new Chart(ctx, {

	//type: 'line',
    //type: 'horizontalBar',
    type: 'doughnut',

    data: {
        labels:date_lable, //['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            //label: 'My First dataset',
            backgroundColor:["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd"],
            borderColor: "rgba(117,61,41,1)",
            data: date_val//[0.5, 10, 5, 2, 20, 30, 45]
        }]
    },
    
});
</script>
@endsection
