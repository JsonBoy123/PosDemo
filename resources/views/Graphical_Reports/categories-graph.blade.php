@extends('layouts.dbf')

@section('content')

<div class="container">
  <div id="page_title">Category Graph</div><br>
  <div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
  <div class="row">
    <a href="{{route('category-graph')}}" class="btn btn-primary btn-sm pull-right">Back</a>
    <canvas id="myChart" width="500" height="200"></canvas>
  </div>  {{-- {{dd($cats)}} --}}
</div>
@php 
$total_arr = [];
    // dd($cat_id);
@endphp
@foreach($cat_id as $catID)

<?php $total_arr[] = total($daterange, $location, $catID, 'category'); ?>
@endforeach

<script>

 var cat_val = <?php echo json_encode($total_arr); ?>;
 var cat_arr = <?php echo json_encode($cat_name); ?>;

 
 var ctx = document.getElementById("myChart").getContext('2d');
 var myChart = new Chart(ctx, {
  type: 'doughnut',
  options: {
    enabled: true,
    is3D: true,
  },
  data: {
    labels: cat_arr,
    datasets: [{
      backgroundColor: [
      "#2ecc71",
      "#3498db",
      "#95a5a6",
      "#9b59b6",
      "#f1c40f",
      "#e74c3c",
      "#34495e",
      "Black",
      "Orange",
      'Skyblue'
      ],
      data: cat_val
    }]
  }
});
</script>
@endsection
