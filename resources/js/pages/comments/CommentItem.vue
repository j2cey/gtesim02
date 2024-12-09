<script setup>

import { computed, ref } from "vue";
import { formatDate } from '../../services/helper.js'
import { useAuthUserStore } from '../../stores/AuthUserStore.js';

const props = defineProps({
    comment: Object,
});

const emit = defineEmits(['commentUpdated', 'commentDeleted']);

const state = ref('default');
const authUserStore = useAuthUserStore();
const commentdata = ref({
    comment_text: props.comment?.comment_text,
    commentable_type: props.comment?.commentable_type,
    commentable_id: props.comment?.commentable_id,
});

const resetEdit = () => {
    state.value = 'default';
    commentdata.value.comment_text = props.comment.comment_text;
};

const saveEdit = () => {
    state.value = 'default';
    emit('commentUpdated', {
        'id': props.comment.id,
        'uuid': props.comment.uuid,
        'author': props.comment.author,
        'comment_text': commentdata.value.comment_text,
    });
};

const deleteComment = () => {
    emit('commentDeleted', {
        'id': props.comment.id,
        'uuid': props.comment.uuid,
    });
}

const editable = computed(() => {
    return authUserStore.user.id === props.comment.author.id;
});

</script>

<template>
    <div>
        <div v-show="state === 'default'">
            <div class="flex justify-between mb-1">
                <span class="text-grey-darkest leading-normal text-sm">{{ comment.comment_text }}</span>
                <br>
                <button v-if="editable" @click="state = 'editing'" class="btn btn-xs ml-2 mt-1 mb-auto text-blue hover:text-blue-dark text-sm">Modifier</button>
            </div>
            <div class="text-muted text-grey-dark leading-normal text-xs">
                <span>{{comment.author.name}} <span class="mx-1 text-xs">&bull;</span>{{ formatDate(comment.created_at) }}</span>
            </div>
        </div>
        <div v-show="state === 'editing'">
            <div class="mb-3">
                <h5 class="text-black text-sm">Modifer Commentaire</h5>
            </div>
            <textarea v-model="commentdata.comment_text"
                      placeholder="Update comment" style="min-width: 50%"
                      class="bg-grey-lighter rounded leading-normal resize-none w-full h-24 py-2 px-3">
            </textarea>
            <div class="flex flex-col md:flex-row items-center mt-2">
                <button class="btn btn-xs border border-blue bg-blue text-white hover:bg-blue-dark py-2 px-4 rounded tracking-wide mb-2 md:mb-0 md:mr-1" @click="saveEdit">Valider</button>
                <button class="btn btn-xs border border-grey-darker text-grey-darker hover:bg-grey-dark hover:text-white py-2 px-4 rounded tracking-wide mb-2 md:mb-0 md:ml-1" @click="resetEdit">Annuler</button>
                <button class="btn btn-xs text-red hover:bg-red hover:text-white py-2 px-4 rounded tracking-wide mb-2 md:mb-0 md:ml-auto" @click="deleteComment">Supprimer</button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
