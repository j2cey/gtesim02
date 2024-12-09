<script setup>

const props = defineProps({
    htmlcontent: String,
});

</script>

<template>
    <div class="card-body">
        <div v-html="htmlcontent"></div>
    </div>
</template>

<style scoped>

</style>
