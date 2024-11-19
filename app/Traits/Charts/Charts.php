<?php


namespace App\Traits\Charts;

trait Charts
{
  public function getIndefinedLabelLabel() {
    return "Indéfinie";
  }
  //$agences_actives = $this->addLabel($esimsattribuees['data']->count(),$agences_actives,$creator->employe->departement->id,$creator->employe->departement->intitule);
  public function addLabel($total_values,$labels_array,$id,$label,$label_full = null) {
      foreach ($labels_array as $index => $label_item) {
          if ($label_item['value'] === $id) {
              $labels_array[$index]['count']++;
              $labels_array[$index]['rate'] = $this->getRate($total_values,$labels_array[$index]['count']);
              return $this->getOrderLabels($labels_array);
          }
      }
      //$color_index = count($agences_array) > count($colors) ? 0 : count($agences_array);
      $labels_array[$label] = [
          'value' => $id,
          'label' => $this->getShortLabelLabel($label),
          'label_full' => is_null($label_full) ? $label : $label_full,
          'count' => 1,
          'ord' => count($labels_array) + 1,
          'rate' =>  $this->getRate($total_values,1),
          'color' => $this->getNextColor(count($labels_array))
      ];
      return $labels_array;
  }

    /**
     * Re-order labels by descending count
     * @param $labels_array
     * @return mixed
     */
  private function getOrderLabels($labels_array) {

      for ($i = 1; $i <= count($labels_array); $i++) {
          if ($i > 1) {
              $currindex = $this->getLabelIndexByOrd($labels_array, $i);
              $previndex = $this->getLabelIndexByOrd($labels_array, $i - 1);
              if ($labels_array[$currindex]['count'] > $labels_array[$previndex]['count']) {
                  $labels_array[$currindex]['ord'] = $i - 1;
                  $labels_array[$previndex]['ord'] = $i;
              }
          }
      }

      return $labels_array;
  }

    /**
     * Get the label which is in an order
     * @param $labels_array
     * @param $ord
     * @param bool $getlabel
     * @return int|string|null
     */
  public function getLabelIndexByOrd($labels_array, $ord, $getlabel = false) {
      foreach ($labels_array as $index => $label_item) {
          if ( $labels_array[$index]['ord'] === $ord ) {
              if ($getlabel) {
                  return $label_item;
              }
              return  $index;
          }
      }

      return null;
  }

  private function getMonthName($monthNum) {
    $month_arr = [
        '1' => "January",
        '2' => "February",
        '3' => "March",
        '4' => "April",
        '5' => "May",
        '6' => "June",
        '7' => "July",
        '8' => "August",
        '9' => "September",
        '10' => "October",
        '11' => "November",
        '12' => "December",
    ];
    return $month_arr[$monthNum];
  }

  public function getNextColor($label_count) {
    $colors = [
        ['hue' => 0, 'hex' => "#ff0000", 'rgb' => "rgb(255, 0, 0)", 'hsl' => "hsl(0, 100%, 50%)"],
        ['hue' => 45, 'hex' => "#ffff00", 'rgb' => "rgb(255, 255, 0)", 'hsl' => "hsl(60, 100%, 50%)"],
        ['hue' => 105, 'hex' => "#00ff00", 'rgb' => "rgb(0, 255, 0)", 'hsl' => "hsl(120, 100%, 50%)"],
        //['hue' => 90, 'hex' => "#40ff00", 'rgb' => "rgb(64, 255, 0)", 'hsl' => "hsl(105, 100%, 50%)"],
        //['hue' => 135, 'hex' => "#00ff40", 'rgb' => "rgb(0, 255, 64)", 'hsl' => "hsl(135, 100%, 50%)"],
        ['hue' => 180, 'hex' => "#00ffff", 'rgb' => "rgb(0, 255, 255)", 'hsl' => "hsl(180, 100%, 50%)"],
        //['hue' => 75, 'hex' => "#80ff00", 'rgb' => "rgb(128, 255, 0)", 'hsl' => "hsl(90, 100%, 50%)"],
        ['hue' => 225, 'hex' => "#0040ff", 'rgb' => "rgb(0, 64, 255)", 'hsl' => "hsl(225, 100%, 50%)"],
        //['hue' => 60, 'hex' => "#bfff00", 'rgb' => "rgb(191, 255, 0)", 'hsl' => "hsl(75, 100%, 50%)"],
        ['hue' => 270, 'hex' => "#8000ff", 'rgb' => "rgb(128, 0, 255)", 'hsl' => "hsl(270, 100%, 50%)"],
        ['hue' => 30, 'hex' => "#ff8000", 'rgb' => "rgb(255, 191, 0)", 'hsl' => "hsl(45, 100%, 50%)"],
        ['hue' => 315, 'hex' => "#ff00bf", 'rgb' => "rgb(255, 0, 191)", 'hsl' => "hsl(315, 100%, 50%)"],
        ['hue' => 15, 'hex' => "#ff4000", 'rgb' => "rgb(255, 64, 0)", 'hsl' => "hsl(15, 100%, 50%)"],
        ['hue' => 120, 'hex' => "#00ff40", 'rgb' => "rgb(0, 255, 0)", 'hsl' => "hsl(135, 100%, 50%)"],
    ];

    $color_index = $label_count >= count($colors) ? 0 : $label_count;

    return $colors[$color_index];
  }

  private function getRate($total,$val) {
      return round(( $val / $total ) * 100, 2);
  }

  private function getLabelIndex($labels,$id) {
      foreach ($labels as $index => $label) {
          if ($label['value'] === $id) {
              return $index;
          }
      }
      return -1;
  }

  private function getShortLabelLabel($label) {
    $search  = array('Agence', 'Principale', 'Grand', 'Compte', 'Entreprise', 'Service', 'Commerciale');
    $replace = array('', 'Pple', 'Grd', 'Cmpt', 'Entpse', 'Sce', 'Ccle');
    //$replace = array('Agce', 'Pple', 'Grd', 'Cmpt', 'Entpse', 'Sce', 'Ccle');
    return str_replace($search, $replace, $label);
  }
}
