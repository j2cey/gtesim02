<?php


namespace App\Traits\Charts;

trait ChartjsData
{
    public function getChartData($labels,$details,$datas) {
      $cdata = ['labels' => $labels,
          'datasets'=> []
      ];

      foreach ($datas as $key => $data) {
        $cdata['datasets'][] = [
          'label' => $details[$key]['label'],
          'borderColor' => $details[$key]['color']['rgb'],
          'borderWidth' => 2,
          'backgroundColor' => $details[$key]['color']['rgb'],
          'pointBackgroundColor' => $details[$key]['color']['rgb'],
          'fill' => false,
          'data' => $data
        ];
      }

      return $cdata;
    }

    private function getLabelsDisplays($lables) {
      $lables_displays = [];
      foreach ($lables as $label) {
          $lables_displays[] = $label['label'];
      }
    }
}
