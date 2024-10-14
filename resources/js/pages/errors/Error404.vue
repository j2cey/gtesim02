<script setup>
import {computed, onMounted, ref, watch} from "vue";
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const initComponent = () => {
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;
};

const currentPath = ref('/');
const lastPath = ref('/');
const prevRoutePath = computed(() => {
    return lastPath;
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
                    <h1 class="m-0 text-danger"></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">Page Introuvable</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <div class="error-page">
                <h2 class="headline text-info"> 404</h2>
                <div class="error-content">
                    <h3><i class="fa fa-warning text-yellow"></i> Oops! Page introuvable.</h3>
                    <p>
                        <span class="text font-weight-light">Nous n'avons pas pu trouver la page que vous recherchiez.
                        En attendant, vous pouvez retourner vers la</span> <router-link :to="prevRoutePath">Page PRÉCÉDENTE</router-link> <span class="text font-weight-light">ou essayez d'utiliser le formulaire de recherche.</span>
                    </p>
                    <form class='search-form'>
                        <div class='input-group'>
                            <input type="text" name="search" class='form-control' placeholder="Search"/>
                            <div class="input-group-btn">
                                <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div><!-- /.input-group -->
                    </form>
                </div><!-- /.error-content -->
            </div><!-- /.error-page -->

        </div>
    </div>
</template>

<style scoped>

</style>
