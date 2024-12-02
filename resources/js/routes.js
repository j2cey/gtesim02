import Contact from "./pages/app/Contact.vue";

import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import Login from './pages/auth/Login.vue';

import Error404 from "./pages/errors/Error404.vue";

import dashboardRoutes from "./routes/dashboard.js";
import { ldapusersRoutes, usersRoutes, rolesRoutes } from "./routes/auth.js";
import esimsRoutes from "./routes/esims.js";
import clientesimsRoutes from "./routes/clientesims.js";
import { phonenumsRoutes, emailaddressesRoutes } from "./routes/persons.js";
import employeesRoutes from "./routes/employees.js";
import settingsRoutes from "./routes/settings.js";
import statusesRoutes from "./routes/statuses.js";
import { howtosRoutes } from "./routes/howtos.js";

export default [
    {
        path: '/',
        name: 'home',
        redirect: '/clientesims',
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
        path: '/profile',
        name: 'profile',
        component: UpdateProfile,
    },
    ...dashboardRoutes,
    ...ldapusersRoutes,
    ...usersRoutes,
    ...rolesRoutes,
    ...esimsRoutes,
    ...clientesimsRoutes,
    ...phonenumsRoutes,
    ...emailaddressesRoutes,
    ...employeesRoutes,
    ...settingsRoutes,
    ...statusesRoutes,
    ...howtosRoutes,
    {
        path: '/:notFound',
        name: 'error.404',
        component: Error404,
    },
]
