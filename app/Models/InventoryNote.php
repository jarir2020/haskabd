<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryNote extends Model
{
    protected $table    = 'inventory_notes';
    protected $fillable = ['content'];
}
