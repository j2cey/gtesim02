<script setup>
import { ref, onMounted, computed } from 'vue';
import AppNavbar from './components/AppNavbar.vue';
import SidebarLeft from './components/SidebarLeft.vue';
import SidebarRight from './components/SidebarRight.vue';
import AppFooter from './components/AppFooter.vue';
import { useAuthUserStore } from "./stores/AuthUserStore.js";
import { useSettingStore } from './stores/SettingStore';
import { useRoute } from "vue-router";

// TODO: add page loader -> https://blog.alexseifert.com/2023/05/10/vue-js-route-level-code-splitting-with-a-page-loader/

const route = useRoute();

const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();

const currentThemeMode = computed(() => {
    return settingStore.theme === 'dark' ? 'dark-mode' : '';
});
</script>

<template>
    <div v-if="authUserStore.user.name !== ''" class="wrapper" :class="currentThemeMode" id="app">
        <AppNavbar />
        <SidebarLeft />
        <div class="content-wrapper">
            <router-view :key="route.fullPath"></router-view>
        </div>
        <SidebarRight />
        <AppFooter />
    </div>
    <div v-else class="login-page" :class="currentThemeMode">
        <router-view :key="route.fullPath"></router-view>
    </div>
</template>

<style scoped>

</style>
