<script setup>
import {onMounted, onUnmounted} from 'vue';
import { useAuthUserStore } from '../stores/AuthUserStore';
import { useRouter, useRoute } from 'vue-router';
import { useSettingStore } from '../stores/SettingStore';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import imgUrl from '../../assets/img/app_logo.png'


const { can, cannot } = useAbility();

const route = useRoute();
const router = useRouter();
const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();

const logout = () => {
    axios.post('/logout')
        .then((response) => {
            // window.location.href = '/login';
            authUserStore.user.name = '';
            router.push('/login');

        });
};

const goToProfile = () => {
    router.push('/profile');
}

onMounted( () => {
    $('[data-widget = "treeview"]').Treeview('init');
    // $('#mainSidebar').Treeview('init');

    console.log('Main sidebar mounted.');
})

onUnmounted(() => {
    $(document).on('click','[data-widget="treeview"] .nav-link', function (e) {
        e.stopImmediatePropagation();
    });

    console.log('Treeview stopped.');
})
</script>

<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <a href="/" class="brand-link">
            <img :src="imgUrl" alt="Admin-IT Logo" class="brand-image img-rounded elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">{{ settingStore.settings.app.name }}</span>
        </a>

        <div class="sidebar">

            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img :src="authUserStore.user.avatar" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" @click.prevent="goToProfile" class="d-block">{{ authUserStore.user.name }}</a>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <router-link to="/dashboard" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </router-link>
                    </li>

                    <li v-if="can('clientesim-list')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>
                                Clients E-SIM
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <router-link to="/clientesims" active-class="active" class="nav-link text text-xs">
                                    <i class="fas fa-list nav-icon text-success"></i>
                                    <p>Liste</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/clientesims/create" active-class="active" :key="route.fullPath" class="nav-link text text-xs">
                                    <i class="fas fa-plus nav-icon text-danger"></i>
                                    <p>Nouveau</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <router-link to="/profile" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Profile
                            </p>
                        </router-link>
                    </li>

                    <li v-if="can('manage-all')" class="nav-header">ADMINISTRATION</li>

                    <li v-if="can('esim-list')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-mobile-alt"></i>
                            <p>
                                Profiles E-SIM
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <router-link to="/esims" active-class="active" class="nav-link text text-xs">
                                    <i class="fas fa-list nav-icon text-success"></i>
                                    <p>Liste</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/esims/create" active-class="active" class="nav-link text text-xs">
                                    <i class="fas fa-plus nav-icon text-danger"></i>
                                    <p>Nouveau</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>
                    <li v-if="can('manage-all')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Parameters
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li v-if="can('status-list')" class="nav-item">
                                <router-link to="/statuses" active-class="active" class="nav-link text text-xs">
                                    <i class="fas fa-flag nav-icon text-primary"></i>
                                    <p>Statuts</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <router-link v-if="can('user-list')" to="/users" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Users
                            </p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <router-link v-if="can('role-list')" to="/roles" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-id-badge"></i>
                            <p>
                                Roles
                            </p>
                        </router-link>
                    </li>

                    <li v-if="can('setting-list')" class="nav-item">
                        <router-link to="/settings" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Settings
                            </p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <form class="nav-link">
                            <a href="#" @click.prevent="logout" class="text text-danger">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </form>
                    </li>
                </ul>
            </nav>

        </div>

    </aside>
</template>

<style scoped>

</style>
