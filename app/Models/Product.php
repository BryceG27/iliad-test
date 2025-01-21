<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'available_quantity'
    ];

    public function validate(Request $request)
    {
        return $request->validate([
            'name' => 'required|string',
            'price' => 'nullabled|numeric',
            'available_quantity' => 'nullable|numeric'
        ]);
    }

    public function available_quantities() {
        dd($this->orders);

        return $this->available_quantity;
    }

    /**
     * The orders that in which the product is present.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
