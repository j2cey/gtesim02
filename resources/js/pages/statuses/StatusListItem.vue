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
    status: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['statusDeleted', 'confirmStatusDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.status);
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="status.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >{{ status.code }}</td>
        <td class="text text-xs" >{{ status.name }}</td>
        <td class="text text-xs" >
            <span v-if="status.style" class="badge" :class="'text-' + status.style ">{{ status.style }}</span>
        </td>
        <td class="text text-xs" >{{ status.description }}</td>
        <td class="text text-xs" >{{ formatDate(status.created_at) }}</td>
        <td class="text text-xs" >{{ formatDate(status.updated_at) }}</td>
        <td>
            <router-link v-if="can('statuses-update')" :to="`/statuses/${status.id}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('statuses-delete')" href="#" @click.prevent="$emit('confirmStatusDeletion', status)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>
