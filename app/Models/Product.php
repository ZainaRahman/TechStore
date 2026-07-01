<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'price', 'brand_id', 'image_url'];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id');
    }

    public function getRouteKeyName()
    {
        return 'product_id';
    }
}