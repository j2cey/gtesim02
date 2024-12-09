<script setup>
import axios from 'axios';
import {onMounted, ref, watch, computed, reactive} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { useAbility } from "@casl/vue";
import HtmlEval from "./HtmlEval.vue";
import CommentsManager from "../comments/CommentsManager.vue";

const { can, cannot } = useAbility();

const modeltype = ref('howtothreads');
const router = useRouter();
const route = useRoute();
const toastr = useToastr();

const currposi = ref(1);

const currentPath = ref('/');
const lastPath = ref('/howtos');
const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

const howtothread = ref(null);
const howtothreadid = ref(null);
const getHowtoThread = () => {
    axios.get(`/api/howtothreads/${howtothreadid.value}/edit`)
        .then((response) => {
            console.log("getHowtoThread, response: ", response)
            howtothread.value = response.data;
        }).then(() => {

        }
    )
};

const goToStep = (step) => {
    currposi.value = step.posi
    //let currentstep = currstep;
    //emit('htmlcontent_reloaded', { 'htmlcontent':currstep.howto.htmlbody });
};
const endReading = () => {
    //window.location = '/howtothreads'
    router.push('/howtothreads');
};
const getStep = (stepIndex) => {
    console.log("getStep, howtothread.value: ", howtothread.value);
    if (howtothread.value) {
        if ( stepIndex >= 0 && stepIndex < howtothread.value.steps.length ) {
            return howtothread.value.steps[stepIndex];
        } else {
            return null;
        }
    } else {
        return null;
    }
};

watch(route, () => {
    if (route.fullPath !== currentPath.value) {
        initComponent();
    }
});

const initComponent = () => {
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;

    howtothreadid.value = route.params.id;
    getHowtoThread();
};

const stepslist_key = computed(() => {
    return howtothread.value.id + '_' + 0;
});

const currstep = computed(() => {
    let stepIndex = (currposi.value - 1);
    return getStep(stepIndex);
});

const prevstep = computed(() => {
    let stepIndex = (currposi.value - 2);
    return getStep(stepIndex);
});

const nextstep = computed(() => {
    let stepIndex = (currposi.value + 0);
    return getStep(stepIndex);
});

const currcomments = computed(() => {
    let stepIndex = (currposi.value - 1)
    return getStep(stepIndex).comments;
});

onMounted(() => {
    initComponent();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/howtothreads">Astuces & Tutoriels</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span>{{ howtothread?.title }}</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <h5 class="m-2">
                    <span>{{ howtothread?.title }}</span>
                </h5>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div v-if="howtothread" class="card">
                        <div class="card-header">

                            <div class="card-tools">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool" data-toggle="dropdown">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </button>
                                    <div v-if="howtothread" class="dropdown-menu dropdown-menu-right">
                                        <a v-for="(step, index) in howtothread.steps" v-if="howtothread.steps" :key="step.id" @click="goToStep(step)" class="dropdown-item text-xs">{{ step.posi + '. ' + step.title }}</a>
                                        <a class="dropdown-divider"></a>
                                        <a @click="endReading()" class="dropdown-item text-xs text-danger">Terminé</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-4 border-right">

                                    <div v-if="howtothread && prevstep" class="description-block">
                                        <h5 class="description-header">
                                            <a @click="goToStep(prevstep)">
                                                <i class="fa fa-arrow-circle-left text-warning" aria-hidden="true"></i>
                                            </a>
                                        </h5>
                                        <span class="text text-xs">{{ prevstep.posi + '. ' + prevstep.title }}</span>
                                    </div>

                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-md-4 col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header"><i class="fa fa-circle" aria-hidden="true"></i></h5>
                                        <span class="text text-xs">{{ currstep.posi + '. ' + currstep.title }}</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-md-4 col-sm-4">
                                    <div v-if="nextstep" class="description-block">
                                        <h5 class="description-header">
                                            <a @click="goToStep(nextstep)">
                                                <i class="fa fa-arrow-circle-right text-success" aria-hidden="true"></i>
                                            </a>
                                        </h5>
                                        <span class="text text-xs">{{ nextstep.posi + '. ' + nextstep.title }}</span>
                                    </div>
                                    <div v-else class="description-block">
                                        <h5 class="description-header">
                                            <a @click="endReading()">
                                                <i class="fa fa-stop-circle text-danger" aria-hidden="true"></i>
                                            </a>
                                        </h5>
                                        <span class="text text-xs">Terminé</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            <HtmlEval :htmlcontent="currstep?.howto.htmlbody"></HtmlEval>
                        </div>

                        <hr>
                        <commentsManager :comments="currcomments" :commentable_type="currstep.model_type" :commentable_id="currstep.id" :key="currstep.id"></commentsManager>

                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>
