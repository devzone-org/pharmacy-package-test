<?php


namespace Devzone\Pharmacy\Models;


use Illuminate\Database\Eloquent\Model;

class InventoryLedger extends Model
{
    protected $table = 'inventory_ledgers';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if ($model->increase < 0) {
                throw new \Exception('Increase value cannot be negative.');
            }

            if ($model->decrease < 0) {
                throw new \Exception('Decrease value cannot be negative.');
            }
        });

        static::updating(function ($model) {

            if ($model->increase < 0) {
                throw new \Exception('Increase value cannot be negative.');
            }

            if ($model->decrease < 0) {
                throw new \Exception('Decrease value cannot be negative.');
            }
        });
    }
}
