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
    esim: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['esimDeleted', 'confirmEsimDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.esim);
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="esim.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('esims-list')" :to="`/esims/${esim.uuid}/show`">
                {{ esim.imsi }}
            </router-link>
            <span v-else>{{ esim.imsi }}</span>
        </td>
        <td class="text text-xs" >{{ esim.iccid }}</td>
        <td class="text text-xs" >{{ esim.ac }}</td>
        <td class="text text-xs" >
            <span v-if="esim.statutesim" class="badge" :class="'text-' + esim.statutesim.style ">{{ esim.statutesim?.libelle }}</span>
        </td>
        <td class="text text-xs small"><small>{{ formatDate(esim.created_at) }}</small></td>
        <td class="text text-xs small"><small>{{ formatDate(esim.updated_at) }}</small></td>
        <td>
            <router-link v-if="can('esims-update')" :to="`/esims/${esim.uuid}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('esims-delete')" href="#" @click.prevent="$emit('confirmEsimDeletion', esim)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>
