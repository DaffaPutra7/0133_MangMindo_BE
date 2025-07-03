<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Dedoc\Scramble\Support\Generator\Attributes\Group; // TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

#[Group("Admin - Menu")] // TAMBAHKAN INI
class MenuController extends Controller
{
    /**
     * Menampilkan daftar semua menu. (Read)
     */
    public function index()
    {
        $menus = Menu::latest()->get();
        return response()->json($menus);
    }

    /**
     * Menyimpan menu baru. (Create)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Gambar opsional
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/menus');
        }

        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath
        ]);

        return response()->json($menu, 201);
    }

    /**
     * Menampilkan detail satu menu. (Read)
     */
    public function show(Menu $menu)
    {
        return response()->json($menu);
    }

    /**
     * Memperbarui data menu. (Update)
     */
    public function update(Request $request, Menu $menu)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($menu->image) {
                Storage::delete($menu->image);
            }
            $imagePath = $request->file('image')->store('public/menus');
        }

        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath
        ]);

        return response()->json($menu);
    }

    /**
     * Menghapus data menu. (Delete)
     */
    public function destroy(Menu $menu)
    {
        // Hapus gambar dari storage
        if ($menu->image) {
            Storage::delete($menu->image);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu successfully deleted.'], 200);
    }
}