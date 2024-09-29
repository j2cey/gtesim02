import Dashboard from "./components/Dashboard.vue";
import Contact from "./pages/app/Contact.vue";
import UserList from "./pages/users/UserList.vue";
import RoleList from "./pages/roles/RoleList.vue";
import RoleForm from "./pages/roles/RoleForm.vue";
import SettingList from "./pages/settings/SettingList.vue";
import SettingForm from "./pages/settings/SettingForm.vue";
import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import Login from './pages/auth/Login.vue';

import StatusList from './pages/statuses/StatusList.vue';
import StatusForm from "./pages/statuses/StatusForm.vue";

import EsimList from "./pages/esims/EsimList.vue";
import EsimForm from "./pages/esims/EsimForm.vue";

import ClientEsimList from "./pages/clientesims/ClientEsimList.vue";
import ClientEsimForm from "./pages/clientesims/ClientEsimForm.vue";

import PhoneNumForm from "./pages/phonenums/PhoneNumForm.vue";
import PhoneNumPdf from "./pages/phonenums/PhoneNumPdf.vue";
import EmailAddressForm from "./pages/emailaddresses/EmailAddressForm.vue";

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
        path: '/contact',
        name: 'contact',
        component: Contact,
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
        path: '/esims/:id/show',
        name: 'esims.show',
        component: EsimForm,
    },
    {
        path: '/clientesims',
        name: 'clientesims',
        component: ClientEsimList,
    },
    {
        path: '/clientesims/create',
        name: 'clientesims.create',
        component: ClientEsimForm,
    },
    {
        path: '/clientesims/:id/edit',
        name: 'clientesims.edit',
        component: ClientEsimForm,
    },
    {
        path: '/clientesims/:id/show',
        name: 'clientesims.show',
        component: ClientEsimForm,
    },
    {
        path: '/phonenums/:modeltype/:modelid?/create',
        name: 'phonenums.create',
        component: PhoneNumForm,
    },
    {
        path: '/phonenums/:id/:modeltype/:modelid/edit',
        name: 'phonenums.edit',
        component: PhoneNumForm,
    },
    {
        path: '/phonenums/:id/previewpdf',
        name: 'phonenums.previewpdf',
        component: PhoneNumPdf,
    },
    {
        path: '/emailaddresses/:modeltype/:modelid?/create',
        name: 'emailaddresses.create',
        component: EmailAddressForm,
    },
    {
        path: '/emailaddresses/:id/:modeltype/:modelid/edit',
        name: 'emailaddresses.edit',
        component: EmailAddressForm,
    },
    {
        path: '/users',
        name: 'users',
        component: UserList,
    },
    {
        path: '/statuses',
        name: 'statuses',
        component: StatusList,
    },
    {
        path: '/statuses/create',
        name: 'statuses.create',
        component: StatusForm,
    },
    {
        path: '/statuses/:id/edit',
        name: 'statuses.edit',
        component: StatusForm,
    },
]
