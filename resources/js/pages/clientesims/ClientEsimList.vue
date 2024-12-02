<script setup>
import axios from 'axios';
import { ref, onMounted, reactive, watch } from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import ClientEsimListItem from './ClientEsimListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import { useAbility } from "@casl/vue";


const { can, cannot } = useAbility();

const loading = ref(false);
const toastr = useToastr();
const clientEsims = ref({'data': []});
const editing = ref(false);

const paginationLinksLimit = ref(5);

const clientEsimIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedClientEsims = ref([]);

const toggleSelection = (clientEsim) => {
    const index = selectedClientEsims.value.indexOf(clientEsim.id);
    if (index === -1) {
        selectedClientEsims.value.push(clientEsim.id);
    } else {
        selectedClientEsims.value.splice(index, 1);
    }
};

const getClientEsims = (page = 1) => {
    loading.value = true;
    console.log("getClientEsims launched, page: ", page);
    axios.get(`/api/clientesims?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getClientEsims, response: ", response);
            clientEsims.value = response.data;
            selectedClientEsims.value = [];
            selectAll.value = false;
        }).finally(() => {
            loading.value = false;
    });
}

const confirmClientEsimDeletion = (clientEsim) => {
    clientEsimIdBeingDeleted.value = clientEsim.id;
    $('#deleteClientEsimModal').modal('show');
};

const deleteClientEsim = () => {
    axios.delete(`/api/clientesims/${clientEsimIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteClientEsimModal').modal('hide');
            toastr.success('ClientEsim supprimé avec succès !');
            clientEsims.value.data = clientEsims.value.data.filter(clientEsim => clientEsim.id !== clientEsimIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/clientesims', {
        data: {
            ids: selectedClientEsims.value
        }
    })
        .then(response => {
            clientEsims.value.data = clientEsims.value.data.filter(clientEsim => !selectedClientEsims.value.includes(clientEsim.id));
            selectedClientEsims.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllClientEsims = () => {
    if (selectAll.value) {
        selectedClientEsims.value = clientEsims.value.data.map(clientEsim => clientEsim.id);
    } else {
        selectedClientEsims.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getClientEsims();
}

watch(searchQuery, debounce(() => {
    //getClientEsims();
}, 300));

onMounted(() => {
    getClientEsims();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste des Clients E-SIM</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">Clients Esim</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link v-if="can('clientesims-create')" to="clientesims/create">
                        <button type="button" class="mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Nouveau
                        </button>
                    </router-link>
                    <div v-if="can('clientesims-delete') && selectedClientEsims.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Supprimer Sélection
                        </button>
                        <span class="ml-2 text-muted"> {{ selectedClientEsims.length }} client(s) sélectionnées</span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="getClientEsims" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="getClientEsims">
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
                    <Bootstrap4Pagination :data="clientEsims" @pagination-change-page="getClientEsims" size="small" :limit="paginationLinksLimit" align="right" />
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllClientEsims" /></th>
                            <th style="width: 10px">#</th>
                            <th class="text text-xs">Nom</th>
                            <th class="text text-xs">Prénom</th>
                            <th class="text text-xs">Telephone(s)</th>
                            <th class="text text-xs">EMail(s)</th>
                            <th v-if="can('clientesims-creator-list')" class="text text-xs">Utilisateur</th>
                            <th class="text text-xs">Création</th>
                            <th class="text text-xs">Modification</th>
                            <th class="text text-xs">Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="clientEsims.data.length > 0">
                        <ClientEsimListItem v-for="(clientEsim, index) in clientEsims.data"
                                      key="clientEsim.id"
                                      :clientesim=clientEsim
                                      :index=index
                                      @confirm-clientesim-deletion="confirmClientEsimDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="10" class="text-center">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>{{ loading ? ' Chargement en cours...' : 'Aucun résultat trouvé...' }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <span v-if="clientEsims.meta?.total > 0" class="text text-xs text-primary">{{ clientEsims.meta.total }} enregistrement(s)</span>
                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="deleteClientEsimModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Client Esim</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer ce Client ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteClientEsim" type="button" class="btn btn-xs btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
