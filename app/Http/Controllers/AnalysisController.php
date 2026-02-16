<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index()
    {
        $startDate = '2022-08-01'; 
        $endDate = '2026-12-31'; 
        //$period = Order::betweenDate($startDate, $endDate)
            //->groupBy('id')
            //->selectRaw('id, sum(subtotal) as total, customer_name, status, created_at')
            //->orderBy('created_at')
            //->paginate(50);

         
$subQuery = Order::betweenDate($startDate, $endDate)
    ->groupBy('id')
    ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase');

$subQuery = DB::table(DB::raw("({$subQuery->toSql()}) as sub1"))
    ->mergeBindings($subQuery->getQuery())
    ->groupBy('customer_id')
    ->selectRaw('customer_id, customer_name, sum(totalPerPurchase) as total')
    ->orderBy('total', 'desc');

    DB::statement('set @row_num = 0;'); 
        $subQuery = DB::table(DB::raw("({$subQuery->toSql()}) as sub2")) 
        ->mergeBindings($subQuery)
        ->selectRaw(' @row_num:= @row_num+1 as row_num, 
        customer_id, 
        customer_name, 
        total'
);

$count = DB::table(DB::raw("({$subQuery->toSql()}) as sub3"))->mergeBindings($subQuery)->count(); 
$total = DB::table(DB::raw("({$subQuery->toSql()}) as sub4"))->mergeBindings($subQuery)->selectRaw('sum(total) as total')->get(); 
$total = $total[0]->total; // 構成比用 

$decile = ceil($count / 10); // 10分の1の件数を変数に入れる 
$bindValues = []; 
$tempValue = 0; 
for($i = 1; $i <= 10; $i++) 
{ 
    array_push($bindValues, 1 + $tempValue); 
    $tempValue += $decile;  
    array_push($bindValues, 1 + $tempValue);
}

DB::statement('set @row_num = 0;'); 
$subQuery = DB::table(DB::raw("({$subQuery->toSql()}) as sub5"))
    ->mergeBindings($subQuery)
    ->selectRaw(" 
            row_num, 
            customer_id, 
            customer_name,  
            total, 
            case  
                when ? <= row_num and row_num < ? then 1 
                when ? <= row_num and row_num < ? then 2 
                when ? <= row_num and row_num < ? then 3 
                when ? <= row_num and row_num < ? then 4 
                when ? <= row_num and row_num < ? then 5 
                when ? <= row_num and row_num < ? then 6 
                when ? <= row_num and row_num < ? then 7 
                when ? <= row_num and row_num < ? then 8 
                when ? <= row_num and row_num < ? then 9 
                when ? <= row_num and row_num < ? then 10 
            end as decile 
            ", $bindValues);

$subQuery = DB::table(DB::raw("({$subQuery->toSql()}) as sub6"))
->mergeBindings($subQuery)
->groupBy('decile') 
->selectRaw('decile,  
round(avg(total)) as average, sum(total) as totalPerGroup');

        return Inertia::render('Analysis');
    }
}
