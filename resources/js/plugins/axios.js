import axios from 'axios';
import { useToastr } from '@/toastr';

// TODO: Remove console log from axios plugin

axios.interceptors.request.use(
    (request) => {
        // eslint-disable-next-line no-param-reassign
        request.config = {
            ...(request.config ?? {}), // preserve a given request.config object
            start: Date.now(),
        };

        return request;
    },
);

axios.interceptors.response.use(
    (response) => {
        const now = Date.now();
        console.info(`Api Call ${response.config.url} took ${now - response.config.config.start}ms`);
        return response;
    },
    (error) => {
        if (error.response.status === 400) {
            //  There was Some Problem, while processing your Request
            useToastr().error(error.response.data.message);
        } else if (error.response.status === 401) {
            // Unauthorized, You are not Allowed
            window.location.href="/";
        } else if (error.response.status === 403) {
            // Sorry, You are not allowed for This Action
            useToastr().error(error.response.data.message);
        } else if (error.response.status === 404) {
            // API Route is Missing or Undefined
            useToastr().error(error.response.data.message);
        } else if (error.response.status === 405) {
            // API Route Method Not Allowed
            useToastr().error(error.response.data.message);
        } else if (error.response.status === 500) {
            // Server Error, please try again later
            useToastr().error(error.response.data.message);
        }
        return Promise.reject(error);
    },
);

export default axios;
