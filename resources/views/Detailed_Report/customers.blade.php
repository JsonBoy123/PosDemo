@extends('layouts.dbf')

@section('content')

<div class="container">
	<div class="row">
		<div id="page_title">Report Input</div>
		<form action="{{route('detailed-customers-show')}}" id="item_form" enctype="multipart/form-data" class="form-horizontal" method="post" accept-charset="utf-8">
			<input type="hidden" name="csrf_ospos_v3" value="f27dc808894e188562b2c781ef012793">
			@csrf()
			<div class="form-group form-group-sm">
				<label for="report_date_range_label" class="control-label col-xs-2 required">Date Range</label>
				<div class="col-xs-3">
					<input type="text" name="daterangepicker" value="" class="form-control input-sm" id="daterangepicker" style="width: 180px;">
				</div>
			</div>
			
			<div class="form-group form-group-sm">
				<label for="reports_stock_location_label" class="required control-label col-xs-2">Customers</label>			
				<div id="report_stock_location" class="col-xs-3">
					<select name="c_name" id="c_name" class="form-control">
						<option name="all" selected="selected">All</option>
						@foreach($customers as $data)
						<option value="{{$data->customer->id}}">{{$data->customer->first_name.' '.$data->customer->last_name}}</option>
						@endforeach
					</select>
				</div>
			</div>
			
			<div class="form-group form-group-sm">
				<label for="reports_sale_type_label" class="required control-label col-xs-2">Payment Type</label>
				<div id="report_sale_type" class="col-xs-3">
					<select name="payment_type" id="input_type" class="form-control">
						<option value="All" selected="selected">All</option>
						<option value="Cash" >Cash</option>
						<option value="Debit Card">Debit Card</option>
						<option value="Credit Card">Credit Card</option>
						<option value="Paytm">Paytm UPI</option>
					</select>
				</div>
			</div>

			{{-- <div class="form-group form-group-sm">
				<label for="reports_sale_type_label" class="required control-label col-xs-2">Transaction Type</label>				
				<div id="report_sale_type" class="col-xs-3">
					<select name="sale_type" id="input_type" class="form-control">
					<option value="all" selected="selected">All</option>	
					<option value="retail">RETAIL</option>
					<option value="wholesale">WHOLESALE</option>
					<option value="franchise">FRANCHISE</option>
					<option value="special">SPECIAL APPROVAL</option>
					<option value="1rupee">1RUPEE</option>
					</select>
				</div>
			</div> --}}
			
			<button name="generate_report" id="generate_report" class="btn btn-primary btn-sm">Submit</button>
		</form>

		<a href="{{route('reports.index')}}" class="btn btn-primary btn-sm pull-right">Back</a>
	</div>
</div>
<script>
	$(document).ready(function () {
		var start = moment().subtract(29, 'days');
		var end = moment();

		function cb(start, end) {
			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		}

		$('#daterangepicker').daterangepicker({

			startDate: start,
			endDate: end,
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		}, cb);

		cb(start, end);

	});


	// $('#daterangepicker').daterangepicker();

	// $(document).on('change', '#daterangepicker', function(){

	// 	var date = $(this).val()
	// 	$('#c_name').empty();
	// 	$('#c_name').append($('<option value="">Select Category</option>'));

	// 	$.ajax({
	// 		headers: {
	// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 		},
	// 		method: 'POST',
	// 		url: '{{route('customers-report.show')}}',
	// 		data: {date:date},
	// 		success:function(data){
	// 			$.each(data, function (key, value) {

	// 				$('#c_name').append(
	// 					$('<option></option>').val(key).html(value));
	// 			});

	// 		}
	// 	})
	// });

  // $(document).on('click','#searchItemsBtn',function(){

  //   var date    = $('#datePicker').val()
  //   var categ   = $('#categoryOption').val()
  //   var tax     = $('#taxRatesOption').val()

  //   $.ajax({
  //     headers: {
  //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //       },
  //     method:'POST',
  //     url:'{{route('sale-items-report.search')}}',
  //     data:{date:date, categ:categ, tax:tax},
  //     success:function(data){
  //       $('#daily_sales').html(data)
  //     }
  //   })
  // })

</script>
@endsection
