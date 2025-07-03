<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Support\Generator\Attributes\Group;

#[Group("Customer - Review")]
class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        // 1. Pastikan pesanan ini milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 2. Pastikan pesanan sudah selesai (completed)
        if ($order->status !== 'completed') {
            return response()->json(['message' => 'Pesanan belum selesai'], 422);
        }

        // 3. Validasi input
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 4. Buat review
        $review = Review::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json($review, 201);
    }
}