<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import {debounce} from "lodash";
import PermissionListItem from "../roles/PermissionListItem.vue";
import {Bootstrap4Pagination} from "laravel-vue-pagination";


// TODO: Load users-in-role
// TODO: Build Permissions-List View
const loadingPermissions = ref(false);
const paginationLinksLimit = ref(5);
const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    name: '',
    guard_name: 'web',
    created_at: '',
});

const formMode = ref('create');
const rolepermissions = ref({});
const affectedpermissions = ref({});

const initForm = () => {
    form.name = '';
    form.guard_name = 'web';
    form.created_at = '';
}

const handleSubmit = (values, actions) => {
    if (formMode.value === 'edit') {
        updateRole(values, actions);
    } else if (formMode.value === 'create') {
        createRole(values, actions);
    }
};

const createRole = (values, actions) => {
    axios.post('/api/roles', form)
        .then((response) => {
            // router.push('/roles');
            role.value = response.data;
            roleid.value = response.data.id;
            formMode.value = 'edit';
            toastr.success('Role created successfully!');
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
            }
        })
};

const updateRole = (values, actions) => {
    axios.put(`/api/roles/${route.params.id}`, form)
        .then((response) => {
            toastr.success('Role updated successfully!');
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
            }
        })
};

const role = ref({})
const roleid = ref(null)
const getRole = () => {
    axios.get(`/api/roles/${route.params.id}/edit`)
        .then(({ data }) => {
            form.name = data.name;
            form.guard_name = data.guard_name;

            role.value = data;
            rolepermissions.value = data.permissions;
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
    loadingPermissions.value = true;
    axios.get(`/api/permissions?page=${page}`, {
        params: {
            query: searchQuery.value,
            roleid: roleid.value,
            permstatus: permStatus.value
        }
    })
        .then((response) => {
            permissions.value = response.data;
            permissionscount.value = response.data.total;

            selectedPermissions.value = [];
            selectAllPermissions.value = false;
            selectAll.value = false;
        }).finally(() => {
            loadingPermissions.value = false;
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
    axios.patch(`/api/roles/${role.value.id}/assign-permissions`, {
        permissionsids: selectedPermissions.value
    })
        .then(response => {
            // let permissions_not_in = response.data.permissions.filter(perm => !rolepermissions.value.includes(perm.id));
            // let permissions_not_in = response.data.permissions.filter(o => !rolepermissions.value.some(i => i.id === o.id));
            affectedpermissions.value = response.data.permissions;
            rolepermissions.value.push(...affectedpermissions.value);

            updatePermissionCurrentList('out_role', affectedpermissions.value);

            selectedPermissions.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        });
};

const bulkRevoke = () => {
    axios.patch(`/api/roles/${role.value.id}/revoke-permissions`, {
        permissionsids: selectedPermissions.value
    })
        .then((response) => {
            affectedpermissions.value = response.data.permissions;
            rolepermissions.value = rolepermissions.value.filter(rp => !affectedpermissions.value.some(ap => ap.id === rp.id));
            updatePermissionCurrentList('in_role', affectedpermissions.value);

            selectedPermissions.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        });
};

const permissionAffectationChanged = (permission) => {
    const index = rolepermissions.value.findIndex(p => p.id === permission.id);
    if (index === -1) {
        // add to rolepermissions, in_role case
        rolepermissions.value.push(permission.id);
        // remove from permissions if current permission status is out_role
        // removeOnePermissionCurrentList('out_role', permission);
        updatePermissionCurrentList('out_role', [permission]);
    } else {
        // remove from rolepermissions, out_role case
        rolepermissions.value.splice(index, 1);
        // remove from permissions if current permission status is in_role
        // removeOnePermissionCurrentList('in_role', permission);
        updatePermissionCurrentList('in_role', [permission]);
    }

    toggleIsInRole([permission]);
};

const updatePermissionCurrentList = (permstatus, affectedpermissions) => {
    // remove permissions to current list i any
    if ( permStatus.value ===  permstatus) {
        // permissions.value.data = permissions.value.data.filter(perm => !permissionIds.includes(perm.id));
        permissions.value.data = permissions.value.data.filter(rp => !affectedpermissions.some(ap => ap.id === rp.id));
    }

    // toggle permissions is_in_role attribute value
    toggleIsInRole(affectedpermissions);
}

const toggleIsInRole = (affectedpermissions) => {
    if ( permStatus.value ===  'all') {
        permissions.value.data.forEach((permission) => {
            const idx = affectedpermissions.findIndex(p => p.id === permission.id);
            if ( idx !== -1 ) {
                permission.is_in_role = !permission.is_in_role;
                const chkbx = document.getElementById('permission_' + permission.id);
                chkbx.setAttribute('checked', false);
            }
        });
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
}

const outRolePermissionsCount = computed(() => {
    return  (permissionscount.value === 0) ? 0 : (rolepermissions.value.length ? permissionscount.value - rolepermissions.value.length : permissionscount.value);
});

watch(searchQuery, debounce(() => {
    getPermissions();
}, 300));

const currentPath = ref('/');
const lastPath = ref('/roles');
const prevRoutePath = computed(() => {
    return lastPath;// ? lastPath.value : '/clientesims';
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
    if (route.name === 'roles.edit' || route.name === 'roles.show') {
        if (route.name === 'roles.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        roleid.value = route.params.id;
        getRole();
    } else {
        formMode.value = 'create';
    }
};

onMounted(() => {
    initComponent();
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
                        <span v-if="formMode === 'edit'">Role Modification</span>
                        <span v-else-if="formMode === 'create'">Create Role</span>
                        <span v-else>Role Details</span>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/roles">Roles</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="formMode === 'edit'">Modification</span>
                            <span v-else-if="formMode === 'create'">Creation</span>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Name</label>
                                            <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" id="name" placeholder="Enter Name" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Guard Name</label>
                                            <input v-model="form.guard_name" type="text" class="form-control" :class="{ 'is-invalid': errors.guard_name }" id="guard_name" placeholder="Enter Guard Name" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.guard_name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button v-if="formMode === 'create' || formMode === 'edit'" type="submit" class="btn btn-sm btn-primary m-2">Submit</button>
                                    <router-link to="/roles">
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

            <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h3 class="card-title">Role Permissions</h3>
                            <div class="card-tools">
                                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                                <div class="btn-group">
                                    <button @click="getPermissionsByStatus('all')" type="button" class="btn btn-xs btn-outline-info" :class="permStatus === 'all' ? 'active' : ''">
                                        <span class="mr-1">All</span>
                                        <span class="badge badge-pill badge-info">{{ permissionscount }}</span>
                                    </button>

                                    <button @click="getPermissionsByStatus('in_role')" type="button" class="btn btn-xs btn-outline-success" :class="permStatus === 'in_role' ? 'active' : ''">
                                        <span class="mr-1">In Role</span>
                                        <span class="badge badge-pill badge-info">{{ rolepermissions.length ? rolepermissions.length : 0 }}</span>
                                    </button>

                                    <button @click="getPermissionsByStatus('out_role')" type="button" class="btn btn-xs btn-outline-warning" :class="permStatus === 'out_role' ? 'active' : ''">
                                        <span class="mr-1">Out Role</span>
                                        <span class="badge badge-pill badge-info">{{ outRolePermissionsCount }}</span>
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
                                            <button :disabled="permStatus === 'in_role'" @click="bulkAssign" type="button" class="ml-2 mb-2 btn btn-sm btn-success">
                                                <i class="fa fa-link mr-1"></i>
                                                Give Selected
                                            </button>
                                            <button :disabled="permStatus === 'out_role'" @click="bulkRevoke" type="button" class="ml-2 mb-2 btn btn-sm btn-warning">
                                                <i class="fa fa-ban mr-1"></i>
                                                Revoke Selected
                                            </button>
                                            <span class="ml-2">Selected {{ selectedPermissions.length }} roles</span>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-group mb-3">
                                            <input type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Search text..." />
                                            <button v-if="searchQuery" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
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
                                        <th v-if="formMode !== 'show'"><input type="checkbox" v-model="selectAll" @change="selectAllPermissions" /></th>
                                        <th class="text text-xs" style="width: 10px">#</th>
                                        <th class="text text-xs">Name</th>
                                        <th class="text text-xs">Guard Name</th>
                                        <th class="text text-xs">Level</th>
                                        <th class="text text-xs">Created</th>
                                        <th class="text text-xs">Updated</th>
                                        <th v-if="formMode !== 'show'" class="text text-xs">Options</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="permissions.data.length > 0">
                                    <PermissionListItem v-for="(permission, index) in permissions.data"
                                                        key="permission.id"
                                                        :formMode=formMode
                                                        :role=role
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
                                <Bootstrap4Pagination :data="permissions" @pagination-change-page="getPermissions"size="small" :limit="paginationLinksLimit" align="right" />
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                        <div v-if="loadingPermissions" class="overlay dark">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
