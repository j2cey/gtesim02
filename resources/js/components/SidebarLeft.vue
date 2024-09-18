<script setup>
import {onMounted, onUnmounted} from 'vue';
import { useAuthUserStore } from '../stores/AuthUserStore';
import { useRouter } from 'vue-router';
import { useSettingStore } from '../stores/SettingStore';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import imgUrl from '../../assets/img/Logo_Fond_Bleu.png'




const { can, cannot } = useAbility();

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
    router.push('/admin/profile');
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

        <a href="index3.html" class="brand-link">
            <img :src="imgUrl" alt="Logo_Fond_Bleu" class="brand-image img-rounded elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">{{ settingStore.settings.app.name }}</span>
        </a>

        <div class="sidebar">

            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img :src="authUserStore.user.avatar" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="" @click.prevent="goToProfile" class="d-block">{{ authUserStore.user.name }}</a>
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


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>
                                Clients
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <router-link v-if="can('user-list')" to="/esims" class="nav-link">
                                        <p>Listes clients</p>
                                    </router-link>

                                </a>
                            </li>

                            <li class="nav-item">

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="pages/examples/login.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Report Types</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('profile-list')" class="nav-item">
                        <router-link to="/admin/profile" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-user" style="color: #0080FF;"></i>
                            <p>
                                Profile
                            </p>
                        </router-link>
                    </li>

                    <li v-if="can('manage-all')" class="nav-header">ADMINISTRATION</li>

                    <li v-if="can('manage-all')" class="nav-item">

                        <ul class="nav nav-treeview">
                            <li class="nav-item">

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="pages/examples/login.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Account Access</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pages/examples/register.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Servers</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="pages/examples/login-v2.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>File MimeTypes</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pages/examples/register-v2.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Access Protocoles</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pages/examples/forgot-password-v2.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>OS Server</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <router-link v-if="can('user-list')" to="/" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-sim-card"></i>
                            <p>
                                Profiles E-sims
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </router-link>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <router-link v-if="can('user-list')" to="/esims" class="nav-link">
                                        <p>E-sims </p>
                                    </router-link>


                                </a>
                            </li>

                            <li class="nav-item">

                                <ul class="nav nav-treeview">

                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <router-link v-if="can('role-list')" to="/admin/roles" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-id-badge"></i>
                            <p>
                                Utilisateurs
                                <i class="fas fa-angle-left right"></i>
                            </p>

                        </router-link>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                        <p>Listes </p>
                                </a>
                            </li>

                            <li class="nav-item">

                                <ul class="nav nav-treeview">

                                </ul>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">

                                    <p>profiles </p>
                                </a>
                            </li>

                            <li class="nav-item">

                                <ul class="nav nav-treeview">

                                </ul>
                            </li>
                        </ul>

                    </li>


                    <li v-if="can('setting-list')" class="nav-item">
                        <router-link to="/admin/settings" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                System
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </router-link>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">

                                    <p>Index </p>
                                </a>
                            </li>

                            <li class="nav-item">

                                <ul class="nav nav-treeview">

                                </ul>
                            </li>
                        </ul>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">

                                    <p>Users Online </p>
                                </a>
                            </li>

                            <li class="nav-item">

                                <ul class="nav nav-treeview">

                                </ul>
                            </li>
                        </ul>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">

                                    <p>Audit </p>
                                </a>
                            </li>

                            <li class="nav-item">

                                <ul class="nav nav-treeview">

                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <form class="nav-link">
                            <a href="#" @click.prevent="logout">
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
.nav-item {
    margin-bottom: 5px; /* Ajuste la valeur selon l'espacement désiré */

}

/* Ajouter de l'espace en haut de chaque section principale */
.nav-header {
    margin-top: 20px; /* Ajuste la valeur selon l'espacement désiré */
}





</style>
