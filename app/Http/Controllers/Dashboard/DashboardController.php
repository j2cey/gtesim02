<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Traits\Charts\EsimCharts;
use App\Http\Controllers\Controller;
use App\Models\Employes\Departement;
use App\Http\Requests\Dashboard\PostDashboardDetailsRequest;

class DashboardController extends Controller
{
    use EsimCharts;

    public function index() {
        return view('dashboards.esims');
    }

    public function detailsget(PostDashboardDetailsRequest $request) {
        //dd(request('period'), request('departement'), request('query'), request()->all(), request());
        /*if ( ! is_null(request('period')) ) {

        } else {
            $period = null;
        }*/
        return $this->getStatsRawByUserBuilder([$request->departement->id], $request->period, $request->searchQuery)->latest()
            ->paginate(10);
    }

    public function detailspost(PostDashboardDetailsRequest $request) {
        //dd($request->all());
        // T23:00:00.000Z
        //dd( $request->all(), str_replace(["T23:",".000Z"], [" ",":00"],$request->period_from), str_replace(["T23:",".000Z"], [" ",":00"],$request->period_to));
        //$freePeriod = $this->getFreePeriod( str_replace(["T23:",".000Z"], [" ",":00"],$request->period_from), str_replace(["T23:",".000Z"], [" ",":00"],$request->period_to) );

        //$agences_ids = [58];

        //dd($request->period, $request->departement, $request->all());

        $data = $this->getStatsRawByUserBuilder([$request->departement->id], $request->period)->latest()
            ->paginate(10);
        //dd($request->all(), $data);
        return [
            'departement' => $request->departement,
            'period' => $request->period ? [$request->period['start']->format('Y-m-d H:i:s'),$request->period['end']->format('Y-m-d H:i:s')] : null ,//? $request->period['start']->format('d-m-Y') . " ~ " . $request->period['end']->format('d-m-Y') : null,
            'statresults' => $data
        ];
    }

    public function fetchmonthsofyear() {
        $monthsofyear = [
            ['label'=> "Janvier", 'value' => 1],
            ['label'=> "Fevrier", 'value' => 2],
            ['label'=> "Mars", 'value' => 3],
            ['label'=> "Avril", 'value' => 4],
            ['label'=> "Mai", 'value' => 5],
            ['label'=> "Juin", 'value' => 6],
            ['label'=> "Juillet", 'value' => 7],
            ['label'=> "Aout", 'value' => 8],
            ['label'=> "Septembre", 'value' => 9],
            ['label'=> "Octobre", 'value' => 10],
            ['label'=> "Novembre", 'value' => 11],
            ['label'=> "Décembre", 'value' => 12],
        ];
        return $monthsofyear;
    }

    public function fetchcurrentmonth() {
        $monthindex = date('m') - 1 ;
        $monthsofyear = $this->fetchmonthsofyear();
        $currentmonth = $monthsofyear[$monthindex];

        return $currentmonth;
    }

    public function fetchweeksofyear() {
        $weeksofyear = [];
        for ($i = 1; $i < 53; $i++) {
            $weeksofyear[] = ['label'=> "Semaine " . $i, 'value' => $i];
        }
        return $weeksofyear;
    }

    public function fetchcurrentweek() {
        $weekindex = date('W') - 1 ;
        $weeksofyear = $this->fetchweeksofyear();
        $currentweek = $weeksofyear[$weekindex];

        return $currentweek;
    }

    public function fetchyears() {
        $years = [
            ['label'=> "2022", 'value' => 2022],
            ['label'=> "2023", 'value' => 2023],
            ['label'=> "2024", 'value' => 2024],
        ];
        return $years;
    }

    public function fetchcurrentyear() {
        $year = date('Y') ;

        return ['label'=> $year, 'value' => $year];
    }


    public function fetchrawstats() {
        $esims_stats_resume = $this->getEsimsStatsResume();
        return $esims_stats_resume;
    }

    public function fetchagencestats($agence)
    {
        if (is_null($agence)) {
            return null;
        } else {
            $agence_stats_resume = $this->getAgenceStatsResume($agence);
            return $agence_stats_resume;
        }
    }

    public function fetchweekstats($week, $agence) {
        $esims_stats_week = $this->getEsimsStatsWeek($week, $agence);
        return $esims_stats_week;
    }

    public function fetchmonthstats($month, $agence) {
        $esims_stats_month = $this->getEsimsStatsMonth($month, $agence);
        return $esims_stats_month;
    }

    public function fetchyearstats($year, $agence) {
        $esims_stats_year = $this->getEsimsStatsYear($year, $agence);
        return $esims_stats_year;
    }
}
