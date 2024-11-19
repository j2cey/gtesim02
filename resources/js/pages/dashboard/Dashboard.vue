<script setup>
import { computed, onMounted, ref, onBeforeMount } from 'vue';
import Multiselect from "vue-multiselect";
import axios from "axios";
import { useAbility } from "@casl/vue";

import monthLineChart from "../dashboard/LineChart.vue";
import weekLineChart from "../dashboard/LineChart.vue";
import yearLineChart from "../dashboard/LineChart.vue";
import { useAuthUserStore } from '../../stores/AuthUserStore.js';

//const monthLineChart = () => import("../dashboard/LineChart.vue");
//const weekLineChart = () => import("../dashboard/LineChart.vue");
//const yearLineChart = () => import("../dashboard/LineChart.vue");

const authUserStore = useAuthUserStore();
const { can, cannot } = useAbility();

//<editor-fold desc="Data">
const rawstats = ref(null);
const agencestats = ref(null);

const monthsofyear = ref([]);
const currentmonth = ref(null);
const monthstats = ref(null);

const weeksofyear = ref([]);
const currentweek = ref(null);
const weekstats = ref(null);

const yearslist = ref([]);
const currentyear = ref(null);
const yearstats = ref(null);

const month_key = ref(0);
const week_key = ref(0);
const year_key = ref(0);

const departements = ref([]);
const currdepartement = ref(null);

const month_chart_data_key = ref('_month_chart_data_' + 0);
const week_chart_data_key = ref('_week_chart_data_' + 0);
const year_chart_data_key = ref('_year_chart_data_' + 0);

const weekstats_loading = ref(true);
const monthstats_loading = ref(true);
const yearstats_loading = ref(true);
//</editor-fold>

//<editor-fold desc="Data">
const forceRerenderMonthLineChart = () => {
    month_key.value += 1;
    month_chart_data_key.value = '_month_chart_data_' + month_key.value;
};

const forceRerenderWeekLineChart = () => {
    week_key.value += 1;
    week_chart_data_key.value = '_week_chart_data_' + week_key.value;
};

const forceRerenderYearLineChart = () => {
    year_key.value += 1;
    year_chart_data_key.value = '_year_chart_data_' + year_key.value;
};

const inityearstats = () => {
    fetchcurrentyear(true);
    fetchyears();
};

const initmonthstats = () => {
    console.log("initmonthstats");
    fetchcurrentmonth(true);
    fetchmonthsofyear();
};

const initweekstats = () => {
    fetchcurrentweek(true);
    fetchweeksofyear();
};

const initrawstats = () => {
    fetchrawstats();
};

const clearSelectedAgence = () => {

    if ( currdepartement.value !== null ) {
        currdepartement.value = null
        fetchallstats();
    }

};

const fetchrawstats = () => {
    axios.get('/api/dashboards.fetchrawstats')
        .then(({data}) => {
            console.log("rawstats: ", data);
            rawstats.value = data
            return data
        });
};

const fetchagencestats = () => {
    let agence = currdepartement.value ? currdepartement.value.id : -1
    axios.get('/api/dashboards.fetchagencestats/' + agence)
        .then(({data}) => {
            //console.log("rawstats: ", data);
            agencestats.value = data
            return data
        });
};

const fetchmonthsofyear = () => {
    axios.get('/api/dashboards.fetchmonthsofyear')
        .then(({data}) => {
            console.log("monthsofyear: ", data);
            monthsofyear.value = data
            return data
        });
};
const fetchcurrentmonth = async (fetchstats) => {
    await axios.get('/api/dashboards.fetchcurrentmonth')
        .then(({data}) => {
            console.log("currentmonth: ", data);
            currentmonth.value = data //{ label: "Juillet", value: 7 }
            if (fetchstats) {
                fetchmonthstats();
            }
            return data
        });
};
const fetchmonthstats = () => {
    monthstats_loading.value = true
    console.log("fetchmonthstats currentmonth.value: ", currentmonth.value);
    console.log("fetchmonthstats currdepartement.value: ", currdepartement.value);
    let month = currentmonth.value.value
    console.log("fetchmonthstats month: ",month);
    let agence = currdepartement.value ? currdepartement.value.id : -1

    axios.get('/api/dashboards.fetchmonthstats/' + month + '/' + agence)
        .then(({data}) => {
            monthstats_loading.value = false
            //console.log("monthstats (" + month + "): ", data);
            monthstats.value = data
            forceRerenderMonthLineChart()
            return data
        });
};

const fetchweeksofyear = () => {
    axios.get('/api/dashboards.fetchweeksofyear')
        .then(({data}) => {
            //console.log("weeksofyear: ", data);
            weeksofyear.value = data
            return data
        });
};
const fetchcurrentweek = async (fetchstats) => {
    await axios.get('/api/dashboards.fetchcurrentweek')
        .then(({data}) => {
            console.log("currentweek: ", data);
            currentweek.value = data
            if (fetchstats) {
                fetchweekstats()
            }
            return data
        });
};
const fetchweekstats = () => {
    weekstats_loading.value = true;

    let week = currentweek.value.value;
    let agence = currdepartement.value ? currdepartement.value.id : -1;

    axios.get('/api/dashboards.fetchweekstats/' + week + '/' + agence)
        .then(({data}) => {
            weekstats_loading.value = false;

            //console.log("monthstats (" + month + "): ", data);
            weekstats.value = data;
            forceRerenderWeekLineChart();

            return data
        });
};

const fetchyears = () => {
    axios.get('/api/dashboards.fetchyears')
        .then(({data}) => {
            yearslist.value = data
            return data
        });
};
const fetchcurrentyear = async (fetchstats) => {
    await axios.get('/api/dashboards.fetchcurrentyear')
        .then(({data}) => {
            console.log("current year: ", data)
            currentyear.value = data
            if (fetchstats) {
                fetchyearstats()
            }
            return data
        });
};
const fetchyearstats = () => {
    yearstats_loading.value = true

    let year = currentyear.value.value;
    let agence = currdepartement.value ? currdepartement.value.id : -1

    axios.get('/api/dashboards.fetchyearstats/' + year + '/' + agence)
        .then(({data}) => {
            yearstats_loading.value = false

            yearstats.value = data
            forceRerenderYearLineChart()
            return data
        });
};

const fetchallstats = () => {
    fetchagencestats()

    fetchweekstats()
    fetchmonthstats()
    fetchyearstats()
};

const getChartData = (chartjsdata) => {
    let cdata = null;

    if (chartjsdata) {
        cdata = {
            labels: chartjsdata.labels,
            datasets: []
        };

        var length = chartjsdata.datasets.length;
        for (var i = 0; i < length; i++) {
            // Do something with yourArray[i].
            cdata.datasets[i] = {

                pointBorderWidth: 0,
                pointRadius: 1,

                'label': chartjsdata.datasets[i].label,

                'backgroundColor': chartjsdata.datasets[i].backgroundColor,
                'borderColor': chartjsdata.datasets[i].borderColor,
                'borderWidth': chartjsdata.datasets[i].borderWidth,
                'fill': chartjsdata.datasets[i].fill,

                pointBackgroundColor: 'black',
                pointBorderColor: chartjsdata.datasets[i].backgroundColor,

                'data': Object.values( chartjsdata.datasets[i].data )
            }
        }
    }

    return cdata;
};

const getDepartements = () => {
    axios.get(`/api/departements/all`)
        .then((response) => {
            console.log("getDepartements, response: ", response)
            departements.value = response.data;
        }).then(() => {

        }
    )
};

//</editor-fold>

//<editor-fold desc="Computed">

const monthChartData = computed(() => {
    return monthstats.value ? getChartData( monthstats.value.chartjsdata ) : null;
});
const weekChartData = computed(() => {
    return weekstats.value ? getChartData( weekstats.value.chartjsdata ) : null;
});
const yearChartData = computed(() => {
    return yearstats.value ? getChartData( yearstats.value.chartjsdata ) : null;
});
const agenceHasStats = computed(() => {
    return !!(currdepartement.value && agencestats.value);
});
const attribueesAgenceRate = computed(() => {
    return agencestats.value && rawstats.value ? parseFloat( agencestats.value.attribuees / rawstats.value.attribuees ).toFixed(2) : null;
});
const clientsesimAgenceRate = computed(() => {
    return agencestats.value && rawstats.value ? parseFloat( agencestats.value.clientsesim / rawstats.value.clientsesim ).toFixed(2) : null;
});
const activesusersAgenceRate = computed(() => {
    return agencestats.value && rawstats.value ? parseFloat( agencestats.value.activesusers / rawstats.value.activesusers ).toFixed(2) : null;
});
const topPerformsLabel = computed(() => {
    return agenceHasStats.value ? "Top Agents" : "Top Agences";
});
const someFetchsAreLoading = computed(() => {
    return weekstats_loading.value || monthstats_loading.value || yearstats_loading.value;
});

//</editor-fold>

onBeforeMount(() => {
    if ( authUserStore.user.name !== '' ) {
        getDepartements();
    }
})

onMounted(() => {
    if ( authUserStore.user.name !== '' ) {
        initmonthstats();
        initweekstats();
        initrawstats();

        inityearstats();
    }
});
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $t('homepage.welcome') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">

            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">eSIM Libres</span>
                                <span class="info-box-number">{{ rawstats ? rawstats.libres : 0 }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">eSIM affectées</span>
                                <span class="info-box-number">{{ rawstats ? rawstats.attribuees : 0 }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <!-- fix for small devices only -->
                    <div class="clearfix hidden-md-up"></div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Clients eSIM</span>
                                <span class="info-box-number">{{ rawstats ? rawstats.clientsesim : 0 }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Utilisateurs actifs</span>
                                <span class="info-box-number">{{ rawstats ? rawstats.activesusers : 0 }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="row mb-3 no-gutters" style="padding: 0 !important;">
                            <div class="col-10">
                                <multiselect
                                    id="m_select_departement"
                                    v-model="currdepartement"
                                    selected.sync="departements"
                                    value=""
                                    :options="departements"
                                    :searchable="true"
                                    :multiple="false"
                                    label="intitule"
                                    track-by="id"
                                    key="id"
                                    placeholder="Agence"
                                    @update:modelValue="fetchallstats"
                                    :disabled="someFetchsAreLoading"
                                >
                                </multiselect>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-default" @click="clearSelectedAgence">
                                    <div v-if="someFetchsAreLoading" class="spinner-border spinner-border-sm" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <span v-else><i class="fa fa-times"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3" v-if="agenceHasStats">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="far fa-thumbs-up"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text text-sm">eSIM affectées <small>(agence)</small> </span>
                                <span class="info-box-number">{{ agencestats ? agencestats.attribuees : 0 }} <span class="badge badge-light">{{ attribueesAgenceRate }}%</span> </span>

                                <div class="progress">
                                    <div class="progress-bar" :style=" 'width:' + attribueesAgenceRate + '%;'"></div>
                                </div>
                                <span class="progress-description"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <!-- fix for small devices only -->
                    <div class="clearfix hidden-md-up" v-if="agenceHasStats"></div>

                    <div class="col-12 col-sm-6 col-md-3" v-if="agenceHasStats">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text text-sm">Clients eSIM <small>(agence)</small> </span>
                                <span class="info-box-number">{{ agencestats ? agencestats.clientsesim : 0 }} <span class="badge badge-light">{{ clientsesimAgenceRate }}%</span> </span>

                                <div class="progress">
                                    <div class="progress-bar" :style=" 'width:' + clientsesimAgenceRate + '%;'"></div>
                                </div>
                                <span class="progress-description"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-12 col-sm-6 col-md-3" v-if="agenceHasStats">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fa fa-users"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text text-sm">Utilisateurs actifs <small>(agence)</small> </span>
                                <span class="info-box-number">{{ agencestats ? agencestats.activesusers : 0 }} <span class="badge badge-light">{{ activesusersAgenceRate }}%</span> </span>

                                <div class="progress">
                                    <div class="progress-bar" :style=" 'width:' + activesusersAgenceRate + '%;'"></div>
                                </div>
                                <span class="progress-description">
                                    <router-link  class="small-box-footer" v-if="can('dashboards-agence-show')" :to="{
                                        name: 'dashboard.agence',
                                        params: {
                                            agenceid: currdepartement.uuid
                                        }
                                    }">
                                        Plus de details <i class="fas fa-arrow-circle-right"></i>
                                    </router-link>
                        </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-md-6 col-sm-8 col-12">
                                    <h5 class="card-title">Recap Hebdomadaire <small> {{ agenceHasStats ? " (" + currdepartement?.intitule + ")" : "" }} </small> </h5>
                                </div>

                                <div class="card-tools col-md-6 col-sm-4 col-12 text-right">
                                    <div class="btn-group">
                                        <multiselect
                                            id="m_select_week"
                                            v-model="currentweek"
                                            selected.sync="weeksofyear"
                                            value=""
                                            :options="weeksofyear"
                                            :searchable="true"
                                            :multiple="false"
                                            label="label"
                                            track-by="value"
                                            key="value"
                                            placeholder="Semaine"
                                            @update:modelValue="fetchweekstats"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-center">
                                            <span class="text text-sm text-bold">Affectations Hebdo</span>
                                        </p>

                                        <div class="chart">
                                            <week-line-chart :key="week_chart_data_key" chartId="weekLineStats" :chartData="weekChartData"></week-line-chart>
                                        </div>
                                        <!-- /.chart-responsive -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-4">
                                        <p class="text-center">
                                            <span class="text text-sm text-bold">{{ topPerformsLabel }}</span>
                                        </p>

                                        <div class="progress-group" v-if="weekstats && weekstats.statlabel_first">
                                            <span class="progress-text text text-xs">{{ weekstats.statlabel_first.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ weekstats.statlabel_first.count }}</b>/{{ weekstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + weekstats.statlabel_first.rate + '%; background-color:' + weekstats.statlabel_first.color.hex "></div>
                                            </div>
                                        </div>

                                        <div class="progress-group" v-if="weekstats && weekstats.statlabel_second">
                                            <span class="progress-text text text-xs">{{ weekstats.statlabel_second.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ weekstats.statlabel_second.count }}</b>/{{ weekstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + weekstats.statlabel_second.rate + '%; background-color:' + weekstats.statlabel_second.color.hex "></div>
                                            </div>
                                        </div>

                                        <div class="progress-group" v-if="weekstats && weekstats.statlabel_third">
                                            <span class="progress-text text text-xs">{{ weekstats.statlabel_third.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ weekstats.statlabel_third.count }}</b>/{{ weekstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + weekstats.statlabel_third.rate + '%; background-color:' + weekstats.statlabel_third.color.hex "></div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- ./card-body -->
                            <div v-if="weekstats_loading" class="overlay dark">
                                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-md-6 col-sm-8 col-12">
                                    <h5 class="card-title">Recap Mensuel <small> {{ agenceHasStats ? " (" + currdepartement?.intitule + ")" : "" }} </small> </h5>
                                </div>

                                <div class="card-tools col-md-6 col-sm-4 col-12 text-right">
                                    <div class="btn-group">
                                        <multiselect
                                            id="m_select_month"
                                            v-model="currentmonth"
                                            selected.sync="monthsofyear"
                                            value=""
                                            :options="monthsofyear"
                                            :searchable="true"
                                            :multiple="false"
                                            label="label"
                                            track-by="value"
                                            key="value"
                                            placeholder="Mois"
                                            @update:modelValue="fetchmonthstats"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-center">
                                            <span class="text text-sm text-bold">Affectations Mensuelles</span>
                                        </p>

                                        <div class="chart">
                                            <month-line-chart :key="month_chart_data_key" chartId="monthLineStats" :chartData="monthChartData"></month-line-chart>
                                        </div>
                                        <!-- /.chart-responsive -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-4">
                                        <p class="text-center">
                                            <span class="text text-sm text-bold">{{ topPerformsLabel }}</span>
                                        </p>

                                        <div class="progress-group" v-if="monthstats && monthstats.statlabel_first">
                                            <span class="progress-text text text-xs">{{ monthstats.statlabel_first.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ monthstats.statlabel_first.count }}</b>/{{ monthstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + monthstats.statlabel_first.rate + '%; background-color:' + monthstats.statlabel_first.color.hex "></div>
                                            </div>
                                        </div>

                                        <div class="progress-group" v-if="monthstats && monthstats.statlabel_second">
                                            <span class="progress-text text text-xs">{{ monthstats.statlabel_second.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ monthstats.statlabel_second.count }}</b>/{{ monthstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + monthstats.statlabel_second.rate + '%; background-color:' + monthstats.statlabel_second.color.hex "></div>
                                            </div>
                                        </div>

                                        <div class="progress-group" v-if="monthstats && monthstats.statlabel_third">
                                            <span class="progress-text text text-xs">{{ monthstats.statlabel_third.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ monthstats.statlabel_third.count }}</b>/{{ monthstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + monthstats.statlabel_third.rate + '%; background-color:' + monthstats.statlabel_third.color.hex "></div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- ./card-body -->
                            <div v-if="monthstats_loading" class="overlay dark">
                                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-md-6 col-sm-8 col-12">
                                    <h5 class="card-title">Recap Annuel <small> {{ agenceHasStats ? " (" + currdepartement?.intitule + ")" : "" }} </small> </h5>
                                </div>

                                <div class="card-tools col-md-6 col-sm-4 col-12 text-right">
                                    <div class="btn-group">
                                        <multiselect
                                            id="m_select_year"
                                            v-model="currentyear"
                                            selected.sync="yearslist"
                                            value=""
                                            :options="yearslist"
                                            :searchable="true"
                                            :multiple="false"
                                            label="label"
                                            track-by="value"
                                            key="value"
                                            placeholder="Années"
                                            @update:modelValue="fetchyearstats"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-center">
                                            <span class="text text-sm text-bold">Affectations Annuelles</span>
                                        </p>

                                        <div class="chart">
                                            <year-line-chart :key="year_chart_data_key" chartId="yearLineStats" :chartData="yearChartData"></year-line-chart>
                                        </div>
                                        <!-- /.chart-responsive -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-4">
                                        <p class="text-center">
                                            <span class="text text-sm text-bold">{{ topPerformsLabel }}</span>
                                        </p>

                                        <div class="progress-group" v-if="yearstats && yearstats.statlabel_first">
                                            <span class="progress-text text text-xs">{{ yearstats.statlabel_first.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ yearstats.statlabel_first.count }}</b>/{{ yearstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + yearstats.statlabel_first.rate + '%; background-color:' + yearstats.statlabel_first.color.hex "></div>
                                            </div>
                                        </div>

                                        <div class="progress-group" v-if="yearstats && yearstats.statlabel_second">
                                            <span class="progress-text text text-xs">{{ yearstats.statlabel_second.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ yearstats.statlabel_second.count }}</b>/{{ yearstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + yearstats.statlabel_second.rate + '%; background-color:' + yearstats.statlabel_second.color.hex "></div>
                                            </div>
                                        </div>

                                        <div class="progress-group" v-if="yearstats && yearstats.statlabel_third">
                                            <span class="progress-text text text-xs">{{ yearstats.statlabel_third.label_full }}</span>
                                            <span class="float-right text text-sm"><b>{{ yearstats.statlabel_third.count }}</b>/{{ yearstats.count }}</span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :style=" 'width:' + yearstats.statlabel_third.rate + '%; background-color:' + yearstats.statlabel_third.color.hex "></div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- ./card-body -->
                            <div v-if="yearstats_loading" class="overlay dark">
                                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

            </div><!--/. container-fluid -->

        </div>
    </div>
</template>
