<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3'
import TextInput from '../Components/TextInput.vue'
import { onMounted } from 'vue'
import axios from 'axios'

const editForm = useForm({
  name: null,
  email: null,
  password: null,
  password_confirmation: null,
  avatar: null,
  preview: null,
  deleteAvatarNow: null,
})

const deleteForm = useForm({
  id: null
})

const editFormSubmit = () => {
  console.log(editForm)
  editForm.post(route('edit'), {
    preserveScroll: true,
    onSuccess: (res) => {
      console.log(res)
      // router.visit(route('home'), { replace: true, preserveState: false }); // preserveState: false - making page to reload
    },
    onError: () => {
      editForm.reset('password', 'password_confirmation')
    }
  })
}
const DeleteFormSubmit = () => {

  /* if user declines deletion we exit here */
  if (!confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    return;
  }

  deleteForm.post(route('delete_user'), {
    onSuccess: (res) => {
      console.log(res)
      // controller must take us to the home and show flash message, aswell as delete our account
    },
    onError: () => {
      console.error('Error deleting the account')
    }
  })
}


const addFile = (e) => {
  editForm.avatar = e.target.files[0]
  editForm.preview = URL.createObjectURL(e.target.files[0])
}

onMounted(() => {
  axios.get('/user')
    .then(response => {
      editForm.name = response.data.name
      editForm.email = response.data.email
      editForm.preview = response.data.avatar ? "storage/" + response.data.avatar : null
      deleteForm.id = response.data.id
      console.log(response)
    })
    .catch(error => {
      console.error('Error fetching user data:', error)
    })
})

</script>

<template>

  <Head :title="`${$page.component}`" />
  <div class="w-1/4 mx-auto">

    <!-- flash message -->
    <p v-if="$page.props.flash.message" class="text-center p-4 bg-green-200">
        {{ $page.props.flash.message }}
    </p>

    <h1 class="text-center my-4">Update this account 🤖⬇</h1>

    <form @submit.prevent="editFormSubmit">
      <div class="grid place-items-center">
        <div class="relative w-28 h-28 rounded-full overflow-hidden border border-slate-300">

          <!--#!! avatar label -->
          <label for="avatar" class="absolute inset-0 grid content-end cursor-pointer">
            <span class="bg-white/70 text-center">Avatar</span>
          </label>

          <!--#!! avatar input file -->
          <input type="file" id="avatar" @input="addFile" hidden>
          <img v-if="editForm.preview" :src="editForm.preview" class="object-cover w-28 h-28">


          <div v-else class="flex justify-center items-center h-full">
            <svg class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" height="66px" viewBox="0 0 24 24"
              width="66px" fill="#000000">
              <path d="M0 0h24v24H0z" fill="none" />
              <path d="M6 7v3H5v10h16V8h-4.05l-1.83-2H9v1H6zm7 2c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5z"
                opacity=".3" />
              <path
                d="M21 6h-3.17L16 4H9v2h6.12l1.83 2H21v12H5V10H3v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM8 14c0 2.76 2.24 5 5 5s5-2.24 5-5-2.24-5-5-5-5 2.24-5 5zm5-3c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3zM5 9V6h3V4H5V1H3v3H0v2h3v3z" />
            </svg>
          </div>




        </div>
        <p>Upload avatar</p>
        <p class="error mt-2">{{ editForm.errors.avatar }}</p>

        <!--#!! delete avatar btn -->
        <label for="deleteAvatarNow">Delete avatar</label>
        <input id="deleteAvatarNow" type="checkbox" name="deleteAvatarNow" v-model="editForm.deleteAvatarNow">

      </div>

      <TextInput name="name" v-model="editForm.name" :message="editForm.errors.name" />
      <TextInput name="email" type="email" v-model="editForm.email" :message="editForm.errors.email" />
      <TextInput name="password" type="password" v-model="editForm.password" :message="editForm.errors.password" />
      <TextInput name="confirm password" type="password" v-model="editForm.password_confirmation" />

      <div>

        <!-- delete account -->
        <div class="text-right">
          <form @submit.prevent="DeleteFormSubmit">
            <input type="hidden" name="id" value="">
            <button type="submit" class="text-red-400">Delete account</button>
          </form>
        </div>

        <!-- submit editForm -->
        <button class="primary-btn" :disabled="editForm.processing">
          Update profile
        </button>


      </div>
    </form>
  </div>
</template>