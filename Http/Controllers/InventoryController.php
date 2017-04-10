<?php

namespace Ignite\Reports\Http\Controllers;

use Carbon\Carbon;
use Excel;
use Ignite\Core\Http\Controllers\AdminBaseController;
use Ignite\Inventory\Entities\InventoryBatch;
use Ignite\Inventory\Entities\InventoryBatchProductSales;
use Ignite\Inventory\Entities\InventoryBatchPurchases;
use Ignite\Inventory\Entities\InventoryDispensing;
use Ignite\Inventory\Entities\InventoryPurchaseOrders;
use Ignite\Inventory\Entities\InventorySalesReturn;
use Ignite\Inventory\Entities\InventoryStock;
use Ignite\Inventory\Entities\InventoryStockAdjustment;
use Illuminate\Http\Request;

class InventoryController extends AdminBaseController {

    //Sales Report
    public function timePeriodSales(Request $request) {
        $this->data['filter'] = null;
        if ($request->isMethod('post')) {
            $kowaski = InventoryBatchProductSales::query();
            if ($request->has('start')) {
                $kowaski->whereHas('payment')->where('created_at', '>=', $request->start);
                $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
            }
            if ($request->has('end')) {
                $kowaski->whereHas('payment')->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
            }
            $this->data['records'] = $kowaski->get();
        } else {
            $this->data['records'] = InventoryBatchProductSales::whereHas('payment')->get();
        }
        return view('inventory::reports.salesreport', ['data' => $this->data]);
    }

    //Item Sales Reports
    public function itemSales(Request $request) {
        $this->data['filter'] = null;
        if ($request->isMethod('post')) {
            $despensing = InventoryDispensing::query();
            if ($request->has('start')) {
                $despensing->where('created_at', '>=', $request->start);
                $this->data['filter']['from'] = (new \Date($request->start))->format('jS M Y');
            }
            if ($request->has('end')) {
                $despensing->where('created_at', '<=', $request->end);
                $this->data['filter']['to'] = (new \Date($request->end))->format('jS M Y');
            }
            $this->data['records'] = $despensing->get();
        } else {
            $this->data['records'] = InventoryDispensing::all();
        }
        return view('inventory::reports.item_sales', ['data' => $this->data]);
    }

    //Expiry Date Report
    public function expiry(Request $request) {
        $this->data['filter'] = null;
        $batch_purchase = InventoryBatchPurchases::query();
        if ($request->has('scope')) {
            if ($request->scope !== null) {
                $scope = Carbon::now()->addMonths($request->scope);
                $batch_purchase->where('expiry_date', '<=', $scope);
            } else {
                $scope = Carbon::now();
                $batch_purchase->where('expiry_date', '>=', $scope);
            }
            $this->data['filter']['to'] = (new \Date())->format('jS M Y');
        }
        $this->data['records'] = $batch_purchase->get();
        return view('inventory::reports.expiry', ['data' => $this->data]);
    }

    public function stocks() {
        $this->data['stocks'] = InventoryStock::orderBy('quantity', 'asc')->get();
        return view('inventory::reports.stock', ['data' => $this->data]);
    }

    public function stockMovement() {
        $this->data['adjustments'] = InventoryStockAdjustment::all();
        return view('inventory::reports.stock_movement', ['data' => $this->data]);
    }

}
