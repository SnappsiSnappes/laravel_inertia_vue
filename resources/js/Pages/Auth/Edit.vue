<script setup>
import { useForm, usePage,router } from '@inertiajs/vue3'
import TextInput from '../Components/TextInput.vue'
import { onMounted } from 'vue'
import axios from 'axios'

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    avatar: null,
    preview:null
})

const submit = () => {
    console.log(form)
    form.post(route('edit'), {
        preserveScroll: true,
        onSuccess:(res)=>{
            console.log(res)
            router.visit(route('home'));
        },
        onError: () => {
            form.reset('password', 'password_confirmation')
        }
    })

}
const addFile = (e) => {
    form.avatar = e.target.files[0]
    form.preview = URL.createObjectURL(e.target.files[0])
}

onMounted(() => {
    axios.get('/user')
        .then(response => {
            form.name = response.data.name
            form.email = response.data.email
            form.preview = response.data.avatar ? "storage/" + response.data.avatar:null
            console.log(response)
        })
        .catch(error => {
            console.error('Error fetching user data:', error)
        })
})

</script>

<template>

    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1 class="text-center my-4">Update this account 🤖⬇</h1>

        <form @submit.prevent="submit">
            <div class="grid place-items-center ">
                <div class="relative w-28 h-28 rounded-full overflow-hidden border border-slate-300  ">
                    <label for="avatar" class="absolute inset-0 grid content-end cursor-pointer">
                        <span class="bg-white/70 text-center">Avatar</span>
                    </label>
                    <input type="file" id="avatar" @input="addFile" hidden class="">
                    <img v-if="form.preview" :src="form.preview" class="object-cover w-28 h-28">
                    <div v-else class="flex justify-center items-center h-full ">
                        <svg class=" cursor-pointer" 
                            xmlns="http://www.w3.org/2000/svg" height="66px" viewBox="0 0 24 24" width="66px"
                            fill="#000000">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M6 7v3H5v10h16V8h-4.05l-1.83-2H9v1H6zm7 2c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5z"
                                opacity=".3" />
                            <path
                                d="M21 6h-3.17L16 4H9v2h6.12l1.83 2H21v12H5V10H3v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM8 14c0 2.76 2.24 5 5 5s5-2.24 5-5-2.24-5-5-5-5 2.24-5 5zm5-3c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3zM5 9V6h3V4H5V1H3v3H0v2h3v3z" />
                        </svg>
                    </div>
                </div>
                <p>upload avatar</p>
                <p class="error mt-2">{{ form.errors.avatar }}</p>
            </div>


            <TextInput name="name" v-model="form.name" :message="form.errors.name" />

            <TextInput name="email" type="email" v-model="form.email" :message="form.errors.email" />

            <TextInput name="password" type="password" v-model="form.password" :message="form.errors.password" />

            <TextInput name="confirm password" type="password" v-model="form.password_confirmation" />

            <div>
                <div class="text-right">
                <a href="#" class="text-red-400">Delete account</a>
            </div>
                <button class="primary-btn" :disabled="form.processing">
                    Update profile
                </button>
            </div>
        </form>
    </div>
</template>
