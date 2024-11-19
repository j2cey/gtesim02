<script setup>
import {computed, onMounted, ref} from 'vue';
import { useToastr } from '@/toastr.js';
import { useAbility } from "@casl/vue";

const { can, cannot } = useAbility();
import { formatDate } from '../../services/helper.js'

const toastr = useToastr();

const props = defineProps({
    statdetail: Object,
    stat_agence: { type: Object, default: null},
    stat_period: { type: Object, default: null},
    selecteds: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['']);

const toggleSelection = (event) => {
    emit('toggleSelection', props.statdetail, event.target.id);
}

const statdetailChecked = computed(() => {
    return props.selecteds.includes(props.statdetail.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="statdetailChecked" @change="toggleSelection" :key="statdetail.id" :id="'state_' + statdetail.id" :ref="'state_' + statdetail.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs text-bold text-success">
            {{ statdetail.name }}
        </td>
        <td class="text text-xs font-weight-lighter" >{{ statdetail.email }}</td>
        <td class="text text-xs font-weight-lighter" >{{ statdetail.employe?.departement?.intitule }}</td>
        <td class="text text-xs font-weight-lighter" >
            <router-link :to="{
                    name: 'esims.index',
                    params: {
                        userid: statdetail.uuid,
                    }
                }">
                {{ statdetail.esimsattributed?.length }}
            </router-link>
        </td>
    </tr>
</template>

<style>
a.disabled {
    pointer-events: none;
    cursor: default;
}
</style>
