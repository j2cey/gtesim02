import PhoneNumAll from "../pages/phonenums/PhoneNumAll.vue";
import PhoneNumForm from "../pages/phonenums/PhoneNumForm.vue";
import PhoneNumPdf from "../pages/phonenums/PhoneNumPdf.vue";

import EmailAddressAll from "../pages/emailaddresses/EmailAddressAll.vue";
import EmailAddressForm from "../pages/emailaddresses/EmailAddressForm.vue";

export const phonenumsRoutes = [
    {
        path: '/phonenums',
        name: 'phonenums.index',
        component: PhoneNumAll,
    },
    {
        path: '/phonenums/:modeltype?/:modelid?/create',
        name: 'phonenums.create',
        component: PhoneNumForm,
    },
    {
        path: '/phonenums/:id/:modeltype?/:modelid?/edit',
        name: 'phonenums.edit',
        component: PhoneNumForm,
    },
    {
        path: '/phonenums/:id/:modeltype?/:modelid?/show',
        name: 'phonenums.show',
        component: PhoneNumForm,
    },
    {
        path: '/phonenums/:id/previewpdf',
        name: 'phonenums.previewpdf',
        component: PhoneNumPdf,
    }
];
export const emailaddressesRoutes = [
    {
        path: '/emailaddresses',
        name: 'emailaddresses.index',
        component: EmailAddressAll,
    },
    {
        path: '/emailaddresses/:modeltype?/:modelid?/create',
        name: 'emailaddresses.create',
        component: EmailAddressForm,
    },
    {
        path: '/emailaddresses/:id/:modeltype?/:modelid?/edit',
        name: 'emailaddresses.edit',
        component: EmailAddressForm,
    },
    {
        path: '/emailaddresses/:id/:modeltype?/:modelid?/show',
        name: 'emailaddresses.show',
        component: EmailAddressForm,
    }
];

export default { phonenumsRoutes, emailaddressesRoutes };
