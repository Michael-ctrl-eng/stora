<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The database table associated with the model.
     */
    protected $table = 'orders';

    /**
     * Fields that can be mass-assigned.
     * Mirrors the orders table columns.
     */
    protected $fillable = [
        'vendor_id',
        'customer_id',
        'guest_email',
        'total_amount',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
