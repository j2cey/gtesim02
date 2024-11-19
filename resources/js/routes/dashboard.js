import Dashboard from "../pages/dashboard/Dashboard.vue";
import DashboardAgence from "../pages/dashboard/DashboardAgence.vue";

const dashboardRoutes = [
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
    },
    {
        path: '/dashboard/:agenceid/agence',
        name: 'dashboard.agence',
        component: DashboardAgence,
    }
];

export default dashboardRoutes;
