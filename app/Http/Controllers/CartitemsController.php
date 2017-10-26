<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\Product;
use Response;

class CartitemsController extends Controller
{
    /**
     * Display the cart.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if the cart is empty
        if ( !isset($_COOKIE['cart']) )
        {
            $cart = array(
                'total_sum' => 0,
                'products_count' => 0,
                'products' => []
            );

            $json = json_encode($cart);
            setcookie('cart', $json);

            return $json;
        };

        // else show cart's contents
        $cookie = $_COOKIE['cart'];
        $cart = stripslashes($cookie);
        $savedCartArray = json_decode($cart, true);

        return $savedCartArray;
    }

    /**
     * Check and if possible find an item in a multidimensional array
     * @param $needle_id
     * @param $needle_param_name
     * @param $multidimensHaystack
     * @return int|string
     */
    public function isInArray($needle_id, $needle_param_name, $multidimensHaystack)
    {
        foreach ($multidimensHaystack as $key => $hayValue)
        {
            if ($hayValue[$needle_param_name] == $needle_id)
            {
                return $key;
            }
        }
        return -1;
    }

    /**
     * Add products to the cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');

        // invalid product_id
        if ($product_id === null)
        {
            return response()->json([
                'params' => [
                    [
                        'code' => 'required',
                        'message' => 'Product_id cannot be blank',
                        'name' => 'product_id'
                    ],

                ],
                'type' => 'invalid_param_error',
                'message' => 'Not found'
            ], 400);
        }

        if ($quantity < 1 || $quantity > 10 || $quantity === null ) {
            // invalid quantity
            if ($quantity === null)
            {
                return response()->json([
                    'params' => [
                        [
                            'code' => 'required',
                            'message' => 'Quantity cannot be blank',
                            'name' => 'quantity'
                        ],

                    ],
                    'type' => 'invalid_param_error',
                    'message' => 'Not found'
                ], 400);
            }
            else
            {
                return response()->json([
                    'params' => [
                        [
                            'code' => 'invalidParameter',
                            'message' => 'Quantity must be from 1 to 10',
                            'name' => 'quantity'
                        ],

                    ],
                    'type' => 'invalid_param_error',
                    'message' => 'Invalid parameter'
                ], 400);
            }

        }

        $product = Product::where('id',$product_id)->first();

        if ($product === null) {
            // product with this id doesn't exist
            return response()->json([
                'params' => [
                    [
                        'code' => 'required',
                        'message' => 'Product with this id doesn\'t exist',
                        'name' => 'product_id'
                    ],

                ],
                'type' => 'invalid_param_error',
                'message' => 'Not found'
            ], 400);
        }

        $price = Product::where('id',$product_id)->first()->price;

        $cart = $_COOKIE['cart'];
        $cart = json_decode($cart, true);

        if (($key = $this->isInArray($product_id, 'id', $cart['products'])) > -1 )
        {
            $cart['products'][$key]['quantity'] += $quantity;
            $cart['products'][$key]['sum'] += $price * $quantity;
        }
        else
        {
            $cart['products'][] = array(
                'id' => $product_id,
                'quantity' => $quantity,
                'sum' => $quantity * $price
            );
        }

        // Recount total sums
        $cart['total_sum'] += $price * $quantity;
        $cart['products_count'] += $quantity;

        $json = json_encode($cart);
        setcookie('cart', $json);
    }

    /**
     * Remove the specified products from the cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::where('id',$id)->first();

        if ($product === null) {
            // product with this id doesn't exist
            abort(400);
        }

        $price = Product::where('id',$id)->first()->price;

        $cart = $_COOKIE['cart'];
        $cart = json_decode($cart, true);

        if (($key = $this->isInArray($id, 'id', $cart['products'])) > -1 )
        {
            if ($cart['products'][$key]['quantity'] > 1)
            {
                $cart['products'][$key]['quantity']--;
                $cart['products'][$key]['sum'] -= $price;
            }
            else
            {
                array_splice($cart['products'], $key, 1);
            }
            // Recount total sums
            $cart['total_sum'] -= $price;
            $cart['products_count']--;
        }
        $json = json_encode($cart);
        setcookie('cart', $json);
    }
}
