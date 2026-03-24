<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuoteWarehouse;
use App\Models\Location;
use App\Models\DispatchTransaction;

class DispatchGuide extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'quote_warehouse_id',
        'guide_number',
        'location_origin_id',
        'location_destination_id',
        'transfer_date',
        'store_entry_date',
    ];

    protected $casts = [
        'transfer_date' => 'datetime',
        'store_entry_date' => 'datetime',
    ];

    public function quoteWarehouse()
    {
        return $this->belongsTo(QuoteWarehouse::class);
    }

    public function originLocation()
    {
        return $this->belongsTo(Location::class, 'location_origin_id');
    }

    public function destinationLocation()
    {
        return $this->belongsTo(Location::class, 'location_destination_id');
    }

    public function dispatchTransactions()
    {
        return $this->hasMany(DispatchTransaction::class);
    }
}
