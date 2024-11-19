import moment from "moment";
import axios from "axios";

export function formatDate(value) {
    if (value) {
        return moment(String(value)).format('DD-MM-YYYY h:m:s');
    }
}

export function formatDateExt1(value) {
    console.log("formatDateExt1, value: ", value);
    if (value) {
        return moment(String(value).replace(" GMT+0100 (GMT+01:00)", '')).format('DD-MM-YYYY h:m:s');
    }
}

// TODO: Formalize logging vue messages
const logRaw = (msg, logtype) => {
    axios.post(`/api/logs/`, {
        msg: msg,
        logtype: logtype,
    })
        .then(response => {
            console.log('logRaw, logtype: ', logtype, 'msg: ', msg);
        });
}
export function logInfo(msg) {
    if (msg) {
        logRaw(msg, 'info');
    }
}
export function logWarn(value) {
    if (msg) {
        logRaw(msg, 'warning');
    }
}
export function logError(value) {
    if (msg) {
        logRaw(msg, 'error');
    }
}
