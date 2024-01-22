<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['image','product_id','created_by','updated_by','created_at','updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
