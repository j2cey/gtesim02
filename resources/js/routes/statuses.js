import StatusList from '../pages/statuses/StatusList.vue';
import StatusForm from "../pages/statuses/StatusForm.vue";

const statusesRoutes = [
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
    }
];

export default statusesRoutes;
