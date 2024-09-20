<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import EsimListItem from './EsimListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr();
const esims = ref({'data': []});
const editing = ref(false);
const form = ref(null);
const formValues = ref();

const esimIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedEsims = ref([]);

const toggleSelection = (esim) => {
    const index = selectedEsims.value.indexOf(esim.id);
    if (index === -1) {
        selectedEsims.value.push(esim.id);
    } else {
        selectedEsims.value.splice(index, 1);
    }
};

const getEsims = (page = 1) => {
    axios.get(`/api/esims?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            esims.value = response.data;
            selectedEsims.value = [];
            selectAll.value = false;
        })
}

const createEsimSchema = yup.object({
    name: yup.string().required(),
    guard_name: yup.string().required(),
});

const editEsimSchema = yup.object({
    name: yup.string().required(),
    guard_name: yup.string().required(),
});

const updateEsim = (values, { setErrors }) => {
    axios.put('/api/esims/' + values.id, values)
        .then((response) => {
            const index = esims.value.findIndex(esim => esim.id === response.data.id);
            esims.value.data[index] = response.data;
            $('#esimFormModal').modal('hide');
            toastr.success('Esim updated successfully!');
        }).catch((error) => {
        console.log("updateEsim-error: ", error);
        setErrors(error.response.data.errors);
    });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateEsim(values, actions);
    } else {
        createEsim(values, actions);
    }
}

const addEsim = () => {
    editing.value = false;
    form.value.resetForm();
    $('#esimFormModal').modal('show');

}

const editEsim = (esim) => {
    editing.value = true;
    form.value.resetForm();
    $('#esimFormModal').modal('show');

    // formValues.value = {
    //     id: esim.id,
    //     name: esim.name,
    //     email: esim.email,
    // };

    form.value.setValues({
        id: esim.id,
        name: esim.name,
        email: esim.email,
    });
};

const confirmEsimDeletion = (esim) => {
    esimIdBeingDeleted.value = esim.id;
    $('#deleteEsimModal').modal('show');
};

const deleteEsim = () => {
    axios.delete(`/api/esims/${esimIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteEsimModal').modal('hide');
            toastr.success('Esim deleted successfully!');
            esims.value.data = esims.value.data.filter(esim => esim.id !== esimIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/esims', {
        data: {
            ids: selectedEsims.value
        }
    })
        .then(response => {
            esims.value.data = esims.value.data.filter(esim => !selectedEsims.value.includes(esim.id));
            selectedEsims.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllEsims = () => {
    if (selectAll.value) {
        selectedEsims.value = esims.value.data.map(esim => esim.id);
    } else {
        selectedEsims.value = [];
    }
}

watch(searchQuery, debounce(() => {
    getEsims();
}, 300));

onMounted(() => {
    getEsims();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste de E-sims</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Esims</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link to="esims/create">
                        <button @click="addEsim" type="button" class="mb-2 btn btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Ajouter une nouvelle E-sim
                        </button>
                    </router-link>
                    <div v-if="selectedEsims.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedEsims.length }} esims</span>
                    </div>
                </div>
                <div>

                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>


            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllEsims" /></th>
                            <th style="width: 10px">#</th>
                            <th>Nom</th>
                            <th>Numéro téléphone</th>
                            <th>Adresse Email</th>
                            <th>user</th>
                            <th>Département</th>
                            <th>Détails</th>
                        </tr>
                        </thead>
                        <tbody v-if="esims.data.length > 0">
                        <EsimListItem v-for="(esim, index) in esims.data"
                                      key="esim.id"
                                      :esim=esim :index=index
                                      @edit-esim="editEsim"
                                      @confirm-esim-deletion="confirmEsimDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="6" class="text-center">No results found...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="esims" @pagination-change-page="getEsims" align="right" />

        </div>
    </div>

    <div class="modal fade" id="deleteEsimModal" data-backdrop="static" tabindex="-1" esim="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" esim="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete Esim</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this esim ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteEsim" type="button" class="btn btn-primary">Delete Esim</button>
                </div>
            </div>
        </div>
    </div>
</template>
