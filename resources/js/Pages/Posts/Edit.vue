<script setup>
import { useForm } from '@inertiajs/vue3'
import TextInput from '../Components/TextInput.vue'

const props = defineProps({
    post: Object,
})

const form = useForm({
    title: props.post.title,
    body: props.post.body,
})

const submit = () => {
    form.put(route('posts.update', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Post updated successfully')
        },
        onError: () => {
            console.error('Error updating post')
        }
    })
}
</script>

<template>
    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>Edit Post</h1>

        <form @submit.prevent="submit">
            <TextInput name="Title" v-model="form.title" :message="form.errors.title" />

            <TextInput name="Body" type="textarea" v-model="form.body" :message="form.errors.body" />

            <div>
                <button class="primary-btn" :disabled="form.processing">
                    Update Post
                </button>
            </div>
        </form>
    </div>
</template>