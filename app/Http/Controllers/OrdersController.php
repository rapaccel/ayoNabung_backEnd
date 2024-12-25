<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey    = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds        = config('services.midtrans.is3ds');
    }

    public function store(Request $request)
    {
        DB::transaction(function() use($request) { 
            $order=Orders::create([
                'product_id' => $request->product_id,
                'status'     => 'pending',
                'total_price'=> $request->total_price,
                'user_id'    => $request->user_id,
            ]);
            $user=User::find($request->user_id);
            $payload = [
                'transaction_details' => [
                    'order_id'     => $order->id,
                    'gross_amount' => $order->total_price, 
                ],
                'customer_details' => [
                    'first_name' => "John",
                    'email'      => $user->email,
                ],
                'item_details' => [
                    [
                        'id'            => $order->id,
                        'price'         => $request->total_price,
                        'quantity'      => 1,
                        'name'          => 'Order to ' . config('app.name'),
                        'brand'         => 'Order',
                        'category'      => 'Order',
                        'merchant_name' => config('app.name'),
                    ],
                ],
            ];
            $snapToken = \Midtrans\Snap::getSnapToken($payload);
            $order->snap_token = $snapToken;
            $order->save();
            $snapTransaction = \Midtrans\Snap::createTransaction($payload);
            $redirectUrl = $snapTransaction->redirect_url;
            $this->response['redirect_url'] = $redirectUrl;
            $this->response['snap_token'] = $snapToken;
            $this->response['status'] = true;
        });
        return response()->json($this->response);
    }

    public function notify(Request $request) {
        $data = $request->all();
        $orderId = $data['order_id'];
        $transactionStatus = $data['transaction_status'];
    
        $order = orders::where('id_order', $orderId)->first();
    
        if(!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        

        if($transactionStatus == 'capture') {
            $order->status = 'success';
            $order->save();
        } elseif($transactionStatus == 'settlement') {
            $order->status = 'success';
            $order->save();
        } elseif($transactionStatus == 'pending') {
            $order->status = 'pending';
            $order->save();
        } elseif($transactionStatus == 'deny') {
            $order->status = 'failure';
            $order->save();
        } elseif($transactionStatus == 'expire') {
            $order->status = 'failure';
            $order->save();
        } elseif($transactionStatus == 'cancel') {
            $order->status = 'failure';
            $order->save();
        }
        return response()->json(['message' => 'Notification handled successfully'], 200);
    }

    public function ordersByUsers($id)
    {
        $orders = Orders::where('user_id', $id)->get();
        return response()->json($orders, 200);
    }
}
