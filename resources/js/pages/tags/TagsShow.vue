<script setup>
import {onMounted, ref, watch} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import {useAbility} from "@casl/vue";
import {useRoute, useRouter} from 'vue-router';
import Multiselect from "vue-multiselect";
import Swal from "sweetalert2";

// TODO: Tags Limit, from settings and from props

const { can, cannot } = useAbility();
const router = useRouter();
const route = useRoute();

const toastr = useToastr();

const props = defineProps({
    tags: Object,
    modelclass: { type: String, default: ''},
    modelid: { type: String, default: ''},
});

const emit = defineEmits(['tagAdded']);

const loading = ref(false);

const tagLimit = ref(5);
const limitText = (count) => {
    return `... et ${count} autres TAGs`
}

const swalDangerActionWithBootstrapBtns = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-sm btn-danger m-2",
        cancelButton: "btn btn-sm btn-success m-2"
    },
    buttonsStyling: false
});

const swalGoodActionWithBootstrapBtns = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-sm btn-success m-2",
        cancelButton: "btn btn-sm btn-danger m-2"
    },
    buttonsStyling: false
});

const addTag = (newTag) => {
    swalGoodActionWithBootstrapBtns.fire({
        html: '<small>Confirmer</small> <span style="color: #04800b; font-weight: bold">Ajout</span> <small>du Tag <b>' + newTag + '</b></small>',
        icon: 'info',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonText: 'Valider',
        cancelButtonText: 'Annuler',
        preConfirm: () => {
            return axios.put(`/api/tags/add`,
                {
                    'modelclass': props.modelclass,
                    'modelid': props.modelid,
                    'relevanttags': [newTag]
                })
                .then(response => {
                    console.log("addTag, preConfirm, response: ", response);
                    return response;
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
        }, allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.value) {
            console.log("addTag, DONE, response: ", result);

            Swal.fire({
                html: '<small>TAG ajouté avec Succes !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                local_tags.value = result.value.data.tags;
            })
        }
    })
};

const removeTag = (oldTag) => {
    swalDangerActionWithBootstrapBtns.fire({
        html: '<small>Confirmer</small> <span style="color: #8c0b05; font-weight: bold;">Retrait</span> <small>du Tag <b>' + tagNameOnly(oldTag) + '</b></small>',
        icon: 'warning',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonText: 'Valider',
        cancelButtonText: 'Annuler',
        preConfirm: () => {
            return axios.put(`/api/tags/remove`,
                {
                    'modelclass': props.modelclass,
                    'modelid': props.modelid,
                    'relevanttags': [oldTag.id]
                })
                .then(response => {
                    console.log("removeTag, preConfirm, response: ", response);
                    return response;
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
        }, allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.value) {
            console.log("removeTag, DONE, response: ", result);

            Swal.fire({
                html: '<small>TAG retiré avec Succes !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {
                local_tags.value = result.value.data.tags;
            })
        }
    })
    local_tags.value.push(oldTag);
};

const customTagName = (tag) => {
    return `${tagKeyOnly(tag)}|${tagNameOnly(tag)}`
}

const tagNameOnly = (tag) => {
    let key = tagKeyOnly(tag);
    return tag.name[key];
}
const tagKeyOnly = (tag) => {
    return Object.keys(tag.name)[0];
}

const local_tags = ref(props.tags);
/*watch(
    () => props.status,
    () => { local_status.value = props.status },
    { immediate: true }
);*/
/*
watch(props.tags, () => {
    console.log("watch props.tags from TagsShow");
    local_tags.value = props.tags
}, { immediate: true });
*/

onMounted(() => {
    console.log("TagsShow, props", props);
});
</script>

<template>
    <multiselect id="tags"
                 v-model="local_tags" tag-placeholder="Ajouter Nouveau tag" placeholder="Rechercher / Ajouter Tag" label="name"
                 track-by="code" :options="local_tags" :multiple="true" :taggable="true" :custom-label="customTagName" @tag="addTag" @remove="removeTag"
                 :limit="tagLimit" :limit-text="limitText" :max-height="500"
    ></multiselect>
</template>

<style>
</style>
