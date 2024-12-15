<script setup>

import { reactive, onMounted, ref, watch, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import ArisStatusList from "../arisstatuses/ArisStatusList.vue";

const router = useRouter();
const route = useRoute();

const loading = ref(false);

const currentPath = ref('/');
const lastPath = ref('/');
const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

watch(route, () => {
    if (route.fullPath !== currentPath.value) {
        initComponent();
    }
});

const initComponent = () => {
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;
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
                        <span>Liste des Statuts ARIS</span>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span>E-Mails</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <ArisStatusList
            ></ArisStatusList>
        </div>
    </div>

</template>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>
