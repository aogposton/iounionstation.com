<script>
import Layout from "@/Layouts/Authenticated.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const threadCount = computed(() => usePage().props.auth.thread_count);
const threadLimit = computed(() => usePage().props.auth.thread_limit);
const showEdit = ref([]);
defineProps({
    threads: Object,
    frequencies: Object,
    statuses: Object,
});

const deleteThread = async (thread) => {
    if (confirm("delete?")) {
        router.delete(`/threads/${thread.id}`);
        router.reload();
    }
};
const saveThread = async (thread) => {
    const newThread = Object.assign({}, thread);

    delete newThread.source;
    delete newThread.status;
    delete newThread.frequency;

    router.post(`/threads/${newThread.id}`, newThread, {
        onSuccess: () => {
            showEdit.value = [];
            router.reload({ only: ["threads"] });
        },
    });
};
</script>
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
</style>
<template>
    <div>
        <h2 class="title">
            <span v-if="threadLimit > -1"
                >Threads: {{ threadCount }}/{{ threadLimit }}</span
            >
            <span v-else>Threads: {{ threadCount }} / inifinity</span>
        </h2>
        <div class="container-fluid">
            <div class="row thread" v-for="thread in threads" :key="thread.id">
                <div class="container-fluid">
                    <div
                        class="row justify-content-center align-items-center g-2"
                    >
                        <div class="col">
                            Status:
                            {{
                                thread.status_id != 0
                                    ? thread.status.name
                                    : "ignored"
                            }}
                        </div>
                        <div class="col">
                            Frequency:
                            <span>
                                {{
                                    frequencies.find(
                                        (f) => f.id == thread.frequency.id
                                    ).name
                                }}
                            </span>
                        </div>
                        <div class="col">
                            Accumulate: {{ !!thread.accumulate }}
                        </div>
                        <div class="col">
                            Thread: {{ thread.query_string }} ({{
                                thread.source.name
                            }})
                        </div>
                        <div class="col">
                            Last Processed: {{ thread.last_processed_at }}
                        </div>
                    </div>
                    <hr />
                    <div class="row" v-if="!showEdit[thread.id]">
                        <div class="col">
                            <button
                                class="btn btn-sm btn-primary mx-2"
                                @click="showEdit[thread.id] = true"
                            >
                                Edit Thread
                            </button>
                        </div>
                    </div>
                    <div class="row" v-if="showEdit[thread.id]">
                        <div class="col">
                            <b><h4>Edit:</h4></b>
                        </div>
                    </div>
                    <div class="row" v-if="showEdit[thread.id]">
                        <div class="col">
                            <b>Change frequency:</b><br />
                            <select v-model="thread.frequency_id">
                                <option
                                    v-for="freq in frequencies"
                                    :key="freq.id"
                                    :value="freq.id"
                                >
                                    {{ freq.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col">
                            <b>Accumulation content:</b><br />
                            <select v-model="thread.accumulate">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div
                            class="col d-flex justify-content-end align-items-center"
                        >
                            <a
                                href="javascript:;"
                                class="btn btn-sm btn-primary m-2"
                                @click="saveThread(thread)"
                            >
                                Save
                            </a>
                            <a
                                href="javascript:;"
                                class="btn btn-sm btn-warning m-2"
                                @click="showEdit[thread.id] = false"
                            >
                                Cancel
                            </a>
                            <a
                                href="javascript:;"
                                class="btn btn-sm btn-danger m-2"
                                @click="deleteThread(thread)"
                            >
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
