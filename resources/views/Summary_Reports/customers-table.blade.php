@extends('layouts.dbf')

@section('content')

<div class="container">
	<div id="page_title">Customers Summery Report</div><br>

	<a href="{{route('customers-report.index')}}" class="btn btn-primary btn-sm pull-right">Back</a>
	<div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
	@if(isset($custmores) == true)
	<div class="row" id="catsTable">
		<div class="col-md-12 col-xl-12">
			<div class="card shadow-xs">
				<div class="card-body table-responsive">
					<table class="table table-striped table-hover" id="CategoryTable">
						<thead>
							<tr class="text-center">
								<th style="text-align:center">Customer</th>
								<th style="text-align:center">Quantity</th>
								<th style="text-align:center">Subtotal</th>
								<th style="text-align:center">Tax</th>
								<th style="text-align:center">Total</th>
								<th style="text-align:center">Profit</th>
							</tr>
						</thead>
						<tbody>
							@php $count = 0;
							//dd($sales);
							foreach($custmores as $row)
							{ 
								
								$dis_value = ($row['sale_items']->item_unit_price*$row['sale_items']->discount_percent)/100;
								$tax_amt = ($row['sale_items']->item_unit_price*$row['sale_items']->taxe_rate)/100;
								$total_gross_value = ($row['sale_items']->item_unit_price);

								$Subtotal = $total_gross_value - $tax_amt;


								@endphp
							<tr class="text-center">
								<td>{{$row->customer->first_name}} {{$row->customer->last_name}}</td>
								<td>{{$row['sale_items']->quantity_purchased}}</td>
								<td>₹{{$Subtotal}}</td>
								<td>₹{{$tax_amt}}</td>
								<td>₹{{$total_gross_value}}</td>
								<td>₹{{$Subtotal}}</td>
							</tr>
							@php
							}
							@endphp
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

		$('#CategoryTable').dataTable({
			order: [[1, 'asc']],
			"columnDefs": [
			{'orderable': false, "target": 0}
			]
		});

	});


</script>
@endsection





