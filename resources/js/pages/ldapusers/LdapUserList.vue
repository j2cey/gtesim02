<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import { useToastr } from '../../toastr.js';
import LdapUserListItem from './LdapUserListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import { useAbility } from "@casl/vue";


const { can, cannot } = useAbility();

const loading = ref(false);
const toastr = useToastr();
const ldapusers = ref({'data': []});

const paginationLinksLimit = ref(5);

const ldapuserIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedLdapUsers = ref([]);

const toggleSelection = (ldapuser) => {
    const index = selectedLdapUsers.value.indexOf(ldapuser.id);
    if (index === -1) {
        selectedLdapUsers.value.push(ldapuser.id);
    } else {
        selectedLdapUsers.value.splice(index, 1);
    }
};

const getLdapUsers = (page = 1) => {
    loading.value = true;
    axios.get(`/api/ldapusers?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getLdapUsers, response: ", response);
            ldapusers.value = response.data;
            selectedLdapUsers.value = [];
            selectAll.value = false;
        }).finally(() => {
        loading.value = false;
    });
}

const confirmLdapUserDeletion = (ldapuser) => {
    ldapuserIdBeingDeleted.value = ldapuser.id;
    $('#deleteLdapUserModal').modal('show');
};

const deleteLdapUser = () => {
    axios.delete(`/api/ldapusers/${ldapuserIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteLdapUserModal').modal('hide');
            toastr.success('LdapUser supprimé avec succès !');
            ldapusers.value.data = ldapusers.value.data.filter(ldapuser => ldapuser.id !== ldapuserIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/ldapusers', {
        data: {
            ids: selectedLdapUsers.value
        }
    })
        .then(response => {
            ldapusers.value.data = ldapusers.value.data.filter(ldapuser => !selectedLdapUsers.value.includes(ldapuser.id));
            selectedLdapUsers.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllLdapUsers = () => {
    if (selectAll.value) {
        selectedLdapUsers.value = ldapusers.value.data.map(ldapuser => ldapuser.id);
    } else {
        selectedLdapUsers.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getLdapUsers();
}

watch(searchQuery, debounce(() => {
    //getLdapUsers();
}, 300));

onMounted(() => {
    getLdapUsers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">LDAP Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Home</router-link>
                        </li>
                        <li class="breadcrumb-item active">LDAP Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link v-if="can('ldapusers-create')" to="ldapusers/create">
                        <button type="button" class="mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-user-plus mr-1"></i>
                            New
                        </button>
                    </router-link>
                    <div v-if="can('ldapusers-delete') && selectedLdapUsers.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-user-minus mr-1"></i>
                            Delete Selection
                        </button>
                        <span class="ml-2 text-muted"> {{ selectedLdapUsers.length }} selected user(s) </span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="getLdapUsers" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="getLdapUsers">
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
                    <Bootstrap4Pagination :data="ldapusers" @pagination-change-page="getLdapUsers" size="small" :limit="paginationLinksLimit" align="right" />
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllLdapUsers" /></th>
                            <th style="width: 10px">#</th>
                            <th class="text text-xs">Name</th>
                            <th class="text text-xs">Login</th>
                            <th class="text text-xs">E-Mail</th>
                            <th class="text text-xs">Phone Number</th>
                            <th class="text text-xs">Title</th>
                            <th class="text text-xs">Created</th>
                            <th class="text text-xs">Updated</th>
                            <th class="text text-xs">Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="ldapusers.data.length > 0">
                        <LdapUserListItem v-for="(ldapuser, index) in ldapusers.data"
                                          key="ldapuser.id"
                                          :ldapuser=ldapuser
                                          :index=index
                                          @confirm-ldapuser-deletion="confirmLdapUserDeletion"
                                          @toggle-selection="toggleSelection"
                                          :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="10" class="text-center">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>{{ loading ? ' Loading...' : 'No Result...' }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <span v-if="ldapusers.meta?.total > 0" class="text text-xs text-primary">{{ ldapusers.meta.total + ' record' + (ldapusers.meta.total > 1 ? 's' : '') }}</span>
                    <Bootstrap4Pagination :data="ldapusers" @pagination-change-page="getLdapUsers" size="small" :limit="paginationLinksLimit" align="right" />
                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="deleteLdapUserModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete LDAP User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Do you want to delete this LDAP User ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteLdapUser" type="button" class="btn btn-xs btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
