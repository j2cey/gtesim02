<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import UserListItem from './UserListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import {useAbility} from "@casl/vue";

const { can, cannot } = useAbility();

const loading = ref(false);
const toastr = useToastr();
const users = ref({'data': []});
const editing = ref(false);
const form = ref(null);
const formValues = ref();
const paginationLinksLimit = ref(5);

const userIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedUsers = ref([]);

const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
    console.log("toggleSelection: ", selectedUsers.value);
};

const getUsers = (page = 1) => {
    loading.value = true;
    axios.get(`/api/users?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log('getUsers: ', response.data);
            users.value = response.data;
            selectedUsers.value = [];
            selectAll.value = false;
        })
        .finally(() => {
            loading.value = false;
        })
}

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup
        .string()
        .nullable()
        .transform((curr, orig) => (orig === "" ? null : curr))
        .min(8, "password must be at least 8"),
});

const createUser = (values, { resetForm, setErrors }) => {
    axios.post('/api/users', values)
        .then((response) => {
            users.value.data.unshift(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success('User created successfully!');
        }).catch((error) => {
            if (error.response.status === 422 && error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
    });
}

const updateUser = (values, { setErrors }) => {
    axios.put('/api/users/' + values.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id === response.data.id);
            users.value.data[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('User updated successfully!');
        }).catch((error) => {
            if (error.response.status === 422) {
                setErrors(error.response.data.errors);
            }
    });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
}

const addUser = () => {
    editing.value = false;
    form.value.resetForm();
    $('#userFormModal').modal('show');
}

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');

    // formValues.value = {
    //     id: user.id,
    //     name: user.name,
    //     email: user.email,
    // };

    form.value.setValues({
        id: user.id,
        name: user.name,
        email: user.email,
    });
};

const confirmUserDeletion = (user) => {
    userIdBeingDeleted.value = user.id;
    $('#deleteUserModal').modal('show');
};

const deleteUser = () => {
    axios.delete(`/api/users/${userIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            toastr.success('User deleted successfully!');
            users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value
        }
    })
        .then(response => {
            users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
            selectedUsers.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllUsers = () => {
    if (selectAll.value) {
        selectedUsers.value = users.value.data.map(user => user.id);
    } else {
        selectedUsers.value = [];
    }
    console.log("selectAllUsers: ", selectedUsers.value);
}

const clearSearchQuery = () => {
    searchQuery.value = '';
}

watch(searchQuery, debounce(() => {
    //getUsers();
}, 300));

onMounted(() => {
    getUsers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link v-if="can('users-create')" to="users/create">
                        <button type="button" class="mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-user-plus mr-1"></i>
                            New User
                        </button>
                    </router-link>
                    <div v-if="selectedUsers.length > 0">
                        <button v-if="can('users-delete') && selectedUsers.length > 0" @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span v-if="selectedUsers.length > 0" class="ml-2 text-muted"> {{ selectedUsers.length }} user(s) selected</span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="getUsers" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="getUsers">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span v-else><i class="fa fa-search"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers" size="small" :limit="paginationLinksLimit" align="right" />
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers" /></th>
                            <th style="width: 10px">#</th>
                            <th class="text text-xs">Name</th>
                            <th class="text text-xs">Email</th>
                            <th class="text text-xs">Created at</th>
                            <th class="text text-xs">Updated at</th>
                            <th class="text text-xs">Roles</th>
                            <th class="text text-xs">Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                        <UserListItem v-for="(user, index) in users.data"
                                      key="user.id"
                                      :user=user :index=index
                                      @edit-user="editUser"
                                      @confirm-user-deletion="confirmUserDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="7" class="text-center">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>{{ loading ? ' Loading...' : 'No results found...' }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <span v-if="users.meta?.total > 0" class="text text-xs text-primary">{{ users.meta.total + ' record' + (users.meta.total > 1 ? 's' : '') }}</span>
                    <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers" size="small" :limit="paginationLinksLimit" align="right" />
                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema" v-slot="{ errors, values }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                   id="name" aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control" :class="{'is-invalid': errors.email}" id="email"
                                   aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <Field name="password" type="password" class="form-control "
                                   :class="{ 'is-invalid': errors.password }" id="password" aria-describedby="nameHelp"
                                   placeholder="Enter password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteUser" type="button" class="btn btn-primary">Delete User</button>
                </div>
            </div>
        </div>
    </div>
</template>
