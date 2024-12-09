<script setup>

import {computed, onMounted, ref, watch} from "vue";
import axios from "axios";
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const howto = ref({});
const howtoid = ref(null);
const loading = ref(false);
const getHowto = () => {
    axios.get(`/api/howtos/${howtoid.value}/edit`)
        .then((response) => {
            console.log("getHowto, response: ", response)
            howto.value = response.data;
        }).then(() => {

        }

    )
};

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
                        Rendu HTML, Rubrique: <span class="text text-sm text-bold">{{ howto.title }}</span>
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
                            <span>Rendu HTML</span>
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
                            <div v-html="howto.htmlbody"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
