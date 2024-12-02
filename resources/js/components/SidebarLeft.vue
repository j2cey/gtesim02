<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { useAuthUserStore } from '../stores/AuthUserStore';
import { useRouter, useRoute } from 'vue-router';
import { useSettingStore } from '../stores/SettingStore';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import imgUrl from '../../assets/img/Logo_Fond_Bleu.png'

// TODO: Drop-Down Menu for connected user
// TODO: Build Sidebar with Accordion style

const { can, cannot, is } = useAbility();

const route = useRoute();
const router = useRouter();
const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();
const loadingLogout = ref(false);

const logout = () => {
    loadingLogout.value = true;
    axios.post('/logout')
        .then((response) => {
            console.log("logout, response: ", response);
            authUserStore.user.name = '';
            router.push('/login');
            //window.location.href = '/login';
        }).finally(() => {
            loadingLogout.value = false;
    });
};

const goToProfile = () => {
    router.push('/profile');
}

onMounted( () => {
    $('[data-widget = "treeview"]').Treeview('init');
    // $('#mainSidebar').Treeview('init');

    //console.log('Main sidebar mounted.');

    //console.log('Is admin: ', authUserStore.hasRole('admin'));
})

onUnmounted(() => {
    $(document).on('click','[data-widget="treeview"] .nav-link', function (e) {
        e.stopImmediatePropagation();
    });

    //console.log('Treeview stopped.');
})
</script>

<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <router-link to="/" class="brand-link">
            <img :src="imgUrl" alt="Admin-IT Logo" class="brand-image img-rounded elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">{{ settingStore.settings.app.name }}</span>
        </router-link>

        <div class="sidebar">

            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img :src="authUserStore.user.avatar" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" @click.prevent="authUserStore.hasRole('admin') ? goToProfile : '#'" class="d-block font-weight-light text-green">
                        {{ authUserStore.user.name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Brad Diesel
                                        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">Call me whenever you can...</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="https://adminlte.io/themes/v3/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        John Pierce
                                        <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">I got your message bro</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="https://adminlte.io/themes/v3/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Nora Silvester
                                        <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">The subject goes here</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                    </div>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <router-link to="/dashboard" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p class="text font-weight-lighter">
                                Dashboard
                            </p>
                        </router-link>
                    </li>

                    <li v-if="can('clientesims-list')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa fa-address-book font-weight-lighter"></i>
                            <p class="text font-weight-lighter">
                                Clients E-SIM
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li v-if="can('clientesims-list')" class="nav-item">
                                <router-link to="/clientesims" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-list nav-icon text-success text-xs"></i>
                                    <p>Liste</p>
                                </router-link>
                            </li>
                            <li v-if="can('clientesims-create')" class="nav-item">
                                <router-link to="/clientesims/create" active-class="active" :key="route.fullPath" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-plus nav-icon text-danger text-xs"></i>
                                    <p>Nouveau</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('phonenums-list') || can('emailaddresses-list')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa fa-universal-access"></i>
                            <p class="text font-weight-lighter">
                                Infos Personnes
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li v-if="can('phonenums-list')" class="nav-item">
                                <router-link to="/phonenums" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-phone nav-icon text-warning text-xs"></i>
                                    <p>Numeros Telephone</p>
                                </router-link>
                            </li>
                            <li v-if="can('emailaddresses-list')" class="nav-item">
                                <router-link to="/emailaddresses" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-mail-bulk nav-icon text-success text-xs"></i>
                                    <p>Adresses Mail</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('manage-all')" class="nav-header">ADMINISTRATION</li>

                    <li v-if="can('esims-list')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-mobile-alt"></i>
                            <p class="text font-weight-lighter">
                                Profiles E-SIM
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li v-if="can('esims-list')" class="nav-item">
                                <router-link to="/esims" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-list nav-icon text-success text-xs"></i>
                                    <p>Liste</p>
                                </router-link>
                            </li>
                            <li v-if="can('esims-create')" class="nav-item">
                                <router-link to="/esims/create" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-plus nav-icon text-danger text-xs"></i>
                                    <p>Nouveau</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('employes-list')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa fa-id-card"></i>
                            <p class="text font-weight-lighter">
                                Employés
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li v-if="can('employes-list')" class="nav-item">
                                <router-link to="/employees" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-list nav-icon text-success text-xs"></i>
                                    <p>Liste</p>
                                </router-link>
                            </li>
                            <li v-if="can('employes-create')" class="nav-item">
                                <router-link to="/employees/create" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-plus nav-icon text-danger text-xs"></i>
                                    <p>Nouveau</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('manage-all')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p class="text font-weight-lighter">
                                Parameters
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li v-if="can('statuses-list')" class="nav-item">
                                <router-link to="/statuses" active-class="active" class="nav-link text text-xs font-weight-lighter">
                                    <i class="fas fa-flag nav-icon text-primary text-xs"></i>
                                    <p>Statuts</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('users-list') || can('ldapusers-list') || can('roles-list')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa fa-key"></i>
                            <p class="text font-weight-lighter">
                                Authorization
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <router-link v-if="can('ldapusers-list')" to="/ldapusers" active-class="active" class="nav-link">
                                    <i class="nav-icon fa fa-university text-xs text-primary"></i>
                                    <p class="text text-xs font-weight-lighter">
                                        LDAP Users
                                    </p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link v-if="can('users-list')" to="/users" active-class="active" class="nav-link">
                                    <i class="nav-icon fas fa-id-badge text-xs text-warning"></i>
                                    <p class="text text-xs font-weight-lighter">
                                        Users
                                    </p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link v-if="can('roles-list')" to="/roles" active-class="active" class="nav-link">
                                    <i class="nav-icon fas fa-gavel text-danger"></i>
                                    <p class="text text-xs font-weight-lighter">
                                        Roles
                                    </p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('settings-list')" class="nav-item">
                        <router-link to="/settings" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p class="text font-weight-lighter">
                                Settings
                            </p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <form class="nav-link">
                            <a href="#" @click.prevent="logout" class="text text-danger">
                                <div v-if="loadingLogout" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span v-else><i class="nav-icon fa fa-power-off"></i></span>
                                <p class="text font-weight-light">
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
