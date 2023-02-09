<script>
import Layout from "@/Layouts/Authenticated.vue";
export default {
    layout: Layout,
};
</script>

<script setup>
import { ref, onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";

const userChanged = ref([]);
const toastMessage = ref("");

defineProps({
    users: Object,
    roles: Object,
    tiers: Object,
    features: Object,
});

let toastElList = null;
let toastList = null;

onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});

function toggleFeature(feature, user) {
    router.post(`/admin/edit-user-feature/${user.id}/${feature.id}`, {
        onSuccess: () => {
            router.reload({ only: ["users"] });
            toastMessage.value = `${feature.name} toggled for ${user.name}`;
            toastList[0].show();
        },
    });
}

function saveChanges(user) {
    const newUser = Object.assign({}, user);
    delete newUser.tier;
    delete newUser.role;
    delete newUser.features;

    router.post(`/admin/edit-user/${newUser.id}`, newUser, {
        onSuccess: () => {
            router.reload({ only: ["users"] });
            toastMessage.value = `Successfully updated ${newUser.name}`;
            toastList[0].show();
        },
    });
}

function deleteUser(user) {
    router.delete(`/admin/delete-user/${user.id}`, user, {
        onSuccess: () => {
            router.reload({ only: ["users"] });
            toastMessage.value = `Successfully Delete ${user.name}`;
            toastList[0].show();
        },
    });
}
</script>

<template>
    <div>
        <Head title="Admin users" />
        <div class="container-fluid">
            <div class="row">
                <div class="col p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">Info</div>
                            <div class="col">Role</div>
                            <div class="col">thread count</div>
                            <div class="col">Delete</div>
                        </div>
                        <div
                            class="row card"
                            v-for="user in users"
                            :key="user.id"
                        >
                            <div class="col card-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-2">
                                            id: {{ user.id }}<br />
                                            email: {{ user.email }} <br />
                                            verified:
                                            {{
                                                user.email_verified_at ||
                                                "not verified"
                                            }}
                                        </div>
                                        <div class="col"></div>
                                        <div class="col">
                                            <form
                                                @submit.prevent="
                                                    saveChanges(user)
                                                "
                                            >
                                                <select
                                                    v-model="user.role_id"
                                                    @change="
                                                        userChanged[
                                                            user.id
                                                        ] = true
                                                    "
                                                >
                                                    <option
                                                        v-for="role in roles"
                                                        href="javascript:;"
                                                        :key="role.id"
                                                        class="btn"
                                                        :value="role.id"
                                                    >
                                                        {{ role.name }}
                                                    </option>
                                                </select>
                                                <button
                                                    type="submit"
                                                    class="btn btn-primary"
                                                    v-if="userChanged[user.id]"
                                                >
                                                    Save Changes
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col">
                                            <form
                                                @submit.prevent="
                                                    saveChanges(user)
                                                "
                                            >
                                                <select
                                                    v-model="user.tier_id"
                                                    @change="
                                                        userChanged[
                                                            user.id
                                                        ] = true
                                                    "
                                                >
                                                    <option
                                                        v-for="tier in tiers"
                                                        href="javascript:;"
                                                        :key="tier.id"
                                                        class="btn"
                                                        :value="tier.id"
                                                    >
                                                        {{ tier.name }}
                                                    </option>
                                                </select>
                                                <button
                                                    type="submit"
                                                    class="btn btn-primary"
                                                    v-if="userChanged[user.id]"
                                                >
                                                    Save Changes
                                                </button>
                                            </form>
                                            <div
                                                class="form-check"
                                                v-for="feature in features"
                                                :key="feature.id"
                                            >
                                                <div
                                                    class="form-check form-switch"
                                                >
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="flexSwitchCheckChecked"
                                                        @input="
                                                            toggleFeature(
                                                                feature,
                                                                user
                                                            )
                                                        "
                                                        :checked="
                                                            !!user.features.find(
                                                                (f) =>
                                                                    f.id ==
                                                                    feature.id
                                                            )
                                                        "
                                                    />
                                                    <label
                                                        class="form-check-label"
                                                        for="flexSwitchCheckChecked"
                                                        >{{
                                                            feature.name
                                                        }}</label
                                                    >
                                                </div>
                                            </div>
                                            Thread Count:
                                            {{ user.threads.length }}
                                        </div>
                                        <div class="col">
                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                                @click="deleteUser(user)"
                                            >
                                                delete
                                            </button>
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
                        <strong class="me-auto">Change Successful</strong>
                    </div>
                    <div class="toast-body">{{ toastMessage }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
