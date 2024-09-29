<script setup>
import { onMounted, ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../helper.js'

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
            <router-link v-if="can('clientesim-list')" :to="`/clientesims/${clientesim.uuid}/show`">
                {{ clientesim.nom_raison_sociale }}
            </router-link>
            <span v-else>{{ clientesim.nom_raison_sociale }}</span>
        </td>
        <td class="text text-xs" >{{ clientesim.prenom }}</td>
        <td class="text text-xs" >{{ clientesim.phone_number_list }}</td>
        <td class="text text-xs" >{{ clientesim.email_address_list }}</td>
        <td class="text text-xs" v-if="can('clientesim-creator-list')" >{{ clientesim.creator?.name }}</td>
        <td class="text text-xs" ><small>{{ formatDate(clientesim.created_at) }}</small></td>
        <td class="text text-xs" ><small>{{ formatDate(clientesim.updated_at) }}</small></td>
        <td>
            <router-link v-if="can('clientesim-update')" :to="`/clientesims/${clientesim.uuid}/edit`">
                <i class="fa fa-edit mr-2 text text-xs"></i>
            </router-link>
            <router-link v-else-if="can('clientesim-list')" :to="`/clientesims/${clientesim.uuid}/show`">
                <i class="fa fa-eye mr-2 text text-xs text-success"></i>
            </router-link>
            <a class="text text-xs" v-if="can('clientesim-delete')" href="#" @click.prevent="$emit('confirmClientEsimDeletion', clientesim)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
