<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
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
        $qties = 0;

        // Get the quantities of the product in the orders where the status of the order is 0
        /* foreach ($this->orders as $order) {
            if ($order->status == 0) {
                dump($order->pivot->quantity);
                $qties += $order->pivot->quantity;
            }
        }

        die(); */

        return $qties;
    }

    /**
     * The orders that in which the product is present.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }
}
