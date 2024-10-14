<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed, watchEffect } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import PhonenumList from "../phonenums/PhonenumList.vue";
import EmailAddressList from "../emailaddresses/EmailAddressList.vue";
import StatusShow from "../statuses/StatusShow.vue";
import Swal from 'sweetalert2';
import { useAbility } from "@casl/vue";
import { formatDate } from '../../helper.js'

const { can, cannot } = useAbility();
const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    nom_raison_sociale: '',
    prenom: '',
    phone_number: '',
    email_address: '',
    esim_id: '',
    model_selected: null,
});

//const modeltype = ref('clientesims');
const loading = ref(false);
const formMode = ref('create');
const creator = ref({});
const updator = ref({});

const initForm = () => {
    form.nom_raison_sociale = '';
    form.prenom = '';
    form.phone_number = '';
    form.email_address = '';
    form.esim_id = '';
    form.model_selected = null;
}

//<editor-fold desc="ClientEsim">
const clientsMatched = ref([
    /*{id: 1,uuid: '96aac6d0-dfcc-4e19-9c1d-da99b3ce7864',nom_raison_sociale: 'Client 1', prenom: 'Prenom 1', created_at:'2022-07-13 23:16:27'},
    {id: 2,uuid: '96aac6d1-4220-4009-a8ae-9c5f456da120',nom_raison_sociale: 'Client 2', prenom: 'Prenom 2', created_at:'2022-07-15 23:16:27'},
    {id: 3,uuid: '96aac6d1-4829-4e18-a56d-25583d8b4db7',nom_raison_sociale: 'Client 3', prenom: 'Prenom 3', created_at:'2022-07-13 23:16:27'},*/
]);

const clientMatchedSelectChanged = () => {
    console.log(form.model_selected);
}
const handleSubmit = (values, actions) => {
    if (formMode.value === 'edit') {
        updateClientEsim(values, actions);
    } else if (formMode.value === 'create') {
        createClientEsim(values, actions);
    } else if (formMode.value === 'addphone') {
        addPhoneToClientEsim(values, actions);
    }
    actions.resetForm();
};

const createClientEsim = (values, actions) => {
    loading.value = true;
    form.model_selected = null;
    axios.post('/api/clientesims.checkbeforecreate', form)
        .then((response) => {
            // router.push('/clientesims');
            console.log("createClientEsim, response: ", response);
            if (response.data.action_type === 1) {
                clientsMatched.value = response.data.val;
                //this.clientesimForm = this.clientesimFormCheck;
                formMode.value = 'addphone';
            } else {
                clientSuccessfullySaved(response.data.val.clientesim, response.data.val.phonenum);
            }
        })
        .catch((error) => {
            console.log("createClientEsim, error: ", error);
            actions.setErrors(error.response?.data.errors);
        })
        .finally(() => {
            loading.value = false;
        })
};

const updateClientEsim = (values, actions) => {
    loading.value = true;
    axios.put(`/api/clientesims/${route.params.id}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>Client modifié avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {

            });
        })
        .catch((error) => {
            actions.setErrors(error.response?.data.errors);
        })
        .finally(() => {
            loading.value = false;
    })
};

const addPhoneToClientEsim = (values, actions) => {
    console.log("addPhoneToClientEsim, form: ", form)
    loading.value = true;
    axios.post(`/api/clientesim/phonenums/add`, form)
        .then((response) => {
            console.log("addPhoneToClientEsim, response: ", response);
            clientSuccessfullySaved(response.data.clientesim, response.data.phonenum);
        })
        .catch((error) => {
            actions.setErrors(error.response?.data.errors);
        })
        .finally(() => {
            loading.value = false;
    })
};

const cancelAddphone = () => {
    formMode.value = 'create';
};

const clientSuccessfullySaved = (clientesim, phonenum) => {
    console.log("clientSuccessfullySaved, resp:", clientesim, phonenum);
    let msg = '';
    if (formMode.value === 'create') {
        msg = 'Client créé avec Succes !';
    } else {
        msg = 'Numéro ajouté au Client avec succès !';
    }

    Swal.fire({
        html: '<small>' + msg + '</small>',
        icon: 'success',
        timer: 3000
    }).then(() => {
        initForm();
        window.location = '/clientesims.previewpdf/' + phonenum.id;
    });
}

const clientesim = ref({})
const clientesimId = ref(null)
const getClientEsim = () => {
    axios.get(`/api/clientesims/${route.params.id}/edit`)
        .then((response) => {
            console.log("getClientEsim, response: ", response)
            form.nom_raison_sociale = response.data.nom_raison_sociale;
            form.prenom = response.data.prenom;
            form.phone_number = response.data.phone_number;
            form.email_address = response.data.email_address;

            //phonenums.value = response.data.phonenums;
            //emailaddresses.value = response.data.emailaddresses;
            creator.value = response.data.creator;
            status.value = response.data.status;

            clientesim.value = response.data;
        }).then(() => {

        }

    )
};
//</editor-fold>

//<editor-fold desc="Status">
const status = ref({});
const statusChanged = (obj) => {
    status.value = obj;
};

const statusKey = computed(() => {
    return status.value.uuid;
});
//</editor-fold>

const currentPath = ref('/');
const lastPath = ref('/clientesims');
const prevRoutePath = computed(() => {
    return lastPath;// ? lastPath.value : '/clientesims';
});

watch(route, () => {
    console.log("watch route from ClientEsim");
    if (route.fullPath !== currentPath.value) {
        initComponent();
    }
});

const initComponent = () => {
    initForm();
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;
    if (route.name === 'clientesims.edit' || route.name === 'clientesims.show') {
        if (route.name === 'clientesims.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        clientesimId.value = route.params.id;
        getClientEsim();
    } else {
        formMode.value = 'create';
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
                    <h1 class="m-0">
                        <span v-if="formMode === 'edit'">Modification</span>
                        <span v-else-if="formMode === 'create'">Création</span>
                        <span v-else-if="formMode === 'addphone'">Ajout Téléphone</span>
                        <span v-else>Détails</span>
                        Client Esim</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/clientesims">Clients Esim</router-link>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="imsi" class="text text-sm">Nom</label>
                                            <input v-model="form.nom_raison_sociale" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.nom_raison_sociale }" id="nom_raison_sociale" placeholder="Nom" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.nom_raison_sociale }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="iccid" class="text text-sm">Prénom</label>
                                            <input v-model="form.prenom" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.prenom }" id="prenom" placeholder="Prénom" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.prenom }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div v-if="formMode === 'create'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin">Téléphone</label>
                                            <input v-model="form.phone_number" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.phone_number }" id="phone_number" placeholder="Numéro Téléphone">
                                            <span class="invalid-feedback">{{ errors.phone_number }}</span>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'create'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk">E-Mail</label>
                                            <input v-model="form.email_address" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.email_address }" id="email_address" placeholder="E-Mail">
                                            <span class="invalid-feedback">{{ errors.email_address }}</span>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk" class="text text-sm">Statut</label> <br>
                                            <StatusShow :key="statusKey" v-if="status"
                                                        :status="status"
                                                        @status-changed="statusChanged"
                                                        :modelclass="clientesim.modelclass"
                                                        :modeltype="clientesim.modeltype"
                                                        :modelid="clientesimId"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                    <div v-if="(formMode === 'edit' || formMode === 'show') && can('clientesims-creator-list')" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk" class="text text-sm">Créé Par</label>
                                            <input v-if="creator" v-model="creator.name" type="text" class="form-control form-control-sm" id="creator" placeholder="creator" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="formMode === 'edit' || formMode === 'create'" type="submit" class="btn btn-sm btn-primary m-2" :disabled="loading">
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

                        <div class="card-body" v-if="formMode === 'addphone'">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <label>Téléphone: <span class="badge badge-warning">{{ form.phone_number }}</span></label>
                                <br/>
                                <div class="card-body table-responsive p-0" style="min-height: 200px;">
                                    <table class="table m-0">
                                        <thead>
                                        <tr class="text text-sm">
                                            <th></th>
                                            <th>Nom</th>
                                            <th>Prenom</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(client, index) in clientsMatched" :key="client.id" class="text text-xs">
                                            <td>
                                                <div class="text border-right">
                                                    <input type="radio" @input="clientMatchedSelectChanged()" v-model="form.model_selected" :value="client.uuid"/>
                                                </div>
                                            </td>
                                            <td><div class="text border-right">{{ client.nom_raison_sociale }}</div></td>
                                            <td><div class="text border-right">{{ client.prenom }}</div></td>
                                            <td>{{ formatDate(client.created_at) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <span v-if="formMode === 'addphone'" class="text text-xs text-danger"><strong>Client Existant !</strong> <br/>Sélectionnez Le Client à attribuer ce Numéro de Téléphone</span>
                                </div>
                                <!-- /.table-responsive -->
                                <div class="btn-group">
                                    <button v-if="formMode === 'addphone'" type="submit" class="btn btn-sm btn-primary m-2" :disabled="loading">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <i class="fa fa-save mr-1"></i> Valider
                                    </button>
                                    <button type="button" @click="cancelAddphone" class="btn btn-sm btn-default m-2">Annuler</button>
                                </div>
                            </Form>
                        </div>

                    </div>
                </div>
            </div>

            <PhonenumList v-if="(formMode === 'edit' || formMode === 'show')"
                          :modeltype="clientesim.modeltype"
                          :modelid="clientesimId"
            ></PhonenumList>

            <EmailAddressList v-if="(formMode === 'edit' || formMode === 'show')"
                          :modeltype="clientesim.modeltype"
                          :modelid="clientesimId"
            ></EmailAddressList>

        </div>
    </div>

</template>

<style>
.no-extras {
    border: 1px !important;
    box-shadow: none !important;
    background-color: white !important;
    cursor: default !important;
}
</style>
