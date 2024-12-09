<script setup>
import axios from 'axios';
import {computed, onMounted, reactive, ref, watch} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import {useToastr} from '@/toastr';
import {Form} from 'vee-validate';
import Swal from "sweetalert2";
import HowtoStepList from "../howtosteps/HowtoStepList.vue";
import {useAbility} from "@casl/vue";
import TagsShow from "../tags/TagsShow.vue";
import StatusShow from "../statuses/StatusShow.vue";

const { can, cannot } = useAbility();

const modeltype = ref('howtothreads');
const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    title: '',
    code: '',
    description: '',
    tags: [],
    image: {},
});

const formMode = ref('create');
const loading = ref(false);
const errorMessage = ref('');

const initForm = () => {
    form.title = '';
    form.code = '';
    form.description = '';
    form.tags = [];
    form.image = {};
}

const handleSubmit = (values, actions) => {
    errorMessage.value = '';
    actions.resetForm();
    if (formMode.value === 'edit') {
        updateHowtoThread(values, actions);
    } else if (formMode.value === 'create') {
        createHowtoThread(values, actions);
    }
};

const createHowtoThread = (values, actions) => {
    loading.value = true;
    axios.post('/api/howtothreads', form)
        .then((response) => {
            // router.push('/howtothreads');
            howtothread.value = response.data;
            howtothreadid.value = response.data.id;
            formMode.value = 'edit';

            Swal.fire({
                html: '<small>Tutoriels Comment-Faire créée avec succès !</small>',
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

const updateHowtoThread = (values, actions) => {
    loading.value = true;
    axios.put(`/api/howtothreads/${route.params.id}`, form)
        .then((response) => {
            Swal.fire({
                html: '<small>Tutoriels Comment-Faire modifiée avec succès !</small>',
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

const howtothread = ref({})
const howtothreadid = ref(null)
const getHowtoThread = () => {
    axios.get(`/api/howtothreads/${howtothreadid.value}/edit`)
        .then((response) => {
            console.log("getHowtoThread, response: ", response)

            form.title = response.data.title;
            form.code = response.data.code;
            form.description = response.data.description;
            form.tags = response.data.tags;

            imageUrl.value = response.data.image;

            howtothread.value = response.data;
            status.value = response.data.status;
        }).then(() => {

        }

    )
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

const imageUrl = ref(null);
const fileInput = ref(null);
const openFileInput = () => {
    console.log("openFileInput");
    if ( formMode.value === 'create' || formMode.value === 'edit' ) {
        fileInput.value.click();
    }
};

const imageUpload = (e) => {
    if (e.target.files.length !== 0 && ( formMode.value === 'create' || formMode.value === 'edit' )) {
        console.log("imageUpload");
        let reader = new FileReader();

        let file = e.target.files[0];

        reader.readAsDataURL(file);

        imageUrl.value = URL.createObjectURL(file);

        reader.onloadend = function () {
            let base64data = reader.result;

            form.image = {
                'lastModified': e.target.files[0].lastModified,
                'name': e.target.files[0].name,
                'size': e.target.files[0].size,
                'type': e.target.files[0].type,
                'base64data': base64data
            };
        }
    }
};

const currentPath = ref('/');
const lastPath = ref('/howtothreads');
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
    if (route.name === 'howtothreads.edit' || route.name === 'howtothreads.show') {
        if (route.name === 'howtothreads.edit') {
            formMode.value = 'edit';
        } else {
            formMode.value = 'show';
        }
        howtothreadid.value = route.params.id;
        getHowtoThread();
    } else {
        formMode.value = 'create';
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
                    <h3 class="m-0">
                        <span v-if="formMode === 'edit'">Modification</span>
                        <span v-else-if="formMode === 'create'">Création</span>
                        <span v-else>Détails</span>
                        Tutoriels Comment-Faire
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/howtothreads">Tutoriels Comment-Faire</router-link>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Titre</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.title }}</span>
                                            <input v-else v-model="form.title" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.title }" id="title" placeholder="Titre">
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code"><span class="text text-xs">Code</span></label>
                                            <span v-if="formMode === 'show'" class="form-control border-0 text-xs">{{ form.code }}</span>
                                            <input v-else v-model="form.code" type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errorMessage.code }" id="code" placeholder="Code" readonly>
                                            <span class="invalid-feedback">{{ errors.code }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
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
                                            <TagsShow :key="howtothread.uuid"
                                                      :tags="form.tags"
                                                      :modelclass="howtothread.modelclass"
                                                      :modelid="howtothreadid"
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
                                                        :modelclass="howtothread.modelclass"
                                                        :modeltype="howtothread.modeltype"
                                                        :modelid="howtothreadid"
                                            ></StatusShow>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title"><span class="text text-xs">Image</span></label>
                                            <div class="text-center">
                                                <input @change="imageUpload" ref="fileInput" type="file" class="custom-file-input">
                                                <div id="preview">
                                                    <img @click="openFileInput" :src="imageUrl" :alt="(formMode === 'create' || formMode === 'edit') ? 'cliquez pour ajouter une image' : ''">
                                                </div>
                                            </div>
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button v-if="(formMode === 'create' && can('howtothreads-create') || formMode === 'edit' && can('howtothreads-update')) && (formMode === 'create' || formMode === 'edit')" type="submit" class="btn btn-sm btn-primary m-2">
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

            <HowtoStepList v-if="(formMode === 'edit' || formMode === 'show')"
                          :howtothreadid="howtothreadid"
                          list_title="Etapes"
                          list_color="success"
            ></HowtoStepList>
        </div>
    </div>

</template>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
body {
    background-color: #e2e2e2;
}

#app {
    padding: 20px;
}

#preview {
    display: flex;
    justify-content: center;
    align-items: center;
}

#preview img {
    max-width: 100%;
    max-height: 200px;
}
</style>
