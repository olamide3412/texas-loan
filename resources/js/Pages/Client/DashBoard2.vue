<script setup>
import CountCard from '@/Components/Auth/Dashboard/CountCard.vue';
import IconUtils from '@/Utils/icons';

defineProps({
    orders: {
        type: Array,
        required: true
    },
    orderStats: {
        type: Object,
        required: true
    }
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN'
    }).format(amount);
};

const getStatusColor = (status) => {
    const statusColors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
    };
    return statusColors[status.toLowerCase()] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <div class="container mx-auto p-4 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Client Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Welcome back! Here's your order summary</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Order
                </button>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <CountCard
                title="Total Orders"
                :count="orderStats.total_orders.toString()"
                :icon="IconUtils.logIcon()"
                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white"
            />
            <CountCard
                title="Total Spent"
                :count="formatCurrency(orderStats.total_spent)"
                :icon="IconUtils.logIcon()"
                class="bg-gradient-to-r from-green-500 to-green-600 text-white"
            />
            <CountCard
                title="Completed Orders"
                :count="orderStats.completed_orders.toString()"
                :icon="IconUtils.logIcon()"
                class="bg-gradient-to-r from-purple-500 to-purple-600 text-white"
            />
            <CountCard
                title="Pending Orders"
                :count="orderStats.pending_orders.toString()"
                :icon="IconUtils.logIcon()"
                class="bg-gradient-to-r from-orange-500 to-orange-600 text-white"
            />
        </div>

        <!-- Detailed Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Processing Orders</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ orderStats.processing_orders }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rejected Orders</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ orderStats.rejected_orders }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-900/30">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cancelled Orders</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ orderStats.cancelled_orders }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Your most recent order activities</p>
            </div>

            <div class="p-6">
                <div v-if="orders.length === 0" class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="mt-4 text-gray-500 dark:text-gray-400">No orders yet</p>
                    <button class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Place Your First Order
                    </button>
                </div>

                <div v-else class="space-y-4">
                    <div v-for="order in orders" :key="order.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Order #{{ order.order_ref }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ order.created_at }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium" :class="getStatusColor(order.status)">
                                {{ order.status }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Items</p>
                                <p class="font-medium">{{ order.items.length }} items</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Amount</p>
                                <p class="font-medium">{{ formatCurrency(order.total_amount) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Details
                            </button>
                            <button v-if="order.status === 'pending'" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Cancel Order
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="orders.length > 0" class="mt-6 text-center">
                    <button class="text-green-600 hover:text-green-800 font-medium">
                        View All Orders →
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="flex items-center justify-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>New Order</span>
                </button>

                <button class="flex items-center justify-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Update Profile</span>
                </button>

                <button class="flex items-center justify-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Payment History</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient-to-r {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
}
</style>
