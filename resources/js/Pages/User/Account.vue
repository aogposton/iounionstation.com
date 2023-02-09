<script>
import Layout from "@/Layouts/AccountControlLayout.vue";
export default {
    layout: Layout,
};
</script>

<script setup>
import { ref, onMounted, computed, reactive } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";

const userChanged = ref([]);
const showPasswordReset = ref(false);
const toastMessage = ref("");
const toastHeading = ref("");
const passwordReset = reactive({
    confirmPassword: null,
    password: null,
});

defineProps({
    user: Object,
});

const user = computed(() => usePage().props.user);

let toastElList = null;
let toastList = null;

onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});

function saveNewPassword() {
    router.post(`/conductor/reset-password`, passwordReset, {
        onSuccess: () => {
            router.reload({ only: ["user"] });
            toastMessage.value = `Successfully updated Password`;
            toastHeading.value = "Password";
            toastList[0].show();
            showPasswordReset.value = false;
            passwordReset.password = null;
            passwordReset.confirmPassword = null;
        },
        onError: (errors) => {
            toastHeading.value = "error";
            toastMessage.value = `${errors.message}`;
            toastList[0].show();
            console.log(errors.message);
        },
    });
}
</script>

<template>
    <div>
        <Head title="Admin users" />
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div
                        class="container"
                        style="height: 100vh; background-color: white"
                    >
                        <div class="row">
                            <div class="col"><h1>User Info</h1></div>
                        </div>
                        <hr />
                        <div class="row m-3">
                            <div class="col-2">email:</div>
                            <div class="col">{{ user.email }}</div>
                        </div>
                        <hr v-if="showPasswordReset == false" />
                        <div class="row m-3" v-if="showPasswordReset == false">
                            <div class="col-2">Password:</div>
                            <div class="col">
                                <a
                                    href="javascript:;"
                                    @click="showPasswordReset = true"
                                    v-if="showPasswordReset == false"
                                >
                                    Reset Password
                                </a>
                            </div>
                        </div>
                        <hr v-if="showPasswordReset == true" />
                        <div class="row m-3" v-if="showPasswordReset == true">
                            <div class="col-2">reset password:</div>
                            <div class="col">
                                <div class="container-fluid">
                                    <div class="row mb-4">
                                        <div class="col">new password:</div>
                                        <div class="col">
                                            <input
                                                class="form-control"
                                                v-model="passwordReset.password"
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">confirm password:</div>
                                        <div class="col">
                                            <input
                                                class="form-control"
                                                v-model="
                                                    passwordReset.confirmPassword
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col"></div>
                                        <div class="col">
                                            <a
                                                href="javascript:;"
                                                class="mx-3"
                                                @click="saveNewPassword()"
                                            >
                                                save new password</a
                                            >
                                            <a
                                                href="javascript:;"
                                                class="mx-3"
                                                @click="
                                                    showPasswordReset = false
                                                "
                                                >cancel</a
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                    </div>
                </div>
            </div>
        </div>
        <div class="toast-container bottom-0 end-0 p-3">
            <div class="toast">
                <div class="toast-header">
                    <strong class="me-auto">{{ toastHeading }}</strong>
                </div>
                <div class="toast-body">{{ toastMessage }}</div>
            </div>
        </div>
    </div>
</template>
