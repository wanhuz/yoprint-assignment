<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'status',
        'error_message',
    ];

    // Optional: Define a relationship to products
    public function products()
    {
        return $this->hasMany(Product::class, 'upload_id');
    }
}
