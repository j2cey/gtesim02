import HowtoList from "../pages/howtos/HowtoList.vue";
import HowtoForm from "../pages/howtos/HowtoForm.vue";
import HowtoHtmlRead from "../pages/howtos/HowtoHtmlRead.vue";
import HowtoHtmlEdit from "../pages/howtos/HowtoHtmlEdit.vue";

import HowtoStepAll from "../pages/howtosteps/HowtoStepAll.vue";
import HowtoStepForm from "../pages/howtosteps/HowtoStepForm.vue";

import HowtoThreadList from "../pages/howtothreads/HowtoThreadList.vue";
import HowtoThreadForm from "../pages/howtothreads/HowtoThreadForm.vue";
import HowtoThreadRead from "../pages/howtothreads/HowtoThreadRead.vue";

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
    },
    {
        path: '/howtos/:id/htmlread',
        name: 'howtos.htmlread',
        component: HowtoHtmlRead,
    },
    {
        path: '/howtos/:id/htmledit',
        name: 'howtos.htmledit',
        component: HowtoHtmlEdit,
    }
];

export const howtostepsRoutes = [
    {
        path: '/howtosteps',
        name: 'howtosteps.index',
        component: HowtoStepAll,
    },
    {
        path: '/howtosteps/:threadid/create',
        name: 'howtosteps.create',
        component: HowtoStepForm,
    },
    {
        path: '/howtosteps/:id/edit',
        name: 'howtosteps.edit',
        component: HowtoStepForm,
    },
    {
        path: '/howtosteps/:id/show',
        name: 'howtosteps.show',
        component: HowtoStepForm,
    }
];

export const howtothreadsRoutes = [
    {
        path: '/howtothreads',
        name: 'howtothreads.index',
        component: HowtoThreadList,
    },
    {
        path: '/howtothreads/create',
        name: 'howtothreads.create',
        component: HowtoThreadForm,
    },
    {
        path: '/howtothreads/:id/edit',
        name: 'howtothreads.edit',
        component: HowtoThreadForm,
    },
    {
        path: '/howtothreads/:id/show',
        name: 'howtothreads.show',
        component: HowtoThreadForm,
    },
    {
        path: '/howtothreads/:id/read',
        name: 'howtothreads.read',
        component: HowtoThreadRead,
    }
];

export default { howtosRoutes, howtostepsRoutes, howtothreadsRoutes };
