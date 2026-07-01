<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    public $timestamps = false;
    protected $fillable = ['product_id', 'quantity', 'reorder_level'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}