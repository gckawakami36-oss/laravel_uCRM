<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Services\AnalysisService;
use App\Services\DecileService;
use App\Services\RfmService;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $subQuery = Order::betweenDate($request->startDate, $request->endDate); 
        
        if($request->type === 'perDay') 
        { 
            list($data, $labels, $totals) = AnalysisService::perDay($subQuery); 
            
            return response()->json([ 
                'data' => $data, 
                'type' => $request->type, 
                'labels' => $labels, 
                'totals' => $totals 
            ], Response::HTTP_OK);
        }

        if($request->type === 'perMonth') 
        { 
            list($data, $labels, $totals) = AnalysisService::perMonth($subQuery); 
            
            return response()->json([ 
                'data' => $data, 
                'type' => $request->type, 
                'labels' => $labels, 
                'totals' => $totals 
            ], Response::HTTP_OK);
        } 
        
        if($request->type === 'perYear') 
        { 
            list($data, $labels, $totals) = AnalysisService::perYear($subQuery); 

            return response()->json([ 
                'data' => $data, 
                'type' => $request->type, 
                'labels' => $labels, 
                'totals' => $totals 
            ], Response::HTTP_OK);
        }

        if($request->type === 'decile') 
        { 
            $data = DecileService::decile($subQuery); 
            
            return response()->json([ 
                'data' => $data, 
                'type' => $request->type
            ], Response::HTTP_OK);
        }
        if($request->type === 'rfm') 
        { 
            list($data, $totals, $eachCount, $rfCrossTable) = RfmService::rfm($subQuery, $request->rfmPrms); 
            return response()->json([ 
                'data' => $data, 
                'type' => $request->type, 
                'eachCount' => $eachCount, 
                'totals' => $totals,
                'rfCrossTable' => $rfCrossTable, 
            ], Response::HTTP_OK);
        }
    }
}
