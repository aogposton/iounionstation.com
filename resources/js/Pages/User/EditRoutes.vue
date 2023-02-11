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

const sources = computed(() => usePage().props.sources);
const destinations = computed(() => usePage().props.destinations);
const routes = computed(() => usePage().props.routes);
const sourceTypes = computed(() => usePage().props.sourceTypes);

const newRoute = reactive({
    source_id: null,
});
onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});
</script>
<template>
    <div>
        <Head title="Add Routes" />
        <h2 class="title">Add Routes</h2>
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <div class="thread">
                        Source:
                        <select
                            class="form-control"
                            v-model="newRoute.source_id"
                        >
                            <option value="cargo">Cargo</option>
                            <option v-for="source in sources" :key="source.id">
                                {{
                                    source.name ||
                                    `[${
                                        sourceTypes.length > 0
                                            ? sourceTypes.find(
                                                  (st) =>
                                                      st.id ==
                                                      source.source_type_id
                                              ).name
                                            : ""
                                    }] ${source.query_string}`
                                }}
                            </option>
                        </select>

                        <select
                            class="form-control"
                            v-if="newRoute.source_id == 'cargo'"
                        >
                            <option value="cargo 1">Cargo 1</option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="thread">
                        parameters:
                        <select class="form-control">
                            <option v-for="source in sources" :key="source.id">
                                {{
                                    source.name ||
                                    `[${
                                        sourceTypes.length > 0
                                            ? sourceTypes.find(
                                                  (st) =>
                                                      st.id ==
                                                      source.source_type_id
                                              ).name
                                            : ""
                                    }] ${source.query_string}`
                                }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="thread">
                        destination:

                        <select class="form-control">
                            <option
                                v-for="destination in destinations"
                                :key="destination.id"
                            >
                                {{ destination.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="thread">
                        &nbsp;
                        <button class="btn btn-success w-100">Add</button>
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
.title {
    color: white;
}
.thread {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
.right-side {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
</style>
