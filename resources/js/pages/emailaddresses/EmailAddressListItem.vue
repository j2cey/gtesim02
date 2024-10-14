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
    modeltype: { type: String, default: ''},
    modelid: { type: String, default: ''},
    selecteds: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['confirmEmailaddressDeletion']);

const toggleSelection = (event) => {
    emit('toggleSelection', props.emailaddress, event.target.id);
}

const emailaddressChecked = computed(() => {
    return props.selecteds.includes(props.emailaddress.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="emailaddressChecked" @change="toggleSelection" :key="emailaddress.id" :id="'state_' + emailaddress.id" :ref="'state_' + emailaddress.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs text-bold text-success">
            <router-link v-if="can( ( modeltype === '' ? 'emailaddresses' : modeltype + '-emailaddress' ) + '-show')" :to="`/emailaddresses/${emailaddress.uuid}/show`">
                {{ emailaddress.email_address }}
            </router-link>
            <span v-else>{{ emailaddress.email_address }}</span>
        </td>
        <td class="text text-xs font-weight-lighter" >{{ emailaddress.hasemailaddress?.intitule }}</td>
        <td class="text text-xs" v-if="can( ( modeltype === '' ? 'creators' : modeltype + '-creator' ) + '-show')">{{ emailaddress.creator?.name }}</td>
        <td class="text text-xs" ><small><span class="text font-weight-lighter">{{ formatDate(emailaddress.created_at) }}</span></small></td>
        <td class="text text-xs font-weight-lighter" ><small><span class="text font-weight-lighter">{{ formatDate(emailaddress.updated_at) }}</span></small></td>
        <td>
            <router-link v-if="can( ( modeltype === '' ? 'emailaddresses' : modeltype + '-emailaddress' ) + '-update')" :to="{
                name: 'emailaddresses.edit',
                params: {
                    id: emailaddress.uuid,
                    modeltype: modeltype,
                    modelid: modelid
                }
            }">
                <i class="fa fa-edit mr-2 text text-xs text-warning font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can( ( modeltype === '' ? 'emailaddresses' : modeltype + '-emailaddress' ) + '-delete')" href="#" @click.prevent="$emit('confirmEmailaddressDeletion', emailaddress)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>

<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
</style>
