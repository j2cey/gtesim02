import moment from "moment";
import axios from "axios";

export function formatDate(value) {
    if (value) {
        return moment(String(value)).format('DD-MM-YYYY h:m:s');
    }
}

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
