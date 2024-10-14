<script setup>
import { useSettingStore } from '../stores/SettingStore';

const settingStore = useSettingStore();
</script>

<template>
    <footer class="main-footer">

        <div class="float-right d-none d-sm-inline">
            v2.0
        </div>

        <strong>Copyright &copy; {{ new Date().getFullYear() }} <a href="#">{{ settingStore.settings?.app.name }}</a>.</strong> All rights reserved.
    </footer>
</template>

<style scoped>

</style>
