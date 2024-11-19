<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import { debounce } from "lodash";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";
import LdapUserFormFields from "./LdapUserFormFields.vue";

const formMode = ref('create');
const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const user = ref(null);

const loading = ref(false);
const errorMessage = ref({});

const emit = defineEmits(['formModeChanged']);

const ldapuserform = reactive({
    guid: '',
    firstname: '',
    lastname: '',
    login: '',
    email: '',
    domain: '',
    telephone: '',
    distinguishedname: '',
    title: '',
    fonction: null,
    departement: null,
});

const initForm = (obj) => {
    ldapuserform.guid = obj ? obj.guid : '';
    ldapuserform.firstname = obj ? obj.firstname : '';
    ldapuserform.lastname = obj ? obj.lastname : '';
    ldapuserform.login = obj ? obj.login : '';
    ldapuserform.email = obj ? obj.email : '';
    ldapuserform.domain = obj ? obj.domain : '';
    ldapuserform.telephone = obj ? obj.telephone : '';
    ldapuserform.distinguishedname = obj ? obj.distinguishedname : '';
    ldapuserform.title = obj ? obj.title : '';
    ldapuserform.fonction = obj ? obj.fonction : null;
    ldapuserform.departement = obj ? obj.departement : null;
}

const handleSubmit = (values, actions) => {
    errorMessage.value = {};
    actions.resetForm();
    if (formMode.value === 'edit') {
        updateLdapUser(values, actions);
    } else if (formMode.value === 'create') {
        createLdapUser(values, actions);
    } else if (formMode.value === 'integrate') {
        integrateLdapUser(values, actions);
    }
};

const createLdapUser = (values, actions) => {
    loading.value = true;
    axios.post('/api/ldapusers' + (user.value === null ? '' : '/' + user.value.uuid ), ldapuserform)
        .then((response) => {
            // router.push('/ldapusers');
            ldapuser.value = response.data;
            ldapuserid.value = response.data.id;
            initForm(response.data);

            formMode.value = 'edit';

            Swal.fire({
                html: '<small>LDAP User successfully created !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                //formMode.value = 'edit';
                //emit('formModeChanged', 'edit');
                if ( ! user.value ) {
                    Swal.fire({
                        html: '<small>Integrate The User <b>' + ldapuser.value.name + '</b></small>',
                        icon: 'warning',
                        showCancelButton: true,
                        showLoaderOnConfirm: true,
                        confirmButtonText: 'Integrate',
                        cancelButtonText: 'Cancel',
                        preConfirm: () => {
                            formMode.value = 'integrate';
                            loading.value = true;
                            return axios.put(`/api/ldapusers/${ldapuserid.value}/integrate`, ldapuserform)
                                .then(response => {
                                    console.log("integrate LdapUser after Create, preConfirm, response: ", response);
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
                            console.log("integrate LdapUser after Create, DONE, response: ", result);
                            Swal.fire({
                                html: '<small>Ldap User Successfully Intagrated !</small>',
                                icon: 'success',
                                timer: 3000
                            }).then(() => {
                                router.push({ name: 'users.edit',  params: { id: result.value.data.uuid } });
                            })
                        }
                    })
                }
            });
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response?.data.errors);
                errorMessage.value = error.response?.data.errors;
            }
        })
        .finally(() => {
            loading.value = false;
        })
};

const updateLdapUser = (values, actions) => {
    loading.value = true;
    axios.put(`/api/ldapusers/${ldapuserid.value}`, ldapuserform)
        .then((response) => {
            Swal.fire({
                html: '<small>LDAP User successfully updated !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                //formMode.value = 'edit';
                emit('formModeChanged', 'edit');
            });
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
            }
        })
        .finally(() => {
            loading.value = false;
        })
};

const integrateLdapUser = (values, actions) => {
    loading.value = true;
    axios.put(`/api/ldapusers/${ldapuserid.value}/integrate`, ldapuserform)
        .then((response) => {
            console.log("integrateLdapUser, response: ", response)
            Swal.fire({
                html: '<small>LDAP User successfully integrated !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                router.push({ name: 'users.edit',  params: { id: response.data.uuid } });
            });
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
            }
        })
        .finally(() => {
            loading.value = false;
        })
};

const ldapuser = ref({});
const ldapuserid = ref(null);
const getLdapUser = () => {
    axios.get(`/api/ldapusers/${ldapuserid.value}/edit`)
        .then((response) => {
            console.log("getLdapUser, response: ", response)
            initForm(response.data);

            ldapuser.value = response.data;
        }).then(() => {

        }

    )
};

const getUser = () => {
    axios.get(`/api/users/${ldapuserform.userid}/edit`)
        .then((response) => {
            console.log("getUser, response: ", response);
            user.value = response.data;
            ldapuserform.email = response.data.email;
        })
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

const currentPath = ref('/');
const lastPath = ref('/ldapusers');
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
    if (route.name === 'ldapusers.edit' || route.name === 'ldapusers.show' || route.name === 'ldapusers.integrate') {
        if (route.name === 'ldapusers.edit') {
            formMode.value = 'edit';
        } else if (route.name === 'ldapusers.show') {
            formMode.value = 'show';
        } else {
            formMode.value = 'integrate';
        }
        ldapuserid.value = route.params.id;
        getLdapUser();
    } else {
        if (route.params.userid) {
            ldapuserform.userid = route.params.userid;
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
                        <span v-if="formMode === 'edit'">Edit</span>
                        <span v-else-if="formMode === 'create'">Create</span>
                        <span v-else-if="formMode === 'integrate'">Integrate</span>
                        <span v-else>Details</span>
                        LDAP User
                        <span v-show="user" class="text text-sm mr-1"> <span class="text text-muted">For User </span><span class="text text-bold text-indigo">{{ user?.name }}</span></span>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/ldapusers">LDAP Users</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="formMode === 'edit'">Modification</span>
                            <span v-else-if="formMode === 'create'">Creation</span>
                            <span v-else-if="formMode === 'integrate'">Integration</span>
                            <span v-else>Details</span>
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

                                <div class="row" v-show="formMode !== 'create'">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="guid"><span class="text text-xs">GUID</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.guid }}</span>
                                            <input v-else v-model="ldapuserform.guid" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.guid }" id="lastname" placeholder="GUID">
                                            <span class="invalid-feedback">{{ errors?.guid }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lastname"><span class="text text-xs">Last Name</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.lastname }}</span>
                                            <input v-else v-model="ldapuserform.lastname" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.lastname }" id="lastname" placeholder="Name">
                                            <span class="invalid-feedback">{{ errors?.lastname }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name"><span class="text text-xs">First Name</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.firstname }}</span>
                                            <input v-else v-model="ldapuserform.firstname" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.firstname }" id="firstname" placeholder="Name">
                                            <span class="invalid-feedback">{{ errors?.firstname }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="login"><span class="text text-xs">Login</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.login }}</span>
                                            <input v-else v-model="ldapuserform.login" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.login }" id="login" placeholder="Login">
                                            <span class="invalid-feedback">{{ errors?.login }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="email"><span class="text text-xs">E-Mail</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.email }}</span>
                                            <input v-else v-model="ldapuserform.email" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.email }" id="email" placeholder="E-Mail">
                                            <span class="invalid-feedback">{{ errors?.email }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-show="formMode !== 'create'">
                                        <div class="form-group">
                                            <label for="telephone"><span class="text text-xs">Phone Number</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.telephone }}</span>
                                            <input v-else v-model="ldapuserform.telephone" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.telephone }" id="telephone" placeholder="Phone Number">
                                            <span class="invalid-feedback">{{ errors?.telephone }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" v-show="formMode !== 'create'">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="distinguishedname"><span class="text text-xs">Distinguished Name</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.distinguishedname }}</span>
                                            <input v-else v-model="ldapuserform.distinguishedname" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.distinguishedname }" id="distinguishedname" placeholder="Distinguished Name">
                                            <span class="invalid-feedback">{{ errors?.distinguishedname }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-show="formMode !== 'create'">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="domain"><span class="text text-xs">Domain</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.domain }}</span>
                                            <input v-else v-model="ldapuserform.domain" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.domain }" id="domain" placeholder="Domain">
                                            <span class="invalid-feedback">{{ errors?.domain }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin"><span class="text text-xs">Title</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.fonction?.intitule }}</span>
                                            <multiselect v-else
                                                         id="fonction"
                                                         v-model="ldapuserform.fonction"
                                                         value=""
                                                         :options="fonctions"
                                                         :searchable="true"
                                                         label="intitule"
                                                         track-by="id"
                                                         key="id"
                                                         :max-height="100"
                                                         placeholder="Title"
                                            >
                                            </multiselect >
                                            <span v-show="errorMessage?.fonction" class="text text-xs text-danger">{{ errors?.fonction }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="departement"><span class="text text-xs">Department</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ ldapuserform.departement?.intitule }}</span>
                                            <multiselect v-else
                                                         id="Departement"
                                                         v-model="ldapuserform.departement"
                                                         value=""
                                                         :options="departements"
                                                         :searchable="true"
                                                         label="intitule"
                                                         track-by="id"
                                                         key="id"
                                                         :max-height="100"
                                                         placeholder="Department"
                                            >
                                            </multiselect >
                                            <span v-show="errorMessage?.departement" class="text text-xs text-danger">{{ errors?.departement }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="formMode === 'create' || formMode === 'edit' || formMode === 'integrate'" type="submit" class="btn btn-sm btn-primary m-2">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <i v-else class="fa fa-save mr-1"></i>
                                        <span> {{ formMode === 'integrate' ? ' Integrate' : ' Save' }} </span>
                                    </button>
                                    <router-link :to="prevRoutePath">
                                        <button type="submit" class="btn btn-sm btn-default m-2">
                                            <i class="fa fa-backward mr-1"></i> Back
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

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>
