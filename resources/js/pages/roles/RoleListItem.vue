<script setup>
import {computed, onMounted, ref, watch} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    role: Object,
    user: Object,
    index: Number,
    formMode: String,
    selectAll: Boolean,
});

const emit = defineEmits(['roleToAssign', 'roleToRevoke','roleDeleted', 'confirmRoleDeletion']);

const roleToAssign = () => {
    emit('roleToAssign', props.role);
};

const roleToRevoke = () => {
    emit('roleToRevoke', props.role);
};

const toggleSelection = () => {
    emit('toggleSelection', props.role);
}

const isInRole = computed(() => {
    return  props.role.is_in_user;
});

/*watch(props.role, (newVal, oldVal) => {
    console.log("watch props.role");
    props.role.is_in_user = newVal.is_in_user;
});*/

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="role.id" :id="'role_' + role.id" :ref="'role_' + role.id" /></td>
        <td class="text text-xs">{{ index + 1 }}</td>
        <td class="text text-xs">
            <router-link v-if="can('roles-show')" :to="`/roles/${role.id}/show`">
                {{ role.name }}
            </router-link>
            <span v-else>{{ role.name }}</span>
        </td>
        <td class="text text-xs" style="width: 95px">{{ role.guard_name }}</td>
        <td class="text text-xs" style="width: 100px"><small>{{ formatDate(role.created_at) }}</small></td>
        <td class="text text-xs" style="width: 100px"><small>{{ formatDate(role.updated_at) }}</small></td>
        <td class="text text-xs">
            ({{role.permissions.length}})<span v-for="permission in role.permissions.slice(0, 10)" class="badge" :class="(permission.level === 1 ? 'text-danger' : (permission.level === 2 || permission.level === 3 ? 'text-orange' : 'text-default'))">{{ permission.name }}</span>
            <span v-if="role.permissions.length > 10">. . .</span>
        </td>
        <td style="width: 105px">
            <router-link class="text text-xs" v-if="can('roles-update')" :to="`/roles/${role.id}/edit`">
                <small><i class="fa fa-edit mr-2 text text-xs font-weight-light"></i></small>
            </router-link>
            <a v-if="can('roles-assign') && user && (! isInRole)" :class="formMode === 'edit' ? '' : 'disabled'" class="text text-xs" href="#" @click.prevent="roleToAssign"><i class="fa fa-link text-success ml-2 font-weight-light" :class="formMode === 'edit' ? '' : 'text-muted'"></i></a>
            <a v-show="can('roles-revoke') && user" :class="formMode === 'edit' ? '' : 'disabled'" disabled="true" class="text text-xs" v-else href="#" @click.prevent="roleToRevoke"><i class="fa fa-unlink text-orange ml-2 font-weight-light" :class="formMode === 'edit' ? '' : 'text-muted'"></i></a>
            <a class="text text-xs" v-if="can('roles-delete')" href="#" @click.prevent="$emit('confirmRoleDeletion', role)">
                <small>
                    <i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i>
                </small>
            </a>
        </td>
    </tr>
</template>
