<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'date',
        'type',
        'status'
    ];

    public function get_status_description() {
        $response = '';

        switch ($this->status) {
            case 0:
                $response = 'Pending';
                break;
            case 1:
                $response = 'Completed';
                break;
            case 2:
                $response = 'Cancelled';
                break;
            
            default:
                $response = 'Order status not found';
                break;
        }

        return $response;
    }

    public function filtered() {
        $costs = 0;

        $this->products->map( function($product) use (&$costs) {
            $costs += $product->price * $product->pivot->quantity;
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'date' => $this->date,
            'status' => $this->get_status_description(),
            'costs' => $costs,
            'products' => $this->products->map(function($product) {
                return [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->pivot->quantity,
                    'available_quantity' => $product->available_quantities()
                ];
            })
        ];
    }

    /**
     * Validate the request
     */
    public static function validate(Request $request)
    {
        return $request->validate([
            'user_id' => 'required|exists:users,id', // The user insert is required and must exist in the users table
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'status' => ''
        ]);
    }

    /**
     * The user who made the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The products in the order
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}