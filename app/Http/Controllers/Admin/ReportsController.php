<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;
use DataTables;
use Session;
use App\UserLoginLogs;

class ReportsController extends Controller
{
    public function salesItemwise()
    {
        abort_if(Gate::denies('report_sales_itemwise_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.reports.sales_itemwise', compact(''));
    }

    /**
     * List all Filter Params
     * @return array
     */
    public function getFilterAttributes()
    {
        return [
            'order_id',
            'product_name',
            'selling_price',
            'special_price',
            'item_quantity',
            'order_status',
            'order_date'
        ];
    }

    public function loginLogs()
    {
      //  abort_if(Gate::denies('deliveryboy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userLoginLogs = UserLoginLogs::all();

        return view('admin.reports.login_logs', compact('userLoginLogs'));
    }


    public function getSalesItemwiseData(Request $request) {
        set_time_limit(0);

        $input = $request->all();
        $filterParams = [];
        if(isset($input['draw']) && $input['draw'] != 1) {
            foreach($input['columns'] as $key => $value) {
                if(isset($value['search']['value']) && !empty($value['search']['value'])) {
                    $filterParams[$value['name']] = $value['search']['value'];
                }
            }
        }
        
        $salesItemsData = DB::table('customer_orders AS co')
            ->leftJoin('customer_order_details AS cod', 'co.id', '=', 'cod.order_id')
            ->leftJoin('products AS p', 'p.id', '=', 'cod.products_id')
            ->leftJoin('product_units AS pu', 'pu.id', '=', 'cod.product_units_id')
            ->leftJoin('unit_master AS um', 'um.id', '=', 'pu.unit_id');

        $salesItemsData->select([
            DB::raw('co.id AS order_id'),
            DB::raw('p.product_name'),
            DB::raw('um.unit'),
            DB::raw('cod.item_quantity'),
            DB::raw('cod.selling_price'),
            DB::raw('cod.special_price'),
            DB::raw('DATE(cod.created_at) AS order_date'),
            DB::raw('cod.is_basket'),
            /* DB::raw('IF(cod.is_basket = 0, "", (
                SELECT GROUP_CONCAT(CONCAT(pn.product_name, " (", umn.unit, ")")) FROM customer_order_details_basket AS codb
                JOIN products AS pn ON pn.id = codb.products_id
                JOIN product_units AS pun ON pun.id = codb.product_units_id
                JOIN unit_master AS umn ON umn.id = pun.unit_id
                WHERE codb.order_id = cod.order_id AND codb.order_details_id = cod.id
            )) AS basket_products'),
            DB::raw('CASE 
                    WHEN cod.order_status = 0 THEN "Pending"
                    WHEN cod.order_status = 1 THEN "Placed"
                    WHEN cod.order_status = 2 THEN "Picked"
                    WHEN cod.order_status = 3 THEN "Out for delivery"
                    WHEN cod.order_status = 4 THEN "Delivered"
                    WHEN cod.order_status = 5 THEN "Cancelled"
                    ELSE "" END AS order_status
                    ') */
            DB::raw('cod.order_status'),
        ]);

        /* $orderDate = "";
        if (!empty($filterParams['order_date_from']) && !empty($filterParams['order_date_to'])) {
            $orderDateFrom = Carbon::parse($filterParams['order_date_from'])->format('Y-m-d');
            $orderDateTo = Carbon::parse($filterParams['order_date_to'])->format('Y-m-d');
            if ($orderDateTo === $orderDateFrom) {
                $orderDate = $orderDateTo;
            } else {
                $salesItemsData->where('DATE(cod.created_at)', '>=', $orderDateFrom . " 00:00:00");
                $salesItemsData->where('DATE(cod.created_at)', '<=', $orderDateTo . " 23:59:59");
            }
        } else if (!empty($filterParams['order_date_from'])) {
            $orderDate = $filterParams['order_date_from'];
        } else if (!empty($filterParams['order_date_to'])) {
            $orderDate = $filterParams['order_date_to'];
        }

        if (!empty($orderDate)) {
            $orderDate = Carbon::parse($orderDate)->format('Y-m-d');
            $salesItemsData->where('DATE(cod.created_at)', '>=', $orderDate . " 00:00:00");
            $salesItemsData->where('DATE(cod.created_at)', '<=', $orderDate . " 23:59:59");
        } */

        if (isset($filterParams['order_id']) && !empty($filterParams['order_id'])) {
            $salesItemsData->where('cod.order_id', '=', $filterParams['order_id']);
        }
        if (isset($filterParams['product_name']) && !empty($filterParams['product_name'])) {
            $salesItemsData->where('p.product_name', 'LIKE', "%".$filterParams['product_name']."%");
        }
       
        // $salesItemsData = collect($salesItemsData->get());
        // return $salesItemsData;
        return datatables()->collection($salesItemsData->get())->toJson();
       // return Datatables::of($salesItemsData)->make(true);

        /* $salesItemsData = DB::table('customer_order_details AS cod')
            ->leftJoin('customer_order_details_basket AS codb', 'cod.id', '=', 'codb.order_details_id')
            // ->leftJoin('products AS p', 'p.id', '=', 'cod.products_id', 'OR', 'p.id', '=', 'codb.products_id')
            ->leftJoin('products AS p', function($join){
                $join->on('p.id', '=', 'cod.products_id');
                $join->orOn('p.id', '=', 'codb.products_id');
            })
            // ->leftJoin('product_units AS pu', 'pu.id', '=', 'cod.product_units_id', 'OR', 'pu.id', '=', 'codb.product_units_id')
            ->leftJoin('product_units AS pu', function($join){
                $join->on('pu.id', '=', 'cod.product_units_id');
                $join->orOn('pu.id', '=', 'codb.product_units_id');
            })
            ->leftJoin('unit_master AS um', 'um.id', '=', 'pu.unit_id')
            ->leftJoin('categories_master AS cm', 'cm.id', '=', 'p.category_id');

        $salesItemsData->select([
            DB::raw('IF(cod.is_basket = 0, cod.product_units_id, codb.product_units_id) AS product_units_id'),
            DB::raw('p.product_name'),
            DB::raw('um.unit'),
            DB::raw('cm.cat_name'),
            DB::raw('DATE(cod.created_at) AS order_date'),
            DB::raw('ROUND(SUM(IFNULL(codb.item_quantity / 2, cod.item_quantity))) AS total_unit'),
        ]);

        $salesItemsData->whereRaw('DATE(cod.created_at) = CURDATE()'); */

        /* SELECT IF(cod.is_basket = 0, cod.product_units_id, codb.product_units_id) AS product_units_id, ROUND(SUM(IFNULL(codb.item_quantity / 2, cod.item_quantity))) AS total_unit, p.product_name, cm.cat_name, um.unit, CASE
            WHEN um.unit = "100gram" THEN (100/1000) * ROUND(SUM(IFNULL(codb.item_quantity / 2, cod.item_quantity)))
            WHEN um.unit = "250gram" THEN (250/1000) * ROUND(SUM(IFNULL(codb.item_quantity / 2, cod.item_quantity)))
            WHEN um.unit = "500gram" THEN (500/1000) * ROUND(SUM(IFNULL(codb.item_quantity / 2, cod.item_quantity)))
            WHEN um.unit = "1kg" THEN 1 * ROUND(SUM(IFNULL(codb.item_quantity / 2, cod.item_quantity)))
            WHEN um.unit = "2kg" THEN 2 * ROUND(SUM(IFNULL(codb.item_quantity / 2, cod.item_quantity)))
            ELSE "" END
            
        FROM customer_order_details AS cod
        LEFT JOIN customer_order_details_basket AS codb ON cod.id = codb.order_details_id
        LEFT JOIN products AS p ON p.id = cod.products_id OR p.id = codb.products_id
        LEFT JOIN product_units AS pu ON pu.id = cod.product_units_id OR pu.id = codb.product_units_id
        LEFT JOIN unit_master AS um ON um.id = pu.unit_id
        LEFT JOIN categories_master AS cm ON cm.id = p.category_id
        WHERE DATE(cod.created_at) = "2021-05-31"
        GROUP BY product_units_id */
    }
}
