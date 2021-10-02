<?php

namespace Devzone\Pharmacy\Models\Payments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPaymentDetail extends Model
{
    use HasFactory;
    protected $table = 'customer_payment_details';
    protected $guarded=[];
}
