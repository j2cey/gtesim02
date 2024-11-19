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
    ldapuser: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['ldapuserDeleted', 'editLdapUser', 'confirmLdapUserDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.ldapuser);
}

onMounted(() => {

});
</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="ldapuser.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('ldapusers-show')" :to="`/ldapusers/${ldapuser.id}/show`">
                {{ ldapuser.name }}
            </router-link>
            <span v-else>{{ ldapuser.name }}</span>
        </td>
        <td class="text text-xs" >{{ ldapuser.login }}</td>
        <td class="text text-xs" >{{ ldapuser.email }}</td>
        <td class="text text-xs" style="width: 115px" >{{ ldapuser.telephone }}</td>
        <td class="text text-xs" >{{ ldapuser.title }}</td>
        <td class="text text-xs" style="width: 95px" ><small>{{ formatDate(ldapuser.created_at) }}</small></td>
        <td class="text text-xs" style="width: 95px" ><small>{{ formatDate(ldapuser.updated_at) }}</small></td>
        <td style="width: 90px">
            <router-link v-if="can('ldapusers-update')" :to="`/ldapusers/${ldapuser.id}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <router-link v-if="can('ldapusers-integrate')" :to="`/ldapusers/${ldapuser.id}/integrate`">
                <i class="fa fa-user-check mr-2 text text-xs text-success"></i>
            </router-link>
            <a class="text text-xs" v-if="can('ldapusers-delete')" href="#" @click.prevent="$emit('confirmLdapUserDeletion', ldapuser)"><i class="fa fa-trash-alt text-danger ml-2 font-weight-light"></i></a>
        </td>
    </tr>
</template>
