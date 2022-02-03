<?php

namespace App\Http\Controllers;

use DB;
use App\Customer;
use App\Models\Sales\Sale;
use Illuminate\Http\Request;
use App\Models\Sales\SalesItem;
use App\Models\Sales\SalesPayment;
use App\Models\Sales\SaleTaxe;
use App\Models\Sales\SaleItemTaxe;
use App\Models\Office\Shop\Shop;
use App\Models\Receivings\Receiving;
use App\Models\Receivings\ReceivingItem;

class DetailController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:god');
    // }

    /*--------------------------------------------------------------------*/    

    public function indexTransactions()
    {
        $shops = Shop::get();
        return view("Detailed_Report.transactions", compact('shops'));
    }

    public function showTransactions(Request $request)
    {$date         = explode(' ', $request->daterangepicker);
    $date['to']   = date('Y-m-d', strtotime($date[0]));
    $date['from'] = date('Y-m-d', strtotime($date[2]));
    $sale_type = $request->sale_type;

    if($request->location == 'all'){
        $sales = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
        ->where('sale_time','<=',$date['from'])
        ->where('sale_time','>=',$date['to'])
        ->get();
    }else{
        $sales = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
        ->where('sale_time','<=',$date['from'])
        ->where('sale_time','>=',$date['to'])
        ->where('sale_type', '=', $request->sale_type)
        ->get();
    }
    //return dd($sales);
   return view("Detailed_Report.transactions_table", compact('sales'));
}

/*----------------------------------------------------------------------*/

public function indexReceiving()
{
    $shops = Shop::get();
    return view("Detailed_Report.receivings", compact("shops"));
}

public function showReceiving(Request $request){ 
    $date         = explode(' ', $request->daterangepicker);
    $date['to']   = date('Y-m-d', strtotime($date[0]));
    $date['from'] = date('Y-m-d', strtotime($date[2]));

    $receivings = Receiving::with('item', 'sale_items')
    ->whereBetween('created_at', [$date['to'],$date['from']]);
    $sign ='';        
    if($request->sale_type == 'receiving'){
        if($request->stock_location !='all'){
            $receivings =  $receivings->orwhere('destination',$request->stock_location)->whereBetween('created_at', [$date['to'],$date['from']]);;
        }

        $name = 'destination';

        // return json_encode($receiving_items);

    }elseif($request->sale_type == 'return') {
        if($request->stock_location !='all'){
            $receivings =  $receivings->where('employee_id',$request->stock_location)->whereBetween('created_at', [$date['to'],$date['from']]);;
        }
        $name = 'employee_id';

        $sign = '-';

    }else{
        if($request->stock_location !='all'){
            $receivings =  $receivings->where('employee_id',$request->stock_location)->orWhere('destination',$request->stock_location)->whereBetween('created_at', [$date['to'],$date['from']]);;
        } 
        $name = 'employee_id';
    }

    $receiving_items = [];
    $receiving_data =[];
    $quantity =[];

    $receivings =  $receivings->get();
    foreach ($receivings as $receiving) {
        foreach ($receiving->receiving_items as $key => $value) {
            $receiving_items[$receiving->id][$key] =[
                $value->item !=null ? $value->item->item_number : '',
                $value->item !=null ? $value->item->name : '',
                $value->item !=null ? $value->item->categoryName->category_name :'',
                $value->quantity_purchased.' ['.get_shop_name($value->item_location)->name.']', 
                $value->item !=null ? $value->item->unit_price : '',                   
                $value->discount_percent !=null ? $value->discount_percent : '%',
            ];

            $quantity[$value->item !=null ? $value->item->item_number : '0'] = $value->item !=null ? $value->item->item_number :'0';

        }
        $receiving_data[] = [
            'id'             => $receiving->id,
            'receiving_date' => date('d/m/Y',strtotime($receiving->receiving_time)),
            'quantity'       => $sign.count($quantity),
            'employee_name'  => get_shop_name($receiving->$name)->name,
            'comment'        =>  $receiving->comment,

        ];
    }       
    return view("Detailed_Report.receivings_table",compact('receiving_items','receiving_data'));
}

/*----------------------------------------------------------------------*/

public function indexCustomers()
{
    $customers = Sale::with('customer')->select('customer_id')->distinct()->get();

    $shops = Shop::get();
    return view("Detailed_Report.customers",compact('customers','shops'));
}

public function showCustomers(Request $request)
{
    $date         = explode(' ', $request->daterangepicker);
    $date['to']   = date('Y-m-d', strtotime($date[0]));
    $date['from'] = date('Y-m-d', strtotime($date[2]));
    $payment_type = $request->payment_type;
    $customer_id = $request->c_name;

    if($payment_type == "All"){
        $sales = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
        ->where('sale_time','<=',$date['from'])
        ->where('sale_time','>=',$date['to'])
        ->get();
    }else{
        $sales = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
        ->where('sale_time','<=',$date['from'])
        ->where('sale_time','>=',$date['to'])
        ->where('customer_id','=', $request->c_name)
        ->where('bill_type', $payment_type)
        ->get();
    }
         //dd($sales);
    return view("Detailed_Report.customers_table", compact("sales"));
}

/*-------------------------------------------------------------------------*/

public function indexDiscount(){
    $shop = all_shopes();
    return view("Detailed_Report.discounts",compact('shop'));
}

public function showDiscount(Request $request){
 $date         = explode(' ', $request->daterangepicker);
 $date['to']   = date('Y-m-d', strtotime($date[0]));
 $date['from'] = date('Y-m-d', strtotime($date[2]));
 $sale_type = $request->sale_type;
 $dis = $request->discount;

 if ($request->sale_type == 'all'){
    $sales = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
    ->where('sale_time','<=',$date['from'])
    ->where('sale_time','>=',$date['to'])
    ->where('sale_type', '=', $request->sale_type)
    ->get();
} else{
   $sales = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
   ->where('sale_time','<=',$date['from'])
   ->where('sale_time','>=',$date['to'])
   ->where('sale_type', '=', $request->sale_type)
   ->get();
}
    //return dd($sales);
return view("Detailed_Report.discounts_table", compact('sales', 'dis'));
}

/*--------------------------------------------------------------------------*/

public function indexEmployee()
{
    $shops = Shop::get();
    return view("Detailed_Report.employees", compact('shops'));
}

public function showEmployee(Request $request)
{
    $date         = explode(' ', $request->daterangepicker);
    $date['to']   = date('Y-m-d', strtotime($date[0]));
    $date['from'] = date('Y-m-d', strtotime($date[2]));
    $sale_type = $request->sale_type;

    if($request->location == 'all'){
        $employees = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
        ->where('sale_time','<=',$date['from'])
        ->where('sale_time','>=',$date['to'])
        ->where('sale_type', '=', $request->sale_type)
        ->get();
    }else{
        $employees = Sale::with('customer', 'shop', 'sale_items', 'sale_payment')
        ->where('sale_time','<=',$date['from'])
        ->where('sale_time','>=',$date['to'])
        ->where('sale_type', '=', $request->sale_type)
        ->get();
    }
    
        // dd($sales);
    return view("Detailed_Report.employees_table", compact('employees'));
}


/*-----------------------------------------------------------------------------*/




}
