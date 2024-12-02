import HowtoList from "../pages/howtos/HowtoList.vue";
import HowtoForm from "../pages/howtos/HowtoForm.vue";

export const howtosRoutes = [
    {
        path: '/howtos',
        name: 'howtos.index',
        component: HowtoList,
    },
    {
        path: '/howtos/create',
        name: 'howtos.create',
        component: HowtoForm,
    },
    {
        path: '/howtos/:id/edit',
        name: 'howtos.edit',
        component: HowtoForm,
    },
    {
        path: '/howtos/:id/show',
        name: 'howtos.show',
        component: HowtoForm,
    }
];

export default { howtosRoutes };
