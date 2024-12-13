<?php

namespace App\Models;

use App\Models\Reviews;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'dis_price',
        'quantity',
        'brand_id',
        'images',
        'status_id',
        'type_id',
        'discount_id',
        'category_id',
        'user_id',
        'options_id',
        'colors_id',
    ];
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function options()
    {
        return $this->belongsTo(Options::class);
    }
    public function colors()
    {
        return $this->belongsTo(Colors::class);
    }
    public function reviews()
    {
        return $this->hasMany(Reviews::class);
    }
    public function ratings()
    {
        return $this->hasMany(Reviews::class);
    }
}
