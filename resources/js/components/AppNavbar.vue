<script setup>
import {onMounted, ref} from 'vue';
import { useSettingStore } from '../stores/SettingStore';
import { useAuthUserStore } from "../stores/AuthUserStore.js";
import { useAbility } from "@casl/vue";

const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();
const availableLanguages = ref([{name: 'Fr', value: "fr"}, {name: 'En', value: "en"}]);
const { can, cannot } = useAbility();

const setLanguage = (event) => {
    console.log('setLocal, event.target.value:', event.target.value)
    settingStore.setLocal(event.target.value.toLowerCase());
};

const saveToggleMenu = () => {
    const toggleMenuIcon = document.getElementById('toggleMenuIcon');
    const body = document.querySelector('body');

    toggleMenuIcon.addEventListener('click', () => {
        if (body.classList.contains('sidebar-collapse')) {
            localStorage.setItem('sidebarState', 'expanded');
        } else {
            localStorage.setItem('sidebarState', 'collapsed');
        }
    });

    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        body.classList.add('sidebar-collapse');
    }
}

onMounted(() => {
    saveToggleMenu();
});
</script>

<template>
    <nav class="main-header navbar navbar-expand" :class="settingStore.theme === 'dark' ? 'navbar-dark': 'navbar-light'">

        <ul class="navbar-nav">
            <li class="nav-item" id="toggleMenuIcon">
                <a href="#" @click.prevent="saveToggleMenu" class="nav-link" data-widget="pushmenu" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <router-link to="/" active-class="active" class="nav-link">
                    <span>
                        Accueil
                    </span>
                </router-link>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <router-link to="/contact" active-class="active" class="nav-link">
                    <span>
                        Contact
                    </span>
                </router-link>
            </li>
        </ul>
        <li class="nav-item d-none d-sm-inline-block">
            <a @click.prevent="settingStore.changeTheme" href="#" class="nav-link">
                <i class="far" :class="settingStore.theme === 'dark' ? 'fa-moon' : 'fa-sun'"></i>
            </a>
        </li>
        <li v-if="authUserStore.hasRole('admin')" class="nav-item d-none d-sm-inline-block">
            <!-- Locale Selector -->
            <select @change="setLanguage" style="height: 2rem; outline: 2px solid transparent;" class="px-1 rounded border-0">
                <option v-for="language in availableLanguages" :selected="(settingStore.currlang === language.value)">{{ language.name }}</option>
            </select>
        </li>

        <ul class="navbar-nav ml-auto">

            <li v-if="authUserStore.hasRole('admin')" class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="fas fa-search"></i>
                </a>
                <div class="navbar-search-block">
                    <form class="form-inline">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>

            <li v-if="authUserStore.hasRole('admin')" class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-comments"></i>
                    <span class="badge badge-danger navbar-badge">3</span>
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
            </li>

            <li v-if="authUserStore.hasRole('admin')" class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 8 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 3 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
            <li v-if="authUserStore.hasRole('admin')" class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-book-reader"></i>
                    <span class="badge badge-warning navbar-badge"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">Rubrique d'Aide</span>
                    <div class="dropdown-divider"></div>
                    <router-link class="text text-xs dropdown-item" v-if="can('howtothreads-list')" :to="`/howtothreads`">
                        <i class="fa fa-lightbulb mr-2 text-xs"></i>&nbsp;Comment Faire
                        <span class="float-right text-muted text-sm"></span>
                    </router-link>
                    <div class="dropdown-divider"></div>
                    <router-link class="text text-xs dropdown-item" v-if="can('howtos-list')" :to="`/howtos`">
                        <i class="fa fa-book mr-2 text-xs"></i>&nbsp;Rubriques de Tutos
                        <span class="float-right text-muted text-sm"></span>
                    </router-link>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer"></a>
                </div>
            </li>

            <li v-if="authUserStore.hasRole('adminxxx')" class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-book-reader text text-warning"></i>
                </a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>

</style>
