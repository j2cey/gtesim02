<script setup>
import {computed, onMounted, ref} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../services/helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    esimstate: Object,
    selectedEsimStates: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['confirmEsimStateDeletion']);

const toggleSelection = (event) => {
    emit('toggleSelection', props.esimstate, event.target.id);
}

const esimStateChecked = computed(() => {
    return props.selectedEsimStates.includes(props.esimstate.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="esimStateChecked" @change="toggleSelection" :key="esimstate.id" :id="'state_' + esimstate.id" :ref="'state_' + esimstate.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >{{ esimstate.statutesim?.libelle }}</td>
        <td class="text text-xs" >{{ esimstate.user?.name }}</td>
        <td class="text text-xs" >{{ esimstate.details }}</td>
        <td class="text text-xs" ><small>{{ formatDate(esimstate.created_at) }}</small></td>
        <td>
            <a class="text text-xs" v-if="can('esimstates-delete')" href="#" @click.prevent="$emit('confirmEsimStateDeletion', esimstate)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>

<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
</style>
