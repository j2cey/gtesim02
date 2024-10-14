<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import { debounce } from "lodash";
import RoleListItem from "../roles/RoleListItem.vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import Swal from "sweetalert2";
import { formatDate } from '../../helper.js'
import { useAbility } from "@casl/vue";
import StatusShow from "../statuses/StatusShow.vue";
import ResetPassword from "./ResetPassword.vue"


const { can, cannot } = useAbility();

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    name: '',
    login: '',
    email: '',
    avatar: '',
    is_local: 0,
    is_ldap: 0,
    last_seen: '',
    created_at: '',
    updated_at: '',
    ldapAccount: {},
    password: '',
});

const formMode = ref('create');
const loading = ref(false);
const loadingRoles = ref(false);
const loadingEmployee = ref(false);
const loadingLdap = ref(false);
const userroles = ref({});
const affectedroles = ref({});
const employee = ref({});
const ldapaccount = ref({});

const initForm = () => {
    form.name = '';
    form.login = '';
    form.email = '';
    form.avatar = '';
    form.is_local = 0;
    form.is_ldap = 0;
    form.last_seen = '';
    form.created_at = '';
    form.updated_at = '';
    form.ldapAccount = {};

    form.password = '';
}

const handleSubmit = (values, actions) => {
    if (formMode.value === 'edit') {
        updateUser(values, actions);
    } else if (formMode.value === 'create') {
        createUser(values, actions);
    }
};

//<editor-fold desc="User">
const createUser = (values, actions) => {
    loading.value = true;
    axios.post('/api/users', form)
        .then((response) => {
            // router.push('/users');
            console.log("createUser, response: ", response);
            user.value = response.data;
            userid.value = response.data.id;

            userroles.value = response.data.roles;
            employee.value = response.data.employe;
            ldapaccount.value = response.data.ldap_account;
            status.value = response.data.status;

            Swal.fire({
                html: '<small>User created successfully !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                formMode.value = 'edit';
            });
        })
        .catch((error) => {
            actions.setErrors(error.response?.data.errors);
        })
        .finally(() => {
            loading.value = false;
        })
};

const updateUser = (values, actions) => {
    loading.value = true;
    axios.put(`/api/users/${route.params.id}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>User updated successfully !</small>',
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

const user = ref({})
const userid = ref(null)
const getUser = () => {
    axios.get(`/api/users/${userid.value}/edit`)
        .then((response) => {
            console.log("getUser, response: ", response);
            form.name = response.data.name;
            form.login = response.data.login;
            form.email = response.data.email;
            form.avatar = response.data.avatar;
            form.is_local = response.data.is_local;
            form.is_ldap = response.data.is_ldap;
            form.last_seen = response.data.last_seen;
            form.created_at = response.data.created_at;
            form.updated_at = response.data.updated_at;
            form.ldapAccount = response.data.ldapAccount;

            user.value = response.data;
            userroles.value = response.data.roles;
            employee.value = response.data.employe;
            ldapaccount.value = response.data.ldap_account;
            status.value = response.data.status;
        })
};

const inputPwdType = ref("password");
const showPwd = () => {
    inputPwdType.value = "text";
}
const hidePwd = () => {
    inputPwdType.value = "password";
}
//</editor-fold>

//<editor-fold desc="Role">
const searchQuery = ref(null);
const roleStatus = ref('all');
const roles = ref({'data': []});
const rolescount = ref(-1);
const selectedRoles = ref([]);
const getRolesCount = () => {
    axios.get('/api/roles/count')
        .then((response) => {
            rolescount.value = response.data;
        })
}

const getRolesByStatus = (status) => {
    roleStatus.value = status;
    getRoles();
}
const getRoles = (page = 1) => {
    loadingRoles.value = true;
    axios.get(`/api/roles?page=${page}`, {
        params: {
            query: searchQuery.value,
            userid: userid.value,
            rolestatus: roleStatus.value
        }
    })
        .then((response) => {
            console.log("getRoles, response: ", response);
            roles.value = response.data;

            //if (roleStatus.value === 'all') {
                rolescount.value = response.data.total;
            //}

            selectedRoles.value = [];
            selectAllRoles.value = false;
            selectAll.value = false;
        })
        .finally(() => {
            loadingRoles.value = false;
        })
}

const selectAll = ref(false);

const toggleSelection = (role, target_ig) => {
    const index = selectedRoles.value.indexOf(role.id);

    if (index === -1) {
        selectedRoles.value.push(role.id);
    } else {
        selectedRoles.value.splice(index, 1);
    }
};
const selectAllRoles = () => {
    if (selectAll.value) {
        selectedRoles.value = roles.value.data.map(role => role.id);
    } else {
        selectedRoles.value = [];
    }
}

const bulkAssign = () => {
    loadingRoles.value = true;
    axios.patch(`/api/users/${user.value.uuid}/assign-roles`, {
        rolesids: selectedRoles.value
    })
        .then(response => {
            console.log("bulkAssign Roles, response: ", response);
            // let roles_not_in = response.data.roles.filter(perm => !userroles.value.includes(perm.id));
            // let roles_not_in = response.data.roles.filter(o => !userroles.value.some(i => i.id === o.id));
            affectedroles.value = response.data.roles;
            userroles.value.push(...affectedroles.value);

            updateRoleCurrentList('out_user', affectedroles.value);

            selectedRoles.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        }).finally(() => {
        loadingRoles.value = false;
    });
};

const bulkRevoke = () => {
    loadingRoles.value = true;
    axios.patch(`/api/users/${user.value.uuid}/revoke-roles`, {
        rolesids: selectedRoles.value
    })
        .then((response) => {
            console.log("bulkRevoke Roles, response: ", response);
            affectedroles.value = response.data.roles;
            userroles.value = userroles.value.filter(rp => !affectedroles.value.some(ap => ap.id === rp.id));
            updateRoleCurrentList('in_user', affectedroles.value);

            selectedRoles.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        }).finally(() => {
        loadingRoles.value = false;
    });
};

const roleAssign = (role) => {
    loadingRoles.value = true;
    axios.patch(`/api/users/${user.value.uuid}/assign-roles`, {
        rolesids: [role.id]
    })
        .then(response => {
            console.log("roleAssign, response: ", response);
            // props.permission.is_in_role = true;
            roleAffectationChanged(response.data.roles[0]);

            toastr.success(response.data.message);
        }).finally(() => {
            loadingRoles.value = false;
    });
};

const roleRevoke = (role) => {
    axios.patch(`/api/users/${user.value.uuid}/revoke-roles`, {
        rolesids: [role.id]
    })
        .then(response => {
            console.log("roleRevoke, response: ", response);
            // props.permission.is_in_role = false;
            roleAffectationChanged(response.data.roles[0]);

            toastr.success(response.data.message);
        });
};

const roleAffectationChanged = (role) => {
    console.log("roleAffectationChanged, role: ", role);
    const index = userroles.value.findIndex(p => p.id === role.id);
    if (index === -1) {
        // add to userroles, in_user case
        userroles.value.push(role.id);
        // remove from roles if current role status is out_user
        updateRoleCurrentList('out_user', [role]);
    } else {
        // remove from userroles, out_user case
        userroles.value.splice(index, 1);
        // remove from roles if current role status is in_user
        updateRoleCurrentList('in_user', [role]);
    }

    //toggleIsInUser([role]);
};

const updateRoleCurrentList = (rolestatus, affectedroles) => {
    console.log("updateRoleCurrentList rolestatus: ", rolestatus, "roleStatus.value: ", roleStatus.value)
    // remove roles to current list if any
    if ( roleStatus.value !==  'all') {
        // roles.value.data = roles.value.data.filter(perm => !roleIds.includes(perm.id));
        roles.value.data = roles.value.data.filter(rp => !affectedroles.some(ap => ap.id === rp.id));
    }

    // toggle roles is_in_user attribute value
    toggleIsInUser(affectedroles);
}

const toggleIsInUser = (affectedroles) => {
    if ( roleStatus.value ===  'all') {
        roles.value.data.forEach((role) => {
            const idx = affectedroles.findIndex(p => p.id === role.id);
            if ( idx !== -1 ) {
                role.is_in_user = !role.is_in_user;

                //if ( roleStatus.value ===  'all') {
                    const chkbx = document.getElementById('role_' + role.id);
                    chkbx.setAttribute('checked', false);
                //}
            }
        });
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getRoles();
}

const outUserRolesCount = computed(() => {
    return  (rolescount.value === 0) ? 0 : (userroles.value.length ? (userroles.value.length > rolescount.value ? 0 : rolescount.value - userroles.value.length) : rolescount.value);
});

const isLdap = computed(() => {
    return  form.is_ldap === 1;
});

watch(searchQuery, debounce(() => {
    //getRoles();
}, 300));
//</editor-fold>

//<editor-fold desc="Employee">
const employeeCreate = () => {
    router.push('/employees/create/' + user.value.uuid);
}

const employeeEdit = () => {
    router.push('/employees/' + employee.value.uuid + '/edit');
}
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
const lastPath = ref('/users');
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
    if (route.name === 'users.edit' || route.name === 'users.show') {
        if (route.name === 'users.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        userid.value = route.params.id;
        getUser();
    } else {
        formMode.value = 'create';
    }
};

onMounted(() => {
    initComponent();
    getRoles();
    // getRolesCount();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="formMode === 'edit'">Edit</span>
                        <span v-else-if="formMode === 'create'">Create</span>
                        <span v-else>Details</span>
                        User
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/users">Users</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="formMode === 'edit'">Edit</span>
                            <span v-else-if="formMode === 'create'">Create</span>
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name"><span class="text text-xs">Name</span></label>
                                            <input v-model="form.name" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.name }" id="name" placeholder="Name" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="login"><span class="text text-xs">Login</span></label>
                                            <input v-model="form.login" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.login }" id="login" placeholder="Login" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.login }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="email"><span class="text text-xs">E-Mail</span></label>
                                            <input v-model="form.email" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.email }" id="email" placeholder="E-Mail" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="avatar"><span class="text text-xs">Avatar</span></label>
                                            <input v-model="form.avatar" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.avatar }" id="avatar" placeholder="Avatar" :disabled="formMode === 'show'">
                                            <span class="invalid-feedback">{{ errors.avatar }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="formMode === 'create'">
                                        <div class="form-group">
                                            <label for="password"><span class="text text-xs">Password</span></label>
                                            <div class="input-group mb-3">
                                                <input v-model="form.password" :type="inputPwdType" class="form-control form-control-sm" :class="{ 'is-invalid': errors.password }" id="password" placeholder="Password">
                                                <button v-if="form.password && !loading" type="button" @pointerdown="showPwd" @pointerup="hidePwd" class="btn btn-sm bg-transparent" style="margin-left: -40px; z-index: 100;">
                                                    <i class="fa fa-eye text-xs text-muted"></i>
                                                </button>
                                                <span class="invalid-feedback">{{ errors.password }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success col-sm-4">
                                                <input type="checkbox" class="custom-control-input" id="is_local" name="is_local" autocomplete="is_local" v-model="form.is_local" :disabled="formMode === 'show'">
                                                <label class="custom-control-label" for="is_local"><span class="text text-xs">Is Local</span></label>
                                                <span class="invalid-feedback">{{ errors.is_local }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success col-sm-4">
                                                <input type="checkbox" class="custom-control-input" id="is_ldap" name="is_ldap" autocomplete="is_ldap" v-model="form.is_ldap" :disabled="formMode === 'show'">
                                                <label class="custom-control-label" for="is_ldap"><span class="text text-xs">Is LDAP</span></label>
                                                <span class="invalid-feedback">{{ errors.is_ldap }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Statut</span></label> <br>
                                            <StatusShow :key="statusKey" v-if="status"
                                                        :status="status"
                                                        @status-changed="statusChanged"
                                                        :modelclass="user.modelclass"
                                                        :modeltype="user.modeltype"
                                                        :modelid="userid"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs text-olive">Last Seen</span></label>
                                            <span class="form-control border-0 text-xs">{{ formatDate(form.last_seen) }}</span>
                                            <span class="invalid-feedback">{{ errors.last_seen }}</span>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs text-olive">Created at</span></label>
                                            <span class="form-control border-0 text-xs">{{ formatDate(form.created_at) }}</span>
                                            <span class="invalid-feedback">{{ errors.created_at }}</span>
                                        </div>
                                    </div>
                                    <div v-if="formMode === 'edit' || formMode === 'show'" class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs text-olive">Updated at</span></label>
                                            <span class="form-control border-0 text-xs">{{ formatDate(form.updated_at) }}</span>
                                            <span class="invalid-feedback">{{ errors.updated_at }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="formMode === 'edit' || formMode === 'create'" type="submit" class="btn btn-sm btn-primary m-2" :disabled="loading">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <i class="fa fa-save mr-1"></i> Save
                                    </button>

                                    <ResetPassword v-if="formMode === 'edit' && can('users-reset-password') && form.is_local" ref="resetPasswordRef"
                                                   :user="user"
                                    ></ResetPassword>

                                    <router-link :to="prevRoutePath">
                                        <button type="submit" class="btn btn-sm btn-default m-2">
                                            <i class="fa fa-backward mr-1"></i> Back
                                        </button>
                                    </router-link>
                                </div>
                            </Form>
                        </div>

                        <div v-if="loading" class="overlay dark">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                <div class="col-lg-12">
                    <div class="card card-warning card-outline direct-chat direct-chat-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">User Roles</h3>
                            <div class="card-tools">
                                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                                <div class="btn-group">
                                    <button @click="getRolesByStatus('all')" type="button" class="btn btn-xs btn-outline-info" :class="roleStatus === 'all' ? 'active' : ''">
                                        <span class="mr-1">All</span>
                                        <span class="badge badge-pill badge-info">{{ rolescount }}</span>
                                    </button>

                                    <button :disabled="!(formMode === 'edit')" @click="getRolesByStatus('in_user')" type="button" class="btn btn-xs btn-outline-success" :class="roleStatus === 'in_user' ? 'active' : ''">
                                        <span class="mr-1">In User</span>
                                        <span class="badge badge-pill badge-info">{{ userroles.length ? userroles.length : 0 }}</span>
                                    </button>

                                    <button :disabled="!(formMode === 'edit')" @click="getRolesByStatus('out_user')" type="button" class="btn btn-xs btn-outline-warning" :class="roleStatus === 'out_user' ? 'active' : ''">
                                        <span class="mr-1">Out User</span>
                                        <span class="badge badge-pill badge-info">{{ outUserRolesCount }}</span>
                                    </button>

                                </div>

                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="d-flex justify-content-between pt-3">
                                    <div class="d-flex">
                                        <div v-if="selectedRoles.length > 0">
                                            <button v-show="can('roles-assign')" :disabled="roleStatus === 'in_user'" @click="bulkAssign" type="button" class="ml-2 mb-2 btn btn-success btn-sm">
                                                <i class="fa fa-link mr-1"></i>
                                                Give Selected
                                            </button>
                                            <button v-show="can('roles-revoke')" :disabled="roleStatus === 'out_user'" @click="bulkRevoke" type="button" class="ml-2 mb-2 btn btn-warning btn-sm">
                                                <i class="fa fa-ban mr-1"></i>
                                                Revoke Selected
                                            </button>
                                            <span class="text text-xs ml-2"> {{ selectedRoles.length }} role(s) selected</span>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-group mb-3">
                                            <input @keyup.enter="getRoles" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="search text..." />
                                            <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-default" @click="getRoles">
                                                    <div v-if="loadingRoles" class="spinner-border spinner-border-sm" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <span v-else><i class="fa fa-search"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" v-model="selectAll" @change="selectAllRoles" /></th>
                                        <th class="text text-xs" style="width: 10px">#</th>
                                        <th class="text text-xs">Name</th>
                                        <th class="text text-xs">Guard Name</th>
                                        <th class="text text-xs">Creation</th>
                                        <th class="text text-xs">Modification</th>
                                        <th class="text text-xs">Permissions</th>
                                        <th class="text text-xs">Options</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="roles.data.length > 0">
                                    <RoleListItem v-for="(role, index) in roles.data"
                                                        key="role.id"
                                                        :user=user
                                                        :role=role
                                                        :index=index
                                                        :formMode=formMode
                                                        @toggle-selection="toggleSelection"
                                                        @role-to-assign="roleAssign"
                                                        @role-to-revoke="roleRevoke"
                                                        :selectAll="selectAll"
                                                        :selectedRoles="selectedRoles"
                                    />
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="8" class="text-center">No results found...</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <Bootstrap4Pagination :data="roles" @pagination-change-page="getRoles" align="right" />
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                        <div v-if="loadingRoles" class="overlay dark">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                <div class="col-lg-12">
                    <div class="card card-outline direct-chat direct-chat-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Infos Employé
                                <button v-show="employee" @click.prevent="employeeEdit" type="button" class="ml-2 mb-1 btn btn-warning btn-xs">
                                    <i class="fa fa-edit font-weight-light"></i> Edit
                                </button>
                                <button v-show="!employee" @click.prevent="employeeCreate" type="button" class="ml-2 mb-1 btn btn-primary btn-xs">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </h3>
                            <div class="card-tools">
                                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="employee" class="card-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Name</span></label>
                                            <span class="form-control border-0 text-xs">{{ employee.nom_complet }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Matricule</span></label>
                                            <span class="form-control border-0 text-xs">{{ employee.matricule }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Departement</span></label>
                                            <span class="form-control border-0 text-xs">{{ employee.departement ? employee.departement.intitule : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Fonction</span></label>
                                            <span class="form-control border-0 text-xs">{{ employee.fonction ? employee.fonction.intitule : '' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Adresse</span></label>
                                            <span class="form-control border-0 text-xs">{{ employee.adresse }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Email</span></label>
                                            <span class="form-control border-0 text-xs">{{ employee.latest_email_address?.email_address }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Téléphone</span></label>
                                            <span class="form-control border-0 text-xs">{{ employee.latest_phonenum?.phone_number }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title">Created at</label>
                                            <span class="form-control border-0 text-xs">{{ formatDate(employee.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                        <div v-if="loadingEmployee" class="overlay dark">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="formMode === 'edit' || formMode === 'show'" class="row">
                <div class="col-lg-12">
                    <div class="card card-outline direct-chat direct-chat-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Infos LDAP</h3>
                            <div class="card-tools">
                                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="ldapaccount" class="card-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">CN</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.cn_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.cn_result === "OK." ? ldapaccount.cn : ldapaccount.cn_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">SN</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.sn_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.sn_result === "OK." ? ldapaccount.sn : ldapaccount.sn_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">TITLE</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.title_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.title_result === "OK." ? ldapaccount.title : ldapaccount.title_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">DESCRIPTION</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.description_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.description_result === "OK." ? ldapaccount.description : ldapaccount.description_result }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">PHYSICAL DELIVERY OFFICE NAME</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.physicaldeliveryofficename_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.physicaldeliveryofficename_result === "OK." ? ldapaccount.physicaldeliveryofficename : ldapaccount.physicaldeliveryofficename_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">TELEPHONE NUMBER</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.telephonenumber_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.telephonenumber_result === "OK." ? ldapaccount.telephonenumber : ldapaccount.telephonenumber_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">GIVEN NAME</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.givenname_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.givenname_result === "OK." ? ldapaccount.givenname : ldapaccount.givenname_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">WHEN CREATED</span></label>
                                            <span v-if="ldapaccount.whencreated_result === 'OK.'" class="form-control border-0 text-xs text-default">{{ formatDate(ldapaccount.whencreated) === 'Invalid date' ? ldapaccount.whencreated : formatDate(ldapaccount.whencreated) }}</span>
                                            <span v-else class="form-control border-0 text-xs text-danger">{{ ldapaccount.whencreated_result }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">DISTINGUISHED NAME</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.distinguishedname_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.distinguishedname_result === "OK." ? ldapaccount.distinguishedname : ldapaccount.distinguishedname_result }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">WHEN CHANGED</span></label>
                                            <span v-if="ldapaccount.whenchanged_result === 'OK.'" class="form-control border-0 text-xs text-default">{{ formatDate(ldapaccount.whenchanged) === 'Invalid date' ? ldapaccount.whenchanged : formatDate(ldapaccount.whenchanged) }}</span>
                                            <span v-else class="form-control border-0 text-xs text-danger">{{ ldapaccount.whenchanged_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">DEPARTMENT</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.department_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.department_result === "OK." ? ldapaccount.department : ldapaccount.department_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">COMPANY</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.company_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.company_result === "OK." ? ldapaccount.company : ldapaccount.company_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">NAME</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.name_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.name_result === "OK." ? ldapaccount.name : ldapaccount.name_result }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">BAD PWD COUNT</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.badpwdcount_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.badpwdcount_result === "OK." ? ldapaccount.badpwdcount : ldapaccount.badpwdcount_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">LOGON COUNT</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.logoncount_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.logoncount_result === "OK." ? ldapaccount.logoncount : ldapaccount.logoncount_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">SAM ACCOUNT NAME</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.samaccountname_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.samaccountname_result === "OK." ? ldapaccount.samaccountname : ldapaccount.samaccountname_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">MAIL</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.mail_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.mail_result === "OK." ? ldapaccount.mail : ldapaccount.mail_result }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">USER PRINCIPAL NAME</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.userprincipalname_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.userprincipalname_result === "OK." ? ldapaccount.userprincipalname : ldapaccount.userprincipalname_result }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">THUMBNAIL PHOTO</span></label>
                                            <span class="form-control border-0 text-xs" :class="'text-' + (ldapaccount.thumbnailphoto_result === 'OK.' ? 'default' : 'danger')">{{ ldapaccount.thumbnailphoto_result === "OK." ? ldapaccount.thumbnailphoto : ldapaccount.thumbnailphoto_result }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                        <div v-if="loadingLdap" class="overlay dark">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>
