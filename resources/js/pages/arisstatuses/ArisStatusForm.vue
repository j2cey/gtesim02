<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import Swal from "sweetalert2";

// TODO: Add a Cancel button to reset the form

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    status: '',
    response_message: '',
});

const loading = ref(false);
const errorMessage = ref({});
const formMode = ref('create');
const arisstatus = ref({});
const arisstatusId = ref(null);

const lastPath = ref('/arisstatuses');

const modeltype = ref('');
const modelid = ref('');

const handleSubmit = (values, actions) => {
    errorMessage.value = {};
    if (formMode.value === 'edit') {
        updateArisStatus(values, actions);
    } else if (formMode.value === 'create') {
        createArisStatus(values, actions);
    }
};

const createArisStatus = (values, actions) => {
    loading.value = true;
    axios.post(`/api/arisstatuses`, form)
        .then((response) => {
            console.log("createArisStatus, response: ", response);

            arisstatus.value = response.data;
            arisstatusId.value = response.data.id;
            formMode.value = 'edit';

            Swal.fire({
                html: '<small>Statut ARIS créé avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                router.push(prevRoutePath.value);
            });
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions?.setErrors(error.response?.data.errors);
                errorMessage.value = error.response?.data.errors;
            }
        })
        .finally(() => {
            loading.value = false;
        })
};

const updateArisStatus = (values, actions) => {
    loading.value = true;
    form.posi -= 1;
    axios.put(`/api/arisstatuses/${arisstatusId.value}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>Statut ARIS modifié avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                router.push(prevRoutePath.value);
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

const getArisStatus = () => {
    axios.get(`/api/arisstatuses/${route.params.id}/edit`)
        .then((response) => {
            console.log("getArisStatus, response: ", response)
            form.response_message = response.data.response_message;
            form.status = response.data.status;

            arisstatus.value = response.data;
        }).then(() => {

        }

    )
};

const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

onMounted(() => {
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    modeltype.value = route.params.modeltype;
    modelid.value = route.params.modelid;

    if (route.name === 'arisstatuses.edit' || route.name === 'arisstatuses.show') {
        if (route.name === 'arisstatuses.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        arisstatusId.value = route.params.id;
        getArisStatus();
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
                        Statut ARIS</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/arisstatuses">Statuts ARIS</router-link>
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
                                            <label for="imsi">Adresse Mail</label>
                                            <input v-model="form.email_address" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.email_address }" id="email_address" placeholder="Adresse Mail">
                                            <span class="invalid-feedback">{{ errors.email_address }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="formMode === 'edit'">
                                        <div class="form-group">
                                            <label for="iccid">Position</label>
                                            <input type="number" v-model="form.posi" min="1" :max="posimax" onkeydown="return false" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.posi }" id="posi" placeholder="Position">
                                            <span class="invalid-feedback">{{ errors.posi }}</span>
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

</template>

<style>

</style>
