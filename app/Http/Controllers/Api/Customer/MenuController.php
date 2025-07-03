<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Dedoc\Scramble\Support\Generator\Attributes\Group; // TAMBAHKAN INI
use Illuminate\Http\Request;

#[Group("Customer - Menu")] // TAMBAHKAN INI
class MenuController extends Controller
{
    /**
     * Menampilkan semua data menu untuk customer.
     */
    public function index()
    {
        $menus = Menu::latest()->get();
        return response()->json($menus);
    }
}