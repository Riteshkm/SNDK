<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSize extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['size','product_id', 'price','created_by','updated_by','created_at','updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
