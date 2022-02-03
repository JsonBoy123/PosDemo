@extends('layouts.dbf')

@section('content')

<div class="container">
	<div id="page_title">Category Summery Report</div><br>

	<a href="{{route('categories-report.index')}}" class="btn btn-primary btn-sm pull-right">Back</a>
	<div style="font-size: 20px"><b>Time Duration - </b> {{$date['to']}} To {{$date['from']}}</div>
	@if(isset($cats) == true)
		<div class="row" id="catsTable">
	    <div class="col-md-12 col-xl-12">
	      <div class="card shadow-xs">
	        <div class="card-body table-responsive">
	          <table class="table table-striped table-hover" id="CategoryTable">
	            <thead>
	              <tr class="text-center">
	                <th style="text-align:center">Category</th>
	                <th style="text-align:center">Quantity</th>
	                <th style="text-align:center">Subtotal</th>
	                <th style="text-align:center">Tax</th>
	                <th style="text-align:center">Total</th>
	                <th style="text-align:center">Profit</th>
	              </tr>
	            </thead>
	            <tbody>
	          @php $count = 0;

				$profit_sum = $total_sum = $gst_sum = $subtotal_sum = $quan_sum = 0;
	           	foreach($cats as $index){ 
	           		$quantity_total = $total = $total = $tax = 0;
	          @endphp
	              <tr class="text-center">
	                <td>{{$index->category_name}}</td>
	                @php
	                	foreach($index['saleItems'] as $index_quantity){

							$quantity_total += $index_quantity['quantity_purchased'];

							if($index_quantity['item_unit_price'] != 0.00){
								$gross_value = $index_quantity['item_unit_price'] - ($index_quantity['item_unit_price']*$index_quantity['discount_percent'])/100;

								$total += $index_quantity['quantity_purchased'] * $gross_value ;

								$tax += ($total * (int)$index_quantity['taxe_rate'])/100;

							}else{
								$total += $index_quantity['quantity_purchased'] * $index_quantity['discount_percent'] ;

								$tax += ($total * str_replace(' ', '', ($index_quantity['taxe_rate'])))/100;
	
							}
	                	}

	                @endphp

	                
	                <td class="quantityTotal"> {{$quantity_total}}</td>
	                <td>₹ {{number_format(($total - $tax),'2')}}</td>
	                <td>₹ {{number_format($tax, 2)}}</td>
	                <td>₹ {{number_format($total, 2)}}</td>
	                <td>₹ {{number_format(($total - $tax), 2)}}</td>
	                <td>...</td>
	                @php 
	                	$quan_sum +=$quantity_total;
	                	$gst_sum +=$tax;
	                	$total_sum +=$total;
	                	$profit_sum += ($total - $tax) ;

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
