<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Support\Generator\Attributes\Group;

#[Group("Customer - Order")]
class OrderController extends Controller
{
    /**
     * Menyimpan pesanan baru dari customer.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderItems = $request->input('items');
        $totalPrice = 0;
        $menuIds = array_column($orderItems, 'menu_id');
        $menus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

        // Memulai transaksi database untuk memastikan semua data konsisten
        DB::beginTransaction();
        try {
            // 2. Hitung total harga
            foreach ($orderItems as $item) {
                $menu = $menus[$item['menu_id']];
                $totalPrice += $menu->price * $item['quantity'];
            }

            // 3. Buat pesanan utama (order)
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $totalPrice,
                'status' => 'pending', // Status awal pesanan
            ]);

            // 4. Masukkan setiap item ke dalam pesanan
            foreach ($orderItems as $item) {
                $menu = $menus[$item['menu_id']];
                $order->items()->create([
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price' => $menu->price, // Simpan harga saat itu
                ]);
            }

            // Jika semua berhasil, konfirmasi transaksi
            DB::commit();

            // Load relasi 'items.menu' untuk ditampilkan di response
            $order->load('items.menu');

            return response()->json($order, 201);

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua yang sudah disimpan
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat pesanan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan riwayat pesanan milik customer yang sedang login.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                        ->with('items.menu', 'review') // Mengambil detail item dan menu sekaligus
                        ->latest()
                        ->get();

        return response()->json($orders);
    }
}