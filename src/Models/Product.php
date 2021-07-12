<?php


namespace Devzone\Pharmacy\Models;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];

    public function inventories()
    {
        return $this->hasMany(ProductInventory::class);
    }
    public function purchases_receive()
    {
        return $this->hasMany(PurchaseReceive::class);
    }

}
