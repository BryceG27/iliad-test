<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Testing\Fluent\Concerns\Has;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'quantity',
    ];

    public static function validate(Request $request, $id = null)
    {
        /**
         * If the id is null, the call is a post then the name is required, otherwise it is a patch and the name is not required 
         * The name must be unique
         * */
        return $request->validate([
            'name' => [Rule::requiredIf($id == null), 'string', Rule::unique('products')->ignore($id)],
            'price' => 'nullable|numeric',
            'quantity' => 'nullable|numeric',
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

    /* WIP */
    public function available_quantities() {
        $qties = $this->quantity;

        // Get the quantities of the product in the orders where the status of the order is 0
        foreach ($this->orders->where('status', 0) as $order) {
            $qties -= $order->pivot->quantity;
        }

        return $qties;
    }

    /**
     * The orders that in which the product is present.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }
}
