<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";
import StatusShow from "../statuses/StatusShow.vue";
import TagsShow from "../tags/TagsShow.vue";
import { useAbility } from "@casl/vue";

const { can, cannot } = useAbility();

const modeltype = ref('howtos');
const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const user = ref({});
const form = reactive({
    title: '',
    howtotype: {},
    code: '',
    view: '',
    description: '',
    tags: [],
});

const formMode = ref('create');
const loading = ref(false);
const errorMessage = ref('');

const initForm = () => {
    form.title = '';
    form.howtotype = {};
    form.code = '';
    form.view = '';
    form.description = '';
    form.tags = [];
}

const handleSubmit = (values, actions) => {
    errorMessage.value = '';
    actions.resetForm();
    if (formMode.value === 'edit') {
        updateHowto(values, actions);
    } else if (formMode.value === 'create') {
        createHowto(values, actions);
    }
};

const createHowto = (values, actions) => {
    loading.value = true;
    axios.post('/api/howtos' + (user ? '/' + user.value.uuid : '' ), form)
        .then((response) => {
            // router.push('/howtos');
            howto.value = response.data;
            howtoid.value = response.data.id;
            formMode.value = 'edit';

            Swal.fire({
                html: '<small>Rubrique créée avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                formMode.value = 'edit';
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

const updateHowto = (values, actions) => {
    loading.value = true;
    axios.put(`/api/howtos/${route.params.id}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>Rubrique modifié avec succès !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                formMode.value = 'edit';
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

const howto = ref({})
const howtoid = ref(null)
const getHowto = () => {
    axios.get(`/api/howtos/${howtoid.value}/edit`)
        .then((response) => {
            console.log("getHowto, response: ", response)
            form.code = response.data.code;

            form.title = response.data.title;
            form.code = response.data.code;
            form.view = response.data.view;
            form.description = response.data.description;
            form.tags = response.data.tags;
            form.howtotype = response.data.howtotype;

            howto.value = response.data;
            status.value = response.data.status;
        }).then(() => {

        }

    )
};

const howtotypes = ref([]);
const getHowtoTypes = () => {
    axios.get(`/api/howtotypes/all`)
        .then((response) => {
            console.log("getHowtoTypes, response: ", response)
            howtotypes.value = response.data;
        }).then(() => {

        })
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
const lastPath = ref('/howtos');
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
    if (route.name === 'howtos.edit' || route.name === 'howtos.show') {
        if (route.name === 'howtos.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        howtoid.value = route.params.id;
        getHowto();
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
    getHowtoTypes();
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
                        Rubriques Comment-Faire
                        <p class="text text-muted text-xs">Une Rubrique de Tuto Comment-Faire est destinée à contenir le détail <b>HTML</b> à afficher</p>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/howtos">Rubriques Comment-Faire</router-link>
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
                                            <label for="title"><span class="text text-xs">Title</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.title }}</span>
                                            <input v-else v-model="form.title" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.title }" id="title" placeholder="Title">
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="code"><span class="text text-xs">Code</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.code }}</span>
                                            <input v-else v-model="form.code" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.code }" id="code" placeholder="Code">
                                            <span class="invalid-feedback">{{ errors.code }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="view"><span class="text text-xs">Vue</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.view }}</span>
                                            <input v-else v-model="form.view" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.view }" id="view" placeholder="Vue">
                                            <span class="invalid-feedback">{{ errors.view }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="description"><span class="text text-xs">Description</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.description }}</span>
                                            <input v-else v-model="form.description" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.description }" id="description" placeholder="Description">
                                            <span class="invalid-feedback">{{ errors.description }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="howtotype"><span class="text text-xs">Type</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.howtotype?.title }}</span>
                                            <multiselect v-else
                                                         id="howtotype"
                                                         v-model="form.howtotype"
                                                         value=""
                                                         :options="howtotypes"
                                                         :searchable="true"
                                                         label="title"
                                                         track-by="id"
                                                         key="id"
                                                         :max-height="100"
                                                         placeholder="Type"
                                            >
                                            </multiselect >
                                            <span v-show="errorMessage.howtotype" class="text text-xs text-danger">{{ errors.howtotype }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="formMode === 'edit' || formMode === 'show'">
                                        <div class="form-group">
                                            <label for="tags"><span class="text text-xs">Tags</span></label>
                                            <TagsShow :key="howto.uuid"
                                                        :tags="form.tags"
                                                        :modelclass="howto.modelclass"
                                                        :modelid="howtoid"
                                            ></TagsShow>
                                            <span v-show="errorMessage.tags" class="text text-xs text-danger">{{ errors.tags }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" v-if="formMode === 'edit' || formMode === 'show'">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Statut</span></label> <br>
                                            <StatusShow :key="statusKey" v-if="status"
                                                        :status="status"
                                                        @status-changed="statusChanged"
                                                        :modelclass="howto.modelclass"
                                                        :modeltype="howto.modeltype"
                                                        :modelid="howtoid"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="(formMode === 'create' && can('howtos-create') || formMode === 'edit' && can('howtos-update')) && (formMode === 'create' || formMode === 'edit')" type="submit" class="btn btn-sm btn-primary m-2">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <i v-else class="fa fa-save mr-1"></i>
                                        Valider
                                    </button>
                                    <router-link :to="prevRoutePath">
                                        <button type="submit" class="btn btn-sm btn-default m-2">
                                            <i class="fa fa-backward mr-1"></i> Retour
                                        </button>
                                    </router-link>
                                    <router-link v-if="(formMode === 'edit' || formMode === 'show') && can('howtos-edithtml')" :to="`/howtos/${howto.uuid}/htmledit`">
                                        <button type="submit" class="btn btn-sm btn-secondary m-2">
                                            <i class="fa fa-file-code mr-1"></i> Modification HTML
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
