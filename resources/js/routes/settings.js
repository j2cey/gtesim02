import SettingList from "../pages/settings/SettingList.vue";
import SettingForm from "../pages/settings/SettingForm.vue";

const settingsRoutes = [
    {
        path: '/settings',
        name: 'settings',
        component: SettingList,
    },
    {
        path: '/settings/:id/edit',
        name: 'settings.edit',
        component: SettingForm,
    }
];

export default settingsRoutes;
