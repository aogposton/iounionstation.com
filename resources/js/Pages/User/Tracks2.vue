<script>
import Layout from "@/Layouts/CentralControlLayout.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { usePage, Head } from "@inertiajs/vue3";

const trackCount = computed(() => usePage().props.auth.track_count);
const trackLimit = computed(() => usePage().props.auth.track_limit);

const showEdit = ref([]);

defineProps({
    tracks: Object,
    frequencies: Object,
    statuses: Object,
});
</script>
<style>
.title {
    color: white;
}

.track {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
</style>
<template>
    <div>
        <Head title="Routes" />
        <h2 class="title"></h2>
        <div class="container-fluid">
            <div class="row track" v-for="track in tracks" :key="track.id">
                <div class="container-fluid">
                    <div
                        class="row justify-content-center align-items-center g-2"
                    >
                        <div class="col">
                            Status:
                            {{
                                track.status_id != 0
                                    ? track.status.name
                                    : "ignored"
                            }}
                        </div>
                        <div class="col">
                            Frequency:
                            <span>
                                {{
                                    frequencies.find(
                                        (f) => f.id == track.frequency.id
                                    ).name
                                }}
                            </span>
                        </div>
                        <div class="col">
                            Accumulate: {{ !!track.accumulate }}
                        </div>
                        <div class="col">
                            Route: {{ track.query_string }} ({{
                                track.source.name
                            }})
                        </div>
                        <div class="col">
                            Last Processed: {{ track.last_processed_at }}
                        </div>
                    </div>
                    <hr />
                    <div class="row" v-if="!showEdit[track.id]">
                        <div class="col">
                            <button
                                class="btn btn-sm btn-primary mx-2"
                                @click="showEdit[track.id] = true"
                            >
                                Edit Route
                            </button>
                        </div>
                    </div>
                    <div class="row" v-if="showEdit[track.id]">
                        <div class="col">
                            <b><h4>Edit:</h4></b>
                        </div>
                    </div>
                    <div class="row" v-if="showEdit[track.id]">
                        <div class="col">
                            <b>Change frequency:</b><br />
                            <select v-model="track.frequency_id">
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
                            <select v-model="track.accumulate">
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
                                @click="savetrack(track)"
                            >
                                Save
                            </a>
                            <a
                                href="javascript:;"
                                class="btn btn-sm btn-warning m-2"
                                @click="showEdit[track.id] = false"
                            >
                                Cancel
                            </a>
                            <a
                                href="javascript:;"
                                class="btn btn-sm btn-danger m-2"
                                @click="deletetrack(track)"
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
