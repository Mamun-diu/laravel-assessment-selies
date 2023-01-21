<?php

namespace App\Models;

use App\Traits\Files;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, Files;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'author',
        'sale_price',
        'discount_price',
        'status',
        'image',
    ];
}
