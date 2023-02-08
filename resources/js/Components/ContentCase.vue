<template>
    <div class="box border-black">
        <div class="px-6 pt-4 pb-2">
            <div class="d-flex">
                <button @click="rejectContent(result.id, 'Rejected')">
                    reject
                </button>
                <button @click="makeThreadWithStatus(result.id, 'watch')">
                    watch
                </button>
                <button @click="makeThreadWithStatus(result.id, 'save')">
                    save
                </button>
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="">
                <a target="_blank" rel="noopener noreferrer" :href="result.link"
                    >{{ result.title }}
                </a>
            </div>
            <p>
                {{ result.body }}
            </p>
        </div>
    </div>
</template>
<script setup>
import { ref } from "vue";
const emit = defineEmits(["rejectContent", "makeThreadWithStatus"]);
const props = defineProps(["result"]);

const makeThreadWithStatus = (id, status) => {
    emit("makeThreadWithStatus", id, status);
};

const rejectContent = (id) => {
    emit("rejectContent", id);
};

const result = ref(props.result);
</script>
