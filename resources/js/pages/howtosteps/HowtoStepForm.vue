<script setup>
import axios from 'axios';
import { reactive, onMounted, ref, watch, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";
import { useAbility } from "@casl/vue";
import TagsShow from "../tags/TagsShow.vue";
import StatusShow from "../statuses/StatusShow.vue";

const { can, cannot } = useAbility();

const modeltype = ref('howtosteps');
const router = useRouter();
const route = useRoute();
const toastr = useToastr();

const form = reactive({
    title: '',
    posi: '',
    description: '',
    howtothread: {},
    howto: {},
    tags: [],
});

const formMode = ref('create');
const loading = ref(false);
const errorMessage = ref('');

const initForm = () => {
    form.title = '';
    form.posi = '';
    form.description = '';
    form.howtothread = {};
    form.howto = {};
    form.tags = [];
}

const handleSubmit = (values, actions) => {
    errorMessage.value = '';
    actions.resetForm();
    if (formMode.value === 'edit') {
        updateHowtoStep(values, actions);
    } else if (formMode.value === 'create') {
        createHowtoStep(values, actions);
    }
};

const createHowtoStep = (values, actions) => {
    loading.value = true;
    axios.post('/api/howtosteps', form)
        .then((response) => {
            // router.push('/howtosteps');
            howtostep.value = response.data;
            howtostepid.value = response.data.id;

            Swal.fire({
                html: '<small>Etape créé avec succès !</small>',
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

const updateHowtoStep = (values, actions) => {
    loading.value = true;
    axios.put(`/api/howtosteps/${route.params.id}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>Etape modifiée avec succès !</small>',
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

const howtostep = ref({})
const howtostepid = ref(null)
const getHowtoStep = () => {
    axios.get(`/api/howtosteps/${howtostepid.value}/edit`)
        .then((response) => {
            console.log("getHowtoStep, response: ", response)
            form.code = response.data.code;

            form.title = response.data.title;
            form.posi = response.data.posi;
            form.description = response.data.description;

            form.howto = response.data.howto;
            form.howtothread = response.data.howtothread;
            form.tags = response.data.tags;

            howtostep.value = response.data;

            status.value = response.data.status;
            //tags.value = response.data.tags;
        }).then(() => {

        }

    )
};

const howtos = ref([]);
const getHowtos = () => {
    axios.get(`/api/howtos/all`)
        .then((response) => {
            console.log("getHowtos, response: ", response)
            howtos.value = response.data;
        }).then(() => {

        }
    )
};

const howtothreadid = ref(null);
const getHowtoThread = () => {
    axios.get(`/api/howtothreads/${howtothreadid.value}/edit`)
        .then((response) => {
            console.log("getHowtoThread, response: ", response)
            form.howtothread = response.data;
        }).then(() => {

        }
    )
};

const posimax = computed(() => {
    console.log("posimax, form.howtothread: ", form.howtothread);
    if ( form.howtothread && form.howtothread.steps ) {
        return form.howtothread.steps.length - 1;
    } else {
        return 1;
    }
});

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
const lastPath = ref('/howtosteps');
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
    if (route.name === 'howtosteps.edit' || route.name === 'howtosteps.show') {
        if (route.name === 'howtosteps.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        howtostepid.value = route.params.id;
        getHowtoStep();
    } else {
        formMode.value = 'create';

        if (route.params.threadid) {
            howtothreadid.value = route.params.threadid;
            getHowtoThread();
        }
    }
};

onMounted(() => {
    initComponent();
    getHowtos();
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
                        Etape
                        <p v-show="form.howtothread" class="text text-sm"> <span class="text text-muted">Pour le Tutoriel </span>
                            <router-link class="text text-indigo" v-if="can('howtothreads-show')" :to="`/howtothreads/${form.howtothread?.uuid}/show`">
                                {{ form.howtothread?.title }}
                            </router-link>
                            <span class="text text-indigo" v-else>{{ form.howtothread?.title }}</span>
                        </p>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/howtosteps">Etapes Tutoriels</router-link>
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
                                            <label for="title"><span class="text text-xs">Titre Etape</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.title }}</span>
                                            <input v-else v-model="form.title" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.title }" id="title" placeholder="Titre Etape">
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pin"><span class="text text-xs">Rubrique</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.howto?.title }}</span>
                                            <multiselect v-else
                                                         id="howto"
                                                         v-model="form.howto"
                                                         value=""
                                                         :options="howtos"
                                                         :searchable="true"
                                                         label="title"
                                                         track-by="id"
                                                         key="id"
                                                         :max-height="100"
                                                         placeholder="Rubrique"
                                            >
                                            </multiselect >
                                            <span v-show="errorMessage.howto" class="text text-xs text-danger">{{ errors.howto }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="formMode === 'edit'">
                                        <div class="form-group">
                                            <label for="posi"><span class="text text-xs">Num. Ordre</span></label>
                                            <input type="number" v-model="form.posi" min="1" :max="posimax" onkeydown="return false" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage?.posi }" id="posi" placeholder="Num. Ordre">
                                            <span class="invalid-feedback">{{ errors.posi }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="description"><span class="text text-xs">Description</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.description }}</span>
                                            <input v-else v-model="form.description" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.description }" id="description" placeholder="Description">
                                            <span class="invalid-feedback">{{ errors.description }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-if="formMode === 'edit' || formMode === 'show'">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tags"><span class="text text-xs">Tags</span></label>
                                            <TagsShow :key="howtostep.uuid"
                                                      :tags="form.tags"
                                                      :modelclass="howtostep.modelclass"
                                                      :modelid="howtostepid"
                                            ></TagsShow>
                                            <span v-show="errorMessage.tags" class="text text-xs text-danger">{{ errors.tags }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="puk"><span class="text text-xs">Statut</span></label> <br>
                                            <StatusShow :key="statusKey" v-if="status"
                                                        :status="status"
                                                        @status-changed="statusChanged"
                                                        :modelclass="howtostep.modelclass"
                                                        :modeltype="howtostep.modeltype"
                                                        :modelid="howtostepid"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="(formMode === 'create' && can('howtosteps-create') || formMode === 'edit' && can('howtosteps-update')) && (formMode === 'create' || formMode === 'edit')" type="submit" class="btn btn-sm btn-primary m-2">
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
