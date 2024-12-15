import ArisStatusAll from "../pages/arisstatuses/ArisStatusAll.vue";
import ArisStatusForm from "../pages/arisstatuses/ArisStatusForm.vue";

export const arisstatusesRoutes = [
    {
        path: '/arisstatuses/create',
        name: 'arisstatuses.create',
        component: ArisStatusForm,
    },
    {
        path: '/arisstatuses',
        name: 'arisstatuses.index',
        component: ArisStatusAll,
    },
    {
        path: '/arisstatuses/:id/edit',
        name: 'arisstatuses.edit',
        component: ArisStatusForm,
    },
    {
        path: '/arisstatuses/:id/show',
        name: 'arisstatuses.show',
        component: ArisStatusForm,
    }
];

export default { arisstatusesRoutes };
