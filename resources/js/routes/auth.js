import LdapUserList from "../pages/ldapusers/LdapUserList.vue";
import LdapUserForm from "../pages/ldapusers/LdapUserForm.vue";

import UserList from "../pages/users/UserList.vue";
import UserForm from "../pages/users/UserForm.vue";

import RoleList from "../pages/roles/RoleList.vue";
import RoleForm from "../pages/roles/RoleForm.vue";

export const ldapusersRoutes = [
    {
        path: '/ldapusers/:userid?/create',
        name: 'ldapusers.create',
        component: LdapUserForm,
    },
    {
        path: '/ldapusers',
        name: 'ldapusers',
        component: LdapUserList,
    },
    {
        path: '/ldapusers/:id/edit',
        name: 'ldapusers.edit',
        component: LdapUserForm,
    },
    {
        path: '/ldapusers/:id/show',
        name: 'ldapusers.show',
        component: LdapUserForm,
    },
    {
        path: '/ldapusers/:id/integrate',
        name: 'ldapusers.integrate',
        component: LdapUserForm,
    }
];
export const usersRoutes = [
    {
        path: '/users/create',
        name: 'users.create',
        component: UserForm,
    },
    {
        path: '/users',
        name: 'users',
        component: UserList,
    },
    {
        path: '/users/:id/edit',
        name: 'users.edit',
        component: UserForm,
    },
    {
        path: '/users/:id/show',
        name: 'users.show',
        component: UserForm,
    }
];
export const rolesRoutes = [
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
        path: '/roles/:id/show',
        name: 'roles.show',
        component: RoleForm,
    }
];

export default { ldapusersRoutes, usersRoutes, rolesRoutes };
