<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed } from 'vue';
import ArisStatusListItem from "./ArisStatusListItem.vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAbility } from "@casl/vue";
import { debounce } from "lodash";

const { can, cannot } = useAbility();

const props = defineProps({
    modeltype: { type: String, default: ''},
    modelid: { type: String, default: ''},
});

//<editor-fold desc="ArisStatus">
const loading = ref(false);
const selectAll = ref(false);
const selecteds = ref([]);
const arisstatuses = ref({'data': []});
const paginationLinksLimit = ref(5);
const toggleSelection = (arisstatus) => {
    const index = selecteds.value.indexOf(arisstatus.id);
    if (index === -1) {
        selecteds.value.push(arisstatus.id);
    } else {
        selecteds.value.splice(index, 1);
    }
};
const selectAllArisstatuses = () => {
    if (selectAll.value) {
        selecteds.value = arisstatuses.value.data.map(arisstatus => arisstatus.id);
    } else {
        selecteds.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getArisstatuses();
}

const searchQuery = ref(null);
const getArisstatuses = (page = 1) => {
    let linkstr = '/api/arisstatuses' + (props.modelid === '' ? '' : '/' + props.modelid + '');
    console.log("getArisstatuses launched, : props.modeltype: ", props.modeltype, ", props.modelid: ", props.modelid, ", linkstr: ", linkstr, ", page: ", page);
    loading.value = true;
    axios.get( `${linkstr}?page=${page}`, {
        params: {
            query: searchQuery.value,
        }
    })
        .then((response) => {
            console.log("getArisstatuses, response: ", response);
            arisstatuses.value = response.data;
        }).finally(() => {
        loading.value = false;
    });
}

const loadingArisStatusDelete = ref(false);
const arisstatusIdBeingDeleted = ref(null);

const confirmArisstatusDeletion = (arisstatus) => {
    arisstatusIdBeingDeleted.value = arisstatus.id;
    $('#deleteArisStatusModal').modal('show');
};

const deleteArisStatus = () => {
    loadingArisStatusDelete.value = true;
    axios.delete(`/api/arisstatuses/${arisstatusIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimStateModal').modal('hide');
            toastr.success('Statut Aris supprimé avec succès !');
            arisstatuses.value.data = arisstatuses.value.data.filter(arisstatus => arisstatus.id !== arisstatusIdBeingDeleted.value);
            loadingArisStatusDelete.value = false;
        });
};

watch(searchQuery, debounce(() => {
    //getArisstatuses();
}, 300));
//</editor-fold>


const initComponent = () => {
    getArisstatuses();
};

onMounted(() => {
    initComponent();
});
</script>

<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-success card-outline direct-chat direct-chat-primary" :class="modeltype === '' ? '' : 'collapsed-card'">
                <div class="card-header">
                    <h5 v-show="modeltype !== ''" class="card-title">Statuts ARIS</h5>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                        <div class="btn-group">

                        </div>

                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas " :class="modeltype === '' ? 'fa-minus' : 'fa-plus'"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between pt-3">
                            <div class="d-flex">
                                <router-link v-if="can( 'arisstatuses-add')" :to="{
                                            name: 'arisstatuses.create',
                                            params: {
                                                modeltype: modeltype,
                                                modelid: modelid
                                            }
                                        }">
                                    <button type="button" class="mb-2 btn btn-sm btn-success btn-xs">
                                        <i class="fa fa-plus-circle mr-1"></i>
                                        Nouveau
                                    </button>
                                </router-link>

                                <div v-if="selecteds.length > 0">
                                    <span class="ml-2"> {{ selecteds.length }} Statut(s) sélectionnés(s)</span>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="input-group mb-3">
                                    <input @keyup.enter="getArisstatuses" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                                    <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-default" @click="getArisstatuses">
                                            <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span v-else><i class="fa fa-search"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <Bootstrap4Pagination :data="arisstatuses" @pagination-change-page="getArisstatuses" size="small" :limit="paginationLinksLimit" align="right"/>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllArisstatuses" /></th>
                                <th style="width: 10px">#</th>
                                <th class="text text-xs">Statut</th>
                                <th class="text text-xs">Date Changement</th>
                                <th class="text text-xs">Date Requête</th>
                                <th class="text text-xs">Date Réponse</th>
                                <th class="text text-xs">Message Réponse</th>
                                <th class="text text-xs">Esim</th>
                                <th class="text text-xs">Création</th>
                                <th class="text text-xs">Modification</th>
                                <th class="text text-xs">Options</th>
                            </tr>
                            </thead>
                            <tbody v-if="arisstatuses.data.length > 0">
                            <ArisStatusListItem v-for="(arisstatus, index) in arisstatuses.data"
                                                           key="arisstatus.id"
                                                           :arisstatus=arisstatus
                                                           :modeltype=modeltype
                                                           :modelid=modelid
                                                           @confirm-arisstatus-deletion="confirmArisstatusDeletion"
                                                           :selecteds="selecteds"
                                                           :index=index
                                                           @toggle-selection="toggleSelection"
                                                           :selectAll="selectAll"
                            />
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td colspan="11" class="text-center">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>{{ loading ? ' Chargement en cours...' : ' Aucun résultat trouvé...' }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <span v-if="arisstatuses.meta?.total > 0" class="text text-xs text-primary">{{ arisstatuses.meta.total + ' enregistrement' + (arisstatuses.meta.total > 1 ? 's' : '') }}</span>
                        <Bootstrap4Pagination v-if="modeltype === ''" :data="arisstatuses" @pagination-change-page="getArisstatuses" size="small" :limit="paginationLinksLimit" align="right"/>
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

    <div class="modal fade" id="deleteArisStatusModal" data-backdrop="static" tabindex="-1" type="dialog"
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
                    <h5>Etes-vous sûr de vouloir supprimer ce Statut ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteArisStatus" type="button" class="btn btn-xs btn-danger" :disabled="loadingArisStatusDelete">
                        <span v-if="loadingArisStatusDelete" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
