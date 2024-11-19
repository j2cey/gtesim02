<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import Swal from "sweetalert2";
import StatusShow from "@/pages/statuses/StatusShow.vue";

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
const errorMessage = ref({});
const formMode = ref('create');
const emailaddress = ref({});
const emailaddressId = ref(null);
const creator = ref({});

const lastPath = ref('/emailaddresses');

const modeltype = ref('');
const modelid = ref('');

const handleSubmit = (values, actions) => {
    errorMessage.value = {};
    if (formMode.value === 'edit') {
        updateEmailAddress(values, actions);
    } else if (formMode.value === 'create') {
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
            formMode.value = 'edit';

            Swal.fire({
                html: '<small>E-Mail créée avec succès !</small>',
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

const updateEmailAddress = (values, actions) => {
    loading.value = true;
    form.posi -= 1;
    axios.put(`/api/emailaddresses/${emailaddressId.value}`, form)
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
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
                errorMessage.value = error.response?.data.errors;
            }
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
            status.value = response.data.status;

            emailaddress.value = response.data;
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
    if ( emailaddress.value && emailaddress.value.hasemailaddress ) {
        return emailaddress.value.hasemailaddress.emailaddress_maxposi;
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

    if (route.name === 'emailaddresses.edit' || route.name === 'emailaddresses.show') {
        if (route.name === 'emailaddresses.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        emailaddressId.value = route.params.id;
        getEmailAddress();
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
                        E-Mail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/emailaddresses">E-Mails</router-link>
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
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Statut</span></label> <br>
                                            <StatusShow :key="statusKey" v-if="status"
                                                        :status="status"
                                                        @status-changed="statusChanged"
                                                        :modelclass="emailaddress.modelclass"
                                                        :modeltype="emailaddress.modeltype"
                                                        :modelid="emailaddressId"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Titulaire</span></label>
                                            <span class="form-control border-0 text-xs">{{ emailaddress.hasemailaddress?.intitule }}</span>
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
