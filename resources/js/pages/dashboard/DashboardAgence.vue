<script setup>
import { computed, onBeforeMount, onMounted, reactive, ref } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { Form } from "vee-validate";
import Multiselect from "vue-multiselect";
import { useRouter, useRoute } from 'vue-router';
import DashboardDetailList from "../dashboard/DashboardDetailList.vue";
import DatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';

const router = useRouter();
const route = useRoute();
const statForm = reactive({
    departement: {},
    period: null,
});
const agenceid = ref(-1);
const statdetails = ref({'data': []});
const stat_agence = ref(null);
const stat_period = ref(null);
const commom_key = ref(0);
const innerdetails_key = ref('_innerdetails_0');
const loading = ref(false);

const initForm = (obj) => {
    statForm.departement = obj ? obj.departement : '';
    statForm.period = obj ? obj.period : '';
}

const forceRerenderInnerDetails = () => {
    commom_key.value += 1;
    innerdetails_key.value = '_innerdetails_' + commom_key.value;
};
const resetForm = () => {
    initForm();
};
const launchInitAgence = () => {
    if ( agenceid.value !== -1 ) {
        initForm();
        statForm.departement = {'uuid': agenceid.value};

        launchSearch();
    }
};
const launchSearch = (values, actions) => {
    loading.value = true;
    //let statForm_tmp = statForm;
    /*statForm_tmp.departement = statForm.departement
    statForm_tmp.period = statForm.period*/

    axios.post('/api/dashboards.details', statForm)
        .then((result) => {
            console.log("launchSearch, result: ", result);
            loading.value = false;

            statdetails.value = result.data.statresults;

            statForm.departement = result.data.departement;
            stat_agence.value = result.data.departement;
            stat_period.value = result.data.period;

            forceRerenderInnerDetails();
        })
        .catch((error) => {
            loading.value = false;
        })
        .finally(() => {
            //statForm = statForm_tmp;
        });
};

const departements = ref([]);
const selectPeriodChange = () => {
    stat_period.value = statForm.period; //{'start': statForm.period[0],'end': statForm.period[1]};
};

const formValid = computed(() => {
    return !!statForm.departement;
});

const getDepartements = () => {
    axios.get(`/api/departements/all`)
        .then((response) => {
            console.log("getDepartements, response: ", response)
            departements.value = response.data;
        }).then(() => {

        }
    )
};

const currentPath = ref('/');
const lastPath = ref('/dashboard');
const prevRoutePath = computed(() => {
    return lastPath ? lastPath.value : '/';
});

const initComponent = () => {
    initForm();
    lastPath.value = router.options.history.state.back ? router.options.history.state.back : lastPath.value;
    currentPath.value = route.fullPath;
    if (route.params.agenceid) {
        agenceid.value = route.params.agenceid;
        launchInitAgence();
    } else {
        agenceid.value = -1;
    }
};

onBeforeMount(() => {
    getDepartements();
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
                    <h3 class="m-0">
                        <span>Details Stats</span>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Accueil</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span>Details Stats Agence</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="card card-default">
                                <Form @submit="launchSearch" v-slot:default="{ errors }">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="m_select_departement" class="form-label text-xs">Agence</label>
                                            <multiselect
                                                id="m_select_departement"
                                                v-model="statForm.departement"
                                                :options="departements"
                                                selected.sync="departements"

                                                value=""
                                                :searchable="true"
                                                :multiple="false"
                                                key="value"

                                                placeholder="Agence"
                                                label="intitule"
                                                track-by="id"
                                                :preselect-first="true"
                                                :disabled="loading"
                                            >
                                            </multiselect>
                                            <span class="invalid-feedback">{{ errors.departement }}</span>
                                            <span v-if="statForm.departement" class="badge badge-default">
                                                    <small>{{ statForm.departement.intitule }}</small>
                                                </span>
                                        </div>

                                        <div class="form-group">
                                            <label for="period" class="form-label text-xs">Periode</label>
                                            <DatePicker id="period" lang="fr" style="width: 100%; height: 25px;" v-model:value="statForm.period" range format="DD-MM-YYYY" @change="selectPeriodChange" :disabled="loading"></DatePicker>
                                            <span class="invalid-feedback">{{ errors.period }}</span>
                                        </div>

                                    </div>
                                    <div class="card-footer">
                                        <div class="buttons">
                                            <button type="button" class="btn btn-sm btn-default m-2" :disabled="loading" @click="resetForm()">Annuler</button>
                                            <button type="submit" class="btn btn-sm btn-primary m-2" :disabled="loading || !formValid">Valider</button>
                                        </div>
                                    </div>
                                </Form>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-9 col-sm-6 col-12">

                            <DashboardDetailList
                                :key="innerdetails_key"
                                :loading="loading"
                                :statdetails_prop="statdetails"
                                :stat_agence="stat_agence"
                                :stat_period="stat_period"
                            ></DashboardDetailList>

                        </div>
                        <!-- /.col -->
                    </div>

        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
input.multiselect__input {
    font-size: 8px
}
</style>
