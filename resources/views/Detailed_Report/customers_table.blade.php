@extends('layouts.dbf')

@section('content')

<div class="container">
	<div id="page_title">Employees Report</div><br>

	<a href="{{route('detailed-customers-index')}}" class="btn btn-primary btn-sm pull-right">Back</a>

	@if(isset($sales) == true)
	<div class="row" id="catsTable">
		<div class="col-md-12 col-xl-12">
			<div class="card shadow-xs">
				<div class="card-body table-responsive">
					<table class="table table-striped table-hover" id="CategoryTable">
						<thead>
							<tr class="text-center">
								<th style="text-align:center">Trans. Id</th>
								<th style="text-align:center">Type</th>
								<th style="text-align:center">Date</th>
								<th style="text-align:center">Quantity</th>
								<th style="text-align:center">Discount</th>
								<th style="text-align:center">sold By</th>
								<th style="text-align:center">sold To</th>
								<th style="text-align:center">Subtotal</th>
								<th style="text-align:center">Tax</th>
								<th style="text-align:center">Total</th>
								<th style="text-align:center">Wholesale</th>
								<th style="text-align:center">Profit</th>
								<th style="text-align:center">Payment Type</th>
								<th style="text-align:center">Invoice</th>
							</tr>
						</thead>
						<tbody>
							@php $count = 0;
							//dd($sales);
							foreach($sales as $val)
							{ 
								
								$dis_value = ($val['sale_items']->item_unit_price*$val['sale_items']->discount_percent)/100;
								$tax_amt = ($val['sale_items']->item_unit_price*$val['sale_items']->taxe_rate)/100;
								$total_gross_value = ($val['sale_items']->item_unit_price);

								$Subtotal = $total_gross_value - $tax_amt;


								@endphp
								<tr class="text-center">
									<td>{{$val->id}}</td>
									<td>{{$val->sale_type}}</td>
									<td>{{$val->sale_time}}</td>
									<td>{{$val['sale_items']->quantity_purchased}}</td>
									<td>{{$dis_value}}</td>
									<td>{{$val['shop']->name}}</td>
									<td>{{$val['customer']->first_name.' '.$val['customer']->last_name}}</td>
									<td>₹{{$Subtotal}}</td>
									<td>₹{{$tax_amt}}</td>
									<td>₹{{$total_gross_value}}</td>
									<td>₹0</td>
									<td>₹{{$Subtotal}}</td>
									<td>{{$val['sale_payment']->payment_type}}</td>
									<td><a href="{{route('sales-invoice',$val->id)}}" target="_blank" class="print_hide" title="invoice_excel (TBD)"><span class="glyphicon glyphicon-barcode"></span></a></td>
									<td>...</td>
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
			],
			dom: 'Bfrtip',
			buttons: [
			'copyHtml5',
			'excelHtml5',
			'csvHtml5',
			'pdfHtml5'
			]
		});
	});

</script>
@endsection
