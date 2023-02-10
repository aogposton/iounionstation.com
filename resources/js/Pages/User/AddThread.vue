<script>
import Layout from "@/Layouts/CentralControlLayout.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import { router, usePage, Head } from "@inertiajs/vue3";

const toastMessage = ref("");
let toastElList = null;
let toastList = null;

const userCan = computed(() => usePage().props.auth.can);
console.log(userCan);

onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});

defineProps({
    results: Object,
});

const form = reactive({
    q: null,
    source: "Reddit/Search",
});

const submitForm = () => {
    router.post(`/routes/query`, form, {
        only: ["results"],
        preserveState: true,
    });
};

const saveQueryToThread = () => {
    router.post(`/routes`, form, {
        onSuccess: () => {
            router.visit("/routes");
        },
        onError: (errors) => {
            toastMessage.value = `${errors.at_limit}`;
            toastList[0].show();
        },
    });
};
</script>
<style>
.thread {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
</style>
<template>
    <div>
        <Head title="Add Routes" />
        <div class="container-fluid">
            <div class="row thread">
                <div class="col">
                    <input
                        type="text"
                        class="form-control my-2 w-100"
                        v-model="form.q"
                        placeholder="Search for Something"
                    />
                    <button
                        @click="submitForm()"
                        class="btn btn-primary btn-lg w-100"
                        type="submit"
                    >
                        Search
                    </button>
                    <button
                        :class="{
                            'btn btn-success w-100 my-1': true,
                        }"
                        type="submit"
                        :disabled="!userCan['add_threads']"
                        v-if="results.data"
                        @click="saveQueryToThread()"
                    >
                        <span v-if="!userCan['add_threads']">
                            You reached your limit
                        </span>
                        <span v-else> + Save Query as Route</span>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col" v-if="results.data">
                    <h5 style="color: white">
                        Results found:
                        {{ results.data.length }}
                    </h5>
                </div>
            </div>
            <div
                v-if="results && results.data && results.data.length > 0"
                class="row thread"
            >
                <div
                    class="col-12 border"
                    v-for="result in results.data"
                    :key="result.id"
                >
                    <div class="conatiner">
                        <div class="row">
                            <div class="col">
                                <h4>
                                    {{ JSON.parse(result.metadata).title }}
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                {{ result.body }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                {{ JSON.parse(result.metadata).author }}
                                {{ JSON.parse(result.metadata).publishedAt }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="toast-container bottom-0 end-0 p-3">
            <div class="toast">
                <div class="toast-header">
                    <strong class="me-auto">Login failed</strong>
                </div>
                <div class="toast-body">{{ toastMessage }}</div>
            </div>
        </div>
    </div>
</template>
<style>
.right-side {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
</style>
