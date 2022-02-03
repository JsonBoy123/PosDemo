@extends('layouts.dbf')

@section('content')

<div class="container">
	<div id="page_title">Payments Summery Report</div><br>

	<a href="{{route('payments-report.index')}}" class="btn btn-primary btn-sm pull-right">Back</a>
	<div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
	@if(isset($payments) == true)
		<div class="row" id="catsTable">
	    <div class="col-md-12 col-xl-12">
	      <div class="card shadow-xs">
	        <div class="card-body table-responsive">
	          <table class="table table-striped table-hover" id="PaymentTable">
	            <thead>
	              <tr class="text-center">
	                <th style="text-align:center">Payment Type</th>
	                <th style="text-align:center">Count</th>
	                <th style="text-align:center">Amount Tenderd</th>
	              </tr>
	            </thead>
	            <tbody>
				@php
					$sum_total = $sum_quantity =  0;

		           	foreach($payments as $payment){

	          	@endphp
	            <tr class="text-center">
	                <td>{{$payment->payment_type}}</td>
	                @php

	                @endphp
	                <td> {{$payment->total_row}}</td>
	                <td>₹ {{$payment->total_amount}} </td>
	                @php

	                	$sum_quantity += $payment->total_row;	                	
	                	$sum_total 	  += $payment->total_amount;

	                @endphp

	              </tr>

	            @php } @endphp
	            </tbody>
	          	</table>

	         	<table class="table table-bordered">
  				<tr class="text-center">
	                <th style="text-align:center"></th>
	                <th style="text-align:center">Quantity</th>
	                <th style="text-align:center">Total</th>
	            </tr>
				<tbody>
					<tr>
				    	<th style="text-align:center">TOTAL</th>
		                <th style="text-align:center">{{$sum_quantity}}</th>
		                <th style="text-align:center">₹ {{$sum_total}} </th>
				    </tr>
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
			order: [[1, 'asc']],
			"columnDefs": [
			{'orderable': false, "target": 0}
			]
		});

	});


</script>
@endsection
