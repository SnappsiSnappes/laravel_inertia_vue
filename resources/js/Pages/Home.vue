<script setup>
import { ref, watch } from "vue";
import PaginationLinks from "./Components/PaginationLinks.vue";
import { router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import axios from "axios";
// Formate date
const getDate = (date) => {
    return new Date(date).toLocaleDateString("en-us", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const props = defineProps({
    users: Object,
    searchTerm: String,
    can: Object
});

const deleteUser = (user) => {
    console.log(user.id)
    axios.post(route('delete_user'), { id: user.id })
        .then(response => {
            console.log(response)
            router.reload({ only: ['users'] })

        })

}

const search = ref(props.searchTerm);


watch(
    search,
    debounce(
        (q) => {
            router.get("/", { search: q }, { preserveState: true })
        }, 1000)
);


</script>
<template>

    <Head :title="`${$page.component}`" />

    <div>
        <div class="flex justify-end mb-4">
            <div class="w-1/4-">
                <input type="search" placeholder="Search" v-model="search" />
            </div>
        </div>
        <table>
            <thead>
                <tr class="bg-slate-300">
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                    <th v-if="can.delete_user">Delete</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users.data" :key="user.id">
                    <td>
                        <img class="avatar" :src="user.avatar
                                ? 'storage/' + user.avatar
                                : 'storage/avatars/upload_preview.svg'
                            " alt="avatar" />
                    </td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ getDate(user.created_at) }}</td>
                    <td v-if="can.delete_user">
                        <button @click="deleteUser(user)" class="bg-red-500 w-6 h-6 rounded-full"></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <PaginationLinks :paginator="users" />
    </div>
</template>
