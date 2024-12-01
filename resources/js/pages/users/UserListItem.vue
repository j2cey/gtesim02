<script setup>
import {onMounted, ref} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import {formatDate} from "../../services/helper.js";

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion', 'confirmAccountInfosSendmail']);

const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
        .then(() => {
            toastr.success('Role changed successfully!');
        })
};

const toggleSelection = () => {
    emit('toggleSelection', props.user);
}

onMounted(() => {

});
</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="user.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('users-show')" :to="`/users/${user.uuid}/show`">
                {{ user.name }}
            </router-link>
            <span v-else>{{ user.name }}</span>
        </td>
        <td class="text text-xs" >{{ user.email }}</td>
        <td class="text text-xs" ><small>{{ formatDate(user.created_at) }}</small></td>
        <td class="text text-xs" ><small>{{ formatDate(user.updated_at) }}</small></td>
        <td class="text text-xs" >
            <span v-for="role in user.roles" class="badge text-muted">{{ role.name }}</span>
        </td>
        <td>
            <router-link v-if="can('users-update')" :to="`/users/${user.uuid}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('users-accountinfos-sendmail')" href="#" @click.prevent="$emit('confirmAccountInfosSendmail', user)">
                <i class="fa fa-envelope mr-2 text text-xs text-success"></i>
            </a>
            <a class="text text-xs" v-if="can('users-delete')" href="#" @click.prevent="$emit('confirmUserDeletion', user)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>
