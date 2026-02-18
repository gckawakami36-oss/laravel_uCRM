<?php  
namespace App\Services; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RfmService 
{ 
  public static function rfm($subQuery, $rfmPrms)
  {
    // RFM分析: Recency(最終購入日), Frequency(購入頻度), Monetary(購入金額)
    
    // デフォルト値の設定
    $defaultPrms = [
      'r' => [14, 28, 60, 90],  // Recency: 日数
      'f' => [7, 5, 3, 2],      // Frequency: 購入回数
      'm' => [300000, 200000, 100000, 30000] // Monetary: 金額
    ];
    
    // フロントエンドからの配列を連想配列に変換
    if (is_array($rfmPrms) && !isset($rfmPrms['r'])) {
      // 配列形式 [r0, r1, r2, r3, f0, f1, f2, f3, m0, m1, m2, m3]
      $rfmPrms = [
        'r' => array_slice($rfmPrms, 0, 4),
        'f' => array_slice($rfmPrms, 4, 4),
        'm' => array_slice($rfmPrms, 8, 4),
      ];
    }
    
    $rfmPrms = $rfmPrms ?? $defaultPrms;
    $rfmPrms['r'] = $rfmPrms['r'] ?? $defaultPrms['r'];
    $rfmPrms['f'] = $rfmPrms['f'] ?? $defaultPrms['f'];
    $rfmPrms['m'] = $rfmPrms['m'] ?? $defaultPrms['m'];
    
    $subQuery = $subQuery->groupBy('id')
      ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase, created_at');

    $subQuery = DB::table(DB::raw("({$subQuery->toSql()}) as sub1"))
      ->mergeBindings($subQuery->getQuery())
      ->groupBy('customer_id')
      ->selectRaw('customer_id, customer_name, 
        MAX(created_at) as recent_date,
        DATEDIFF(NOW(), MAX(created_at)) as recency,
        COUNT(customer_id) as frequency,
        SUM(totalPerPurchase) as monetary')
      ->orderBy('recency', 'asc');

    // RFMランクの設定
    $data = $subQuery->get();
    
    foreach ($data as $item) {
      // Rランク (Recency)
      if ($item->recency <= $rfmPrms['r'][0]) {
        $item->r_rank = 5;
      } elseif ($item->recency <= $rfmPrms['r'][1]) {
        $item->r_rank = 4;
      } elseif ($item->recency <= $rfmPrms['r'][2]) {
        $item->r_rank = 3;
      } elseif ($item->recency <= $rfmPrms['r'][3]) {
        $item->r_rank = 2;
    } else {
      $item->r_rank = 1;
    }

    // Fランク (Frequency)
      if ($item->frequency >= $rfmPrms['f'][0]) {
        $item->f_rank = 5;
      } elseif ($item->frequency >= $rfmPrms['f'][1]) {
        $item->f_rank = 4;
      } elseif ($item->frequency >= $rfmPrms['f'][2]) {
        $item->f_rank = 3;
      } elseif ($item->frequency >= $rfmPrms['f'][3]) {
        $item->f_rank = 2;
      } else {
        $item->f_rank = 1;
      }

      // Mランク (Monetary)
      if ($item->monetary >= $rfmPrms['m'][0]) {
        $item->m_rank = 5;
      } elseif ($item->monetary >= $rfmPrms['m'][1]) {
        $item->m_rank = 4;
      } elseif ($item->monetary >= $rfmPrms['m'][2]) {
        $item->m_rank = 3;
      } elseif ($item->monetary >= $rfmPrms['m'][3]) {
        $item->m_rank = 2;
      } else {
        $item->m_rank = 1;
      }

      $item->total_rank = $item->r_rank + $item->f_rank + $item->m_rank;
    }

    // 各ランクの数をカウント
    $eachCount = [
      'r' => [
        5 => $data->where('r_rank', 5)->count(),
        4 => $data->where('r_rank', 4)->count(),
        3 => $data->where('r_rank', 3)->count(),
        2 => $data->where('r_rank', 2)->count(),
        1 => $data->where('r_rank', 1)->count(),
      ],
      'f' => [
        5 => $data->where('f_rank', 5)->count(),
        4 => $data->where('f_rank', 4)->count(),
        3 => $data->where('f_rank', 3)->count(),
        2 => $data->where('f_rank', 2)->count(),
        1 => $data->where('f_rank', 1)->count(),
      ],
      'm' => [
        5 => $data->where('m_rank', 5)->count(),
        4 => $data->where('m_rank', 4)->count(),
        3 => $data->where('m_rank', 3)->count(),
        2 => $data->where('m_rank', 2)->count(),
        1 => $data->where('m_rank', 1)->count(),
      ],
    ];
       Log::debug($subQuery->get());

    // RとFのクロス集計（ranksテーブルを使用）
    $ranks = DB::table('ranks')->orderBy('rank', 'desc')->pluck('rank');
    
    $rfCrossTable = [];
    foreach ($ranks as $rRank) {
        $row = ['r_rank' => $rRank];
        foreach ($ranks as $fRank) {
            $count = $data->where('r_rank', $rRank)->where('f_rank', $fRank)->count();
            $row["f_{$fRank}"] = $count;
        }
        $rfCrossTable[] = $row;
    }

    $totals = [
      'totalCount' => $data->count(),
      'totalRecency' => $data->avg('recency'),
      'totalFrequency' => $data->sum('frequency'),
      'totalMonetary' => $data->sum('monetary'),
    ];

    return [$data, $totals, $eachCount, $rfCrossTable]; 
  }
}
