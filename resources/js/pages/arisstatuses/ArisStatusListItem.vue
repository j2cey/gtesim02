<script setup>
import { computed, onMounted, ref } from 'vue';
import { useToastr } from '../../toastr.js';
import { useAbility } from "@casl/vue";
import { formatDate } from '../../services/helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    arisstatus: Object,
    modeltype: { type: String, default: ''},
    modelid: { type: String, default: ''},
    selecteds: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['confirmArisstatusDeletion']);

const toggleSelection = (event) => {
    emit('toggleSelection', props.arisstatus, event.target.id);
}

const arisstatusChecked = computed(() => {
    return props.selecteds.includes(props.arisstatus.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="arisstatusChecked" @change="toggleSelection" :key="arisstatus.id" :id="'state_' + arisstatus.id" :ref="'state_' + arisstatus.id" /></td>
        <td class="text text-xs" >{{ arisstatus.id }}</td>
        <td class="text text-xs text-bold text-success">
            <router-link v-if="can( 'arisstatuses-show')" :to="`/arisstatuses/${arisstatus.uuid}/show`">
                {{ arisstatus.formatted_status }}
            </router-link>
            <span v-else>{{ arisstatus.formatted_status }}</span>
        </td>
        <td class="text text-xs" ><small><span class="text font-weight-bold">{{ formatDate(arisstatus.status_change_date) }}</span></small></td>
        <td class="text text-xs" ><small><span class="text font-weight-lighter">{{ formatDate(arisstatus.requested_at) }}</span></small></td>
        <td class="text text-xs" ><small><span class="text font-weight-lighter">{{ formatDate(arisstatus.responded_at) }}</span></small></td>
        <td class="text text-xs" >{{ arisstatus.response_message }}</td>
        <td class="text text-xs" >
            <router-link v-if="can( 'esims-show')" :to="`/esims/${arisstatus.esim.uuid}/show`">
                {{ arisstatus.esim?.iccid }}
            </router-link>
            <span v-else>{{ arisstatus.esim?.iccid }}</span>
        </td>
        <td class="text text-xs" ><small><span class="text font-weight-lighter">{{ formatDate(arisstatus.created_at) }}</span></small></td>
        <td class="text text-xs font-weight-lighter" ><small><span class="text font-weight-lighter">{{ formatDate(arisstatus.updated_at) }}</span></small></td>
        <td>
            <router-link v-if="can( 'arisstatuses-update')" :to="{
                name: 'arisstatuses.edit',
                params: {
                    id: arisstatus.id,
                    modeltype: modeltype,
                    modelid: modelid
                }
            }">
                <i class="fa fa-edit mr-2 text text-xs text-warning font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('arisstatuses-arisstatus')" href="#" @click.prevent="$emit('confirmArisstatusDeletion', arisstatus)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>

<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
</style>
