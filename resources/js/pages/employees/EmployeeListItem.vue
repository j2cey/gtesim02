<script setup>
import { onMounted, ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../services/helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    employee: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['employeeDeleted', 'confirmEmployeeDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.employee);
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="employee.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('employes-show')" :to="`/employees/${employee.uuid}/show`">
                {{ employee.nom_complet }}
            </router-link>
            <span v-else>{{ employee.nom_complet }}</span>
        </td>
        <td class="text text-xs" ><small>{{ employee.matricule }}</small></td>
        <td class="text text-xs" ><small>{{ employee.phone_number_list }}</small></td>
        <td class="text text-xs" ><small>{{ employee.email_address_list }}</small></td>
        <td class="text text-xs" ><small>{{ employee.departement?.intitule }}</small></td>
        <td class="text text-xs" ><small>{{ employee.fonction?.intitule }}</small></td>
        <td class="text text-xs" v-if="can('employes-creator-list')" >{{ employee.creator?.name }}</td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(employee.created_at) }}</small></td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(employee.updated_at) }}</small></td>
        <td style="width: 90px">
            <router-link v-if="can('employes-update')" :to="`/employees/${employee.uuid}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('employes-delete')" href="#" @click.prevent="$emit('confirmEmployeeDeletion', employee)"><i class="fa fa-trash-alt text-danger font-weight-light"></i></a>
        </td>
    </tr>
</template>
