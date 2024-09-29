<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import Swal from "sweetalert2";

// TODO: Manage EmailAddress Posi
// TODO: Add a Cancel button to reset the form

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    email_address: '',
    posi: '',
});

const loading = ref(false);
const editMode = ref(false);
const emailaddress = ref({});
const emailaddressId = ref(null);
const creator = ref({});

const lastPath = ref('/');

const modeltype = ref('');
const modelid = ref('');

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updateEmailAddress(values, actions);
    } else {
        createEmailAddress(values, actions);
    }
};

const createEmailAddress = (values, actions) => {
    loading.value = true;
    axios.put(`/api/${modeltype.value}/${modelid.value}/emailaddresses/add`, form)
        .then((response) => {
            console.log("createEmailAddress, response: ", response);

            emailaddress.value = response.data;
            emailaddressId.value = response.data.id;
            editMode.value = true;

            Swal.fire({
                html: '<small>E-Mail créée avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                router.push(prevRoutePath.value);
            });
        })
        .catch((error) => {
            actions.setErrors(error.response?.data.errors);
        })
        .finally(() => {
            loading.value = false;
        })
};

const updateEmailAddress = (values, actions) => {
    loading.value = true;
    axios.put(`/api/${modeltype.value}/${modelid.value}/emailaddresses/${emailaddressId.value}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>E-Mail modifié avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                router.push(prevRoutePath.value);
            });
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
        .finally(() => {
            loading.value = false;
    })
};

const getEmailAddress = () => {
    axios.get(`/api/emailaddresses/${route.params.id}/edit`)
        .then((response) => {
            console.log("getEmailAddress, response: ", response)
            form.email_address = response.data.email_address;
            form.posi = response.data.posi;

            creator.value = response.data.creator;

            emailaddress.value = response.data;
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
    console.log('EmailAddressForm onMounted, route.params: ', route.params);
    if (route.name === 'emailaddresses.edit') {
        emailaddressId.value = route.params.id;
        editMode.value = true;
        getEmailAddress();
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
                        E-Mail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/">E-Mails</router-link>
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
                                            <label for="imsi">Adresse Mail</label>
                                            <input v-model="form.email_address" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.email_address }" id="email_address" placeholder="Adresse Mail">
                                            <span class="invalid-feedback">{{ errors.email_address }}</span>
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
