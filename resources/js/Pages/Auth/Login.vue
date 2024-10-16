<script setup>
import { useForm } from '@inertiajs/vue3'
import TextInput from '../Components/TextInput.vue'

const form = useForm({
    email: null,
    password: null,
    remember: null
})

const submit = () => {
    console.log(form)
    form.post( '#', {
        preserveScroll: true,
        onSuccess:()=>{
            console.log('suscces')
        },
        onError: () => {
            form.reset('password',)
        }
    })
}
</script>

<template>

    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto ">
        <h1>Login an account</h1>
        <form @submit.prevent="submit">
            <TextInput name="email" type="email" v-model="form.email" :message="form.errors.email" />

            <TextInput name="password" type="password" v-model="form.password" :message="form.errors.password" />

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <label for="remember">remember me</label>
                    <input type="checkbox" id="remember" v-model="form.remember">
                </div>
                <p class="text-stale-600 ">
                    Need an account?
                    <a :href="route('register')" class="text-blue-500 border  bg-blue-200">Register</a>
                </p>
            </div>

            <div>

                <button class="primary-btn" :disabled="form.processing">
                    Login
                </button>
            </div>
        </form>
    </div>
</template>
