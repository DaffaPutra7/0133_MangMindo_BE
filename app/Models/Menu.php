<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory; // Tambahkan ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // Tambahkan properti $fillable ini
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
    ];
}