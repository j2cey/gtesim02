<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import HowtoThreadListItem from './HowtoThreadListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import { useAbility } from "@casl/vue";

const { can, cannot } = useAbility();

const loading = ref(false);
const toastr = useToastr();
const howtothreads = ref({'data': []});

const paginationLinksLimit = ref(5);

const howtothreadIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedHowtoThreads = ref([]);

const toggleSelection = (howtothread) => {
    const index = selectedHowtoThreads.value.indexOf(howtothread.id);
    if (index === -1) {
        selectedHowtoThreads.value.push(howtothread.id);
    } else {
        selectedHowtoThreads.value.splice(index, 1);
    }
};

const getHowtoThreads = (page = 1) => {
    loading.value = true;
    axios.get(`/api/howtothreads?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getHowtoThreads, response: ", response);
            howtothreads.value = response.data;
            selectedHowtoThreads.value = [];
            selectAll.value = false;
        }).finally(() => {
            loading.value = false;
    });
}

const confirmHowtoThreadDeletion = (howtothread) => {
    howtothreadIdBeingDeleted.value = howtothread.id;
    $('#deleteHowtoThreadModal').modal('show');
};

const deleteHowtoThread = () => {
    axios.delete(`/api/howtothreads/${howtothreadIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteHowtoThreadModal').modal('hide');
            toastr.success('HowtoThread supprimé avec succès !');
            howtothreads.value.data = howtothreads.value.data.filter(howtothread => howtothread.id !== howtothreadIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/howtothreads', {
        data: {
            ids: selectedHowtoThreads.value
        }
    })
        .then(response => {
            howtothreads.value.data = howtothreads.value.data.filter(howtothread => !selectedHowtoThreads.value.includes(howtothread.id));
            selectedHowtoThreads.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllHowtoThreads = () => {
    if (selectAll.value) {
        selectedHowtoThreads.value = howtothreads.value.data.map(howtothread => howtothread.id);
    } else {
        selectedHowtoThreads.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getHowtoThreads();
}

watch(searchQuery, debounce(() => {
    //getHowtoThreads();
}, 300));

onMounted(() => {
    getHowtoThreads();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Aide, Trucs & Astuces pour Prendre la Main</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">Tutoriels Comment-Faire</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link v-if="can('howtothreads-create')" to="howtothreads/create">
                        <button type="button" class="mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Nouveau
                        </button>
                    </router-link>
                    <div v-if="can('howtothreads-delete') && selectedHowtoThreads.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Supprimer Sélection
                        </button>
                        <span class="ml-2 text-muted"> {{ selectedHowtoThreads.length }} rubrique(s) sélectionnées</span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="getHowtoThreads" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="getHowtoThreads">
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
                    <Bootstrap4Pagination :data="howtothreads" @pagination-change-page="getHowtoThreads" size="small" :limit="paginationLinksLimit" align="right" />
                    <div class="card-columns">
                        <HowtoThreadListItem v-for="(howtothread, index) in howtothreads.data" key="howtothread.id"
                            detailtype="card"
                            :howtothread="howtothread"
                            :selectAll="selectAll"
                            :index="index"
                        ></HowtoThreadListItem>
                    </div>
                    <span v-if="howtothreads.meta?.total > 0" class="text text-xs text-primary">{{ howtothreads.meta.total }} enregistrement(s)</span>
                    <Bootstrap4Pagination :data="howtothreads" @pagination-change-page="getHowtoThreads" size="small" :limit="paginationLinksLimit" align="right" />
                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="deleteHowtoThreadModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Tutoriel</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer cette Tutoriel ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteHowtoThread" type="button" class="btn btn-xs btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
