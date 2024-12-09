<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import HowtoListItem from './HowtoListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import { useAbility } from "@casl/vue";

const { can, cannot } = useAbility();

const loading = ref(false);
const toastr = useToastr();
const howtos = ref({'data': []});

const paginationLinksLimit = ref(5);

const howtoIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedHowtos = ref([]);

const toggleSelection = (howto) => {
    const index = selectedHowtos.value.indexOf(howto.id);
    if (index === -1) {
        selectedHowtos.value.push(howto.id);
    } else {
        selectedHowtos.value.splice(index, 1);
    }
};

const getHowtos = (page = 1) => {
    loading.value = true;
    axios.get(`/api/howtos?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getHowtos, response: ", response);
            howtos.value = response.data;
            selectedHowtos.value = [];
            selectAll.value = false;
        }).finally(() => {
        loading.value = false;
    });
}

const confirmHowtoDeletion = (howto) => {
    howtoIdBeingDeleted.value = howto.id;
    $('#deleteHowtoModal').modal('show');
};

const deleteHowto = () => {
    axios.delete(`/api/howtos/${howtoIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteHowtoModal').modal('hide');
            toastr.success('Howto supprimé avec succès !');
            howtos.value.data = howtos.value.data.filter(howto => howto.id !== howtoIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/howtos', {
        data: {
            ids: selectedHowtos.value
        }
    })
        .then(response => {
            howtos.value.data = howtos.value.data.filter(howto => !selectedHowtos.value.includes(howto.id));
            selectedHowtos.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllHowtos = () => {
    if (selectAll.value) {
        selectedHowtos.value = howtos.value.data.map(howto => howto.id);
    } else {
        selectedHowtos.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getHowtos();
}

watch(searchQuery, debounce(() => {
    //getHowtos();
}, 300));

onMounted(() => {
    getHowtos();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste des Rubriques Comment-Faire</h1>
                    <span class="text text-muted text-xs">Une Rubrique de Tuto Comment-Faire est destinée à contenir le détail <b>HTML</b> à afficher</span>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">Rubriques Comment-Faire</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link v-if="can('howtos-create')" to="howtos/create">
                        <button type="button" class="mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Nouveau
                        </button>
                    </router-link>
                    <div v-if="can('howtos-delete') && selectedHowtos.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Supprimer Sélection
                        </button>
                        <span class="ml-2 text-muted"> {{ selectedHowtos.length }} client(s) sélectionnées</span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="getHowtos" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="getHowtos">
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
                    <Bootstrap4Pagination :data="howtos" @pagination-change-page="getHowtos" size="small" :limit="paginationLinksLimit" align="right" />
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllHowtos" /></th>
                            <th style="width: 10px">#</th>
                            <th class="text text-xs">Titre</th>
                            <th class="text text-xs">Code</th>
                            <th class="text text-xs">Vue</th>
                            <th class="text text-xs">Type</th>
                            <th class="text text-xs">Description</th>
                            <th v-if="can('howtos-creator-list')" class="text text-xs">Utilisateur</th>
                            <th class="text text-xs">Création</th>
                            <th class="text text-xs">Modification</th>
                            <th class="text text-xs">Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="howtos.data.length > 0">
                        <HowtoListItem v-for="(howto, index) in howtos.data"
                                          key="howto.id"
                                          :howto=howto
                                          :index=index
                                          @confirm-howto-deletion="confirmHowtoDeletion"
                                          @toggle-selection="toggleSelection"
                                          :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="11" class="text-center">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>{{ loading ? ' Chargement en cours...' : 'Aucun résultat trouvé...' }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <span v-if="howtos.meta?.total > 0" class="text text-xs text-primary">{{ howtos.meta.total }} enregistrement(s)</span>
                    <Bootstrap4Pagination :data="howtos" @pagination-change-page="getHowtos" size="small" :limit="paginationLinksLimit" align="right" />
                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="deleteHowtoModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Rubrique Comment-Faire</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer cette Rubrique ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteHowto" type="button" class="btn btn-xs btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
