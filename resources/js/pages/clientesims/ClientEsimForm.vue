<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import { debounce } from "lodash";
import EsimClientPhonenumListItem from "./EsimClientPhonenumListItem.vue";
import EsimClientEmailAdressListItem from "./EsimClientEmailAdressListItem.vue";
import {Bootstrap4Pagination} from "laravel-vue-pagination";
import Swal from 'sweetalert2';
import {useAbility} from "@casl/vue";
import { formatDate } from '../../helper.js'


// TODO: Add PhoneNum to ClientEsim
// TODO: Add EmailAddress to ClientEsim

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

const modeltype = ref('clientesim');
const loading = ref(false);
const formMode = ref('create');
const phonenums = ref({'data': []});
const emailaddresses = ref({'data': []});
const creator = ref({});
const updator = ref({});
const status = ref({});

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
            console.log("execs after getClientEsim")
            getPhonenums();
            getEmailaddresses();
        }

    )
};
//</editor-fold>

//<editor-fold desc="PhoneNum">
const loadingPhonenums = ref(false);
const selectPhonenumsAll = ref(false);
const selectedPhonenums = ref([]);
const togglePhonenumsSelection = (phonenum) => {
    const index = selectedPhonenums.value.indexOf(phonenum.id);
    if (index === -1) {
        selectedPhonenums.value.push(phonenum.id);
    } else {
        selectedPhonenums.value.splice(index, 1);
    }
};
const selectAllPhonenums = () => {
    if (selectPhonenumsAll.value) {
        selectedPhonenums.value = phonenums.value.data.map(phonenum => phonenum.id);
    } else {
        selectedPhonenums.value = [];
    }
}

const clearSearchPhonenumQuery = () => {
    searchPhonenumQuery.value = '';
    getPhonenums();
}

const searchPhonenumQuery = ref(null);
const getPhonenums = (page = 1) => {
    loadingPhonenums.value = true;
    axios.get(`/api/clientesims/${clientesimId.value}/phonenumindex?page=${page}`, {
        params: {
            query: searchPhonenumQuery.value,
        }
    })
        .then((response) => {
            console.log("getPhonenums, response: ", response);
            phonenums.value = response.data;
        }).finally(() => {
        loadingPhonenums.value = false;
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
const phonenumIdEsimBeingRecycled = ref(null);

const confirmPhonenumEsimRecycle = (phonenum) => {
    phonenumIdEsimBeingRecycled.value = phonenum.uuid;
    //$('#recyclePhoneNumEsimModal').modal('show');
    Swal.fire({
        html: '<small>Affecter une nouvelle eSIM au <b>' + phonenum.numero + '</b></small>',
        icon: 'warning',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonText: 'Valider',
        cancelButtonText: 'Annuler',
        preConfirm: () => {
            return axios.put(`/api/phonenums/${phonenumIdEsimBeingRecycled.value}/esimrecycle`, null)
                .then(response => {
                    console.log("confirmPhonenumEsimRecycle, preConfirm, response: ", response);
                    /*
                    if (!response.ok) {
                    throw new Error(response.statusText)
                    }*/
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
                router.push({ name: 'phonenums.previewpdf', params: { id: result.value.data.id } });
            })
        }
    })
};

const setPhoneNumUpdated = (phonenum) => {
    const index = phonenums.value.data.findIndex(pnum => pnum.id === phonenum.id);
    phonenums.value.data[index] = phonenum;
}

const recyclePhoneNumEsim = () => {
    loadingPhoneNumEsimRecycle.value = true;
    axios.put(`/api/phonenums/${phonenumIdEsimBeingRecycled.value}/esimrecycle`)
        .then((response) => {
            console.log("recyclePhoneNumEsim, response: ", response);

            $('#recyclePhoneNumEsimModal').modal('hide');
            toastr.success('Esim recyclé avec succès !');

            const index = phonenums.value.findIndex(phonenum => phonenum.id === response.data.id);
            phonenums.value.data[index] = response.data;

            loadingPhoneNumEsimRecycle.value = false;
        });
};

watch(searchPhonenumQuery, debounce(() => {
    //getPhonenums();
}, 300));
//</editor-fold>

//<editor-fold desc="EmailAddress">
const loadingEmailaddresses = ref(false);
const selectEmailaddressesAll = ref(false);
const selectedEmailaddresses = ref([]);
const toggleEmailaddressesSelection = (emailaddress) => {
    const index = selectedEmailaddresses.value.indexOf(emailaddress.id);
    if (index === -1) {
        selectedEmailaddresses.value.push(emailaddress.id);
    } else {
        selectedEmailaddresses.value.splice(index, 1);
    }
};
const selectAllEmailaddresses = () => {
    if (selectEmailaddressesAll.value) {
        selectedEmailaddresses.value = emailaddresses.value.data.map(emailaddress => emailaddress.id);
    } else {
        selectedEmailaddresses.value = [];
    }
}

const clearSearchEmailaddressQuery = () => {
    searchEmailaddressQuery.value = '';
    getEmailaddresses();
}

const searchEmailaddressQuery = ref(null);
const getEmailaddresses = (page = 1) => {
    loadingEmailaddresses.value = true;
    axios.get(`/api/clientesims/${clientesimId.value}/emailaddressindex?page=${page}`, {
        params: {
            query: searchEmailaddressQuery.value,
        }
    })
        .then((response) => {
            console.log("getEmailaddresses, response: ", response);
            emailaddresses.value = response.data;
        }).finally(() => {
        loadingEmailaddresses.value = false;
    });
}

const loadingEmailAddressDelete = ref(false);
const emailaddressIdBeingDeleted = ref(null);

const confirmEmailaddressDeletion = (emailaddress) => {
    emailaddressIdBeingDeleted.value = emailaddress.id;
    $('#deleteEmailAddressModal').modal('show');
};

const deleteEmailAddress = () => {
    loadingEmailAddressDelete.value = true;
    axios.delete(`/api/emailaddresses/${emailaddressIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimStateModal').modal('hide');
            toastr.success('Adresse E-Mail supprimé avec succès !');
            emailaddresses.value.data = emailaddresses.value.data.filter(emailaddress => emailaddress.id !== emailaddressIdBeingDeleted.value);
            loadingEmailAddressDelete.value = false;
        });
};

watch(searchEmailaddressQuery, debounce(() => {
    //getEmailaddresses();
}, 300));
//</editor-fold>

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
                            <router-link to="/dashboard">Accueil</router-link>
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
                        <div class="card-body" v-if="formMode === 'edit' || formMode === 'create'">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="imsi">Nom</label>
                                            <input v-model="form.nom_raison_sociale" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.nom_raison_sociale }" id="nom_raison_sociale" placeholder="Nom" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.nom_raison_sociale }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="iccid">Prénom</label>
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
                                            <label for="puk">Statut</label>
                                            <input v-if="status" v-model="status.name" type="text" class="form-control form-control-sm" :class="'text-' + status.style" id="status" placeholder="status" readonly>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk">Créé Par</label>
                                            <input v-if="creator" v-model="creator.name" type="text" class="form-control form-control-sm" id="creator" placeholder="creator" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="formMode === 'edit' || formMode === 'create'" type="submit" class="btn btn-sm btn-primary m-2" :disabled="loading">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                         Valider
                                    </button>
                                    <router-link :to="prevRoutePath">
                                        <button type="submit" class="btn btn-sm btn-default m-2">Retour</button>
                                    </router-link>
                                </div>
                            </Form>
                        </div>

                        <div class="card-body" v-else-if="formMode === 'addphone'">
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
                                        Valider
                                    </button>
                                    <button type="button" @click="cancelAddphone" class="btn btn-sm btn-default m-2">Annuler</button>
                                </div>
                            </Form>
                        </div>

                    </div>
                </div>
            </div>

            <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                <div class="col-lg-12">
                    <div class="card card-warning card-outline direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h5 class="card-title">Numéro(s) Téléphone</h5>
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
                                        <router-link v-if="can('clientesim-phonenum-add')" :to="{
                                            name: 'phonenums.create',
                                            params: {
                                                modeltype: modeltype,
                                                modelid: clientesimId
                                            }
                                        }">
                                            <button type="button" class="mb-2 btn btn-sm btn-warning btn-xs">
                                                <i class="fa fa-plus-circle mr-1"></i>
                                                Nouveau
                                            </button>
                                        </router-link>

                                        <div v-if="selectedPhonenums.length > 0">
                                            <span class="ml-2"> {{ selectedPhonenums.length }} Numéro(s) sélectionnés(s)</span>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-group mb-3">
                                            <input @keyup.enter="getPhonenums" type="search" v-model="searchPhonenumQuery" class="form-control text-xs" placeholder="Recherche text..." />
                                            <button v-if="searchPhonenumQuery && !loadingPhonenums" @click="clearSearchPhonenumQuery" type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-default" @click="getPhonenums">
                                                    <div v-if="loadingPhonenums" class="spinner-border spinner-border-sm" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <span v-else><i class="fa fa-search"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <Bootstrap4Pagination :data="phonenums" @pagination-change-page="getPhonenums" size="small" align="right" :limit="3" />
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" v-model="selectPhonenumsAll" @change="selectAllPhonenums" /></th>
                                        <th style="width: 10px">#</th>
                                        <th class="text text-sm">Numéro</th>
                                        <th class="text text-sm">IMSI</th>
                                        <th class="text text-sm">ICCID</th>
                                        <th class="text text-sm">Créé Par</th>
                                        <th class="text text-sm">Création</th>
                                        <th class="text text-sm">Modification</th>
                                        <th class="text text-sm">Options</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="phonenums.data.length > 0">
                                    <EsimClientPhonenumListItem v-for="(phonenum, index) in phonenums.data"
                                                        key="esimstate.id"
                                                        :phonenum=phonenum
                                                        :clientesim=clientesim
                                                        @confirm-phonenum-deletion="confirmPhonenumDeletion"
                                                        @confirm-phonenum-esim-recycle="confirmPhonenumEsimRecycle"
                                                        :selectedPhonenums="selectedPhonenums"
                                                        :index=index
                                                        @toggle-selection="togglePhonenumsSelection"
                                                        :selectAll="selectPhonenumsAll"
                                    />
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <span v-if="loadingPhonenums" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            <span>{{ loadingPhonenums ? ' Chargement en cours...' : ' Aucun résultat trouvé...' }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                    </div>
                </div>
            </div>

            <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                <div class="col-lg-12">
                    <div class="card card-success card-outline direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h3 class="card-title">Adresse(s) E-Mail</h3>
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
                                        <router-link v-if="can('clientesim-emailaddress-add')" :to="{
                                            name: 'emailaddresses.create',
                                            params: {
                                                modeltype: modeltype,
                                                modelid: clientesimId
                                            }
                                        }">
                                            <button type="button" class="mb-2 btn btn-sm btn-success btn-xs">
                                                <i class="fa fa-plus-circle mr-1"></i>
                                                Nouveau
                                            </button>
                                        </router-link>

                                        <div v-if="selectedEmailaddresses.length > 0">
                                            <span class="ml-2"> {{ selectedEmailaddresses.length }} E-Mail(s) sélectionnés(s)</span>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-group mb-3">
                                            <input @keyup.enter="getEmailaddresses" type="search" v-model="searchEmailaddressQuery" class="form-control text-xs" placeholder="Recherche text..." />
                                            <button v-if="searchEmailaddressQuery && !loadingEmailaddresses" @click="clearSearchEmailaddressQuery" type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-default" @click="getEmailaddresses">
                                                    <div v-if="loadingEmailaddresses" class="spinner-border spinner-border-sm" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <span v-else><i class="fa fa-search"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <Bootstrap4Pagination :data="emailaddresses" @pagination-change-page="getEmailaddresses" size="small" align="right" :limit="3" />
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" v-model="selectEmailaddressesAll" @change="selectAllEmailaddresses" /></th>
                                        <th style="width: 10px">#</th>
                                        <th class="text text-sm">Email</th>
                                        <th class="text text-sm">Créé Par</th>
                                        <th class="text text-sm">Création</th>
                                        <th class="text text-sm">Modification</th>
                                        <th class="text text-sm">Options</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="emailaddresses.data.length > 0">
                                    <EsimClientEmailAdressListItem v-for="(emailaddress, index) in emailaddresses.data"
                                                                   key="emailaddress.id"
                                                                   :emailaddress=emailaddress
                                                                   :clientesim=clientesim
                                                                   @confirm-emailaddress-deletion="confirmEmailaddressDeletion"
                                                                   :selectedEmailaddresses="selectedEmailaddresses"
                                                                   :index=index
                                                                   @toggle-selection="toggleEmailaddressesSelection"
                                                                   :selectAll="selectEmailaddressesAll"
                                    />
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <span v-if="loadingEmailaddresses" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            <span>{{ loadingEmailaddresses ? ' Chargement en cours...' : 'Aucun résultat trouvé...' }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                    </div>
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

    <div class="modal fade" id="deleteEmailAddressModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Adresse E-Mail</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer cet E-Mail ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteEmailAddress" type="button" class="btn btn-xs btn-danger" :disabled="loadingEmailAddressDelete">
                        <span v-if="loadingEmailAddressDelete" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                         Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="recyclePhoneNumEsimModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Recyclage Numéro</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="text text-center">Recycler la Esim de ce Numero ?</h5>
                    <p class="text text-center text-xs text-danger">une nouvelle Esim sera affectée</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="recyclePhoneNumEsim" type="button" class="btn btn-xs btn-danger" :disabled="loadingPhoneNumEsimRecycle">
                        <span v-if="loadingPhoneNumEsimRecycle" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                         Recycler
                    </button>
                </div>
            </div>
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
