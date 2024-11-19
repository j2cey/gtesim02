<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthUserStore } from '../../stores/AuthUserStore';

// TODO: Display all login errors if any
// TODO: Manage 419 Error Csrf token missing

const authUserStore = useAuthUserStore();
const router = useRouter();
const form = reactive({
    email: '',
    password: '',
});

const inputPwdType = ref("password");
const loading = ref(false);
const errorMessage = ref('');
const handleSubmit = () => {
    loading.value = true;
    errorMessage.value = '';
    axios.post('/login', form)
        .then(() => {
            // authUserStore.getAbilities();
            //router.push('/');
            // authUserStore.getAuthUser();
            window.location.href="/";
        })
        .catch((error) => {
            if (error.response.status === 422) {
                errorMessage.value = error.response.data.message;
            }
        })
        .finally(() => {
            loading.value = false;
        });
};

const showPassword = () => {
    if (form.password) {
        inputPwdType.value = "text";
    }
}
const hidePassword = () => {
    if (form.password) {
        inputPwdType.value = 'password';
    }
}
</script>

<template>
    <div class="login-box">
        <div class="login-logo">
            <a href="#" class="h1"><b>GT</b>Esim</a>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Connectez-vous pour Accéder</p>
                <div v-if="errorMessage" class="alert alert-danger" role="alert">
                    {{ errorMessage }}
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="input-group mb-3">
                        <input v-model="form.email" type="text" class="form-control" placeholder="E-mail">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input v-model="form.password" :type="inputPwdType" class="form-control" placeholder="Mot de Passe">
                        <div class="input-group-append">
                            <div class="input-group-text" @pointerdown="showPassword" @pointerup="hidePassword">
                                <span v-if="form.password" class="fas fa-eye"></span>
                                <span v-else class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                     <span class="text text-sm font-weight-light mr-1 ml-1">Se Souvenir de moi</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-4">
                            <button type="submit" class="btn btn-sm btn-primary btn-block" :disabled="loading">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span v-else><i class="fa fa-sign-in-alt mr-1"></i> Valider</span>
                            </button>
                        </div>

                    </div>
                </form>

                <p class="mb-1">
                    <a href="forgot-password.html"><span class="text text-xs">J'ai oublié mon mot de passe</span></a>
                </p>
            </div>

        </div>
    </div>
</template>
