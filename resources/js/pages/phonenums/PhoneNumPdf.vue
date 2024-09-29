<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';

// TODO: Manage PhoneNum Posi
// TODO: Add a Cancel button to reset the form

const router = useRouter();
const route = useRoute();

const loading = ref(false);
const phonenum = ref({});
const phonenumId = ref(null);
const creator = ref({});

const lastPath = ref('/');

const modeltype = ref('');
const modelid = ref('');

const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

const showPreviewPDF = (id) => {
    window.location = '/clientesims.previewpdf/' + id
};

onMounted(() => {
    lastPath.value = router.options.history.state.back;
    phonenumId.value = route.params.id;
    showPreviewPDF(route.params.id);
});
</script>

<template>

</template>

<style>

</style>
