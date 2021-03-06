@extends('layouts.dbf')

@section('content')
<script src="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.js"></script>

<div class="container">
	<div id="page_title">Receivings Report</div><br>

	<a href="{{route('detailed-receivings-index')}}" class="btn btn-primary btn-sm pull-right">Back</a>
	<br>
	<br>
	<div id="table_holder">

		{{-- @php echo json_encode($receiving_data); @endphp --}}

		<table id="table"></table>
	</div>

	<div id="report_summary">
		<div class="summary_row">Total: ₹ 0</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function()
	{
	 	(jQuery);
		var details_data = @php echo json_encode($receiving_items); @endphp;
				var init_dialog = function() {
							// table_support.submit_handler('http://newpos.dbfindia.com/reports/get_detailed_receivings_row');
				// dialog_support.init("a.modal-dlg");
					};

		$('#table').bootstrapTable({
			columns: [{"field":"id","title":"Receiving ID","switchable":true,"sortable":true,"checkbox":false,"class":"","sorter":""},{"field":"receiving_date","title":"Date","switchable":true,"sortable":"","checkbox":false,"class":"","sorter":""},{"field":"quantity","title":"Quantity","switchable":true,"sortable":true,"checkbox":false,"class":"","sorter":""},{"field":"employee_name","title":"Origin","switchable":true,"sortable":true,"checkbox":false,"class":"","sorter":""},{"field":"comment","title":"Comments","switchable":true,"sortable":true,"checkbox":false,"class":"","sorter":""}],
			stickyHeader: true,
			pageSize: 20,
			striped: true,
			pagination: true,
			sortable: true,
			showColumns: true,
			uniqueId: 'id',
			showExport: true,
			exportDataType: 'all',
			exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
			data: @php echo json_encode($receiving_data); @endphp,
			iconSize: 'sm',
			paginationVAlign: 'bottom',
			detailView: true,
			escape: false,
			onPageChange: init_dialog,
			onPostBody: function() {
				// dialog_support.init("a.modal-dlg");
			},
			onExpandRow: function (index, row, $detail) {
				$detail.html('<table></table>').find("table").bootstrapTable({
					columns: [{"field":0,"title":"Barcode","sortable":true,"switchable":true},{"field":1,"title":"Name","sortable":true,"switchable":true},{"field":2,"title":"Category","sortable":true,"switchable":true},{"field":3,"title":"Quantity","sortable":true,"switchable":true},{"field":4,"title":"Total","sortable":true,"switchable":true},{"field":5,"title":"Discount","sortable":true,"switchable":true}],
					data: details_data[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(/(POS|RECV)\s*/g, '')]
				});
			}
		});

		init_dialog();
	});
</script>

@endsection
