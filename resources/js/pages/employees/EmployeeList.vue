<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import EmployeeListItem from './EmployeeListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';
import { useAbility } from "@casl/vue";


const { can, cannot } = useAbility();

const loading = ref(false);
const toastr = useToastr();
const employees = ref({'data': []});

const paginationLinksLimit = ref(5);

const employeeIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedEmployees = ref([]);

const toggleSelection = (employee) => {
    const index = selectedEmployees.value.indexOf(employee.id);
    if (index === -1) {
        selectedEmployees.value.push(employee.id);
    } else {
        selectedEmployees.value.splice(index, 1);
    }
};

const getEmployees = (page = 1) => {
    loading.value = true;
    axios.get(`/api/employes?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            console.log("getEmployees, response: ", response);
            employees.value = response.data;
            selectedEmployees.value = [];
            selectAll.value = false;
        }).finally(() => {
            loading.value = false;
    });
}

const confirmEmployeeDeletion = (employee) => {
    employeeIdBeingDeleted.value = employee.id;
    $('#deleteEmployeeModal').modal('show');
};

const deleteEmployee = () => {
    axios.delete(`/api/employes/${employeeIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEmployeeModal').modal('hide');
            toastr.success('Employee supprimé avec succès !');
            employees.value.data = employees.value.data.filter(employee => employee.id !== employeeIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/employes', {
        data: {
            ids: selectedEmployees.value
        }
    })
        .then(response => {
            employees.value.data = employees.value.data.filter(employee => !selectedEmployees.value.includes(employee.id));
            selectedEmployees.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllEmployees = () => {
    if (selectAll.value) {
        selectedEmployees.value = employees.value.data.map(employee => employee.id);
    } else {
        selectedEmployees.value = [];
    }
}

const clearSearchQuery = () => {
    searchQuery.value = '';
    getEmployees();
}

watch(searchQuery, debounce(() => {
    //getEmployees();
}, 300));

onMounted(() => {
    getEmployees();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste des Employés</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">Employés</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link v-if="can('employes-create')" to="employees/create">
                        <button type="button" class="mb-2 btn btn-sm btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Nouveau
                        </button>
                    </router-link>
                    <div v-if="can('employes-delete') && selectedEmployees.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Supprimer Sélection
                        </button>
                        <span class="ml-2 text-muted"> {{ selectedEmployees.length }} client(s) sélectionnées</span>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="input-group mb-3">
                        <input @keyup.enter="getEmployees" type="search" v-model="searchQuery" class="form-control text-xs form-control-sm" placeholder="Recherche text..." />
                        <button v-if="searchQuery && !loading" @click="clearSearchQuery" type="button" class="btn btn-sm bg-transparent" style="margin-left: -30px; z-index: 100;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-default" @click="getEmployees">
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
                    <Bootstrap4Pagination :data="employees" @pagination-change-page="getEmployees" size="small" :limit="paginationLinksLimit" align="right" />
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllEmployees" /></th>
                            <th style="width: 10px">#</th>
                            <th class="text text-xs">Nom</th>
                            <th class="text text-xs">Matricule</th>
                            <th class="text text-xs">Telephone(s)</th>
                            <th class="text text-xs">EMail(s)</th>
                            <th class="text text-xs">Département</th>
                            <th class="text text-xs">Fonction</th>
                            <th v-if="can('employes-creator-list')" class="text text-xs">Utilisateur</th>
                            <th class="text text-xs">Création</th>
                            <th class="text text-xs">Modification</th>
                            <th class="text text-xs">Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="employees.data.length > 0">
                        <EmployeeListItem v-for="(employee, index) in employees.data"
                                      key="employee.id"
                                      :employee=employee
                                      :index=index
                                      @confirm-employee-deletion="confirmEmployeeDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="11" class="text-center">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>{{ loading ? ' Chargement en cours...' : 'Aucun résultat trouvé...' }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <span v-if="employees.meta?.total > 0" class="text text-xs text-primary">{{ employees.meta.total }} enregistrement(s)</span>
                    <Bootstrap4Pagination :data="employees" @pagination-change-page="getEmployees" size="small" :limit="paginationLinksLimit" align="right" />
                </div>

                <div v-if="loading" class="overlay dark">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="deleteEmployeeModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Suppression Client Esim</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Etes-vous sûr de vouloir supprimer ce Client ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Annuler</button>
                    <button @click.prevent="deleteEmployee" type="button" class="btn btn-xs btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
