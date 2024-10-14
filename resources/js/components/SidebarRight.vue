<script setup>
import { useAbility } from "@casl/vue";

const { can, cannot } = useAbility();
</script>

<template>
    <aside class="control-sidebar control-sidebar-dark">

        <div class="p-3">
            <h5>Aide</h5>
            <p>
                <router-link class="text text-xs" v-if="can('howtothreads-list')" :to="`/howtothreads`">
                    <span class="text text-warning"><i class="fa fa-lightbulb mr-2 text-xs"></i>&nbsp;Comment Faire</span>
                </router-link>
                <br>
                <router-link class="text text-xs" v-if="can('howtothreads-list')" :to="`/howtos`">
                    <span class="text text-danger"><i class="fa fa-book mr-2 text-xs"></i>&nbsp;Rubriques de Tutos</span>
                </router-link>
            </p>
        </div>
    </aside>
</template>

<style scoped>

</style>
