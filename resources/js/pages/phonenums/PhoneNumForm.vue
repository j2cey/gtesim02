<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import Swal from "sweetalert2";
import StatusShow from "../statuses/StatusShow.vue";

// TODO: Manage PhoneNum Posi
// TODO: Add a Cancel button to reset the form

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    phone_number: '',
    posi: '0',
    model_selected: 'XXX'
});

const loading = ref(false);
const formMode = ref('create');
const phonenum = ref({});
const phonenumId = ref(null);
const creator = ref({});

const lastPath = ref('/');

const modeltype = ref('');
const modelid = ref('');

const handleSubmit = (values, actions) => {
    if (formMode.value === 'edit') {
        updatePhoneNum(values, actions);
    } else if (formMode.value === 'create') {
        createPhoneNum(values, actions);
    }
};

const createPhoneNum = (values, actions) => {
    loading.value = true;
    axios.put(`/api/${modeltype.value}/${modelid.value}/phonenums/add`, form)
        .then((response) => {
            console.log("createPhoneNum, response: ", response);
            // router.push('/phonenum');
            formMode.value = 'edit';

            if (modeltype.value === 'clientesim') {
                Swal.fire({
                    html: '<small>Numéro Téléphone créé avec succès !</small>',
                    icon: 'success',
                    timer: 3000
                }).then(() => {
                    window.location = '/clientesims.previewpdf/' + response.data?.phonenum.id;
                });
            } else {
                toastr.success('Numéro Téléphone créé avec succès !');
            }
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
        .finally(() => {
            loading.value = false;
        })
};

const updatePhoneNum = (values, actions) => {
    loading.value = true;
    axios.put(`/api/${modeltype.value}/${modelid.value}/phonenums/${phonenumId.value}`, form)
        .then((response) => {
            toastr.success('Numéro Téléphone modifié avec succès !');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
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
        phonenumId.value = route.params.id;
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
                                            <input v-model="form.phone_number" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.phone_number }" id="phone_number" placeholder="Numéro">
                                            <span class="invalid-feedback">{{ errors.phone_number }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="formMode === 'edit'">
                                        <div class="form-group">
                                            <label for="iccid"><span class="text text-xs">Position</span></label>
                                            <input v-model="form.posi" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.posi }" id="posi" placeholder="Position">
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
                                                        :modelid="phonenumId"
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
                                        <i class="fa fa-save mr-1"></i> Valider
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

</template>

<style>

</style>
