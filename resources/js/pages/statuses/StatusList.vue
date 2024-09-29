<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import StatusListItem from './StatusListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import {useAbility} from "@casl/vue";


const { can, cannot } = useAbility();

const loading = ref(false);
const toastr = useToastr();
const statuses = ref({'data': []});
const editing = ref(false);

const paginationLinksLimit = ref(5);

const statusIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedStatuses = ref([]);

const toggleSelection = (status) => {
    const index = selectedStatuses.value.indexOf(status.id);
    if (index === -1) {
        selectedStatuses.value.push(status.id);
    } else {
        selectedStatuses.value.splice(index, 1);
    }
};

const getStatuses = (page = 1) => {
    loading.value = true;
    axios.get(`/api/statuses?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getStatuses, response: ", response);
            statuses.value = response.data;
            selectedStatuses.value = [];
            selectAll.value = false;
        }).finally(() => {
            loading.value = false;
    });
}

const confirmStatusDeletion = (status) => {
    statusIdBeingDeleted.value = status.id;
    $('#deleteStatusModal').modal('show');
};

const deleteStatus = () => {
    axios.delete(`/api/statuses/${statusIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteStatusModal').modal('hide');
            toastr.success('Statut supprimé avec succès !');
            statuses.value.data = statuses.value.data.filter(status => status.id !== statusIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/statuses', {
        data: {
            ids: selectedStatuses.value
        }
    })
        .then(response => {
            statuses.value.data = statuses.value.data.filter(status => !selectedStatuses.value.includes(status.id));
            selectedStatuses.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllStatuses = () => {
    if (selectAll.value) {
        selectedStatuses.value = statuses.value.data.map(status => status.id);
    } else {
        selectedStatuses.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getStatuses();
}

watch(searchQuery, debounce(() => {
    //getStatuses();
}, 300));

onMounted(() => {
    getStatuses();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste de E-sims</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                        <li class="breadcrumb-item active">Statuts</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link v-if="can('status-create')" to="statuses/create">
                        <button type="button" class="mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Nouveau
                        </button>
                    </router-link>
                    <div v-if="can('status-delete') && selectedStatuses.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Supprimer Sélection
                        </button>
                        <span class="ml-2 text-muted"> {{ selectedStatuses.length }} statut(s) sélectionnées</span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="getStatuses" type="search" v-model="searchQuery" class="form-control text-xs" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="getStatuses">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span v-else><i class="fa fa-search"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-body">
                    <Bootstrap4Pagination :data="statuses" @pagination-change-page="getStatuses" size="small" :limit="paginationLinksLimit" align="right" />
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllStatuses" /></th>
                            <th style="width: 10px">#</th>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Style</th>
                            <th>Description</th>
                            <th>Création</th>
                            <th>Modification</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="statuses.data.length > 0">
                        <StatusListItem v-for="(status, index) in statuses.data"
                                      key="status.id"
                                      :status=status :index=index
                                      @confirm-status-deletion="confirmStatusDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="8" class="text-center">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Chargement en cours...</span>
                                </div>
                                <span v-else>Aucun résultat trouvé...</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <span v-if="statuses.total > 0" class="text text-xs text-primary">{{ statuses.total }} enregistrement(s)</span>

                </div>

            </div>


        </div>
    </div>

    <div class="modal fade" id="deleteStatusModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Status</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer cette Status ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteStatus" type="button" class="btn btn-xs btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
