<script setup>
import { useEsimStore } from "../../stores/EsimStore.js";
import { formatDate } from '../../services/helper.js'
import {computed} from "vue";

const esimStore = useEsimStore();

const emit = defineEmits(['pickupCanceled','pickupSaved']);
const pickupCanceled = () => {
    esimStore.pickupEsimRelease();
    emit('pickupCanceled');
}

const arisStatus = computed(() => {
    return esimStore.esimpicked?.latestarisstatus ? esimStore.esimpicked?.latestarisstatus?.formatted_status : 'NaN';
});

</script>

<template>
    <div class="modal fade" id="pickupEsimModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span class="text text-sm">Selection de la Nouvelle ESIM</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th class="text text-sm text-capitalize">imsi</th>
                            <th class="text text-sm text-capitalize">iccid</th>
                            <th class="text text-sm text-capitalize">Etat</th>
                            <th class="text text-xs text-capitalize">Statut ARIS</th>
                            <th class="text text-xs text-capitalize">Date ARIS</th>
                        </tr>
                        </thead>
                        <tbody v-if="esimStore.esimpicked">
                        <tr>
                            <td class="text text-xs" ><small>{{ esimStore.esimpicked?.imsi }}</small></td>
                            <td class="text text-xs" ><small>{{ esimStore.esimpicked?.iccid }}</small></td>
                            <td class="text text-xs" ><small>{{ esimStore.esimpicked?.lateststate?.prevesimstate ? esimStore.esimpicked?.lateststate?.prevesimstate?.statutesim?.libelle : esimStore.esimpicked?.statutesim?.libelle }}</small></td>
                            <td class="text text-xs text-bold" :class="'text-' + (arisStatus === 'NaN' ? 'default' : (arisStatus === 'Libre' ? 'success' : 'danger')) " >{{ arisStatus }}</td>
                            <td class="text text-xs" style="width: 100px"><small>{{ esimStore.esimpicked?.latestarisstatus ? formatDate(esimStore.esimpicked?.latestarisstatus?.status_change_date) : 'NaN' }}</small></td>
                        </tr>
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="4" class="text-center">
                                <span v-if="esimStore.loadingesim" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>{{ esimStore.loadingesim ? ' Chargement en cours...' : 'Aucun résultat trouvé...' }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p><span v-show="esimStore.esimpicked" class="text text-xs text-danger font-weight-bold">Valider Cette ESIM ?</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" @click.prevent="pickupCanceled">Annuler</button>
                    <button @click.prevent="esimStore.pickupEsim()" type="button" class="btn btn-xs btn-warning" :disabled="esimStore.loadingesim">
                        <span v-if="esimStore.loadingesim" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Re-Selectionner
                    </button>
                    <button @click.prevent="emit('pickupSaved')" type="button" class="btn btn-xs btn-success" :disabled="esimStore.loadingesim">
                        <span v-if="esimStore.loadingesim" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Valider
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
