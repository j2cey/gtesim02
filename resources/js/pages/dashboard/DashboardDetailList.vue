<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed } from 'vue';
import DashboardDetailListItem from "./DashboardDetailListItem.vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAbility } from "@casl/vue";
import { debounce } from "lodash";
import { formatDate } from '../../services/helper.js'

const { can, cannot } = useAbility();

const props = defineProps({
    loading: Boolean,
    statdetails_prop: Object,
    stat_agence: { type: Object, default: null},
    stat_period: { type: Object, default: null},
});

//<editor-fold desc="EmailAddress">
//const loading = ref(false);
const loading_local = ref(false);
const selectAll = ref(false);
const selecteds = ref([]);

const statdetails = ref({'data': []});
const stat_agence_local = ref(null);
const stat_period_local = ref(null);

const paginationLinksLimit = ref(5);
const toggleSelection = (statdetail) => {
    const index = selecteds.value.indexOf(statdetail.id);
    if (index === -1) {
        selecteds.value.push(statdetail.id);
    } else {
        selecteds.value.splice(index, 1);
    }
};
const selectAllEmailaddresses = () => {
    if (selectAll.value) {
        selecteds.value = statdetails.value.data.map(statdetail => statdetail.id);
    } else {
        selecteds.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    searchStatDetails();
}

const searchQuery = ref(null);
const searchStatDetails = (page = 1) => {
    loading_local.value = true;
    axios.get(`/api/dashboards.details?page=${page}`, {
        params: {
            searchQuery: searchQuery.value,
            departement: stat_agence_local.value,// ? stat_agence_local.value.id : null,
            period: stat_period_local.value
        }
    })
        .then((response) => {
            console.log("searchStatDetails, response: ", response);
            statdetails.value = response.data;
        }).finally(() => {
        loading_local.value = false;
    });
}

const loadingEmailAddressDelete = ref(false);
const statdetailIdBeingDeleted = ref(null);

const confirmEmailaddressDeletion = (statdetail) => {
    statdetailIdBeingDeleted.value = statdetail.id;
    $('#deleteEmailAddressModal').modal('show');
};

const deleteEmailAddress = () => {
    loadingEmailAddressDelete.value = true;
    axios.delete(`/api/statdetails/${statdetailIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimStateModal').modal('hide');
            toastr.success('Adresse E-Mail supprimé avec succès !');
            statdetails.value.data = statdetails.value.data.filter(statdetail => statdetail.id !== statdetailIdBeingDeleted.value);
            loadingEmailAddressDelete.value = false;
        });
};

watch(searchQuery, debounce(() => {
    //getEmailaddresses();
}, 300));
//</editor-fold>

const initComponent = () => {
    //getEmailaddresses();
};

onMounted(() => {
    initComponent();
    statdetails.value = props.statdetails_prop;
    stat_agence_local.value = props.stat_agence;
    stat_period_local.value = props.stat_period;
});
</script>

<template>
    <div class="card card-outline direct-chat direct-chat-primary">
        <div class="card-header">
            <h5 class="card-title">Détails Agents
                <span class="text badge badge-default text-xs mr-2">{{ stat_agence_local ? stat_agence_local.intitule : '' }}</span>
                <span v-if="stat_period_local">
                    <span class="text text-xs text-muted">Du</span>
                    <span class="text badge badge-default text-xs mr-1">{{ formatDate(stat_period_local[0]) }}</span>
                    <span class="text text-xs text-muted">Au</span>
                    <span class="text badge badge-default text-xs mr-1">{{ formatDate(stat_period_local[1]) }}</span>
                </span>
            </h5>
            <div class="card-tools">
                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                <div class="btn-group">

                </div>

                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="d-flex justify-content-between pt-3">
                    <div class="d-flex">
                        <div v-if="selecteds.length > 0">
                            <span class="ml-2"> {{ selecteds.length }} Agent(s) sélectionnés(s)</span>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="input-group mb-3">
                            <input @keyup.enter="searchStatDetails" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                            <button v-if="searchQuery && (!loading_local || !loading)" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                                <i class="fa fa-times"></i>
                            </button>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-default" @click="searchStatDetails">
                                    <div v-if="loading_local || loading" class="spinner-border spinner-border-sm" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <span v-else><i class="fa fa-search"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <Bootstrap4Pagination :data="statdetails" @pagination-change-page="searchStatDetails" size="small" :limit="paginationLinksLimit" align="right"/>
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th><input type="checkbox" v-model="selectAll" @change="selectAllEmailaddresses" /></th>
                        <th style="width: 10px">#</th>
                        <th class="text text-xs">Nom</th>
                        <th class="text text-xs">E-Mail</th>
                        <th class="text text-xs">Departement</th>
                        <th class="text text-xs">Esims Affecté(s)</th>
                    </tr>
                    </thead>
                    <tbody v-if="statdetails.data.length > 0">
                    <DashboardDetailListItem v-for="(statdetail, index) in statdetails.data"
                                             key="statdetail.id"
                                             :statdetail=statdetail
                                             :stat_agence=stat_agence_local
                                             :stat_period=stat_period_local
                                             :selecteds="selecteds"
                                             :index=index
                                             @toggle-selection="toggleSelection"
                                             :selectAll="selectAll"
                    />
                    </tbody>
                    <tbody v-else>
                    <tr>
                        <td colspan="6" class="text-center">
                            <span v-if="loading_local || loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>{{ loading ? ' Chargement en cours...' : ' Aucun résultat trouvé...' }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <span v-if="statdetails?.total > 0" class="text text-xs text-primary">{{ statdetails.total + ' enregistrement' + (statdetails.total > 1 ? 's' : '') }}</span>
                <Bootstrap4Pagination :data="statdetails" @pagination-change-page="searchStatDetails" size="small" :limit="paginationLinksLimit" align="right"/>
            </div>
        </div>
        <div class="card-footer">

        </div>

        <div v-if="loading_local || loading" class="overlay dark">
            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>

    <div class="modal fade" id="deleteEmailAddressModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Adresse E-Mail</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer cet E-Mail ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteEmailAddress" type="button" class="btn btn-xs btn-danger" :disabled="loadingEmailAddressDelete">
                        <span v-if="loadingEmailAddressDelete" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
