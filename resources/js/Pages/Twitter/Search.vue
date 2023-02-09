<script setup>
import navBar from "../../components/navBar.vue";
import { reactive, onMounted, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";

const toastMessage = ref("");
let toastElList = null;
let toastList = null;

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
    source: "Twitter/Search",
});

const submit = () => {
    router.visit(`/twitter/search?q=${form.q}`, {
        only: ["results"],
        preserveState: true,
    });
};

const saveQueryToThread = () => {
    router.post(`/threads`, form, {
        onSuccess: () => {
            router.visit("/threads");
        },
        onError: (errors) => {
            toastMessage.value = `${errors.at_limit}`;
            toastList[0].show();
            console.log(errors);
        },
    });
};
</script>

<template>
    <div>
        <Head title="Reddit" />
        <navBar></navBar>
        <div class="container-fluid">
            <div class="row">
                <sidebar class="col-2"></sidebar>
                <div class="col-10 p-0">
                    <div class="container my-5">
                        <div class="row">
                            <div class="col">
                                <form @submit.prevent="submit">
                                    <input
                                        type="text"
                                        class="form-control my-2"
                                        v-model="form.q"
                                        placeholder="Search for Something"
                                    />
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                    >
                                        Submit
                                    </button>
                                </form>
                                <form @submit.prevent="saveQueryToThread">
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        v-if="results.data"
                                    >
                                        Save Query as Thread
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col" v-if="results.data">
                                <h5>
                                    Results.data found:
                                    {{ results.data.length }}
                                </h5>
                            </div>
                        </div>
                        <div
                            v-if="results.data && results.data.length > 0"
                            class="row"
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
                                                {{
                                                    JSON.parse(result.metadata)
                                                        .title
                                                }}
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
                                            {{
                                                JSON.parse(result.metadata)
                                                    .author
                                            }}
                                            {{
                                                JSON.parse(result.metadata)
                                                    .publishedAt
                                            }}
                                        </div>
                                    </div>
                                </div>
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
