<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import { debounce } from "lodash";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";
import PhonenumList from "../phonenums/PhoneNumList.vue";
import EmailAddressList from "../emailaddresses/EmailAddressList.vue";
import StatusShow from "../statuses/StatusShow.vue";

const modeltype = ref('employes');
const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const user = ref({});
const form = reactive({
    nom: '',
    prenom: '',
    matricule: '',
    adresse: '',
    objectguid: '',
    thumbnailphoto: '',
    fonction: {},
    departement: {},
    departements_responsable: {},
    userid: null,
});

const formMode = ref('create');
const loading = ref(false);
const errorMessage = ref('');

const initForm = () => {
    form.nom = '';
    form.prenom = '';
    form.matricule = '';
    form.adresse = '';
    form.objectguid = '';
    form.thumbnailphoto = '';
    form.fonction = {};
    form.departement = {};
    form.departements_responsable = {};
    form.userid = null;
}

const handleSubmit = (values, actions) => {
    errorMessage.value = '';
    actions.resetForm();
    if (formMode.value === 'edit') {
        updateEmployee(values, actions);
    } else if (formMode.value === 'create') {
        createEmployee(values, actions);
    }
};

const createEmployee = (values, actions) => {
    loading.value = true;
    axios.post('/api/employes' + (user ? '/' + user.value.uuid : '' ), form)
        .then((response) => {
            // router.push('/employees');
            employee.value = response.data;
            employeeid.value = response.data.id;
            formMode.value = 'edit';

            Swal.fire({
                html: '<small>Employé créé avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                formMode.value = 'edit';
            });
        })
        .catch((error) => {
            //console.log("createEmployee, error: ", error);
            actions.setErrors(error.response?.data.errors);
            errorMessage.value = error.response?.data.errors;
            //console.log("createEmployee, errorMessage.value: ", errorMessage.value);
        })
        .finally(() => {
            loading.value = false;
        })
};

const updateEmployee = (values, actions) => {
    loading.value = true;
    axios.put(`/api/employes/${route.params.id}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>Employé modifié avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                formMode.value = 'edit';
            });
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
        .finally(() => {
            loading.value = false;
    })
};

const employee = ref({})
const employeeid = ref(null)
const getEmployee = () => {
    axios.get(`/api/employes/${employeeid.value}/edit`)
        .then((response) => {
            console.log("getEmployee, response: ", response)
            form.code = response.data.code;

            form.nom = response.data.nom;
            form.prenom = response.data.prenom;
            form.matricule = response.data.matricule;
            form.adresse = response.data.adresse;
            form.objectguid = response.data.objectguid;
            form.thumbnailphoto = response.data.thumbnailphoto;
            form.fonction = response.data.fonction;
            form.departement = response.data.departement;
            form.departements_responsable = response.data.departements_responsable;

            employee.value = response.data;
            status.value = response.data.status;
        }).then(() => {

        }

    )
};

const getUser = () => {
    axios.get(`/api/users/${form.userid}/edit`)
        .then((response) => {
            console.log("getUser, response: ", response);
            user.value = response.data;
        })
};

const departements = ref([]);
const getDepartements = () => {
    axios.get(`/api/departements/all`)
        .then((response) => {
            console.log("getDepartements, response: ", response)
            departements.value = response.data;
        }).then(() => {

        }

    )
};

const fonctions = ref([]);
const getFonctions = () => {
    axios.get(`/api/fonctionemployes/all`)
        .then((response) => {
            console.log("getFunctions, response: ", response)
            fonctions.value = response.data;
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

const currentPath = ref('/');
const lastPath = ref('/employees');
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
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;
    if (route.name === 'employees.edit' || route.name === 'employees.show') {
        if (route.name === 'employees.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        employeeid.value = route.params.id;
        getEmployee();
    } else {
        if (route.params.userid) {
            form.userid = route.params.userid;
            getUser();
        }
        formMode.value = 'create';
    }
};

onMounted(() => {
    initComponent();
    getDepartements();
    getFonctions();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <span v-if="formMode === 'edit'">Modification</span>
                        <span v-else-if="formMode === 'create'">Création</span>
                        <span v-else>Détails</span>
                        Employé
                        <span v-show="form.userid" class="text text-sm"> <span class="text text-muted">Pour l'utilisateur </span><span class="text text-bold text-indigo">{{ user.name }}</span></span>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/employees">Employés</router-link>
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
                                            <label for="nom"><span class="text text-xs">Nom</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.nom }}</span>
                                            <input v-else v-model="form.nom" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.nom }" id="nom" placeholder="Nom">
                                            <span class="invalid-feedback">{{ errors.nom }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="prenom"><span class="text text-xs">Prénom</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.prenom }}</span>
                                            <input v-else v-model="form.prenom" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.prenom }" id="prenom" placeholder="Prénom">
                                            <span class="invalid-feedback">{{ errors.prenom }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="matricule"><span class="text text-xs">Matricule</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.matricule }}</span>
                                            <input v-else v-model="form.matricule" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.matricule }" id="matricule" placeholder="Matricule">
                                            <span class="invalid-feedback">{{ errors.matricule }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="adresse"><span class="text text-xs">Adresse</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.adresse }}</span>
                                            <input v-else v-model="form.adresse" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.adresse }" id="adresse" placeholder="Adresse">
                                            <span class="invalid-feedback">{{ errors.adresse }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin"><span class="text text-xs">Fonction</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.fonction?.intitule }}</span>
                                            <multiselect v-else
                                                         id="fonction"
                                                         v-model="form.fonction"
                                                         value=""
                                                         :options="fonctions"
                                                         :searchable="true"
                                                         label="intitule"
                                                         track-by="id"
                                                         key="id"
                                                         :max-height="100"
                                                         placeholder="Fonction"
                                            >
                                            </multiselect >
                                            <span v-show="errorMessage.fonction" class="text text-xs text-danger">{{ errors.fonction }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin"><span class="text text-xs">Département</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.departement?.intitule }}</span>
                                            <multiselect v-else
                                                         id="departement"
                                                         v-model="form.departement"
                                                         value=""
                                                         :options="departements"
                                                         :searchable="true"
                                                         label="intitule"
                                                         track-by="id"
                                                         key="id"
                                                         :max-height="100"
                                                         placeholder="Département"
                                            >
                                            </multiselect >
                                            <span v-show="errorMessage.departement" class="text text-xs text-danger">{{ errors.departement }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin"><span class="text text-xs">Département en charge</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.departements_responsable?.intitule }}</span>
                                            <multiselect v-else
                                                         id="departements_responsable"
                                                         v-model="form.departements_responsable"
                                                         value=""
                                                         :options="departements"
                                                         :searchable="true"
                                                         label="intitule"
                                                         track-by="id"
                                                         key="id"
                                                         :max-height="100"
                                                         placeholder="Département à charge"
                                            >
                                            </multiselect >
                                            <span v-show="errorMessage.departements_responsable" class="text text-xs text-danger">{{ errors.departements_responsable }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" v-if="formMode === 'edit' || formMode === 'show'">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin"><span class="text text-xs">Object GUID</span></label>
                                            <span class="form-control border-0 text-xs">{{ form.objectguid }}</span>
                                            <span class="invalid-feedback">{{ errors.objectguid }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin"><span class="text text-xs">THUMBNAIL PHOTO</span></label>
                                            <span class="form-control border-0 text-xs">{{ form.thumbnailphoto }}</span>
                                            <span class="invalid-feedback">{{ errors.thumbnailphoto }}</span>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Statut</span></label> <br>
                                            <StatusShow :key="statusKey" v-if="status"
                                                        :status="status"
                                                        @status-changed="statusChanged"
                                                        :modelclass="employee.modelclass"
                                                        :modeltype="employee.modeltype"
                                                        :modelid="employeeid"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="formMode === 'create' && formMode === 'edit'" type="submit" class="btn btn-sm btn-primary m-2">
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

            <PhonenumList v-if="(formMode === 'edit' || formMode === 'show')"
                          :modeltype="modeltype"
                          :modelid="employeeid"
            ></PhonenumList>

            <EmailAddressList v-if="(formMode === 'edit' || formMode === 'show')"
                          :modeltype="modeltype"
                          :modelid="employeeid"
            ></EmailAddressList>
        </div>
    </div>

</template>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>
