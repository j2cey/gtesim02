<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import {debounce} from "lodash";
import PermissionListItem from "@/pages/esims/PermissionListItem.vue";
import {Bootstrap4Pagination} from "laravel-vue-pagination";

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    imsi: '',
    iccid: '',
    pin: '',
    puk:'',
    ac:'',
    eki:'',
    pin2:'',
    adm1:'',
    puk2:'',
    opc:'',
    created_at: '',
});

const editMode = ref(false);
const esimpermissions = ref({});
const affectedpermissions = ref({});

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updateEsim(values, actions);
    } else {
        createEsim(values, actions);
    }
};

const createEsim = (values, actions) => {
    axios.post('/api/esims', form)
        .then((response) => {
            // router.push('/esims');
            esim.value = response.data;
            esimid.value = response.data.id;
            editMode.value = true;
            toastr.success('Esim created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
};

const updateEsim = (values, actions) => {
    axios.put(`/api/esims/${route.params.id}`, form)
        .then((response) => {
            toastr.success('Esim updated successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
};

/*const esim = ref({})
const esimid = ref(null)
const getEsim = () => {
    axios.get(`/api/esims/${route.params.id}/edit`)
        .then(({ data }) => {
            form.name = data.name;
            form.guard_name = data.guard_name;
            esim.value = data;
            esimpermissions.value = data.permissions;
        })
};*/
const esim = ref({})
const esimid = ref(null)
const getEsim = () => {
    axios.get(`/api/esims/${route.params.id}/edit`)
        .then(({ data }) => {
            form.imsi = data.imsi;
            form.iccid = data.iccid;
            form.pin = data.pin;
            form.puk = data.puk;
            form.ac = data.ac;
            form.eki = data.eki;
            form.pin2 = data.pin2;
            form.puk2 = data.puk2;
            form.adm1 = data.adm1;
            form.opc = data.opc;
            esim.value = data;
            esimpermissions.value = data.permissions;
        })
};

const searchQuery = ref(null);
const permStatus = ref('all');
const permissions = ref({'data': []});
const permissionscount = ref(0);
const selectedPermissions = ref([]);
const getPermissionsCount = () => {
    axios.get('/api/permissions/count')
        .then((response) => {
            permissionscount.value = response.data;
        })
}

const getPermissionsByStatus = (status) => {
    permStatus.value = status;
    getPermissions();
}
const getPermissions = (page = 1) => {
    axios.get(`/api/permissions?page=${page}`, {
        params: {
            query: searchQuery.value,
            esimid: esimid.value,
            permstatus: permStatus.value
        }
    })
        .then((response) => {
            permissions.value = response.data;
            permissionscount.value = response.data.total;

            selectedPermissions.value = [];
            selectAllPermissions.value = false;
            selectAll.value = false;
        })
}

const selectAll = ref(false);

const toggleSelection = (permission, target_ig) => {
    const index = selectedPermissions.value.indexOf(permission.id);

    if (index === -1) {
        selectedPermissions.value.push(permission.id);
    } else {
        selectedPermissions.value.splice(index, 1);
    }
};
const selectAllPermissions = () => {
    if (selectAll.value) {
        selectedPermissions.value = permissions.value.data.map(permission => permission.id);
    } else {
        selectedPermissions.value = [];
    }
}

const bulkAssign = () => {
    axios.patch(`/api/esims/${esim.value.id}/assign-permissions`, {
        permissionsids: selectedPermissions.value
    })
        .then(response => {
            // let permissions_not_in = response.data.permissions.filter(perm => !esimpermissions.value.includes(perm.id));
            // let permissions_not_in = response.data.permissions.filter(o => !esimpermissions.value.some(i => i.id === o.id));
            affectedpermissions.value = response.data.permissions;
            esimpermissions.value.push(...affectedpermissions.value);

            updatePermissionCurrentList('out_esim', affectedpermissions.value);

            selectedPermissions.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        });
};

const bulkRevoke = () => {
    axios.patch(`/api/esims/${esim.value.id}/revoke-permissions`, {
        permissionsids: selectedPermissions.value
    })
        .then((response) => {
            affectedpermissions.value = response.data.permissions;
            esimpermissions.value = esimpermissions.value.filter(rp => !affectedpermissions.value.some(ap => ap.id === rp.id));
            updatePermissionCurrentList('in_esim', affectedpermissions.value);

            selectedPermissions.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        });
};

const permissionAffectationChanged = (permission) => {
    const index = esimpermissions.value.findIndex(p => p.id === permission.id);
    if (index === -1) {
        // add to esimpermissions, in_esim case
        esimpermissions.value.push(permission.id);
        // remove from permissions if current permission status is out_esim
        // removeOnePermissionCurrentList('out_esim', permission);
        updatePermissionCurrentList('out_esim', [permission]);
    } else {
        // remove from esimpermissions, out_esim case
        esimpermissions.value.splice(index, 1);
        // remove from permissions if current permission status is in_esim
        // removeOnePermissionCurrentList('in_esim', permission);
        updatePermissionCurrentList('in_esim', [permission]);
    }

    toggleIsInEsim([permission]);
};

const updatePermissionCurrentList = (permstatus, affectedpermissions) => {
    // remove permissions to current list i any
    if ( permStatus.value ===  permstatus) {
        // permissions.value.data = permissions.value.data.filter(perm => !permissionIds.includes(perm.id));
        permissions.value.data = permissions.value.data.filter(rp => !affectedpermissions.some(ap => ap.id === rp.id));
    }

    // toggle permissions is_in_esim attribute value
    toggleIsInEsim(affectedpermissions);
}

const toggleIsInEsim = (affectedpermissions) => {
    if ( permStatus.value ===  'all') {
        permissions.value.data.forEach((permission) => {
            const idx = affectedpermissions.findIndex(p => p.id === permission.id);
            if ( idx !== -1 ) {
                permission.is_in_esim = !permission.is_in_esim;
                const chkbx = document.getElementById('permission_' + permission.id);
                chkbx.setAttribute('checked', false);
            }
        });
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
}

const outEsimPermissionsCount = computed(() => {
    return  (permissionscount.value === 0) ? 0 : (esimpermissions.value.length ? permissionscount.value - esimpermissions.value.length : permissionscount.value);
});

watch(searchQuery, debounce(() => {
    getPermissions();
}, 300));

onMounted(() => {
    if (route.name === 'esims.edit') {
        esimid.value = route.params.id;
        editMode.value = true;
        getEsim();
    }
    getPermissions();
    // getPermissionsCount();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Create</span>
                        Esim</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/esims">Esims</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Create</span>
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
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">imsi</label>
                                            <input v-model="form.imsi" type="text" class="form-control" :class="{ 'is-invalid': errors.imsi }" id="imsi" placeholder="Enter imsi">
                                            <span class="invalid-feedback">{{ errors.imsi }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">iccid</label>
                                            <input v-model="form.iccid" type="text" class="form-control" :class="{ 'is-invalid': errors.iccid }" id="iccid" placeholder="Enter iccid">
                                            <span class="invalid-feedback">{{ errors.iccid }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">pin</label>
                                            <input v-model="form.pin" type="text" class="form-control" :class="{ 'is-invalid': errors.pin }" id="pin" placeholder="Enter pin">
                                            <span class="invalid-feedback">{{ errors.pin }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">puk</label>
                                            <input v-model="form.puk" type="text" class="form-control" :class="{ 'is-invalid': errors.puk }" id="puk" placeholder="Enter puk">
                                            <span class="invalid-feedback">{{ errors.puk }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">ac</label>
                                            <input v-model="form.ac" type="text" class="form-control" :class="{ 'is-invalid': errors.ac }" id="ac" placeholder="Enter ac">
                                            <span class="invalid-feedback">{{ errors.ac }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">eki</label>
                                            <input v-model="form.eki" type="text" class="form-control" :class="{ 'is-invalid': errors.eki }" id="eki" placeholder="Enter eki">
                                            <span class="invalid-feedback">{{ errors.eki }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">pin2</label>
                                            <input v-model="form.pin2" type="text" class="form-control" :class="{ 'is-invalid': errors.pin2 }" id="pin2" placeholder="Enter pin2">
                                            <span class="invalid-feedback">{{ errors.pin2 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">puk2</label>
                                            <input v-model="form.puk2" type="text" class="form-control" :class="{ 'is-invalid': errors.puk2 }" id="puk2" placeholder="Enter puk2">
                                            <span class="invalid-feedback">{{ errors.puk2 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">adm1</label>
                                            <input v-model="form.adm1" type="text" class="form-control" :class="{ 'is-invalid': errors.adm1 }" id="adm1" placeholder="Enter adm1">
                                            <span class="invalid-feedback">{{ errors.adm1 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="title">opc</label>
                                            <input v-model="form.opc" type="text" class="form-control" :class="{ 'is-invalid': errors.opc }" id="opc" placeholder="Enter opc">
                                            <span class="invalid-feedback">{{ errors.opc }}</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary m-2">Create</button>
                                    <router-link to="/esims">
                                        <button type="submit" class="btn btn-sm btn-default m-2">Back</button>
                                    </router-link>
                                </div>
                            </Form>
                        </div>

                    </div>
                </div>
            </div>

            <div v-if="editMode" class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h3 class="card-title">Esim Permissions</h3>
                            <div class="card-tools">
                                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                                <div class="btn-group">
                                    <button @click="getPermissionsByStatus('all')" type="button" class="btn btn-xs btn-outline-info" :class="permStatus === 'all' ? 'active' : ''">
                                        <span class="mr-1">All</span>
                                        <span class="badge badge-pill badge-info">{{ permissionscount }}</span>
                                    </button>

                                    <button :disabled="!editMode" @click="getPermissionsByStatus('in_esim')" type="button" class="btn btn-xs btn-outline-success" :class="permStatus === 'in_esim' ? 'active' : ''">
                                        <span class="mr-1">In Esim</span>
                                        <span class="badge badge-pill badge-info">{{ esimpermissions.length ? esimpermissions.length : 0 }}</span>
                                    </button>

                                    <button :disabled="!editMode" @click="getPermissionsByStatus('out_esim')" type="button" class="btn btn-xs btn-outline-warning" :class="permStatus === 'out_esim' ? 'active' : ''">
                                        <span class="mr-1">Out Esim</span>
                                        <span class="badge badge-pill badge-info">{{ outEsimPermissionsCount }}</span>
                                    </button>

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
                                        <div v-if="selectedPermissions.length > 0">
                                            <button :disabled="permStatus === 'in_esim'" @click="bulkAssign" type="button" class="ml-2 mb-2 btn btn-success">
                                                <i class="fa fa-link mr-1"></i>
                                                Give Selected
                                            </button>
                                            <button :disabled="permStatus === 'out_esim'" @click="bulkRevoke" type="button" class="ml-2 mb-2 btn btn-warning">
                                                <i class="fa fa-ban mr-1"></i>
                                                Revoke Selected
                                            </button>
                                            <span class="ml-2">Selected {{ selectedPermissions.length }} esims</span>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-group mb-3">
                                            <input type="search" v-model="searchQuery" class="form-control text-xs" placeholder="Search text..." />
                                            <button v-if="searchQuery" @click="clearSearchQuery" type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" v-model="selectAll" @change="selectAllPermissions" /></th>
                                        <th style="width: 10px">#</th>
                                        <th>Name</th>
                                        <th>Guard Name</th>
                                        <th>Level</th>
                                        <th>Registered Date</th>
                                        <th>Options</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="permissions.data.length > 0">
                                    <PermissionListItem v-for="(permission, index) in permissions.data"
                                                        key="permission.id"
                                                        :editMode=editMode
                                                        :esim=esim
                                                        :permission=permission
                                                        :index=index
                                                        @toggle-selection="toggleSelection"
                                                        @permission-affectation-changed="permissionAffectationChanged"
                                                        :selectAll="selectAll"
                                                        :selectedPermissions="selectedPermissions"
                                    />
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="6" class="text-center">No results found...</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <Bootstrap4Pagination :data="permissions" @pagination-change-page="getPermissions" align="right" />
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card-body{
    border: solid rgb(255, 145, 6);
    flex-direction: column;
    align-items: center;

}


form {

    max-width: 50em;
    margin: 0.5em auto;
    background: rgb(0, 91, 170);
    padding: 3em;
    border-radius: 10px;
}


label {
    display: inline-block;
    margin: 1.5em;
    font-size: 0.8em;
    text-transform: uppercase;
    color: #aaa;
    font-weight: 500;
}

.personal-info,


p {
    margin: 1em 0 1em 0;
    color: #aaa;
    text-transform: uppercase;
    font-size: 0.9em;
    font-weight: bold;
}

textarea {
    width: 100%;
    padding: 3em;
    height: 8em;
    resize: none;
    font-family: Arial, Helvetica, sans-serif;
    letter-spacing: 0.1ch;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.answers {
    color: black;
    text-transform: none;
    font-weight: 400;
    font-size: 1.2em;
}


</style>


