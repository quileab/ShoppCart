<?php

namespace quileab\ShoppCart;

use Illuminate\Support\Facades\Session;
// use App\Models\Product;
// use App\Models\Order;
// use App\Models\OrderItem;

class ShoppCart
{
    protected $items = [];

    public function __construct()
    {
        $this->items = Session::get('cart', []);
    }

    public function addItem($item)
    {
        //$product = Product::findOrFail($code);

        $cartItem = [
            'id' => $item->id,
            'brand' => $item->brand,
            'description' => $item->description,
            'price' => $item->price,
            'discount' => $item->discount,
            'tax_rate' => $item->tax_rate,
            'quantity' => $item->quantity
        ];

        if (isset($this->items[$item->id])) {
            $this->items[$item->id]['quantity'] += $item->quantity;
        } else {
            $this->items[$item->id] = $cartItem;
        }

        $this->saveCart();
    }

    public function removeItem($code)
    {
        if (isset($this->items[$code])) {
            unset($this->items[$code]);
            $this->saveCart();
        }
    }

    public function updateQuantity($code, $quantity)
    {
        if (isset($this->items[$code])) {
            $this->items[$code]['quantity'] = $quantity;
            $this->saveCart();
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    public function calculateSubtotal()
    {
        return collect($this->items)->sum(function ($item) {
            return ($item['price'] - $item['discount']) * $item['quantity'];
        });
    }

    public function calculateTotalDiscount()
    {
        return collect($this->items)->sum(function ($item) {
            return $item['discount'] * $item['quantity'];
        });
    }

    public function calculateTaxes()
    {
        return collect($this->items)->sum(function ($item) {
            return ($item['price'] - $item['discount']) * $item['quantity'] * $item['tax_rate'];
        });
    }

    public function calculateTotal()
    {
        return $this->calculateSubtotal() + $this->calculateTaxes();
    }

    protected function saveCart()
    {
        Session::put('cart', $this->items);
    }

    public function emptyCart()
    {
        $this->items = [];
        Session::forget('cart');
    }

    // public function saveOrder()
    // {
    //     if (empty($this->items)) {
    //         throw new \Exception('The cart is empty');
    //     }

    //     return \DB::transaction(function () {
    //         $order = Order::create([
    //             'total' => $this->calculateTotal(),
    //             'taxes' => $this->calculateTaxes(),
    //             'discount' => $this->calculateTotalDiscount(),
    //         ]);

    //         foreach ($this->items as $item) {
    //             OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'product_id' => $item['id'],
    //                 'quantity' => $item['quantity'],
    //                 'price' => $item['price'],
    //                 'discount' => $item['discount'],
    //                 'tax_rate' => $item['tax_rate'],
    //             ]);
    //         }

    //         $this->emptyCart();
            
    //         return $order;
    //     });
    // }
}
