<script setup>
import {computed, onMounted, ref} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { useAbility } from "@casl/vue";
import { formatDate } from '../../helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    emailaddress: Object,
    clientesim: Object,
    selectedEmailaddresses: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['confirmEmailaddressDeletion']);
const modeltype = ref('clientesim');

const toggleSelection = (event) => {
    emit('toggleSelection', props.emailaddress, event.target.id);
}

const emailaddressChecked = computed(() => {
    return props.selectedEmailaddresses.includes(props.emailaddress.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="emailaddressChecked" @change="toggleSelection" :key="emailaddress.id" :id="'state_' + emailaddress.id" :ref="'state_' + emailaddress.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs text-bold text-primary">{{ emailaddress.email_address }}</td>
        <td class="text text-xs" >{{ emailaddress.creator?.name }}</td>
        <td class="text text-xs" >{{ formatDate(emailaddress.created_at) }}</td>
        <td class="text text-xs" >{{ formatDate(emailaddress.updated_at) }}</td>
        <td>
            <router-link v-if="can('clientesim-emailaddress-update')" :to="{
                name: 'emailaddresses.edit',
                params: {
                    id: emailaddress.uuid,
                    modeltype: modeltype,
                    modelid: clientesim.uuid
                }
            }">
                <i class="fa fa-edit mr-2 text text-xs text-warning"></i>
            </router-link>
            <a class="text text-xs" v-if="can('clientesim-emailaddress-delete')" href="#" @click.prevent="$emit('confirmEmailaddressDeletion', emailaddress)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>

<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
</style>
