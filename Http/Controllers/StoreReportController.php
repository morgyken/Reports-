<?php

namespace Ignite\Reports\Http\Controllers;

use Carbon\Carbon;
use Ignite\Core\Http\Controllers\AdminBaseController;
use Ignite\Inventory\Entities\Store;
use Ignite\Inventory\Entities\StoreProducts;

class StoreReportController extends AdminBaseController
{

    public function index () {

        $stores = Store::all();

        return view('reports::stores.index', compact('stores'));
    }

    public function stockReport ($storeId)
    {
        $store = Store::find($storeId);

        $storeProducts = StoreProducts::where('store_id', $storeId)->get();

        $report = $storeProducts->map(function ($storeProduct)
        {
            $cost = $storeProduct->selling_price ? $storeProduct->selling_price: $storeProduct->product->selling_p;

            $stock = $storeProduct->quantity;

            return [

                'name' => $storeProduct->product->name,

                'cost' => $cost,

                'stock' =>$stock,

                'value' => $stock * $cost,
            ];
        });
        $stockValue = array_sum(array_pluck($report, 'value'));

        return view('reports::stores.stocks', compact('report', 'stockValue'));
    }

    public function stockMovement ($storeId)
    {
        return view('reports::stores.movement');
    }


    public function stockExpiry ($storeId)
    {
        $store = Store::find($storeId);

        $expiries = array();

        $products = array();

        $store->products->each(function($product) use(&$expiries, &$products){
            $quantity = $product->pivot->quantity;
            $expiries = $product->batches->map(function($batch) use($quantity){
                    return [
                        'quantity' => $quantity,
                        'product_name' => $batch->products->name,
                        'arrival_date' => Carbon::parse($batch->created_at),
                        'expiry_date' => Carbon::parse($batch->expiry_date),
                        'status' => Carbon::now()->diffInDays(Carbon::parse($batch->expiry_date))
                ];
            });
            array_push($products, $expiries);
        });

        return view('reports::stores.expiry', compact('products'));
    }
}






