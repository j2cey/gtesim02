import axios from "axios";

export async function pickupEsim(loadingEsim, esimid = null) {
    loadingEsim.value = true
    await axios.get(`/api/esims/${esimid}/pickup`)
        .then((response) => {
            console.log('pickupesim, response: ', response);
            return response.data;
        }).catch((error) => {
            return null;
        });
}
