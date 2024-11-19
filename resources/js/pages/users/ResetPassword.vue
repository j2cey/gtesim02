<script setup>
import {reactive, ref} from 'vue';
import {Form} from "vee-validate";

const props = defineProps({
    user: Object,
});

const emit = defineEmits(['launchResetPassword']);

const resetpwdform = reactive({
    newpwd: '',
    renewpwd: '',
});

//<editor-fold desc="Reset Password">
const inputNewPwdType = ref("password");
const inputReNewPwdType = ref("password");
const loadingResetPassword = ref(false);
const resetPwdErrorMessage = ref('');
const launchResetPassword = () => {
    $('#passwordResetModal').modal('show');
}

const resetPassword = (values, actions) => {
    loadingResetPassword.value = true;
    resetPwdErrorMessage.value = '';
    axios.put(`/api/users/${props.user.uuid}/resetpwd`, resetpwdform)
        .then((response) => {
            $('#passwordResetModal').modal('hide');
            Swal.fire({
                html: '<small>Password successfully reset !</small>',
                icon: 'success',
                timer: 3000
            }).then(() => {

            });
        })
        .catch((error) => {
            if (error.response.status === 422) {
                actions.setErrors(error.response.data.errors);
                resetPwdErrorMessage.value = error.response?.data.errors;
            }
        })
        .finally(() => {
            loadingResetPassword.value = false;
        })
};

const showNewPwd = (event) => {
    inputNewPwdType.value = "text";
}
const hideNewPwd = () => {
    inputNewPwdType.value = "password";
}
const showReNewPwd = () => {
    inputReNewPwdType.value = "text";
}
const hideReNewPwd = () => {
    inputReNewPwdType.value = "password";
}
//</editor-fold>

</script>

<template>
    <button type="button" class="btn btn-sm btn-warning m-2" @click="launchResetPassword" :disabled="loadingResetPassword">
        <span v-if="loadingResetPassword" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        <i v-else class="fa fa-key mr-1"></i> Reset Password
    </button>
    <div class="modal fade" id="passwordResetModal" data-backdrop="static" tabindex="-1" type="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" type="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Reset Password</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form @submit="resetPassword" v-slot:default="{ errors }">
                    <div class="modal-body">
                        <span class="text text-xs font-weight-light">for User <b>{{ user.name }}</b></span>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newpwd"><span class="text text-xs">Password</span></label>
                                    <div class="input-group mb-3">
                                        <input v-model="resetpwdform.newpwd" :type="inputNewPwdType" class="form-control form-control-sm" :class="{ 'is-invalid': resetPwdErrorMessage.newpwd }" id="newpwd" placeholder="New Password">
                                        <button v-if="resetpwdform.newpwd && !loadingResetPassword" type="button" @pointerdown="showNewPwd" @pointerup="hideNewPwd" class="btn btn-sm bg-transparent" style="margin-left: -40px; z-index: 100;">
                                            <i class="fa fa-eye text-xs text-muted"></i>
                                        </button>
                                        <span class="invalid-feedback">{{ errors.newpwd }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="renewpwd"><span class="text text-xs">Confirm Password</span></label>
                                    <div class="input-group mb-3">
                                        <input v-model="resetpwdform.renewpwd" :type="inputReNewPwdType" class="form-control form-control-sm" :class="{ 'is-invalid': resetPwdErrorMessage.renewpwd }" id="renewpwd" placeholder="Confirm New Password">
                                        <button v-if="resetpwdform.renewpwd && !loadingResetPassword" type="button" @pointerdown="showReNewPwd" @pointerup="hideReNewPwd" class="btn btn-sm bg-transparent" style="margin-left: -40px; z-index: 100;">
                                            <i class="fa fa-eye text-xs text-muted"></i>
                                        </button>
                                        <span class="invalid-feedback">{{ errors.renewpwd }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-xs btn-danger" :disabled="loadingResetPassword">
                            <span v-if="loadingResetPassword" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Submit
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
