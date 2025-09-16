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

            <!-- Profile Details -->
            <div class="px-6 py-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Personal Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Full Name</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.full_name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Email Address</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.email || 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Phone Number</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.phone_number || 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Gender</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.gender || 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date of Birth</h3>
                        <p class="text-gray-900 dark:text-white">
                            {{ client.date_of_birth ? formatDate(client.date_of_birth) : 'Not provided' }}
                        </p>

                    </div>
                </div>

                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Contact Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Residential Address</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.residential_address || 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Local Government</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.local_government || 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">State</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.state || 'Not provided' }}</p>
                    </div>
                </div>

                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Financial Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Annual Income</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.annual_income ? formatCurrency(client.annual_income) : 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Occupation</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.occupation || 'Not provided' }}</p>
                    </div>
                </div>

                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Identification</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">NIN</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.nin || 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">BVN</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.bvn || 'Not provided' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Verification Status</h3>
                        <p class="text-gray-900 dark:text-white">{{ client.verification_status }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
