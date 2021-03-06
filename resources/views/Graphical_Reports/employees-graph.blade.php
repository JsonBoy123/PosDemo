@extends('layouts.dbf')

@section('content')

<div class="container">
	<div class="row">
        <a href="{{route('employee-graph')}}" class="btn btn-primary btn-sm pull-right">Back</a>
		<canvas id="myChart" width="500" height="200"></canvas>
	</div>
</div>
 @php 
    $total_arr = [];
    // dd($cat_id);
  @endphp
  @foreach($emp_id as $empId)
    {{-- {{ dd($empId) }} --}}
      <?php $total_arr[] = total($daterange, $location, $empId,'customer');?>
    }
    }
  @endforeach
  
<script>

   var emp_val = <?php echo json_encode($total_arr); ?>;
   var emp_name = <?php echo json_encode($emp_name); ?>;
	
var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {

    type: 'doughnut',

    data: {
        labels: emp_name,//['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            label: 'My First dataset',
            backgroundColor:["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3cba9f","#3e95cd"],
            borderColor: "rgba(117,61,41,1)",
            data: emp_val//[0.5, 10, 5, 2, 20, 30, 45]
        }]
    },
    
});
</script>
@endsection
