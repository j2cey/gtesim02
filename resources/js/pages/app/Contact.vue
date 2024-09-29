<script setup>
import {onMounted, onUnmounted} from 'vue';
import { useAuthUserStore } from '../../stores/AuthUserStore';
import { useRouter } from 'vue-router';
import { useSettingStore } from '../../stores/SettingStore';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import imgUrl from '../../../assets/img/app_logo.png'

// TODO: Envoyer le message de contact

const { can, cannot } = useAbility();

const router = useRouter();
const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();

const goToProfile = () => {
    router.push('/profile');
}

onMounted( () => {

})

onUnmounted(() => {

})
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Contactez-nous</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                        <li class="breadcrumb-item active">Contact</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="card">
            <div class="card-body row">
                <div class="col-5 text-center d-flex align-items-center justify-content-center">
                    <div class>
                        <h2>{{ settingStore.settings.app.name }}</h2>
                        <p class="lead mb-5">Gabon Télécom, Division Services Informatique, SI Métiers<br>
                            Phone: +241 65 300 354
                        </p>
                    </div>
                </div>
                <div class="col-7">
                    <div class="form-group">
                        <label for="inputName">Nom</label>
                        <input type="text" id="inputName" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">E-Mail</label>
                        <input type="email" id="inputEmail" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="inputSubject">Sujet</label>
                        <input type="text" id="inputSubject" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="inputMessage">Message</label>
                        <textarea id="inputMessage" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-sm btn-primary" value="Enoyer message">
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
