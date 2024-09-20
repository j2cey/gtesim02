import Dashboard from "./components/Dashboard.vue";
import UserList from "./pages/users/UserList.vue";
import RoleList from "./pages/roles/RoleList.vue";
import RoleForm from "./pages/roles/RoleForm.vue";
import SettingList from "./pages/settings/SettingList.vue";
import SettingForm from "./pages/settings/SettingForm.vue";
import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import Login from './pages/auth/Login.vue';

import EsimList from "./pages/esims/EsimList.vue";
import EsimForm from "./pages/esims/EsimForm.vue";

export default [
    {
        path: '/',
        name: 'home',
        redirect: '/dashboard',
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
    },
    {
        path: '/users',
        name: 'users',
        component: UserList,
    },
    {
        path: '/roles',
        name: 'roles',
        component: RoleList,
    },
    {
        path: '/roles/create',
        name: 'roles.create',
        component: RoleForm,
    },
    {
        path: '/roles/:id/edit',
        name: 'roles.edit',
        component: RoleForm,
    },
    {
        path: '/settings',
        name: 'settings',
        component: SettingList,
    },
    {
        path: '/settings/:id/edit',
        name: 'settings.edit',
        component: SettingForm,
    },
    {
        path: '/profile',
        name: 'profile',
        component: UpdateProfile,
    },
    {
        path: '/esims',
        name: 'esims',
        component: EsimList,
    },
    {
        path: '/esims/create',
        name: 'esims.create',
        component: EsimForm,
    },
    {
        path: '/esims/:id/edit',
        name: 'esims.edit',
        component: EsimForm,
    },
    {
        path: '/users',
        name: 'users',
        component: UserList,
    },
]
