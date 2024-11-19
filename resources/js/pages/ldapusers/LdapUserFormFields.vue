<script setup>
import Multiselect from "vue-multiselect";
import {onMounted, reactive, ref, watch} from "vue";
import axios from "axios";
import {Form} from "vee-validate";
import Swal from "sweetalert2";

const props = defineProps({
    formMode: String,
    ldapuserid: { type: String, default: null},
    ldapuser: { type: Object, default: null},
    showsave: { type: Boolean, default: false},
    showback: { type: Boolean, default: false},
    userid: { type: String, default: null},
    prevRoutePath: { type: String, default: '/'},
});

const emit = defineEmits(['formModeChanged']);



const local_user = ref(null);






onMounted(() => {
    getDepartements();
    getFonctions();

    initComponent();
});
</script>

<template>

</template>

<style scoped>

</style>
