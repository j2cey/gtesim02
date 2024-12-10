<script setup>

import CommentItem from "./CommentItem.vue";
import { reactive, ref } from "vue";

const local_comments = [
    {
        id: 1,
        comment_text: "How's it going?",
        edited: false,
        created_at: new Date().toLocaleString(),
        author: {
            id: 1,
            name: 'Nick Basile',
        }
    },
    {
        id: 2,
        comment_text: "Pretty good. Just making a painting.",
        edited: false,
        created_at: new Date().toLocaleString(),
        author: {
            id: 2,
            name: 'Bob Ross',
        }
    }
];

const props = defineProps({
    comments: [],
    commentable_type: '',
    commentable_id: 0
});

const loading = ref(false);
const state = ref('default');
const commentsdata = ref(props.comments);
const form = reactive({
    commentable_type: props.commentable_type,
    commentable_id: props.commentable_id,
    comment_text: props.comment_text,
});

const startEditing = () => {
    state.value = 'editing';
};
const stopEditing = () => {
    state.value = 'default';
    form.comment_text = '';
};
const saveComment = () => {
    console.log("saveComment, form: ", form);
    axios.post('/api/comments', form)
        .then(resp => {

            console.log("comment created: resp.data", resp.data);
            commentsdata.value.unshift(resp.data);

            stopEditing();

        }).catch(error => {
        loading.value = false
    });
};
const updateComment = ($event) => {
    let updateForm = ({
        commentable_type: props.commentable_type,
        commentable_id: props.commentable_id,
        comment_text: $event.comment_text,
        author: $event.author,
        id: $event.id,
        uuid: $event.uuid,
    });
    console.log("updateComment, updateForm", updateForm);
    axios.put(`/api/comments/${$event.uuid}`, updateForm)
        .then(resp => {
            console.log("comment updated: ", resp.data);
            commentsdata.value[commentIndex($event.id)].comment_text = resp.data.comment_text;
        })
};
const deleteComment = ($event) => {
    axios.delete(`/api/comments/${$event.uuid}`)
        .then(() => {
            commentsdata.value.splice(commentIndex($event.id), 1);
        })
};
const commentIndex = (commentId) => {
    return commentsdata.value.findIndex((element) => {
        return element.id === commentId;
    });
}

</script>

<template>
    <div class="container max-w-3xl mx-auto tw-p-0 bg-grey-lightest font-nunito py-4 px-5">
        <div class="bg-white rounded shadow-sm p-8 mb-4">
            <div class="mb-4">
                <h5 class="text-black">Commentaires</h5>
            </div>
            <textarea v-model="form.comment_text" placeholder="Laissez un commentaire" style="min-width: 100%; min-height: 5px;"
                      class="bg-grey-lighter rounded leading-normal resize-none w-full py-2 px-3"
                      :class="[state === 'editing' ? 'h-24' : 'h-10']" @focus="startEditing">
            </textarea>
            <div v-show="state === 'editing'" class="mt-3">
                <button class="btn btn-xs border border-blue bg-blue text-white hover:bg-blue-dark py-2 px-4 rounded tracking-wide mr-1" @click="saveComment">Valider</button>
                <button class="btn btn-xs border border-grey-darker text-grey-darker hover:bg-grey-dark hover:text-white py-2 px-4 rounded tracking-wide ml-1" @click="stopEditing">Annuler</button>
            </div>
        </div>

        <div class="bg-white rounded shadow-sm p-8">
            <CommentItem v-for="(comment, index) in comments"
                     :key="comment.id"
                     :comment="comment"
                     :class="[index === comments.length - 1 ? '' : 'mb-3']"
                     @comment-updated="updateComment"
                     @comment-deleted="deleteComment">
            </CommentItem>
        </div>
    </div>
</template>

<style scoped>

</style>
