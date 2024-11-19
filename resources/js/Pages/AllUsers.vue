<script setup>
import { ref, watch } from "vue";
import PaginationLinks from "./Components/PaginationLinks.vue";
import { router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import axios from "axios";

// Format date
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
    can: Object,
});

const deleteUser = (user) => {
    console.log(user.id);
    axios.post(route("delete_user"), { id: user.id }).then((response) => {
        console.log(response);
        router.reload({ only: ["users"] });
    });
};

const search = ref(props.searchTerm);
const spinnerFlag = ref(false);

/* 
genius approuch to make spinner visible while typing isnt over yet
basicly it add 1 second to typingTimer variable while input were triggered
*/
let typingTimer; // Timer identifier

const showSpinner = () => {
    clearTimeout(typingTimer); // Clear the previous timer


    spinnerFlag.value = true; //  Show spinner
    typingTimer = setTimeout(() => {
        spinnerFlag.value = false; // Hide the spinner after 1 second of no typing
    }, 1000);
};


watch(
    search,
    debounce((q) => {
        router.get(route('all_users'), { search: q }, { preserveState: true });
    }, 1000)
);
</script>

<template>
    <Head :title="`${$page.component}`" />

    <!-- flash message -->
    <p v-if="$page.props.flash.message" class="p-4 bg-green-200">
        {{ $page.props.flash.message }}
    </p>
    


    <div>
        <!-- search -->
        <div class="flex justify-start mb-4">
            <div class="w-1/4-">
                <input
                    type="search"
                    placeholder="Search"
                    v-model="search"
                    @keyup="showSpinner()"
                />
            </div>

            <!-- spinner -->
            <div id="spinner" class="text-center"  v-show="spinnerFlag">
                <div role="status">
                    <svg
                        class="inline absolute w-8 h-8 text-gray-50 animate-spin dark:text-gray-500 fill-blue-400"
                        viewBox="0 0 100 101"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"
                        />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"
                        />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>

        <!-- table -->
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
                <!-- v-for -->
                <tr v-for="user in users.data" :key="user.id">
                    <td>
                        <img
                            class="avatar"
                            :src="
                                user.avatar
                                    ? 'storage/' + user.avatar
                                    : 'storage/avatars/upload_preview.png'
                            "
                            alt="avatar"
                        />
                    </td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ getDate(user.created_at) }}</td>
                    <td v-if="can.delete_user">
                        <button
                            @click="deleteUser(user)"
                            class="bg-red-500 w-6 h-6 rounded-full"
                        ></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <PaginationLinks :paginator="users" />
    </div>
</template>
