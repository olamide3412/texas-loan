<script setup>
import TextInput from '@/Components/Forms/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useToast } from 'vue-toastification';
import { formatCurrency } from '@/Utils/formatCurrency';

const toast = useToast();
const page = usePage();

// Get the authenticated client
const client = computed(() => page.props.auth.client || {});

// Password change form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const changePassword = () => {
    passwordForm.post(route('client.password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            //toast.success('Password updated successfully');
            passwordForm.reset();
        },
        onError: () => {
            toast.error('Failed to update password. Please check your inputs.');
        },
    });
};
</script>

<template>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <Head title="Client Profile" />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-8 text-white">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="w-24 h-24 rounded-full bg-white bg-opacity-20 p-1 mb-4 md:mb-0">
                        <img
                            v-if="client.client_photo"
                            :src="client.client_photo"
                            alt="Profile Photo"
                            class="w-full h-full rounded-full object-cover"
                        />
                        <div v-else class="w-full h-full rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-600">
                                {{ client.first_name?.[0] }}{{ client.last_name?.[0] }}
                            </span>
                        </div>
                    </div>
                    <div class="md:ml-6 text-center md:text-left">
                        <h1 class="text-2xl font-bold">{{ client.full_name }}</h1>
                        <p class="text-green-100">{{ client.occupation }}</p>
                        <p class="text-green-100 mt-2">{{ client.email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Change Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mt-8 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Change Password</h2>
            </div>

            <div class="px-6 py-6">
                <form @submit.prevent="changePassword">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <TextInput
                            name="current_password"
                            label="Current Password"
                            type="password"
                            v-model="passwordForm.current_password"
                            :message="passwordForm.errors.current_password"
                            :required="true"
                        />

                        <div></div> <!-- Empty column for layout -->

                        <TextInput
                            name="password"
                            label="New Password"
                            type="password"
                            v-model="passwordForm.password"
                            :message="passwordForm.errors.password"
                            :required="true"
                        />

                        <TextInput
                            name="password_confirmation"
                            label="Confirm New Password"
                            type="password"
                            v-model="passwordForm.password_confirmation"
                            :message="passwordForm.errors.password_confirmation"
                            :required="true"
                        />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                            :disabled="passwordForm.processing"
                        >
                            <span v-if="passwordForm.processing">Updating...</span>
                            <span v-else>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
