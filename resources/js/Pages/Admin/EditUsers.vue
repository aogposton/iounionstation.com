<script setup>
import { ref, onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
// import navBar from "../../components/navBar.vue";

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
        <navBar></navBar>

        <div class="container-fluid">
            <div class="row">
                <div class="col-8 offset-2 p-0">
                    <table class="table">
                        <thead>
                            <td>Id</td>
                            <td>email</td>
                            <td>verified</td>
                            <td>Role</td>
                            <td>thread count</td>
                            <td>Delete</td>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.id }}</td>
                                <td>
                                    {{ user.email }}
                                </td>
                                <td>
                                    {{
                                        user.email_verified_at == null
                                            ? "not verified"
                                            : "verified"
                                    }}
                                </td>
                                <td>
                                    <form @submit.prevent="saveChanges(user)">
                                        <select
                                            v-model="user.role_id"
                                            @change="
                                                userChanged[user.id] = true
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
                                </td>
                                <td>
                                    <form @submit.prevent="saveChanges(user)">
                                        <select
                                            v-model="user.tier_id"
                                            @change="
                                                userChanged[user.id] = true
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
                                </td>
                                <td width="40%">
                                    <div
                                        class="form-check"
                                        v-for="feature in features"
                                        :key="feature.id"
                                    >
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="flexSwitchCheckChecked"
                                                @input="
                                                    toggleFeature(feature, user)
                                                "
                                                :checked="
                                                    !!user.features.find(
                                                        (f) =>
                                                            f.id == feature.id
                                                    )
                                                "
                                            />
                                            <label
                                                class="form-check-label"
                                                for="flexSwitchCheckChecked"
                                                >{{ feature.name }}</label
                                            >
                                        </div>
                                    </div>
                                </td>
                                <td>Thread Count: {{ user.threads.length }}</td>
                                <td>
                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        @click="deleteUser(user)"
                                    >
                                        delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
