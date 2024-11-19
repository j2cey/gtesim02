<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch, computed} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import EsimListItem from './EsimListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import {useAbility} from "@casl/vue";
import { useRoute, useRouter } from "vue-router";

const router = useRouter();
const route = useRoute();

const { can, cannot } = useAbility();

const loading = ref(false);
const userid = ref(null);
const user = ref(null);
const toastr = useToastr();
const esims = ref({'data': []});
const editing = ref(false);

const paginationLinksLimit = ref(5);

const esimIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedEsims = ref([]);

const toggleSelection = (esim) => {
    const index = selectedEsims.value.indexOf(esim.id);
    if (index === -1) {
        selectedEsims.value.push(esim.id);
    } else {
        selectedEsims.value.splice(index, 1);
    }
};

const searchEsims = (page = 1) => {
    if (user.value) {
        getEsimsAttributed(page);
    } else {
        getEsims(page);
    }
}

const getEsims = (page = 1) => {
    loading.value = true;
    axios.get(`/api/esims?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getEsims, response: ", response);

            esims.value = response.data;

            selectedEsims.value = [];
            selectAll.value = false;
        }).finally(() => {
            loading.value = false;
    });
}
const getEsimsAttributed = (page = 1) => {
    loading.value = true;
    axios.get(`/api/esims/${userid.value}/attributed?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getEsimsAttributed, response: ", response);

            esims.value = response.data;

            selectedEsims.value = [];
            selectAll.value = false;
        }).finally(() => {
            loading.value = false;
    });
}

const getUser = () => {
    axios.get(`/api/users/${userid.value}/edit`)
        .then((response) => {
            console.log("getUser, response: ", response);
            user.value = response.data;
        })
};

const confirmEsimDeletion = (esim) => {
    esimIdBeingDeleted.value = esim.uuid;
    $('#deleteEsimModal').modal('show');
};

const loadingEsimDelete= ref(false);

const deleteEsim = () => {
    loadingEsimDelete.value = true
    axios.delete(`/api/esims/${esimIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimModal').modal('hide');
            toastr.success('Esim supprimé avec succès !');
            esims.value.data = esims.value.data.filter(esim => esim.uuid !== esimIdBeingDeleted.value);
            loadingEsimDelete.value = false
        });
};

const bulkDelete = () => {
    axios.delete('/api/esims', {
        data: {
            ids: selectedEsims.value
        }
    })
        .then(response => {
            esims.value.data = esims.value.data.filter(esim => !selectedEsims.value.includes(esim.id));
            selectedEsims.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllEsims = () => {
    if (selectAll.value) {
        selectedEsims.value = esims.value.data.map(esim => esim.id);
    } else {
        selectedEsims.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    searchEsims();
}

watch(searchQuery, debounce(() => {
    //searchEsims();
}, 300));

const currentPath = ref('/');
const lastPath = ref('/esims');
const prevRoutePath = computed(() => {
    return lastPath;// ? lastPath.value : '/clientesims';
});

watch(route, () => {
    console.log("watch route from EsimList");
    if (route.fullPath !== currentPath.value) {
        initComponent();
    }
});

const initComponent = () => {
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;
    if (route.params.userid) {
        userid.value = route.params.userid;
        getUser();
        getEsimsAttributed();
    } else {
        getEsims();
    }
};

onMounted(() => {
    initComponent();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste de E-sims</h1>
                    <span v-show="user" class="text text-sm mr-1"> <span class="text text-muted"> Attribués Par </span><span class="text text-bold text-indigo">{{ user?.name }}</span></span>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            Esims
                            <span v-show="user" class="text text-sm mr-1"> <span class="text text-muted"> Attribués Par </span><span class="text text-xs ">{{ user?.name }}</span></span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link :to="prevRoutePath">
                        <button type="button" class="mb-2 btn btn-sm btn-default">
                            <i class="fa fa-backward mr-1"></i> Retour
                        </button>
                    </router-link>

                    <router-link v-if="can('esims-create') && !user" to="esims/create">
                        <button type="button" class="ml-2 mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Nouveau
                        </button>
                    </router-link>
                    <div v-if="can('esims-delete') && selectedEsims.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Supprimer Sélection
                        </button>
                        <span class="ml-2 text-muted"> {{ selectedEsims.length }} esim(s) sélectionnées</span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="searchEsims" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="searchEsims">
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
                    <Bootstrap4Pagination :data="esims" @pagination-change-page="searchEsims" size="small" :limit="paginationLinksLimit" align="right" />
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllEsims" /></th>
                            <th class="text text-sm" style="width: 10px">#</th>
                            <th class="text text-sm text-capitalize">imsi</th>
                            <th class="text text-sm text-capitalize">iccid</th>
                            <th class="text text-sm text-capitalize">ac</th>
                            <th class="text text-sm text-capitalize">Etat</th>
                            <th class="text text-sm text-capitalize">Création</th>
                            <th class="text text-sm text-capitalize">Modification</th>
                            <th class="text text-sm text-capitalize">Attribution</th>
                            <th class="text text-sm text-capitalize">Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="esims.data.length > 0">
                        <EsimListItem v-for="(esim, index) in esims.data"
                                      key="esim.id"
                                      :esim=esim :index=index
                                      @confirm-esim-deletion="confirmEsimDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="9" class="text-center">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Chargement en cours...</span>
                                </div>
                                <span v-else>Aucun résultat trouvé...</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <span v-if="esims.meta?.total > 0" class="text text-xs text-primary">{{ esims.meta.total }} enregistrement(s)</span>
                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="deleteEsimModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Esim</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer cette Esim ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteEsim" type="button" class="btn btn-xs btn-danger" :disabled="loadingEsimDelete">
                        <span v-if="loadingEsimDelete" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
