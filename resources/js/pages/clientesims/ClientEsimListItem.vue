<script setup>
import { onMounted, ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../services/helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    clientesim: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['clientEsimDeleted', 'confirmClientEsimDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.clientesim);
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="clientesim.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('clientesims-show')" :to="`/clientesims/${clientesim.uuid}/show`">
                {{ clientesim.nom_raison_sociale }}
            </router-link>
            <span v-else>{{ clientesim.nom_raison_sociale }}</span>
        </td>
        <td class="text text-xs" >{{ clientesim.prenom }}</td>
        <td class="text text-xs" >{{ clientesim.phone_number_list }}</td>
        <td class="text text-xs" >{{ clientesim.email_address_list }}</td>
        <td class="text text-xs" v-if="can('clientesims-creator-list')" >{{ clientesim.creator?.name }}</td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(clientesim.created_at) }}</small></td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(clientesim.updated_at) }}</small></td>
        <td style="width: 90px">
            <router-link v-if="can('clientesims-update')" :to="`/clientesims/${clientesim.uuid}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('clientesims-delete')" href="#" @click.prevent="$emit('confirmClientEsimDeletion', clientesim)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>
