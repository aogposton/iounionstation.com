<script>
import Layout from "@/Layouts/CentralControlLayout.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import { router, usePage, Head } from "@inertiajs/vue3";
import axios from "axios";

const toastMessage = ref("");
const toastHeading = ref("");
const loading = ref(false);

let toastElList = null;
let toastList = null;

const userCan = computed(() => usePage().props.auth.can);
const sources = computed(() => usePage().props.sources);
const sourceTypes = computed(() => usePage().props.sourceTypes);

const newSource = reactive({
    source_type_id: 0,
    name: null,
    query_string: null,
    verified_at: false,
});
const querystring = computed(() => {
    let selected =
        sourceTypes.value.find((st) => st.id == newSource.source_type_id)
            ?.name || "";

    switch (selected) {
        case "Twitter/User":
            return "@user";
            break;

        case "Twitter/Search":
            return "some search query";
            break;

        case "Reddit/Subreddit":
            return "r/~";
            break;

        case "Reddit/Search":
            return "some search query";
            break;
        default:
            return selected;
    }
});

function verifyNewSource() {
    console.log("verify source", newSource);
    loading.value = true;
    axios.post(`/sources/verify`, newSource).then((response) => {
        loading.value = false;

        if (response.status == 204) {
            newSource.verified_at = false;
        }

        if (response.status == 200) {
            console.log(response.data);
            newSource.verified_at = response.data;
        }
    });
}

function addSource() {
    axios.post(`/sources`, newSource).then((response) => {
        router.reload({ only: ["sources"], preserveState: true });
    });
}

function deleteSource(source) {
    axios.delete(`/sources/${source.id}`).then(() => {
        router.reload({ only: ["sources"], preserveState: true });
    });
}

onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});
</script>
<template>
    <div>
        <Head title="Edit Sources" />
        <h2 class="title">Edit Sources</h2>
        <div class="container-fluid">
            <div class="row g-3">
                <div class="col-3" v-for="source in sources" :key="source.id">
                    <div class="thread w-100 m-0">
                        <span :if="source.name">{{ source.name }}</span>
                        <select
                            class="form-control"
                            v-model="source.source_type_id"
                            disabled
                        >
                            <option
                                v-for="st in sourceTypes"
                                :key="st.id"
                                :value="st.id"
                            >
                                {{ st.name }}
                            </option>
                        </select>
                        <input
                            type="text"
                            class="form-control my-2 w-100"
                            v-model="source.query_string"
                            disabled
                        />
                        <button
                            class="btn btn-danger w-100"
                            type="submit"
                            @click="deleteSource(source)"
                        >
                            Delete
                        </button>
                    </div>
                </div>
                <div class="col-3">
                    <div class="thread w-100 m-0">
                        <input
                            type="text"
                            class="form-control my-2 w-100"
                            placeholder="Name (optional)"
                            v-model="newSource.name"
                        />
                        <select
                            class="form-control"
                            v-model="newSource.source_type_id"
                            @change="newSource.verified_at = false"
                        >
                            <option value="0">Select a type</option>
                            <option
                                v-for="st in sourceTypes"
                                :key="st.id"
                                :value="st.id"
                            >
                                {{ st.name }}
                            </option>
                        </select>
                        <input
                            type="text"
                            class="form-control my-2 w-100"
                            :placeholder="querystring"
                            v-model="newSource.query_string"
                            @input="newSource.verified_at = false"
                        />
                        <button
                            class="btn btn-info w-100 my-1"
                            @click="verifyNewSource()"
                        >
                            <span v-if="loading"> Loading... </span>
                            <span v-else> Verify Source</span>
                        </button>

                        <button
                            :disabled="!newSource.verified_at"
                            class="btn btn-danger w-100"
                            type="submit"
                            @click="addSource()"
                        >
                            Add Source
                        </button>
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
<style>
.title {
    color: white;
}
.right-side {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
.thread {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
</style>
