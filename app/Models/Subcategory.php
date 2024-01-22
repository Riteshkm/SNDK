<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Subcategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name','category_id','created_by','updated_by','created_at','updated_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
