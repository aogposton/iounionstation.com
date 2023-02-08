<template>
    <div>
        <div style="border: 1px solid; border-color: black; margin: 20px">
            <div class="d-flex">
                <p>{{ source }}</p>
                <p>
                    <span v-if="thread.query_string">{{
                        thread.query_string
                    }}</span>
                </p>
            </div>
        </div>
        <div
            v-show="settingsVisible"
            class="items-center p-5 border-t border-slate-200/60 text-white"
            style="background-color: black; margin: 0 15px 15px 15px"
        >
            <div class="grid-flow-row w-100">
                <div>
                    <h3
                        class="text-2xl font-medium leading-none mt-3 clear-both"
                    >
                        Destination
                    </h3>
                    <div
                        style="margin: 10px"
                        v-for="destination in selectedDestinations"
                        :key="destination.id"
                    >
                        <div class="mt-2">
                            <div class="form-check form-switch">
                                <input
                                    id="checkbox-switch-7"
                                    class="form-check-input"
                                    type="checkbox"
                                    v-model="destination.selected"
                                />
                                <label
                                    class="form-check-label"
                                    for="checkbox-switch-7"
                                    >{{ destination.name }}</label
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-flow-row w-100 border-t border-slate-200/60">
                <h3 class="text-2xl font-medium leading-none">Frequency</h3>
                <div style="margin: 10px">
                    <select v-model="selectedFrequency" style="color: black">
                        <option
                            v-for="frequency in frequencies"
                            :key="frequency.id"
                            :value="frequency.id"
                        >
                            {{ frequency.name }}
                        </option>
                    </select>

                    <div class="mt-3">
                        <div class="mt-2">
                            <div class="form-check form-switch">
                                <input
                                    id="checkbox-switch-7"
                                    class="form-check-input"
                                    type="checkbox"
                                    v-model="accumulate"
                                />
                                <label
                                    class="form-check-label"
                                    for="checkbox-switch-7"
                                    >Accumulate</label
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100">
                <button @click="saveThraed" class="btn btn-primary">
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, watch } from "vue";
const props = defineProps(["thread", "frequencies", "destinations", "source"]);
const selectedDestinations = ref([]);
const settingsVisible = ref(false);
const selectedFrequency = ref(props.thread.frequency.id);
const accumulate = ref(props.thread.accumulate ? "true" : "false");
const emit = defineEmits(["setThreadStatus", "updateThread", "deleteThread"]);

const toggleSettings = () => (settingsVisible.value = !settingsVisible.value);
const setThreadStatus = (id, status) => emit("setThreadStatus", id, status);
const deleteThread = (id, status) => emit("deleteThread", id, status);
const saveThraed = () => {
    props.thread.destinations = selectedDestinations.value.filter(
        (d) => d.selected === true
    );
    props.thread.frequency_id = selectedFrequency;
    props.thread.accumulate = accumulate.value ? 1 : 0;
    emit("updateThread", props.thread);
};
const setSelectedDestinations = (destinations) => {
    destinations.forEach((d) => {
        selectedDestinations.value.push({
            id: d.id,
            name: d.name,
            selected: !!props.thread.destinations.find((des) => {
                return des.id == d.id;
            }),
        });
    });
};

if (props.destinations.length) {
    setSelectedDestinations(props.destinations);
} else {
    watch(
        () => props.destinations,
        (destinations) => setSelectedDestinations(destinations)
    );
}
</script>
