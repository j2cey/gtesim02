<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import {debounce} from "lodash";
import Swal from "sweetalert2";

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
const editMode = ref(false);
const phonenum = ref({});
const phonenumId = ref(null);
const creator = ref({});

const lastPath = ref('/');

const modeltype = ref('');
const modelid = ref('');

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updatePhoneNum(values, actions);
    } else {
        createPhoneNum(values, actions);
    }
};

const createPhoneNum = (values, actions) => {
    loading.value = true;
    axios.put(`/api/${modeltype.value}/${modelid.value}/phonenums/add`, form)
        .then((response) => {
            console.log("createPhoneNum, response: ", response);
            // router.push('/phonenum');
            editMode.value = true;

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
        }).then(() => {

        }

    )
};

const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

onMounted(() => {
    lastPath.value = router.options.history.state.back;
    modeltype.value = route.params.modeltype;
    modelid.value = route.params.modelid;
    form.model_selected = route.params.modelid;

    console.log('PhoneNumForm onMounted, route.params: ', route.params);

    if (route.name === 'phonenums.edit') {
        phonenumId.value = route.params.id;
        editMode.value = true;
        getPhoneNum();
    }
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Modification</span>
                        <span v-else>Création</span>
                        Numéro Téléphone</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/">Numéros Téléphone</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Modification</span>
                            <span v-else>Création</span>
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
                                            <label for="imsi">Numéro</label>
                                            <input v-model="form.phone_number" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.phone_number }" id="phone_number" placeholder="Numéro">
                                            <span class="invalid-feedback">{{ errors.phone_number }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="editMode">
                                        <div class="form-group">
                                            <label for="iccid">Position</label>
                                            <input v-model="form.posi" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.posi }" id="posi" placeholder="Position">
                                            <span class="invalid-feedback">{{ errors.posi }}</span>
                                        </div>
                                    </div>

                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary m-2">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Valider
                                    </button>
                                    <router-link :to="prevRoutePath">
                                        <button type="submit" class="btn btn-sm btn-default m-2">Retour</button>
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
