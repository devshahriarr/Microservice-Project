<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'seller_id',
        'property_type',
        'project_name',
        'developer_name',
        'unit_number',
        'bedrooms',
        'bathrooms',
        'size_sqft',
        'floor_number',
        'view_type',
        'parking_slots',
        'status_type',
        'is_rented',
        'rent_amount',
        'contract_end_date',
        'handover_date',
        'outstanding_balance',
        'asking_price',
        'title_deed_url',
        'spa_document_url',
        'extra_files',
        'source',
    ];

    protected $casts = [
        'is_rented' => 'boolean',
        'size_sqft' => 'decimal:2',
        'rent_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'asking_price' => 'decimal:2',
        'extra_files' => 'array',
        'contract_end_date' => 'date',
        'handover_date' => 'date',
    ];
}
