import ClientEsimList from "../pages/clientesims/ClientEsimList.vue";
import ClientEsimForm from "../pages/clientesims/ClientEsimForm.vue";

const clientesimsRoutes = [
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
    }
];

export default clientesimsRoutes;
