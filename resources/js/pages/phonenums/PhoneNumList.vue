<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed } from 'vue';
import PhonenumListItem from "./PhonenumListItem.vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { useAbility } from "@casl/vue";
import { debounce } from "lodash";
import Swal from 'sweetalert2';
import { useEsimStore } from '../../stores/EsimStore.js';

import pickupEsimModal from "../esims/EsimPickup.vue"

const { can, cannot } = useAbility();

const props = defineProps({
    modeltype: { type: String, default: ''},
    modelid: { type: String, default: ''},
});

const form = reactive({
    phone_number: '',
    esim_id: '',
});

//<editor-fold desc="PhoneNum">
const loading = ref(false);
const selectAll = ref(false);
const selecteds = ref([]);
const phonenums = ref({'data': []});
const paginationLinksLimit = ref(5);
const esimStore = useEsimStore();

const toggleSelection = (phonenum) => {
    const index = selecteds.value.indexOf(phonenum.id);
    if (index === -1) {
        selecteds.value.push(phonenum.id);
    } else {
        selecteds.value.splice(index, 1);
    }
};
const selectAllPhonenums = () => {
    if (selectAll.value) {
        selecteds.value = phonenums.value.data.map(phonenum => phonenum.id);
    } else {
        selecteds.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getPhonenums();
}

const searchQuery = ref(null);
const getPhonenums = (page = 1) => {
    let linkstr = '/api/' + (props.modeltype === '' ? 'phonenums' : props.modeltype + '/' + props.modelid + '/phonenums');
    console.log("getPhonenums launched, : props.modeltype: ", props.modeltype, ", props.modelid: ", props.modelid, ", linkstr: ", linkstr, ", page: ", page);
    loading.value = true;
    axios.get( `${linkstr}?page=${page}`, {
        params: {
            query: searchQuery.value,
        }
    })
        .then((response) => {
            console.log("getPhonenums, response: ", response);
            phonenums.value = response.data;
        }).finally(() => {
        loading.value = false;
    });
}

const loadingPhoneNumDelete = ref(false);
const phonenumIdBeingDeleted = ref(null);

const confirmPhonenumDeletion = (phonenum) => {
    phonenumIdBeingDeleted.value = phonenum.id;
    $('#deletePhoneNumModal').modal('show');
};

const deletePhoneNum = () => {
    loadingPhoneNumDelete.value = true;
    axios.delete(`/api/phonenums/${phonenumIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimStateModal').modal('hide');
            toastr.success('Numero Téléphone supprimé avec succès !');
            phonenums.value.data = phonenums.value.data.filter(phonenum => phonenum.id !== phonenumIdBeingDeleted.value);
            loadingPhoneNumDelete.value = false;
        });
};

const loadingPhoneNumEsimRecycle = ref(false);
const phonenumEsimBeingRecycled = ref(null);
//const phonenumIdEsimBeingRecycled = ref(null);

const confirmPhonenumEsimRecycle = (phonenum) => {
    phonenumEsimBeingRecycled.value = phonenum;
    pickupNewEsim();
};

const pickupNewEsim = () => {
    esimStore.pickupEsim();
    $('#pickupEsimModal').modal('show');
};

const pickupNewEsimSaved = () => {
    $('#pickupEsimModal').modal('hide');
    form.esim_id = esimStore.esimpicked.id;
    loadingPhoneNumEsimRecycle.value = true;

    saveEsimRecycle();
};

const saveEsimRecycle = () => {
    Swal.fire({
        html: '<small>Confirmer Affectation de nouvelle eSIM au <b>' + phonenumEsimBeingRecycled.value.phone_number + '</b></small>',
        icon: 'warning',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonText: 'Valider',
        cancelButtonText: 'Annuler',
        preConfirm: () => {
            loadingPhoneNumEsimRecycle.value = true;
            return axios.put(`/api/phonenums/${phonenumEsimBeingRecycled.value.uuid}/esimrecycle`, form)
                .then(response => {
                    console.log("confirmPhonenumEsimRecycle, preConfirm, response: ", response);
                    return response;
                })
                .catch(error => {
                    //console.log("request failed: ", error)
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
        }, allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.value) {
            console.log("confirmPhonenumEsimRecycle, DONE, response: ", result);
            setPhoneNumUpdated(result.value.data)
            Swal.fire({
                html: '<small>Nouvelle eSIM affectée avec Succes !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                const index = phonenums.value.data.findIndex(phonenum => phonenum.id === result.value.data.id);
                phonenums.value.data[index] = result.value.data;

                loadingPhoneNumEsimRecycle.value = false;

                window.location = '/clientesims.previewpdf/' + result.value.data.id;
            })
        }
    })
};

const pickupNewEsimCanceled = () => {
    esimStore.pickupEsimReset();
    $('#pickupEsimModal').modal('hide');
};

const setPhoneNumUpdated = (phonenum) => {
    const index = phonenums.value.data.findIndex(pnum => pnum.id === phonenum.id);
    phonenums.value.data[index] = phonenum;
}

watch(searchQuery, debounce(() => {
    //getPhonenums();
}, 300));
//</editor-fold>

const initComponent = () => {
    getPhonenums();
};

onMounted(() => {
    initComponent();
});
</script>

<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-warning card-outline direct-chat direct-chat-primary" :class="modeltype === '' ? '' : 'collapsed-card'">
                <div class="card-header">
                    <h5 v-show="modeltype !== ''" class="card-title">Numéro(s) Téléphone</h5>
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
                                <router-link v-if="can( ( props.modeltype === '' ? 'phonenums' : props.modeltype + '-phonenum' ) + '-add')" :to="{
                                            name: 'phonenums.create',
                                            params: {
                                                modeltype: modeltype,
                                                modelid: modelid
                                            }
                                        }">
                                    <button type="button" class="mb-2 btn btn-sm btn-warning btn-xs">
                                        <i class="fa fa-plus-circle mr-1"></i>
                                        Nouveau
                                    </button>
                                </router-link>

                                <div v-if="selecteds.length > 0">
                                    <span class="ml-2"> {{ selecteds.length }} Numéro(s) sélectionnés(s)</span>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="input-group mb-3">
                                    <input @keyup.enter="getPhonenums" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                                    <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-default" @click="getPhonenums">
                                            <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span v-else><i class="fa fa-search"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <Bootstrap4Pagination :data="phonenums" @pagination-change-page="getPhonenums" size="small" :limit="paginationLinksLimit" align="right" />
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllPhonenums" /></th>
                                <th style="width: 10px">#</th>
                                <th class="text text-xs">Numéro</th>
                                <th class="text text-xs">Titulaire</th>
                                <th class="text text-xs">IMSI</th>
                                <th class="text text-xs">ICCID</th>
                                <th class="text text-xs" v-if="can( ( modeltype === '' ? 'creators' : modeltype + '-creator' ) + '-show')">Créé Par</th>
                                <th class="text text-xs">Création</th>
                                <th class="text text-xs">Modification</th>
                                <th class="text text-xs">Options</th>
                            </tr>
                            </thead>
                            <tbody v-if="phonenums.data.length > 0">
                            <PhonenumListItem v-for="(phonenum, index) in phonenums.data"
                                                        key="esimstate.id"
                                                        :phonenum=phonenum
                                                        :modeltype=modeltype
                                                        :modelid=modelid
                                                        @confirm-phonenum-deletion="confirmPhonenumDeletion"
                                                        @confirm-phonenum-esim-recycle="confirmPhonenumEsimRecycle"
                                                        :selecteds="selecteds"
                                                        :index=index
                                                        @toggle-selection="toggleSelection"
                                                        :selectAll="selectAll"
                            />
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td colspan="10" class="text-center">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>{{ loading ? ' Chargement en cours...' : ' Aucun résultat trouvé...' }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <span v-if="phonenums.meta?.total > 0" class="text text-xs text-primary">{{ phonenums.meta.total + ' enregistrement' + (phonenums.meta.total > 1 ? 's' : '') }}</span>
                        <Bootstrap4Pagination v-if="modeltype === ''" :data="phonenums" @pagination-change-page="getPhonenums" size="small" :limit="paginationLinksLimit" align="right" />
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

    <div class="modal fade" id="deletePhoneNumModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Numéro Téléphone</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer ce Numéro ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deletePhoneNum" type="button" class="btn btn-xs btn-danger" :disabled="loadingPhoneNumDelete">
                        <span v-if="loadingPhoneNumDelete" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <pickupEsimModal @pickup-saved="pickupNewEsimSaved" @pickup-canceled="pickupNewEsimCanceled"></pickupEsimModal>
</template>

<style scoped>

</style>
