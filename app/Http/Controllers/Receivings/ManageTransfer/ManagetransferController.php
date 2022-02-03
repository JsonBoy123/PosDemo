<?php

namespace App\Http\Controllers\Receivings\ManageTransfer;

use Auth;
use Excel;
use Session;
use App\User;
use Notification;
use App\Models\Item\Item;
use Illuminate\Http\Request;
use App\Models\Office\Shop\Shop;
use App\Models\Manager\ItemRack;
use App\Models\Stock\StockMovent;
use App\Models\Item\Item_Discount;
use App\Exports\AllShopsItemExport;
use App\Models\Stock\StockTransfer;
use App\Models\Item\item_quantities;
use App\Http\Controllers\Controller;
use App\Models\Receivings\Receiving;
use App\Notifications\Notifications;
use App\Models\Manager\ManageItemRack;
use App\Models\Receivings\ReceivingItem;
use App\Models\Office\Employees\Employees;
use App\Models\Receivings\ReceivingRequest;
use App\Models\Receivings\ReceivingRequestItems;
use App\Imports\ItemsImport;
use App\Exports\ItemErrorsExport;
use App\Exports\ItemUpdateErrorsExport;


class ManagetransferController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $user       = User::find(Auth::id());
        $shop_id    = get_shop_id_name()->id;
        if($user->isAbleTo('stock-maintainance')){
         $transfers = Receiving::with('stock_movement.item')
         ->where('receiving_request', null)
         ->where('delete_at', null)
         ->get();

            // dd($transfers);

         $stocks_in = Receiving::with('stock_movement.item')
         ->where('completed', 1)
            //->where('laxyo_house', '!=', 1)
         ->where('destination', $shop_id)
         ->orderBy('id', 'DESC')
         ->get();

            // dd($stocks_in);

         $receivings_request = ReceivingRequest::with(['receivings', 'return_receivings'])
         ->whereNotIn('status',[2])
         ->whereNotIn('laxyo_admin',[4])
         ->orderBy('id', 'desc')
         ->get();

         // dd($receivings_request);

         $request_log = ReceivingRequest::with(['receivings', 'return_receivings'])
         ->where('status', 2)
         ->orWhere('status', 4)
         ->orderBy('id', 'desc')->limit(750)->get();

            // dd($request_log);

     }else{
        $transfers = Receiving::with('stock_movement.item')
        ->where('destination',$shop_id)
        ->orderBy('id', 'desc')->limit(500)->get();

        $stocks_in = Receiving::with('stock_movement.item')
        ->where('completed', 1)
        ->where('destination', $shop_id)
        ->whereNull('receiving_request')
        ->orderBy('id', 'DESC')
        ->limit(10)->get();

        $receivings_request = ReceivingRequest::where('laxyo_admin','!=','4')->whereRaw('requested_by = ? OR requested_to = ? AND laxyo_admin != ?', [$shop_id, $shop_id, 0])->orderBy('id', 'desc')->get();

        $request_log =  ReceivingRequest::with(['receivings', 'return_receivings'])
        ->orWhere('requested_by', $shop_id)
        ->orWhere('requested_to', $shop_id)
        ->whereIn('status', [2, 4])
        ->orderBy('id', 'desc')->get();
    }
    return view('receivings.manage-transfer.index',compact('transfers','stocks_in', 'receivings_request', 'request_log'));
}

public function show($id){
    return $id;
} 

public function manage_transfer_show(){
    $receivings =  Receiving::has('stocks_transfer')->with(['stocks_transfer'])->whereHas('receiving_items',function($q){
        $q->where('item_location',1);
    })->with(['receiving_items'=>function($query){
        $query->select('receiving_id','item_id','quantity_purchased','item_location')->where('item_location',1);
    }])->where('completed','1')->whereNull('receiving_request')->get();
    return $receivings;
    $items = [];
}

public function accept_data_stockIn(Request $request){
    $data = StockMovent::with('receivingData')
    ->where('receiving_id', $request->id)
    ->get();
    foreach ($data as $value) {
        $user = User::find(Auth::id());
        $stock_transfer = [
            'receiving_id'  => $request->id,
            'item_id'       => $value->item_id,
            'accept'        => $value->quantity,
        ];

        StockTransfer::create($stock_transfer);
        item_quantities::where('item_id', $value->item_id)
        ->where('location_id', $value['receivingData']->destination)
        ->increment('quantity', $value->quantity);

            //___________________________________//

        Receiving::where('id', $request->id)
        ->update(['completed' => 2]);

        StockMovent::where('receiving_id', $request->id)
        ->where('item_id', $value->item_id)
        ->update(['processed' => 2]);
    }
    return "Successfully Accepted..";
}

public function f_comment_stockIn(Request $request){
    Receiving::where('id',$request->id)->update(['final_comment' => $request->f_comment,]);
    return "Successfully Uploaded..";
}

// View Button Function

public function stock_in_data(Request $request){
    $user = User::find(Auth::id());

    if($user->isAbleTo('stock-maintainance')){
        $data = ReceivingItem::with(['item' => function($que){
            $que->select('id','item_number', 'name');
        } ])
        ->where('receiving_id', $request->receive_id)
        ->select('item_id', 'quantity_purchased', 'line')
        ->get();
    }else{
        $data = ReceivingItem::with(['item' => function( $que){
            $que->select('id', 'item_number', 'name');
        } ])
        ->where('receiving_id', $request->receive_id)
        ->where('item_location', get_shop_id_name()->id)
        ->select('item_id', 'quantity_purchased')
        ->get();
    }
    // dd($data);
    return view('receivings.manage-transfer.show', compact('data'));
}

// Receiving Request Item Show Function Click On Eye Button In Receiving Request

public function showRequestedItems($id){
    $items = ReceivingRequestItems::with(['item'])
    ->where('receiving_request_id', $id)
    ->get();

    $qty   = ReceivingRequestItems::with(['item'])
    ->where('receiving_request_id', $id)
    ->sum('quantity');

    $request = ReceivingRequest::where('id', $id)->first();
    return view('receivings.manage-transfer.request.index', compact('items', 'qty', 'request'));
}

// Generate DC After Approve Request Function

public function receivingRequestUpdate(Request $request){
    $req = ReceivingRequest::where('id', $request->request_id)
    ->update(['laxyo_admin' => $request->value]);

    /*** Notification for new requests ***/

    $request = ReceivingRequest::where('id', $request->request_id)->select('requested_to')->first();
    $shop    = Employees::where('shop_id', $request->requested_to)->select('user_id')->first();
    $user    = User::find($shop->user_id);
    $data['id']     = $request->id;
    $data['user']   = 'requested_to';
    $data['url']    = 'manage_transfer';
    $data['message']= 'You have new request for item.';
    Notification::send($user, new Notifications($data));
    $msg = $req == true ? "Request has been accepted." : 'There is an error occured';
    return $msg;
}

// Generate DC Function

public function generateReceivingShow(Request $request){

    $user = User::find(Auth::id());
    $items = ReceivingRequest::with(['requested_items.item', 'receiving_items'])
    ->where('id', $request->request_id)->first();
    $receiving_request = [];
    foreach ($items['requested_items'] as $index) {
       $location = $user->isAbleTo('stock-maintainance') == true ? 1 : $items->requested_to ;
       $actualQty = item_quantities::where('item_id', $index->item_id)->where('location_id', $location)->first();
       $item_disc = Item_Discount::where('item_id', $index->item_id)->first();
       $item[$index->item_id] = [
         'item_id'       	=> $index->item_id,
         'actual_qty'    	=> $actualQty->quantity,
         'item_number'   	=> $index->item_number,
         'name'          	=> $index->name,
         'qty'           	=> $index->quantity,
         'unit_price'    	=> $index->unit_price == 0 ? (float)$item_disc->retail : $index->unit_price,
         'repair_cat'    	=> 'null',
         'repair_cat_id'    => 'null'
     ];  
 }

 $request_location = $user->isAbleTo('stock-maintainance') == false ? 1 : $items->requested_by;
 $receiving_request['requested_by'] 	= Shop::where('id', $request_location)->select('id', 'name')->first();
 $receiving_request['request_id']	= $items->id;
 $receiving_request['requested_to']	= Shop::where('id', get_shop_id_name()->id)->select('id', 'name')->first();

 session()->put('receiving_session', 1);
 session()->put('receiving_request', $receiving_request);
 session()->put('receiving_data', $item);
 return redirect()->route('receivings.index');

}

// DC Approve Function

public function receivingApprove(Request $request){
 $receiving_req = ReceivingRequest::where('id', $request->request_id)->first();
 if($request->process == 'approve_admin'){
    $receiving_id = $receiving_req->return_receiving_id;
}else{
    $receiving_id = $receiving_req->reference_receiving_id;
}

$receiving 	= Receiving::where('id', $receiving_id)->first();
$data = ReceivingItem::where('receiving_id', $receiving_id)
->where('item_location', $receiving->destination)
->get();

foreach ($data as $value) {
    $stock_transfer = [
        'receiving_id'  => $value->receiving_id,
        'item_id'       => $value->item_id,
        'accept'        => $value->quantity_purchased,
    ];

    StockTransfer::create($stock_transfer);
    item_quantities::where('item_id', $value->item_id)
    ->where('location_id', $value->item_location)
    ->increment('quantity', $value->quantity_purchased);

    Receiving::where('id', $value->receiving_id)
    ->update(['completed' => 2]);

    StockMovent::where('receiving_id', $value->receiving_id)
    ->where('item_id', $value->item_id)
    ->update(['processed' => 2]);
}

$column = $request->process == 'approve_admin' ? 'laxyo_admin' : 'status';
ReceivingRequest::where('id', $request->request_id)
->update([$column => 2]);
return "Successfully Accepted..";
}

public function searchItems(Request $request){
    $search = $request->search_items;
    $string = $request->type;
    if($search == ""){
        return redirect()->route('req_for_item');            
    }

    $query = $string == 'name' ? 'name' : 'item_number';
    $shops = Shop::all();
    $items = Item::select('id', 'item_number', 'name', 'unit_price')
    ->where($query, 'ilike', '%'.$search.'%')
    ->whereHas('item_quantities',function($que){
        $que->whereIN('location_id',[1, 2, 5, 6, 7, 12])
        ->where('quantity','>','0');
    })->with(['item_quantities' => function($q){
        $q->select('item_id','location_id','quantity')
        ->whereIn('location_id',[1, 2, 5, 6, 7, 12])
        ->orderBy('location_id');
    }])->paginate(20);

    $users = User::wherePermissionIs('hide_shops')->get()->pluck('id');
    $employee = Employees::where('user_id', Auth::id())
    ->whereNotIn('user_id', $users)
    ->first();
    return view('request_for_items',compact('shops','items', 'employee'));
}

public function receivingNotification($id){
    $notif = Auth::user()->notifications->where('id', $id)->first();
    $notif->markAsRead();
    return redirect($notif->data['url']);
}

public function declineRecevingRequest(Request $request){
    if($request->value == 'decline-admin'){
        ReceivingRequest::where('id', $request->request_id)
        ->update([
            'laxyo_admin'          => 4,
            'status'               => 4,
            'decline_admin_reason' => $request->reason]);
    }else{}
}

public function deleteDC(Request $request){
    $dc_items = StockMovent::where('receiving_id', $request->receiving)->get();
    foreach($dc_items as $item){
        item_quantities::where('item_id', $item->item_id)
        ->where('location_id', 1)
        ->increment('quantity', $item->quantity);
    }

    StockMovent::where('receiving_id', $request->receiving)->delete();
    ReceivingItem::where('receiving_id', $request->receiving)->delete();
    Receiving::where('id', $request->receiving)->delete();
    ReceivingRequest::where('id', $request->request_id)
    ->update([
        'laxyo_admin'            => 2,
        'reference_receiving_id' => null,
        'deleted_by'             => Auth::id()]);
}

public function exportAllShopsItem(){
    return Excel::download(new AllShopsItemExport, 'All Shops items.xlsx');
}

public function itemsQuantityIndex(){
    $items = item_quantities::where('location_id', 1)->with(['item'])->paginate(100);
    $racks = ItemRack::all();
    return view('RequestItems.update-item-index');
}

public function itemsDetailsUpdate(Request $request){
    $item = ManageItemRack::where('rack_id', $request->rack)->where('item_id', $request->item_id)->first();
    if($item == true ){
        ManageItemRack::where('rack_id', $request->rack)
        ->where('item_id', $request->item_id)
        ->increment('quantity', $request->quantity);
    }else{
        ManageItemRack::create([
            'rack_id' => $request->rack,
            'item_id' => $request->item_id,
            'quantity'=> $request->quantity
        ]);
    }

    item_quantities::where('item_id', $request->item_id)
    ->where('location_id', 1)
    ->increment('quantity', $request->quantity);

}

public function itemsDetailSearch(Request $request){
    $barcode = $request->item_barcode;
    $bar = Item::where('item_number', $barcode)->first();
    if($bar == true){
        $items = Item::with(['item_quantity' => function($q) use($bar){
            $q->where('location_id', 1);
        }])->where('id', $bar->id)->first();
        $racks = ItemRack::all();
        return view('RequestItems.update-item-index',compact('items', 'racks'));
    }else{
        return back()->with('failure', 'Barcode not found.');
    }
}

public function updateItemsracks(Request $request){
    $status = true;
    $errors = array();
    $datas  = Excel::toCollection(new ItemsImport,$request->file('file_path'));
    foreach ($datas as $data) {
        foreach ($data as $item) {
            $item_num   = (string)$item['item_number'];
            $rack_id    = (string)$item['rack_id'];
            $qty        = (string)$item['quantity'];

            if($item_num !='' && $rack_id !='' && $qty != ''){
                $items    =  Item::where('item_number', $item_num)->first();
                $ItemRack =  ItemRack::where('id', $rack_id)->first();
                $item_id  =  $items['id'];
                $ItemRack_id  =  $ItemRack['id'];

                if($item_id && $ItemRack_id){
                    item_quantities::where('item_id', $item_id)->where('location_id', '1')
                    ->update(['quantity' => $qty]);

                    /*--------------------*/

                    $rack_item = ManageItemRack::where('item_id', $item_id)->where('rack_id', $ItemRack_id)->first();
                    if($rack_item == true){
                        ManageItemRack::where('item_id', $item_id)
                        ->where('rack_id', $ItemRack_id)
                        ->update(['quantity' => $qty]);
                    }else{
                        ManageItemRack::create([
                            'rack_id' => $ItemRack_id,
                            'item_id' => $item_id,
                            'quantity'=> $qty
                        ]);
                    }
                }
            }else{
                $status = false;
            }

            if($status == false){
                $errors[] = [
                    'item_number'     => $item['item_number'],
                    'rack_id'  => $item['rack_id'],
                    'qty'  => $qty,
                ];

                dd($errors);
            }
        }
    }
    return back()->with('success','Item Quantity Updated Successfully.');
}

public function updateItemsracksTest(Request $request){
    $status = true;
    $errors = array();
    $datas  = Excel::toCollection(new ItemsImport,$request->file('file_path'));
    foreach ($datas as $data) {
        foreach ($data as $item) {
            $item_num   = (string)$item['item_number'];
            $qty        = (string)$item['quantity'];
            if($item_num !='' && $qty != ''){
                $items    =  Item::where('item_number', $item_num)->first();
                $item_id  =  $items['id'];
                if($item_id){
                    item_quantities::where('item_id', $item_id)->where('location_id', '1')
                    ->decrement('quantity', $qty);

                    item_quantities::where('item_id', $item_id)->where('location_id', '31')
                    ->increment('quantity', $qty);
                }
            }else{
                $status = false;
            }
            if($status == false){
                $errors[] = [
                    'item_number'     => $item['item_number'],
                    'qty'  => $qty,
                ];
                dd($errors);
            }
        }
    }
    return back()->with('success','Item Quantity Updated Successfully.');
}

public function receivingSessionDestroy(){
   Session::remove('receiving_session');
   Session::remove('receiving_request');
   Session::remove('receiving_data');
   return back();
}
}