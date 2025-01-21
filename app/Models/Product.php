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
        'quantity',
        'available_quantity'
    ];

    public static function validate(Request $request)
    {
        return $request->validate([
            'name' => 'required_if:id,null|string', // If the id is null, the call is a post then the name is required, otherwise it is a patch and the name is not required
            'price' => 'nullable|numeric',
            'quantity' => 'nullable|numeric',
            'available_quantity' => 'nullable|numeric'
        ]);
    }

    public function filtered() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'available_quantities' => $this->available_quantities()
        ];
    }

    public function available_quantities() {
        dd($this->orders()->where('status', 0));

        return 1;
        return $this->available_quantity;
    }

    /**
     * The orders that in which the product is present.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }
}
