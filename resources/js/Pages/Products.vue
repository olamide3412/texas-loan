<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatCurrency } from '@/Utils/formatCurrency';

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    }
});

const { auth } = usePage().props;
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <Head title="Products" />

        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Our Products</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Browse our collection of high-quality products</p>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
                >
                    <!-- Product Image -->
                    <div class="h-48 bg-gray-200 dark:bg-gray-700 relative">
                        <img
                            v-if="product.photo"
                            :src="product.product_photo"
                            :alt="product.name"
                            class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>

                        <!-- Availability Badge -->
                        <span
                            class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full"
                            :class="product.is_available ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'"
                        >
                            {{ product.is_available ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ product.name }}</h3>

                        <p v-if="product.description" class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-3">
                            {{ product.description }}
                        </p>

                        <div class="flex items-center justify-between mt-4">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ formatCurrency(product.price) }}
                            </span>

                            <span class=" hidden text-sm text-gray-500 dark:text-gray-400">
                                {{ product.quantity }} available
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons (for authenticated users) -->
                    <div v-if="auth.user" class=" hidden px-4 pb-4">
                        <button
                            :disabled="!product.is_available || product.quantity === 0"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-2 px-4 rounded-md transition-colors duration-200"
                        >
                            Add to Cart
                        </button>
                    </div>

                    <!-- Guest Message -->
                    <div v-else class="px-4 pb-4">
                        <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-2">
                            <Link
                                :href="route('login')"
                                class="text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                Sign in
                            </Link> to purchase
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="products.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-16" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products available</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check back later for new products.</p>
            </div>
        </main>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
