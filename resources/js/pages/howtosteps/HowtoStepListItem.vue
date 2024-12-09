<script setup>
import {computed, onMounted, ref} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { useAbility } from "@casl/vue";
import { formatDate } from '../../services/helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    howtostep: Object,
    selecteds: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['confirmHowtostepDeletion']);

const toggleSelection = (event) => {
    emit('toggleSelection', props.howtostep, event.target.id);
}

const howtostepChecked = computed(() => {
    return props.selecteds.includes(props.howtostep.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="howtostepChecked" @change="toggleSelection" :key="howtostep.id" :id="'state_' + howtostep.id" :ref="'state_' + howtostep.id" /></td>
        <td class="text text-xs font-weight-lighter" >{{ howtostep.posi }}</td>
        <td class="text text-xs text-bold text-success">
            <router-link v-if="can( 'howtosteps-show')" :to="`/howtosteps/${howtostep.uuid}/show`">
                {{ howtostep.title }}
            </router-link>
            <span v-else>{{ howtostep.title }}</span>
        </td>
        <td class="text text-xs font-weight-lighter" >{{ howtostep.howtothread?.title }}</td>
        <td class="text text-xs font-weight-lighter" >{{ howtostep.description }}</td>
        <td class="text text-xs" v-if="can( 'howtosteps-creator-show')">{{ howtostep.creator?.name }}</td>
        <td style="width: 115px" class="text text-xs font-weight-lighter" ><small>{{ formatDate(howtostep.created_at) }}</small></td>
        <td style="width: 115px" class="text text-xs font-weight-lighter" ><small>{{ formatDate(howtostep.updated_at) }}</small></td>
        <td style="width: 100px">
            <router-link v-if="can( 'howtosteps-update')" :to="{
                name: 'howtosteps.edit',
                params: {
                    id: howtostep.uuid
                }
            }">
                <i class="fa fa-edit mr-2 text text-xs text-warning font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can( 'howtosteps-delete')" href="#" @click.prevent="$emit('confirmHowtostepDeletion', howtostep)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>

<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
</style>
