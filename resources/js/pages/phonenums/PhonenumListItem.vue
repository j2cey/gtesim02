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
    phonenum: Object,
    modeltype: { type: String, default: ''},
    modelid: { type: String, default: ''},
    selecteds: Object,
    index: Number,
    selectAll: Boolean
});

const emit = defineEmits(['confirmPhonenumDeletion','confirmPhonenumEsimRecycle']);

const toggleSelection = (event) => {
    emit('toggleSelection', props.phonenum, event.target.id);
}

const phonenumChecked = computed(() => {
    return props.selecteds.includes(props.phonenum.id) ? true : (!!props.selectAll);
});

const previewPdf = (phonenum) => {
    if ( props.modeltype === 'clientesims' ) {
        window.location = '/clientesims.previewpdf/' + phonenum.id;
    }
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="phonenumChecked" @change="toggleSelection" :key="phonenum.id" :id="'state_' + phonenum.id" :ref="'state_' + phonenum.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs text-bold text-orange" >
            <router-link v-if="can( ( modeltype === '' ? 'phonenums' : modeltype + '-phonenum' ) + '-show')" :to="`/phonenums/${phonenum.uuid}/show`">
                {{ phonenum.phone_number }}
            </router-link>
            <span v-else>{{ phonenum.phone_number }}</span>
        </td>
        <td class="text text-xs font-weight-lighter" >{{ phonenum.hasphonenum?.intitule }}</td>
        <td class="text text-xs font-weight-lighter" >
            <router-link v-if="can('esims-show') && phonenum.esim" :to="`/esims/${phonenum.esim.uuid}/show`">
                {{ phonenum.esim?.imsi }}
            </router-link>
            <span v-else>{{ phonenum.esim?.imsi }}</span>
        </td>
        <td class="text text-xs font-weight-lighter" >{{ phonenum.esim?.iccid }}</td>
        <td class="text text-xs" v-if="can( ( modeltype === '' ? 'creators' : modeltype + '-creator' ) + '-show')">{{ phonenum.creator?.name }}</td>
        <td class="text text-xs" style="width: 105px"><small><span class="text font-weight-lighter">{{ formatDate(phonenum.created_at) }}</span></small></td>
        <td class="text text-xs" style="width: 105px"><small><span class="text font-weight-lighter">{{ formatDate(phonenum.updated_at) }}</span></small></td>
        <td style="width: 105px">
            <router-link v-if="can( ( modeltype === '' ? 'phonenums' : modeltype + '-phonenum' ) + '-update')" :to="{
                name: 'phonenums.edit',
                params: {
                    id: phonenum.uuid,
                    modeltype: modeltype,
                    modelid: modelid
                }
            }">
                <i class="fa fa-edit mr-2 text text-xs text-warning font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="phonenum.esim && can('phonenums-esim-recycle')" href="#" @click.prevent="$emit('confirmPhonenumEsimRecycle', phonenum)">
                <i class="fa fa-recycle mr-2 text text-xs text-success"></i>
            </a>
            <a class="text text-xs" v-if="can( ( modeltype === '' ? '' : modeltype + '-' ) + 'previewpdf')" href="#" @click.prevent="previewPdf(phonenum)">
                <i class="fa fa-file-pdf mr-2 text text-xs font-weight-light"></i>
            </a>
            <a class="text text-xs" v-if="can( ( modeltype === '' ? 'phonenums' : modeltype + '-phonenum' ) + '-delete')" href="#" @click.prevent="$emit('confirmPhonenumDeletion', phonenum)">
                <i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i>
            </a>
        </td>
    </tr>
</template>

<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
</style>
