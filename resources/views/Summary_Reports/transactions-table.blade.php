@extends('layouts.dbf')

@section('content')

<div class="container">
	<div id="page_title">Transaction Summery Report</div><br>

	<a href="{{route('transactions-report.index')}}" class="btn btn-primary btn-sm pull-right">Back</a>
	<div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
	@if(isset($payments) == true)
		<div class="row" id="catsTable">
	    <div class="col-md-12 col-xl-12">
	      <div class="card shadow-xs">
	        <div class="card-body table-responsive">
	          <table class="table table-striped table-hover" id="PaymentTable">
	            <thead>
	              <tr class="text-center">
	                <th style="text-align:center">Date</th>
	                <th style="text-align:center">Quantity </th>
	                <th style="text-align:center">Subtotal </th>
	                <th style="text-align:center">Tax </th>
	                <th style="text-align:center">Total </th>
	                <th style="text-align:center">Wholesale </th>
	                <th style="text-align:center">Profit</th>
	              </tr>
	            </thead>
	            <tbody>
				@php
					$sum_subtotal = $sum_quantity =  $sum_tax = $sum_total = 0;

		           	foreach($payments as $payment){
		           		// dd($payment);
		           		$tax = tax($daterange, $location, $payment->created_at ,'transactions');
                        $total = total($daterange, $location, $payment->created_at ,'transactions');

	          	@endphp
	            <tr class="text-center">
	                <td>{{date('Y-m-d',strtotime($payment->created_at))}}</td>	                
	                <td>{{$payment->count}}</td>
	                <td>₹ {{number_format($total-$tax,2)}} </td>
	                <td>₹ {{number_format($tax, 2)}}</td>
                    <td>₹ {{number_format($total, 2)}}</td>
                    <td>₹ 0</td>
                    <td>₹ {{number_format($total - $tax, 2)}}</td>
	                @php

	                	$sum_quantity += $payment->count;	                	
	                	$sum_subtotal += $total - $tax ;
	                	$sum_tax      += $tax;
                        $sum_total    += $total;

	                @endphp

	              </tr>

	            @php } @endphp
	            </tbody>
	          	</table>
	        </div>
	      </div>
	    </div>
  	</div>
  	@endif
</div>
<script>
	$(document).ready( function () {
		
		$('#PaymentTable').dataTable({
			order: [[0, 'asc']],
			"columnDefs": [
			{'orderable': false, "target": 0}
			]
		});

	});


</script>
@endsection
