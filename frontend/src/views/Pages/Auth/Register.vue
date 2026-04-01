<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import GuestLayout from '../../Layouts/GuestLayout.vue';
import InputError from '../../Components/InputError.vue';
import InputLabel from '../../Components/InputLabel.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import TextInput from '../../Components/TextInput.vue';

const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const errors = ref({});
const processing = ref(false);

const submit = async () => {
  processing.value = true;
  errors.value = {};

  try {
    const response = await axios.post('/api/register', form);

    if (response.status === 200 || response.status === 201) {
      await router.push('/dashboard');
    }
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {};
    } else {
      console.error('Registration error:', error);
      if (error.response) {
        console.error('Error response:', error.response.data);
        alert(`Error: ${error.response.data.message || 'An error occurred during registration'}`);
      } else {
        alert('An error occurred during registration. Please try again.');
      }
    }
  } finally {
    processing.value = false;
    form.password = '';
    form.password_confirmation = '';
  }
};
</script>

<template>
  <GuestLayout>
    <div class="max-w-md mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6">Register</h2>
      <form @submit.prevent="submit">
        <!-- Name Field -->
        <div class="mb-4">
          <InputLabel for="name" value="Name" />

          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
            autocomplete="name"
          />

          <InputError class="mt-2" :message="errors.name ? errors.name[0] : ''" />
        </div>

        <!-- Email Field -->
        <div class="mt-4">
          <InputLabel for="email" value="Email" />

          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autocomplete="username"
          />

          <InputError class="mt-2" :message="errors.email ? errors.email[0] : ''" />
        </div>

        <!-- Password Field -->
        <div class="mt-4">
          <InputLabel for="password" value="Password" />

          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="new-password"
          />

          <InputError class="mt-2" :message="errors.password ? errors.password[0] : ''" />
        </div>

        <!-- Confirm Password Field -->
        <div class="mt-4">
          <InputLabel
            for="password_confirmation"
            value="Confirm Password"
          />

          <TextInput
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
          />

          <InputError
            class="mt-2"
            :message="errors.password_confirmation ? errors.password_confirmation[0] : ''"
          />
        </div>

        <!-- Actions -->
        <div class="mt-4 flex items-center justify-end">
          <router-link
            to="/login"
            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Already registered?
          </router-link>

          <PrimaryButton
            class="ms-4"
            :class="{ 'opacity-25': processing }"
            :disabled="processing"
          >
            Register
          </PrimaryButton>
        </div>
      </form>
    </div>
  </GuestLayout>
</template>
