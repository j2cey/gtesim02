<script setup>
import {computed, onMounted, ref} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../helper.js'

// TODO: Navigate from PhoneNum to Esim details
// TODO: Add PhoneNum pdf view button

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    phonenum: Object,
    clientesim: Object,
    selectedPhonenums: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['confirmPhonenumDeletion','confirmPhonenumEsimRecycle']);
const modeltype = ref('clientesim');

const toggleSelection = (event) => {
    emit('toggleSelection', props.phonenum, event.target.id);
}

const phonenumChecked = computed(() => {
    return props.selectedPhonenums.includes(props.phonenum.id) ? true : (!!props.selectAll);
});

const previewPdf = (phonenum) => {
    window.location = '/clientesims.previewpdf/' + phonenum.id;
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="phonenumChecked" @change="toggleSelection" :key="phonenum.id" :id="'state_' + phonenum.id" :ref="'state_' + phonenum.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs text-bold text-orange" >{{ phonenum.phone_number }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('esim-list') && phonenum.esim" :to="`/esims/${phonenum.esim.uuid}/show`">
                {{ phonenum.esim?.imsi }}
            </router-link>
            <span v-else>{{ phonenum.esim?.imsi }}</span>
        </td>
        <td class="text text-xs" >{{ phonenum.esim?.iccid }}</td>
        <td class="text text-xs" >{{ phonenum.creator?.name }}</td>
        <td class="text text-xs" >{{ formatDate(phonenum.created_at) }}</td>
        <td class="text text-xs" >{{ formatDate(phonenum.updated_at) }}</td>
        <td>
            <router-link v-if="can('clientesim-phonenum-update')" :to="{
                name: 'phonenums.edit',
                params: {
                    id: phonenum.uuid,
                    modeltype: modeltype,
                    modelid: clientesim.uuid
                }
            }">
                <i class="fa fa-edit mr-2 text text-xs text-warning"></i>
            </router-link>
            <a class="text text-xs" v-if="can('phonenum-esim-recycle')" href="#" @click.prevent="$emit('confirmPhonenumEsimRecycle', phonenum)">
                <i class="fa fa-recycle mr-2 text text-xs text-success"></i>
            </a>
            <a class="text text-xs" v-if="can('clientesim-previewpdf')" href="#" @click.prevent="previewPdf(phonenum)">
                <i class="fa fa-file-pdf mr-2 text text-xs"></i>
            </a>
            <a class="text text-xs" v-if="can('clientesim-phonenum-delete')" href="#" @click.prevent="$emit('confirmPhonenumDeletion', phonenum)">
                <i class="fa fa-trash text-danger ml-2"></i>
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
