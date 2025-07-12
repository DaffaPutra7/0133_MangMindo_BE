<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    // protected $appends = ['image_url'];

    // public function getImageUrlAttribute()
    // {
    //     if ($this->image) {
    //         return Storage::url($this->image);
    //     }
    //     return null;
    // }
}