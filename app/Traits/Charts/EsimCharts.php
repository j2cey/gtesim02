<?php

namespace App\Traits\Charts;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Status;
use App\Models\Esims\Esim;
use App\Models\Esims\StatutEsim;
use App\Models\Esims\ClientEsim;

trait EsimCharts
{
    use Charts, ChartjsData;

    public function getEsimsStatsYear($year, $agence) {
        $year_period = $this->getYearPeriod($year);
        return $this->getEsimsStatsPeriod($year_period, $agence);
    }

    public function getEsimsStatsMonth($month, $agence) {
        $month_period = $this->getMonthPeriod($month);
        return $this->getEsimsStatsPeriod($month_period, $agence);
    }

    public function getEsimsStatsWeek($week, $agence) {
        $week_period = $this->getWeekPeriod($week);
        return $this->getEsimsStatsPeriod($week_period, $agence);
    }

    public function getEsimsStatsPeriod($period, $selected_agence) {
        $esimsattribuees = $this->getStatsRaw($period, ($selected_agence == -1 ? -1 : [$selected_agence]) );

        // les agences/agents actifs de la période (les courbes)
        $statlabels_actifs = [];

        foreach ($esimsattribuees as $esim) {
            if ($esim && $esim->attributor && $esim->attributor->employe) {
                $attributor = $esim->attributor;
                // set statlabel data according to agence
                if ( $selected_agence == -1 ) {
                    $statlabels_actifs = $this->addLabel($esimsattribuees->count(),$statlabels_actifs,$attributor->employe->departement->id,$attributor->employe->departement->intitule);
                } else {
                    $statlabels_actifs = $this->addLabel($esimsattribuees->count(),$statlabels_actifs,$attributor->id,$attributor->username,$attributor->name);
                }
            } else {
                $statlabels_actifs = $this->addLabel($esimsattribuees->count(),$statlabels_actifs,0,$this->getIndefinedLabelLabel());
            }
        }

        $statlabels_actifs_colors_hex = [];

        foreach ($statlabels_actifs as $statlabel) {
            $statlabels_actifs_colors_hex[] = $statlabel['color']['hex'];
        }

        // remplissage des valeurs (les y)
        $valeurs_distribuees = $this->getDistributedValues($esimsattribuees,$statlabels_actifs,$period,$selected_agence);

        return [
            'period' => $period,
            'statlabels' => $statlabels_actifs,
            'statlabel_first' => $this->getLabelIndexByOrd($statlabels_actifs, 1, true),
            'statlabel_second' => $this->getLabelIndexByOrd($statlabels_actifs, 2, true),
            'statlabel_third' => $this->getLabelIndexByOrd($statlabels_actifs, 3, true),
            'statlabels_colors_hex' => $statlabels_actifs_colors_hex,
            'xvalues' => $period['xvalues'],
            'values_by_xvalue' => $valeurs_distribuees['by_xvalue'],
            'values_by_statlabel' => $valeurs_distribuees['by_statlabel'],
            'count' => $esimsattribuees->count(),
            'chartjsdata' => $this->getChartData($period['xvalues'],$statlabels_actifs,$valeurs_distribuees['by_statlabel'])
        ];
    }

    private function getXvaluesDays(Carbon $start, Carbon $end) {

        $start_str = $start->format("Y-m-d H:i:s");
        $end_str = $end->format("Y-m-d H:i:s");

        $curr_day = Carbon::createFromFormat("Y-m-d H:i:s", $start_str);
        $curr_day->hour(23)->minute(59)->second(59);
        $is_last_day = ($curr_day->year === $end->year && $curr_day->month === $end->month && $curr_day->day === $end->day);

        $xvalues[] = $curr_day->day . "|" . $curr_day->month;
        while ( ! $is_last_day ) {
            $curr_day->addDay();
            $xvalues[] = $curr_day->day . "|" . $curr_day->month;
            $is_last_day = ($curr_day->year === $end->year && $curr_day->month === $end->month && $curr_day->day === $end->day);
        }

        return $xvalues;
    }

    public function getFreePeriod($from, $to) {
        $start_ofPeriod = Carbon::createFromFormat("Y-m-d H:i:s", $from)->addDay();
        $start_ofPeriod->hour(0)->minute(0)->second(0);

        $end_ofPeriod = Carbon::createFromFormat("Y-m-d H:i:s", $to)->addDay();
        $end_ofPeriod->hour(23)->minute(59)->second(59);

        $xvalues = $this->getXvaluesDays($start_ofPeriod, $end_ofPeriod);

        return [
            'type' => "free",
            'xref' => "day",
            'xvalues' => $xvalues,
            'start' => $start_ofPeriod,
            'end' => $end_ofPeriod
        ];
    }

    public function getWeekPeriod($weeknumber) {
        $week_start = (new \DateTime())->setISODate(date("Y"),$weeknumber)->format("Y-m-d H:i:s");

        $start_ofWeek = Carbon::createFromFormat("Y-m-d H:i:s", $week_start);
        $start_ofWeek->hour(0)->minute(0)->second(0);
        $end_ofWeek = $start_ofWeek->copy()->endOfWeek();

        // xvalues de la période les x
        $xvalues = $this->getXvaluesDays($start_ofWeek, $end_ofWeek);

        return [
            'type' => "week",
            'xref' => "day",
            'xvalues' => $xvalues,
            'start' => $start_ofWeek,
            'end' => $end_ofWeek
        ];
    }

    private function getMonthPeriod($month) {
        $year = Carbon::now()->year;
        $date_str = "first day of " . $this->getMonthName($month) . " " . $year;
        $start_ofMonth = new Carbon($date_str);
        $end_ofMonth = (new Carbon($date_str))->endOfMonth();

        // xvalues de la période les x
        $xvalues = [];

        for ($i = $start_ofMonth->day; $i <= $end_ofMonth->day; $i++) {
            $xvalues[] = $i;
        }

        return [
            'type' => "month",
            'xref' => "day",
            'xvalues' => $xvalues,
            'start' => $start_ofMonth,
            'end' => $end_ofMonth,
        ];
    }

    public function getYearPeriod($year) {

        $date_str = "first day of january " . $year;
        $start_ofYear = new Carbon($date_str);
        $date_str = "first day of december " . $year;
        $end_ofYear = (new Carbon($date_str))->endOfMonth();

        // mois de la période les x
        $xvalues = [];

        for ($i = $start_ofYear->month; $i <= $end_ofYear->month; $i++) {
            $xvalues[] = $i;
        }

        return [
            'type' => "year",
            'xref' => "month",
            'xvalues' => $xvalues,
            'start' => $start_ofYear,
            'end' => $end_ofYear,
        ];
    }

    public function getStatsRaw($period, $agences_ids) {
        //$statutesim_attribue = StatutEsim::attribue()->first();

        $period_start = $period['start'];
        $period_end = $period['end'];

        // toutes les sims attribuées durant cette periode
        if ( $agences_ids == -1 ) {
            // without selected agence
            /*$esimsattribuees_req = Esim::with(['phonenum', 'phonenum.creator', 'phonenum.creator.employe.departement'])
              ->whereHas('statutesim', function ($query) use ($statutesim_attribue) {
                  $query->where('id', $statutesim_attribue->id);
              })
              ->whereHas('phonenum', function ($query) use ($period_start, $period_end) {
                  $query->whereBetween('created_at', [$period_start, $period_end]);
              });*/

            /*$esimsattribuees_req = PhoneNum::with(['creator', 'creator.employe.departement'])
                ->where('hasphonenum_type', ClientEsim::class)
                ->whereBetween('created_at', [$period_start, $period_end]);*/

            $esimsattribuees_req = Esim::with(['attributor', 'attributor.employe.departement'])
                ->whereBetween('attributed_at', [$period_start, $period_end])
            ;

        } else {
            // with selected agence

            /*$esimsattribuees_req = Esim::with(['phonenum','phonenum.creator','phonenum.creator.employe.departement'])
                ->whereHas('statutesim', function ($query) use ($statutesim_attribue) {
                    $query->where( 'id', $statutesim_attribue->id );
                })
                ->whereHas('phonenum', function ($query) use ($period_start, $period_end) {
                    $query->whereBetween('created_at', [$period_start, $period_end]);
                })
                ->whereHas('phonenum', function ($query) use ($agences_ids) {
                    $query->whereHas('creator', function ($query) use ($agences_ids) {
                        $query->whereHas('employe', function ($query) use ($agences_ids) {
                            $query->whereIn( 'departement_id', $agences_ids );
                        });
                    });
                });*/

            /*$esimsattribuees_req = PhoneNum::with(['creator', 'creator.employe.departement'])
                ->where('hasphonenum_type', ClientEsim::class)
                ->whereBetween('created_at', [$period_start, $period_end])
                ->whereHas('creator', function ($query) use ($agences_ids) {
                    $query->whereHas('employe', function ($query) use ($agences_ids) {
                        $query->whereIn( 'departement_id', $agences_ids );
                    });
                });*/

            $esimsattribuees_req = Esim::with(['attributor', 'attributor.employe.departement'])
                ->whereBetween('attributed_at', [$period_start, $period_end])
                ->whereHas('attributor', function ($query) use ($agences_ids) {
                    $query->whereHas('employe', function ($query) use ($agences_ids) {
                        $query->whereIn( 'departement_id', $agences_ids );
                    });
                });
        }

        return $esimsattribuees_req->get();

        /*return [
            'period_start' => $period_start,
            'period_end' => $period_end,
            'data' => $esimsattribuees_req->get()
        ];*/
    }

    public function getStatsRawByUserBuilder($agences_ids, $period = null, $searchQuery = null) {

        $users_req = User::with(['esimsattributed','esimsattributed.phonenum','employe','employe.departement'])
            ->whereHas('employe', function ($query) use ($agences_ids) {
                $query->whereIn( 'departement_id', $agences_ids );
            })
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                ;
            })
        ;
        if ( ! is_null($period) ) {
            $period_start = $period['start'];
            $period_end = $period['end'];

            $users_req->
            whereHas('esimsattributed', function ($query) use ($period_start, $period_end) {
                $query->whereBetween('attributed_at', [$period_start, $period_end]);
            });
        }

        return $users_req;
    }

    public function getStatsRawByUser($period, $agences_ids) {
        return $this->getStatsRawByUserBuilder($agences_ids, $period)->get();
    }

    private function getDistributedValues($esimsattribuees, $statlabels, $period, $selected_agence) {
        $distributed_values_byxvalue = [];
        $distributed_values_bystatlabel = [];

        // init by x values
        foreach ($period['xvalues'] as $xvalue) {
            $init_x = [];
            foreach ($statlabels as $key => $statlabel) {
                $init_x[$key] = 0;
            }
            $distributed_values_byxvalue[$xvalue] = $init_x;
        }

        // init by statlabel
        foreach ($statlabels as $key => $statlabel) {
            $init_statlabel = [];
            foreach ($period['xvalues'] as $xvalue) {
                $init_statlabel[$xvalue] = 0;
            }
            $distributed_values_bystatlabel[$key] = $init_statlabel;
        }

        foreach ($esimsattribuees as $index => $esim) {
            if ($period['type'] === "week") {
                $esim_xvalue = Carbon::parse($esim->attributed_at)->day . "|" . Carbon::parse($esim->attributed_at)->month;
            } else {
                $esim_xvalue = Carbon::parse($esim->attributed_at)->{$period['xref']};
            }

            if (array_key_exists($esim_xvalue, $distributed_values_byxvalue)) {

                if ($esim->attributor && $esim->attributor->employe) {
                    if ( $selected_agence == -1 ) {
                        $statlabel_idx = $esim->attributor->employe->departement->intitule;
                    } else {
                        $statlabel_idx = $esim->attributor->username;
                    }

                    $distributed_values_byxvalue[$esim_xvalue][$statlabel_idx]++;
                    $distributed_values_bystatlabel[$statlabel_idx][$esim_xvalue]++;
                } else {
                    $statlabel_idx = $this->getIndefinedLabelLabel();
                    $distributed_values_byxvalue[$esim_xvalue][$statlabel_idx]++;
                    $distributed_values_bystatlabel[$statlabels[$statlabel_idx]['label']][$esim_xvalue]++;
                }
            }
        }

        return [
            'by_xvalue' => $distributed_values_byxvalue,
            'by_statlabel' => $distributed_values_bystatlabel,
            ];
    }

    public function getEsimsStatsResume() {

      $status_active = Status::active()->first();

      $statutesim_libre = StatutEsim::nouveau()->first();
      $statutesim_attribue = StatutEsim::attribue()->first();

      $usersactive = User::whereHas('status', function ($query) use ($status_active) {
          $query->where( 'id', $status_active->id );
      })->get();

      $clientsesim = ClientEsim::with(['creator','creator.employe.departement'])->get();

      $esimslibres = Esim::whereHas('statutesim', function ($query) use ($statutesim_libre) {
            $query->where( 'id', $statutesim_libre->id );
      })->get();

      $esimsattribuees_req = Esim::with(['phonenum','phonenum.creator','phonenum.creator.employe.departement'])
            ->whereHas('statutesim', function ($query) use ($statutesim_attribue) {
            $query->where( 'id', $statutesim_attribue->id );
            })
            ->whereHas('phonenum');
      $esimsattribuees = $esimsattribuees_req->get();

      return [
          'activesusers' => $usersactive->count(),
          'clientsesim' => $clientsesim->count(),
          'libres' => $esimslibres->count(),
          'attribuees' => $esimsattribuees->count(),
      ];
  }

    public function getAgenceStatsResume($agence) {

        $status_active = Status::active()->first();
        $statutesim_attribue = StatutEsim::attribue()->first();

        $usersactive = User::whereHas('status', function ($query) use ($status_active) {
            $query->where( 'id', $status_active->id );
        })
            ->whereHas('employe', function ($query) use ($agence) {
                $query->where( 'departement_id', $agence );
            })
            ->get();

        $clientsesim = ClientEsim::with(['creator','creator.employe.departement'])
            ->whereHas('creator', function ($query) use ($agence) {
                $query->whereHas('employe', function ($query) use ($agence) {
                    $query->where( 'departement_id', $agence );
                });
            })
            ->get();

        $esimsattribuees_req = Esim::with(['phonenum','phonenum.creator','phonenum.creator.employe.departement'])
            ->whereHas('statutesim', function ($query) use ($statutesim_attribue) {
                $query->where( 'id', $statutesim_attribue->id );
            })
            ->whereHas('phonenum', function ($query) use ($agence) {
                $query->whereHas('creator', function ($query) use ($agence) {
                    $query->whereHas('employe', function ($query) use ($agence) {
                        $query->where( 'departement_id', $agence );
                    });
                });
            })
            ->whereHas('phonenum');
        $esimsattribuees = $esimsattribuees_req->get();

        return [
            'activesusers' => $usersactive->count(),
            'clientsesim' => $clientsesim->count(),
            'attribuees' => $esimsattribuees->count(),
        ];
    }
}
