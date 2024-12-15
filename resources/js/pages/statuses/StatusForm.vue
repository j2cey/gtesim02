<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import { debounce } from "lodash";

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    code: '',
    name: '',
    style: '',
    description: '',
});

const editMode = ref(false);
const loading = ref(false);

const initForm = () => {
    form.code = '';
    form.name = '';
    form.style = '';
    form.description = '';
}

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updateStatus(values, actions);
    } else {
        createStatus(values, actions);
    }
};

const createStatus = (values, actions) => {
    loading.value = true;
    axios.post('/api/statuses', form)
        .then((response) => {
            // router.push('/statuses');
            status.value = response.data;
            statusid.value = response.data.id;
            editMode.value = true;
            toastr.success('Statut créé avec succès !');
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

const updateStatus = (values, actions) => {
    loading.value = true;
    axios.put(`/api/statuses/${route.params.id}`, form)
        .then((response) => {
            toastr.success('Statut modifié avec succès !');
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

const status = ref({})
const statusid = ref(null)
const getStatus = () => {
    axios.get(`/api/statuses/${route.params.id}/edit`)
        .then((response) => {
            console.log("getStatus, response: ", response)
            form.code = response.data.code;
            form.name = response.data.name;
            form.style = response.data.style;
            form.description = response.data.description;

            status.value = response.data;
        }).then(() => {

        }

    )
};

const currentPath = ref('/');
const lastPath = ref('/statuses');
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
    if (route.name === 'statuses.edit') {
        statusid.value = route.params.id;
        editMode.value = true;
        getStatus();
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
                        <span v-if="editMode">Modification</span>
                        <span v-else>Création</span>
                        Statut</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/statuses">Statuts</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Modification</span>
                            <span v-else>Création</span>
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
                                            <label for="imsi">Code</label>
                                            <input v-model="form.code" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.code }" id="code" placeholder="Code">
                                            <span class="invalid-feedback">{{ errors.code }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="iccid">Nom</label>
                                            <input v-model="form.name" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.name }" id="name" placeholder="Nom">
                                            <span class="invalid-feedback">{{ errors.name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ac">Style</label>
                                            <input v-model="form.style" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.style }" id="style" placeholder="Style">
                                            <span class="invalid-feedback">{{ errors.style }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pin">Description</label>
                                            <input v-model="form.description" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.description }" id="description" placeholder="Description">
                                            <span class="invalid-feedback">{{ errors.description }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary m-2">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <i v-else class="fa fa-save mr-1"></i>
                                        Valider
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
        </div>
    </div>

</template>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>
