<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Support\Generator\Attributes\Group;

#[Group("Admin - Order")]
class OrderController extends Controller
{
    /**
     * Menampilkan semua pesanan yang masuk.
     */
    public function index()
    {
        $orders = Order::with('user', 'items.menu')->latest()->get();
        return response()->json($orders);
    }

    /**
     * Mengubah status sebuah pesanan.
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:diterima,dimasak,diantar,selesai,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json($order);
    }
}