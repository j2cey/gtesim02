<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import HowtoStepListItem from "./HowtoStepListItem.vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import {useAbility} from "@casl/vue";
import { debounce } from "lodash";

const { can, cannot } = useAbility();

const props = defineProps({
    howtothreadid: { type: String, default: ''},
    list_title: { type: String, default: ''},
    list_color: { type: String, default: ''},
});

//<editor-fold desc="HowtoStep">
const loading = ref(false);
const selectAll = ref(false);
const selecteds = ref([]);
const howtosteps = ref({'data': []});
const paginationLinksLimit = ref(5);
const toggleSelection = (howtostep) => {
    const index = selecteds.value.indexOf(howtostep.id);
    if (index === -1) {
        selecteds.value.push(howtostep.id);
    } else {
        selecteds.value.splice(index, 1);
    }
};
const selectAllHowtoSteps = () => {
    if (selectAll.value) {
        selecteds.value = howtosteps.value.data.map(howtostep => howtostep.id);
    } else {
        selecteds.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getHowtoSteps();
}

const searchQuery = ref(null);
const getHowtoSteps = (page = 1) => {
    let linkstr = '/api/howtosteps' + (props.howtothreadid === '' ? '' : '/howtothread/' + props.howtothreadid + '/threadsteps');
    console.log("getHowtoSteps launched, : props.howtothreadid: ", props.howtothreadid, "linkstr: ", linkstr, ", page: ", page);
    loading.value = true;
    axios.get( `${linkstr}?page=${page}`, {
        params: {
            query: searchQuery.value,
        }
    })
        .then((response) => {
            console.log("getHowtoSteps, response: ", response);
            howtosteps.value = response.data;
        }).finally(() => {
        loading.value = false;
    });
}

const loadingHowtoStepDelete = ref(false);
const howtostepIdBeingDeleted = ref(null);

const confirmEmailaddressDeletion = (howtostep) => {
    howtostepIdBeingDeleted.value = howtostep.id;
    $('#deleteHowtoStepModal').modal('show');
};

const deleteHowtoStep = () => {
    loadingHowtoStepDelete.value = true;
    axios.delete(`/api/howtosteps/${howtostepIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimStateModal').modal('hide');
            toastr.success('Adresse E-Mail supprimé avec succès !');
            howtosteps.value.data = howtosteps.value.data.filter(howtostep => howtostep.id !== howtostepIdBeingDeleted.value);
            loadingHowtoStepDelete.value = false;
        });
};

watch(searchQuery, debounce(() => {
    //getHowtoSteps();
}, 300));
//</editor-fold>


const initComponent = () => {
    getHowtoSteps();
};

onMounted(() => {
    initComponent();
});
</script>

<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-success card-outline direct-chat direct-chat-primary" :class="howtothreadid === '' ? '' : 'collapsed-card'">
                <div class="card-header">
                    <h5 v-show="howtothreadid !== ''" class="card-title">Etapes</h5>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                        <div class="btn-group">

                        </div>

                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas " :class="howtothreadid === '' ? 'fa-minus' : 'fa-plus'"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between pt-3">
                            <div class="d-flex">
                                <router-link v-if="can( 'howtosteps-create') && howtothreadid !== ''" :to="{
                                            name: 'howtosteps.create',
                                            params: {
                                                threadid: howtothreadid
                                            }
                                        }">
                                    <button type="button" class="mb-2 btn btn-sm btn-success btn-xs">
                                        <i class="fa fa-plus-circle mr-1"></i>
                                        Nouveau
                                    </button>
                                </router-link>

                                <div v-if="selecteds.length > 0">
                                    <span class="ml-2"> {{ selecteds.length }} Etape(s) sélectionnée(s)</span>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="input-group mb-3">
                                    <input @keyup.enter="getHowtoSteps" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                                    <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-default" @click="getHowtoSteps">
                                            <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span v-else><i class="fa fa-search"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <Bootstrap4Pagination :data="howtosteps" @pagination-change-page="getHowtoSteps" size="small" :limit="paginationLinksLimit" align="right"/>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllHowtoSteps" /></th>
                                <th style="width: 10px" class="text text-xs">Ordre</th>
                                <th class="text text-xs">Titre Etape</th>
                                <th class="text text-xs">Titre Rubrique</th>
                                <th class="text text-xs">Description</th>
                                <th style="width: 100px" class="text text-xs" v-if="can( 'howtosteps-creator-show')">Créé Par</th>
                                <th class="text text-xs">Création</th>
                                <th class="text text-xs">Modification</th>
                                <th class="text text-xs">Options</th>
                            </tr>
                            </thead>
                            <tbody v-if="howtosteps.data.length > 0">
                            <HowtoStepListItem v-for="(howtostep, index) in howtosteps.data"
                                                           key="howtostep.id"
                                                           :howtostep=howtostep
                                                           @confirm-howtostep-deletion="confirmEmailaddressDeletion"
                                                           :selecteds="selecteds"
                                                           :index=index
                                                           @toggle-selection="toggleSelection"
                                                           :selectAll="selectAll"
                            />
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td colspan="9" class="text-center">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>{{ loading ? ' Chargement en cours...' : ' Aucun résultat trouvé...' }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <span v-if="howtosteps.meta?.total > 0" class="text text-xs text-primary">{{ howtosteps.meta.total + ' enregistrement' + (howtosteps.meta.total > 1 ? 's' : '') }}</span>
                        <Bootstrap4Pagination v-if="howtothreadid === ''" :data="howtosteps" @pagination-change-page="getHowtoSteps" size="small" :limit="paginationLinksLimit" align="right"/>
                    </div>
                </div>
                <div class="card-footer">

                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteHowtoStepModal" data-backdrop="static" tabindex="-1" type="dialog"
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
                    <button @click.prevent="deleteHowtoStep" type="button" class="btn btn-xs btn-danger" :disabled="loadingHowtoStepDelete">
                        <span v-if="loadingHowtoStepDelete" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
