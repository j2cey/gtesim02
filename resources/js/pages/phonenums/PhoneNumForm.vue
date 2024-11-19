<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import Swal from "sweetalert2";
import StatusShow from "../statuses/StatusShow.vue";
import { useEsimStore } from '../../stores/EsimStore.js';

import pickupEsimModal from "../esims/EsimPickup.vue"

// TODO: Add a Cancel button to reset the form
// TODO: Phone Num Status Show ?
// TODO: Phone Num Show

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    phone_number: '',
    posi: '0',
    model_selected: 'XXX',
    esim_id: null
});

const loading = ref(false);
const errorMessage = ref({});
const formMode = ref('create');
const phonenum = ref({});
const phonenumid = ref(null);
const creator = ref({});

const lastPath = ref('/');

const modeltype = ref('');
const modelid = ref('');
const esimStore = useEsimStore();

const handleSubmit = (values, actions) => {
    errorMessage.value = {};
    if (formMode.value === 'edit') {
        updatePhoneNum(values, actions);
    } else if (formMode.value === 'create') {
        createPhoneNum(values, actions);
    }
};

const pickupNewEsim = () => {
    esimStore.pickupEsim();
    $('#pickupEsimModal').modal('show');
};
const pickupNewEsimSaved = () => {
    $('#pickupEsimModal').modal('hide');
    form.esim_id = esimStore.esimpicked.id;
    if (formMode.value === 'create' && modeltype.value === 'clientesims') {
        storePhoneNum();
    }
    esimStore.pickupEsimReset();
};
const pickupNewEsimCanceled = () => {
    esimStore.pickupEsimReset();
    $('#pickupEsimModal').modal('hide');
};

const createPhoneNum = (values, actions) => {
    if (modeltype.value === 'clientesims') {
        validatePhoneNum(values, actions);
    } else {
        storePhoneNum(values, actions);
    }
};

const validatePhoneNum = (values, actions) => {
    loading.value = true;
    axios.put(`/api/${modeltype.value}/${modelid.value}/phonenums/validate`, form)
        .then((response) => {
            console.log("createPhoneNum, response: ", response);
            // router.push('/phonenum');
            pickupNewEsim();
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
                errorMessage.value = error.response?.data.errors;
            }
        })
        .finally(() => {
            loading.value = false;
        })
};

const storePhoneNum = (values, actions) => {
    loading.value = true;
    axios.put(`/api/${modeltype.value}/${modelid.value}/phonenums/add`, form)
        .then((response) => {
            console.log("createPhoneNum, response: ", response);
            // router.push('/phonenum');
            formMode.value = 'edit';

            Swal.fire({
                html: '<small>Numéro Téléphone créé avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                form.posi = response.data.posi;
                phonenum.value = response.data;
                if (modeltype.value === 'clientesims') {
                    window.location = '/clientesims.previewpdf/' + response.data?.phonenum.id;
                }
            });
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
                errorMessage.value = error.response?.data.errors;
            }
        })
        .finally(() => {
            loading.value = false;
        })
};

const updatePhoneNum = (values, actions) => {
    loading.value = true;
    form.posi -= 1;
    axios.put(`/api/phonenums/${phonenumid.value}`, form)
        .then((response) => {
            console.log("updatePhoneNum, response: ", response)
            Swal.fire({
                html: '<small>Numéro Téléphone modifié avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                phonenum.value = response.data;
            });
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
                errorMessage.value = error.response?.data.errors;
            }
        })
        .finally(() => {
            loading.value = false;
    })
};

const getPhoneNum = () => {
    axios.get(`/api/phonenums/${route.params.id}/edit`)
        .then((response) => {
            console.log("getPhoneNum, response: ", response)
            form.phone_number = response.data.phone_number;
            form.posi = response.data.posi;

            creator.value = response.data.creator;

            phonenum.value = response.data;
            status.value = response.data.status;
        }).then(() => {

        }

    )
};

//<editor-fold desc="Status">
const status = ref({});
const statusChanged = (obj) => {
    status.value = obj;
};

const statusKey = computed(() => {
    return status.value.uuid;
});
//</editor-fold>

const posimax = computed(() => {
    if ( phonenum.value && phonenum.value.hasphonenum ) {
        return phonenum.value.hasphonenum.phonenum_maxposi;
    } else {
        return 1;
    }
});

const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

onMounted(() => {
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    modeltype.value = route.params.modeltype;
    modelid.value = route.params.modelid;
    form.model_selected = route.params.modelid;

    if (route.name === 'phonenums.edit' || route.name === 'phonenums.show') {
        if (route.name === 'phonenums.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        phonenumid.value = route.params.id;
        getPhoneNum();
    } else {
        formMode.value = 'create';
    }
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
                        Numéro Téléphone</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/phonenums">Numéros Téléphone</router-link>
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
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="imsi"><span class="text text-xs">Numéro</span></label>
                                            <input v-model="form.phone_number" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.phone_number }" id="phone_number" placeholder="Numéro">
                                            <span class="invalid-feedback">{{ errors.phone_number }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="formMode === 'edit'">
                                        <div class="form-group">
                                            <label for="posi"><span class="text text-xs">Position</span></label>
                                            <input type="number" v-model="form.posi" min="1" :max="posimax" onkeydown="return false" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.posi }" id="posi" placeholder="Position">
                                            <span class="invalid-feedback">{{ errors.posi }}</span>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Statut</span></label> <br>
                                            <StatusShow :key="statusKey" v-if="status"
                                                        :status="status"
                                                        @status-changed="statusChanged"
                                                        :modelclass="phonenum.modelclass"
                                                        :modeltype="phonenum.modeltype"
                                                        :modelid="phonenumid"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Titulaire</span></label>
                                            <span class="form-control border-0 text-xs">{{ phonenum.hasphonenum?.intitule }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary m-2">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <i v-else class="fa fa-save mr-1"></i>
                                        Valider
                                    </button>
                                    <router-link :to="prevRoutePath">
                                        <button type="submit" class="btn btn-sm btn-default m-2">
                                            <i class="fa fa-backward mr-1"></i> Retour
                                        </button>
                                    </router-link>
                                </div>
                            </Form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <pickupEsimModal @pickup-saved="pickupNewEsimSaved" @pickup-canceled="pickupNewEsimCanceled"></pickupEsimModal>
</template>

<style>

</style>
