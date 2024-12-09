<script setup>
import { onMounted } from 'vue';
import { useToastr } from '../../toastr.js';
import { useAbility } from "@casl/vue";
import { formatDate } from '../../services/helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    detailtype: { type: String, default: 'card'},
    howtothread: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['howtothreadDeleted', 'confirmHowtothreadDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.howtothread);
}

onMounted(() => {

});
</script>

<template>
    <div v-if="detailtype === 'cardx'" class="card">
        <img class="card-img-top" src="images/logo.png" alt="Card image cap">
        <div class="card-body">
            <h5 class="card-title">Card title that wraps to a new line</h5>
            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
        </div>
    </div>
    <div v-if="detailtype === 'card'" class="info-box">
        <span class="info-box-icon">
            <img :src="howtothread.image" alt="">
        </span>
        <div class="info-box-content">
            <span class="info-box-text">
                <router-link v-if="can('howtothreads-show')" :to="`/howtothreads/${howtothread.uuid}/show`">
                {{ howtothread.title }}
                </router-link>
                <span v-else>{{ howtothread.title }}</span>
            </span>
            <span class="info-box-text text-xs text-md font-weight-lighter">{{ howtothread.description }}</span>
            <div class="progress">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
            <span class="progress-description">
                <router-link v-if="can('howtothreads-update')" :to="`/howtothreads/${howtothread.uuid}/edit`">
                    <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
                </router-link>
                <a class="text text-xs" v-if="can('howtothreads-delete')" href="#" @click.prevent="$emit('confirmHowtothreadDeletion', howtothread)">
                    <i class="fa fa-trash-alt mr-2 text-danger font-weight-light"></i>
                </a>
                <router-link v-if="can('howtothreads-update')" :to="`/howtothreads/${howtothread.uuid}/read`">
                    Lire <i class="fa fa-hand-point-right mr-2 font-weight-light"></i>
                </router-link>
            </span>
        </div>
    </div>
    <tr v-else>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="howtothread.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >
            <router-link v-if="can('howtothreads-show')" :to="`/howtothreads/${howtothread.uuid}/show`">
                {{ howtothread.title }}
            </router-link>
            <span v-else>{{ howtothread.title }}</span>
        </td>
        <td class="text text-xs" ><small>{{ howtothread.code }}</small></td>
        <td class="text text-xs" ><small>{{ howtothread.description }}</small></td>
        <td class="text text-xs" v-if="can('howtothreads-creator-list')" >{{ howtothread.creator?.name }}</td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(howtothread.created_at) }}</small></td>
        <td class="text text-xs" style="width: 110px" ><small>{{ formatDate(howtothread.updated_at) }}</small></td>
        <td style="width: 90px">
            <router-link v-if="can('howtothreads-update')" :to="`/howtothreads/${howtothread.uuid}/edit`">
                <i class="fa fa-edit mr-2 text text-xs font-weight-light"></i>
            </router-link>
            <a class="text text-xs" v-if="can('howtothreads-delete')" href="#" @click.prevent="$emit('confirmHowtothreadDeletion', howtothread)"><i class="fa fa-trash-alt text-danger font-weight-light"></i></a>
        </td>
    </tr>
</template>
