<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { useAbility } from "@casl/vue";
import { useRouter, useRoute } from 'vue-router';
import Swal from 'sweetalert2';

const { can, cannot } = useAbility();
const router = useRouter();
const route = useRoute();

const toastr = useToastr();

const props = defineProps({
    status: Object,
    modelclass: { type: String, default: ''},
    modeltype: { type: String, default: ''},
    modelid: { type: String, default: ''},
});

const emit = defineEmits(['statusChanged']);

const loading = ref(false);
const statuses = ref({});

const statusChange = () => {
    let linkstr = '/api/' + ( (props.modelclass !== '' && props.modelid !== '') ? 'statuses/change' : props.modeltype !== '' ? (props.modeltype + '/' + props.modelid + '/statuschange') : '/404');

    if (linkstr === '/404') {
        //Can change 7 to 2 for longer results.
        let randStr = (Math.random() + 1).toString(36).substring(2);
        router.push({ name: 'error.404',  params: { notFound: randStr } });
    } else {
        Swal.fire({
            html: '<span class="text font-weight-bold"> Select a Status</span>',
            input: "select",
            inputOptions: statuses.value,
            inputPlaceholder: "Select a Status",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {

                return new Promise((resolve) => {
                    if (value && statuses.value[value] !== local_status.value.code) {
                        resolve()
                    } else {
                        if (!value) {
                            resolve("You need to select Status");
                        } else {
                            resolve("You need to select a different Status");
                        }
                    }
                });
            },
            preConfirm: (value) => {
                loading.value = true;

                return axios.get(`${linkstr}`, {
                    params: {
                        statuscode: statuses.value[value],
                        modelclass: props.modelclass,
                        modeltype: props.modeltype,
                        modelid: props.modelid,
                    }
                })
                    .then(response => {
                        console.log("statusChange, preConfirm, response: ", response);
                        return response;
                    })
                    .catch(error => {
                        //console.log("request failed: ", error)
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            }, allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value) {
                //local_status.value = result.value.data;
                emit('statusChanged', result.value.data);
                toastr.success('Status Successfully changed !');
                loading.value = false;
            }
        })
    }
}

const getStatusesCode = () => {
    loading.value = true;
    axios.get(`/api/statuses/codes`)
        .then((response) => {
            statuses.value = response.data; //Object.assign({}, response.data);
        }).then(() => {

        }
    ).finally(() => {
        loading.value = false;
    })
};

const canLinkAPI = computed(() => {
    return props.modelclass !== '' ? true : (props.modeltype !== '');
});

const local_status = ref(props.status);
/*watch(
    () => props.status,
    () => { local_status.value = props.status },
    { immediate: true }
);*/
watch(props.status, () => {
    console.log("watch props.status from StatusShow");
    local_status.value = props.status
}, { immediate: true });

onMounted(() => {
    console.log("StatusShow, props", props);
    getStatusesCode();
});
</script>

<template>
    <span class="badge text-xs" :class="'text-' + local_status.style">
        {{ local_status.name }}
        <a class="badge text-xs" href="#" @click.prevent="statusChange">
            <div v-if="loading || ! local_status" class="spinner-border spinner-border-sm" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <span v-else>
                <i v-if="can( ( modeltype === '' ? 'statuses' : modeltype + '-status' ) + '-change') && canLinkAPI" class="fa fa-edit mr-2 text text-xs text-muted font-weight-light"></i>
            </span>
        </a>
    </span>
</template>

<style>
</style>
