import EsimList from "../pages/esims/EsimList.vue";
import EsimForm from "../pages/esims/EsimForm.vue";

const esimsRoutes = [
    {
        path: '/esims/:userid?',
        name: 'esims.index',
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
        path: '/esims/:userid/attributed',
        name: 'esims.attributed',
        component: EsimList,
    }
];

export default esimsRoutes;
