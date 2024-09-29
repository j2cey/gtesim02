<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import {debounce} from "lodash";
import StateListItem from "./StateListItem.vue";
import {Bootstrap4Pagination} from "laravel-vue-pagination";
import Multiselect from 'vue-multiselect';
import Swal from "sweetalert2";

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    imsi: '',
    iccid: '',
    ac: '',
    pin: '',
    puk: '',
    eki: '',
    pin2: '',
    puk2: '',
    adm1: '',
    opc: '',
    statutesim: {},
    technologieesim: {},
});

const loading = ref(false);
const loadingEsim = ref(false);
const formMode = ref('create');
const phonenum = ref({});
const esimstates = ref({'data': []});
const loadingEsimStates = ref(false);

const initForm = () => {
    form.imsi = '';
    form.iccid = '';
    form.ac = '';
    form.pin = '';
    form.puk = '';
    form.eki = '';
    form.pin2 = '';
    form.puk2 = '';
    form.adm1 = '';
    form.opc = '';
    form.statutesim = {};
    form.technologieesim = {};
}

const handleSubmit = (values, actions) => {
    if (formMode.value === 'edit') {
        updateEsim(values, actions);
    } else if (formMode.value === 'create') {
        createEsim(values, actions);
    }
};

const createEsim = (values, actions) => {
    loadingEsim.value = true;
    axios.post('/api/esims', form)
        .then((response) => {
            // router.push('/esims');
            esim.value = response.data;
            esimid.value = response.data.uuid;
            formMode.value = 'edit';

            Swal.fire({
                html: '<small>Esim créée avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {

            });
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
        .finally(() => {
            loadingEsim.value = false;
        })
};


const updateEsim = (values, actions) => {
    loadingEsim.value = true;
    axios.put(`/api/esims/${esimid.value}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>Esim modifiée avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                router.push('/esims');
            });
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
        .finally(() => {
            loadingEsim.value = false;
    })
};
const deleteEsim = (values, actions) => {
    loadingEsim.value = true;
    axios.delete('/api/esims', form)
        .then((response) => {
            Swal.fire({
                html: '<small>Esim supprimer avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                router.push('/esims');
            });
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
        .finally(() => {
            loadingEsim.value = false;
        })
};

const esim = ref({})
const esimid = ref(null)
const getEsim = () => {
    loading.value = true;
    axios.get(`/api/esims/${route.params.id}/edit`)
        .then((response) => {
            console.log("getEsim, response: ", response)
            form.imsi = response.data.imsi;
            form.iccid = response.data.iccid;
            form.ac = response.data.ac;
            form.pin = response.data.pin;
            form.puk = response.data.puk;
            form.eki = response.data.eki;
            form.pin2 = response.data.pin2;
            form.puk2 = response.data.puk2;
            form.adm1 = response.data.adm1;
            form.opc = response.data.opc;
            form.statutesim = response.data.statutesim;
            form.technologieesim = response.data.technologieesim;

            phonenum.value = response.data.phonenum;

            esim.value = response.data;
        }).then(() => {
            console.log("exec getEsimStates")
            getEsimStates();
            loading.value = false;
        }

    )
};

const esimstatuses = ref([]);
const esimStateIdBeingDeleted = ref(null);

const confirmEsimStateDeletion = (esimstate) => {
    esimStateIdBeingDeleted.value = esimstate.id;
    $('#deleteEsimStateModal').modal('show');
};

const loadingEsimDelete= ref(false);

const deleteEsimState = () => {
    loadingEsimDelete.value = true;
    axios.delete(`/api/esimstates/${esimStateIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimStateModal').modal('hide');
            toastr.success('Etat Esim supprimé avec succès !');
            esimstates.value.data = esimstates.value.data.filter(esimstate => esimstate.id !== esimStateIdBeingDeleted.value);
            loadingEsimDelete.value = false;
        });
};

const getEsimStatuses = () => {
    axios.get(`/api/esimstatuses`)
        .then((response) => {
            //console.log("getEsimStatuses, response: ", response)
            esimstatuses.value = response.data;
        })
}

const esimtechnologies = ref([]);
const getEsimTechnologies = () => {
    axios.get(`/api/esimtechnologies`)
        .then((response) => {
            console.log("getEsimTechnologies, response: ", response)
            esimtechnologies.value = response.data;
        })
}

const selectAll = ref(false);
const selectedEsimStates = ref([]);
const toggleSelection = (esimstate) => {
    const index = selectedEsimStates.value.indexOf(esimstate.id);
    if (index === -1) {
        selectedEsimStates.value.push(esimstate.id);
    } else {
        selectedEsimStates.value.splice(index, 1);
    }
};
const selectAllEsimStates = () => {
    if (selectAll.value) {
        selectedEsimStates.value = esimstates.value.data.map(esimstate => esimstate.id);
    } else {
        selectedEsimStates.value = [];
    }
}

const clearSearchEsimStateQuery = () => {
    searchEsimStateQuery.value = '';
    getEsimStates();
}

const searchEsimStateQuery = ref(null);
const getEsimStates = (page = 1) => {
    loadingEsimStates.value = true;
    axios.get(`/api/esimstates/${esimid.value}/esimindex?page=${page}`, {
        params: {
            query: searchEsimStateQuery.value,
        }
    })
        .then((response) => {
            console.log("getEsimStates, response: ", response);
            esimstates.value = response.data;
        }).finally(() => {
        loadingEsimStates.value = false;
    });
}

watch(searchEsimStateQuery, debounce(() => {
    //getEsimStates();
}, 300));

const currentPath = ref('/');
const lastPath = ref('/');
const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

watch(route, () => {
    if (route.fullPath !== currentPath.value) {
        initComponent();
    }
});

const initComponent = () => {
    initForm();
    lastPath.value = router.options.history.state.back;
    currentPath.value = route.fullPath;
    if (route.name === 'esims.edit' || route.name === 'esims.show') {
        if (route.name === 'esims.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        esimid.value = route.params.id;
        getEsim();
    } else {
        formMode.value = 'create';
    }
};

onMounted(() => {
    initComponent();

    getEsimStatuses();
    getEsimTechnologies();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="formMode === 'edit'">Modification</span>
                        <span v-else-if="formMode === 'create'">Création</span>
                        <span v-else>Détails</span>
                        Esim</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/dashboard">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/esims">Esims</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="formMode === 'edit'">Modification</span>
                            <span v-else-if="formMode === 'create'">Création</span>
                            <span v-else>Détails</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row small">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="imsi">imsi</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.imsi }}</span>
                                            <input v-else v-model="form.imsi" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.imsi }" id="imsi" placeholder="imsi">
                                            <span class="invalid-feedback">{{ errors.imsi }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="iccid">iccid</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.iccid }}</span>
                                            <input v-else v-model="form.iccid" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.iccid }" id="iccid" placeholder="iccid">
                                            <span class="invalid-feedback">{{ errors.iccid }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ac">ac</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.ac }}</span>
                                            <input v-else v-model="form.ac" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.ac }" id="ac" placeholder="ac">
                                            <span class="invalid-feedback">{{ errors.ac }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row small">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin">pin</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.pin }}</span>
                                            <input v-else v-model="form.pin" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.pin }" id="pin" placeholder="pin">
                                            <span class="invalid-feedback">{{ errors.pin }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk">puk</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.puk }}</span>
                                            <input v-else v-model="form.puk" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.puk }" id="puk" placeholder="puk">
                                            <span class="invalid-feedback">{{ errors.puk }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="eki">eki</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.eki }}</span>
                                            <input v-else v-model="form.eki" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.eki }" id="eki" placeholder="eki">
                                            <span class="invalid-feedback">{{ errors.eki }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin2">pin2</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.pin2 }}</span>
                                            <input v-else v-model="form.pin2" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.pin2 }" id="pin2" placeholder="pin2">
                                            <span class="invalid-feedback">{{ errors.pin2 }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row small">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk2">puk2</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.puk2 }}</span>
                                            <input v-else v-model="form.puk2" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.puk2 }" id="puk2" placeholder="puk2">
                                            <span class="invalid-feedback">{{ errors.puk2 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="adm1">adm1</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.adm1 }}</span>
                                            <input v-else v-model="form.adm1" type="text" class="form-control form-control-sm" name="adm1" :class="{ 'is-invalid': errors.adm1 }" id="adm1" placeholder="adm1">
                                            <span class="invalid-feedback">{{ errors.adm1 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="opc">opc</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.opc }}</span>
                                            <input v-else v-model="form.opc" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.opc }" id="opc" placeholder="opc">
                                            <span class="invalid-feedback">{{ errors.opc }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row small">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="statutesim">Statut</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs" :class="'text-' + (form.statutesim ? form.statutesim.style : '' )">{{ form.statutesim?.libelle }}</span>
                                            <multiselect v-else
                                                id="statutesim"
                                                v-model="form.statutesim"
                                                value=""
                                                :options="esimstatuses"
                                                :searchable="true"
                                                label="libelle"
                                                track-by="id"
                                                key="id"
                                                :max-height="100"
                                                placeholder="Statut Esim"
                                            >
                                            </multiselect >
                                            <span class="invalid-feedback">{{ errors.statutesim }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="technologie">Technologie</label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.technologieesim?.libelle }}</span>
                                            <multiselect v-else
                                                id="technologie"
                                                v-model="form.technologieesim"
                                                value=""
                                                :options="esimtechnologies"
                                                :searchable="true"
                                                label="libelle"
                                                track-by="id"
                                                key="id"
                                                :max-height="100"
                                                placeholder="Technologie Esim"
                                            >
                                            </multiselect >
                                            <span class="invalid-feedback">{{ errors.technologieesim }}</span>
                                        </div>
                                    </div>

                                    <div v-if="(formMode === 'edit' || formMode === 'show') && phonenum" class="col-md-3">
                                        <div class="form-group">
                                            <label for="opc" class="text text-danger">Téléphone rattaché</label>
                                            <input v-model="phonenum.phone_number" type="text" class="form-control form-control-sm border-0" id="phonenum" placeholder="Numéo Téléphone" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary m-2" :disabled="loadingEsim">
                                        <span v-if="loadingEsim" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Valider
                                    </button>
                                    <router-link to="/esims">
                                        <button type="submit" class="btn btn-sm btn-default m-2">Retour</button>
                                    </router-link>
                                </div>
                            </Form>
                        </div>

                        <div v-if="loading" class="overlay dark">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h3 class="card-title">Historique</h3>
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
                                        <div v-if="selectedEsimStates.length > 0">

                                            <span class="ml-2">Selected {{ selectedEsimStates.length }} esims</span>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-group mb-3">
                                            <input @keyup.enter="getEsimStates" type="search" v-model="searchEsimStateQuery" class="form-control text-xs" placeholder="Recherche text..." />
                                            <button v-if="searchEsimStateQuery && !loadingEsimStates" @click="clearSearchEsimStateQuery" type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-default" @click="getEsimStates">
                                                    <div v-if="loadingEsimStates" class="spinner-border spinner-border-sm" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <span v-else><i class="fa fa-search"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" v-model="selectAll" @change="selectAllEsimStates" /></th>
                                        <th class="text text-sm" style="width: 10px">#</th>
                                        <th class="text text-sm">Statut</th>
                                        <th class="text text-sm">Utilisateur</th>
                                        <th class="text text-sm">Détails</th>
                                        <th class="text text-sm">Date</th>
                                        <th class="text text-sm">Options</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="esimstates.data.length > 0">
                                    <StateListItem v-for="(esimstate, index) in esimstates.data"
                                                        key="esimstate.id"
                                                        :esimstate=esimstate
                                                        @confirm-esim-state-deletion="confirmEsimStateDeletion"
                                                        :selectedEsimStates="selectedEsimStates"
                                                        :index=index
                                                        @toggle-selection="toggleSelection"
                                                        :selectAll="selectAll"
                                    />
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div v-if="loadingEsimStates" class="spinner-border spinner-border-sm" role="status">
                                                <span class="sr-only">Chargement en cours...</span>
                                            </div>
                                            <span v-else>Aucun résultat trouvé...</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <Bootstrap4Pagination :data="esimstates" @pagination-change-page="getEsimStates" size="small" align="right" :limit="3" />
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                        <div v-if="loadingEsimStates" class="overlay dark">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteEsimStateModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Etat Esim</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer cet Etat de Esim ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteEsimState" type="button" class="btn btn-xs btn-danger" :disabled="loadingEsimDelete">
                        <span v-if="loadingEsimDelete" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>
