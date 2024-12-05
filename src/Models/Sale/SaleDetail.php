<?php

namespace Devzone\Pharmacy\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->qty < 0) {
                throw new \Exception('Quantity cannot be negative.');
            }
        });

        static::updating(function ($model) {
            if ($model->qty < 0) {
                throw new \Exception('Quantity cannot be negative.');
            }
        });

    }
}
