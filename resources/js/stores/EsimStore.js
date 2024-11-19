import axios from 'axios';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useEsimStore = defineStore('EsimStore', () => {
    const esimpicked = ref(null);
    const loadingesim = ref(false);

    const pickupEsimReset = () => {
        esimpicked.value = null;
    }
    const pickupEsim = () => {
        loadingesim.value = true;
        axios.get('/api/esims/' + ( esimpicked.value ?  esimpicked.value.uuid + '/' : esimpicked.value + '/') + 'pickup')
            .then((response) => {
                esimpicked.value = response.data;
                console.log('STORE EsimStore pickupEsim, response: ', response);
                console.log('STORE EsimStore esimpicked.value: ', esimpicked.value);
                loadingesim.value = false;
            }).catch((error) => {
                loadingesim.value = false;
            });
    };
    const pickupEsimRelease = () => {
        if (esimpicked.value) {
            loadingesim.value = true;
            axios.get('/api/esims/' + esimpicked.value.uuid + '/release')
                .then((response) => {
                    pickupEsimReset();
                    console.log('STORE EsimStore releaseEsim, response: ', response);
                    console.log('STORE EsimStore esimpicked.value: ', esimpicked.value);
                    loadingesim.value = false;
                }).catch((error) => {
                loadingesim.value = false;
            });
        }
    };

    return { esimpicked, loadingesim, pickupEsim, pickupEsimReset, pickupEsimRelease };
});
