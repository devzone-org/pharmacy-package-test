<?php

namespace Devzone\Pharmacy\Models\Payments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;
    protected $table = 'customer_payments';
    protected $guarded=[];
}
