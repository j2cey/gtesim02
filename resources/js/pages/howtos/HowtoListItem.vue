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
    howto: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['howtoDeleted', 'confirmHowtoDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.howto);
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="howto.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('howtos-show')" :to="`/howtos/${howto.uuid}/show`">
                {{ howto.title }}
            </router-link>
            <span v-else>{{ howto.title }}</span>
        </td>
        <td class="text text-xs" ><small>{{ howto.code }}</small></td>
        <td class="text text-xs" ><small>{{ howto.view }}</small></td>
        <td class="text text-xs" ><small>{{ howto.howtotype?.title }}</small></td>
        <td class="text text-xs" ><small>{{ howto.description }}</small></td>
        <td class="text text-xs" v-if="can('howtos-creator-list')" >{{ howto.creator?.name }}</td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(howto.created_at) }}</small></td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(howto.updated_at) }}</small></td>
        <td style="width: 100px">
            <router-link v-if="can('howtos-update')" :to="`/howtos/${howto.uuid}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <router-link v-if="can('howtos-readhtml')" :to="`/howtos/${howto.uuid}/htmlread`">
                <i class="fa fa-eye mr-2 text text-xs text-success font-weight-light"></i>
            </router-link>
            <router-link v-if="can('howtos-edithtml')" :to="`/howtos/${howto.uuid}/htmledit`">
                <i class="fa fa-file-code mr-2 text text-xs text-secondary font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('howtos-delete')" href="#" @click.prevent="$emit('confirmHowtoDeletion', howto)"><i class="fa fa-trash-alt text-danger font-weight-light"></i></a>
        </td>
    </tr>
</template>
