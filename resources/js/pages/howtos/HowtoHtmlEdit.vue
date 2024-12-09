<script setup>

import {computed, onMounted, ref, watch, useTemplateRef, reactive} from "vue";
import axios from "axios";
import { useRouter, useRoute } from 'vue-router';

// Import Quill required dependencies
import {QuillEditor, Quill} from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.core.css';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import '@vueup/vue-quill/dist/vue-quill.bubble.css';
import 'quill-image-uploader/dist/quill.imageUploader.min.css';
import ImageResize from "quill-image-resize/src/ImageResize.js";
import {Form} from "vee-validate";
import { useAbility } from "@casl/vue";
import Swal from "sweetalert2";

const { can, cannot } = useAbility();

const myQuillEditor = ref();

const form = reactive({
    htmlbody: null,
    howto: null,
    images: []
});

const router = useRouter();
const route = useRoute();

const howto = ref({});
const howtoid = ref(null);
const loading = ref(false);
const getHowto = () => {
    axios.get(`/api/howtos/${howtoid.value}/edit`)
        .then((response) => {
            console.log("getHowto, response: ", response)

            form.htmlbody = response.data.htmlbody;
            form.howto = response.data.id;

            howto.value = response.data;
        }).then(() => {

        }

    )
};

// Quill Configs

// Register ImageResize module
Quill.register('modules/imageResize', ImageResize);

const toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote', 'code-block'],

    [{'header': 1}, {'header': 2}],
    [{'list': 'ordered'}, {'list': 'bullet'}],
    [{'script': 'sub'}, {'script': 'super'}],
    [{'indent': '-1'}, {'indent': '+1'}],
    [{'direction': 'rtl'}],

    [{'size': ['small', false, 'large', 'huge']}],
    [{'header': [1, 2, 3, 4, 5, 6, false]}],

    [{'color': []}, {'background': []}],
    [{'font': []}],
    [{'align': []}],
    ['link', 'image', 'video'],
    ['clean'],
];

const editorOption = ref({
    modules: {
        toolbar: {
            container: toolbarOptions,
            handlers: {
                'image': function (value) {
                    if (value) {
                        document.querySelector('#imageUpload').click();
                    } else {
                        quill.format('image', false);
                    }
                }
            },
        },
        history: {
            delay: 1000,
            maxStack: 50,
            userOnly: false
        },
        imageResize: {
            displayStyles: {
                backgroundColor: 'black',
                border: 'none',
                color: 'white'
            },
            modules: [ 'Resize', 'DisplaySize', 'Toolbar' ]
        }
    }
});

const imageUpload = (e) => {
    if (e.target.files.length !== 0) {
        let quill = myQuillEditor.value.getQuill();
        let reader = new FileReader();

        reader.readAsDataURL(e.target.files[0]);

        reader.onloadend = function () {
            let base64data = reader.result;

            let imgarr = {
                'lastModified': e.target.files[0].lastModified,
                'name': e.target.files[0].name,
                'size': e.target.files[0].size,
                'type': e.target.files[0].type,
                'base64data': base64data
            };

            form.images.push(imgarr);

            // Get cursor location
            let length = quill.getSelection().index;

            // Insert image at cursor location
            quill.insertEmbed(length, 'image', base64data);

            // Set cursor to the end
            quill.setSelection(length + 1);
        }
    }
};

const onEditorBlur = (editor) => {
    //console.log('editor blur!', editor);
};
const onEditorFocus = (editor) => {
    //console.log('editor focus!', editor);
};
const onEditorReady = (editor) => {
    //console.log('editor ready!', editor);
}

const submitForm = () => {
    loading.value = true;
    axios.put(`/api/howtos/${route.params.id}/storehtml`,
        { htmlbody: form.htmlbody, images: form.images, howto: howto.value.id })
        .then(response => {
            loading.value = false;

            Swal.fire({
                html: '<small>HTML Rubrique modifié avec Succes !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                //window.location = '/howtos.readhtml/' + this.howto.id
                router.push('/howtos/' + howto.value.uuid + '/htmlread');
            })

        })
        .catch(error => {
            console.log("Error", error)
        })
}

/*const editor = computed(() => {
    return document.querySelector('#myquillRichText').quill; // useTemplateRef('myQuillEditor').quill; // document.getElementById('myquillRichText').quill; // document.getElementById('myquillRichText').getEditor().quill;//
});*/
//

const initComponent = () => {
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;
    howtoid.value = route.params.id;
    getHowto();
};

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
                        Modification HTML
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
                            <span>Modification HTML</span>
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

                        <Form @submit="submitForm" v-slot:default="{ errors }">

                            <div class="card-body">
                                <QuillEditor
                                    v-model:content="form.htmlbody"
                                    id="myquillRichText"
                                    rows="20"
                                    :options="editorOption"
                                    contentType="html"
                                    theme="snow"
                                    ref="myQuillEditor"
                                    @blur="onEditorBlur($event)"
                                    @focus="onEditorFocus($event)"
                                    @ready="onEditorReady($event)"
                                />
                                <div class="custom-file d-none">
                                    <input ref="image" @change="imageUpload($event)" type="file" class="custom-file-input" id="imageUpload" aria-describedby="imageUploadAddon">
                                    <label class="custom-file-label" for="imageUpload">Choose file</label>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="btn-group">
                                    <button v-if="can('howtos-edithtml')" type="submit" class="btn btn-sm btn-primary m-2">
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
                            </div>
                        </Form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
