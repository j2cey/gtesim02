<script setup>
import { useEsimStore } from "../../stores/EsimStore.js";
import { formatDate } from '../../services/helper.js'

const esimStore = useEsimStore();

const emit = defineEmits(['pickupCanceled','pickupSaved']);
const pickupCanceled = () => {
    esimStore.pickupEsimRelease();
    emit('pickupCanceled');
}

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
                            <th class="text text-sm text-capitalize">Statut ARIS</th>
                            <th class="text text-sm text-capitalize">Date Statut ARIS</th>
                        </tr>
                        </thead>
                        <tbody v-if="esimStore.esimpicked">
                        <tr>
                            <td class="text text-xs" >{{ esimStore.esimpicked?.imsi }}</td>
                            <td class="text text-xs" >{{ esimStore.esimpicked?.iccid }}</td>
                            <td class="text text-xs" >{{ esimStore.esimpicked?.lateststate?.prevesimstate ? esimStore.esimpicked?.lateststate?.prevesimstate?.statutesim?.libelle : esimStore.esimpicked?.statutesim?.libelle }}</td>
                            <td class="text text-xs" >{{ esimStore.esimpicked?.latestarisstatus ? esimStore.esimpicked?.latestarisstatus?.formatted_status : 'NaN' }}</td>
                            <td class="text text-xs" >{{ esimStore.esimpicked?.latestarisstatus ? formatDate(esimStore.esimpicked?.latestarisstatus?.status_change_date) : 'NaN' }}</td>
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
