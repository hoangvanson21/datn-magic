<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorites extends Model
{
    // Specify the table name if it does not follow the convention
    protected $table = "wish_list";

    // Allow mass assignment for specific fields
    protected $fillable = ['user_id', 'product_id'];

    // Each favorite belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Each favorite belongs to one product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
