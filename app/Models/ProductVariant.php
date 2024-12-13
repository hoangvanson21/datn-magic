<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $fillable = ['product_id', 'color_id', 'option_id', 'variant_price', 'variant_quantity'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Colors::class);
    }

    public function option()
    {
        return $this->belongsTo(Options::class);
    }
}
