<script setup>
import CountCard from '@/Components/Auth/Dashboard/CountCard.vue';
import IconUtils from '@/Utils/icons';
import { usePage } from '@inertiajs/vue3';

const client = usePage().props.auth.client;

defineProps({
    orderStats: {
        type: Object,
        required: true
    },
    orders: {
        type: Array,
        required: true
    }
});



</script>

<template>
  <div class="container mx-auto p-4 pb-24 space-y-8">
    <!-- Dashboard Header -->
    <div class="flex justify-between items-center">
      <h2 class="text-2xl md:text-3xl font-bold text-primary dark:text-gray-200">
        Welcome Back 👋
      </h2>
      <Link
        :href="route('order.create', client.id)"
        class="bg-primary text-white px-4 py-2 rounded-lg shadow hover:bg-primary/90 transition"
      >
        + New Order
      </Link>
    </div>

    <!-- Quick Stats -->
    <div>
      <h3 class="text-lg font-semibold mb-4">Order Overview</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <CountCard title="Pending Orders" :count="orderStats.pending_orders.toString()" :icon="IconUtils.pendingIcon()" />
        <CountCard title="Processing" :count="orderStats.processing_orders.toString()" :icon="IconUtils.processingIcon()" />
        <CountCard title="Completed" :count="orderStats.completed_orders.toString()" :icon="IconUtils.successIcon()" />
        <CountCard title="Rejected" :count="orderStats.rejected_orders.toString()" :icon="IconUtils.rejectIcon()" />
        <CountCard title="Cancelled" :count="orderStats.cancelled_orders.toString()" :icon="IconUtils.cancelIcon()" />
        <CountCard title="Total Orders" :count="orderStats.total_orders.toString()" :icon="IconUtils.logIcon()" />
      </div>
    </div>

    <!-- Spending Summary -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-3">Your Spending</h3>
      <p class="text-2xl font-bold text-primary">
        ₦ {{ Number(orderStats.total_spent).toLocaleString() }}
      </p>
      <p class="text-sm text-gray-500">Total spent on completed orders</p>
    </div>

    <!-- Recent Orders -->
    <div>
    <h3 class="text-lg font-semibold mb-4">Recent Orders</h3>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full text-sm">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-700 text-left">
            <th class="px-4 py-2">Order ID</th>
            <th class="px-4 py-2">Items</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Balance</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2 text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr
            v-for="order in orders"
            :key="order.id"
            class="border-b hover:bg-gray-50 dark:hover:bg-gray-700"
            >
            <td class="px-4 py-2 font-medium">#{{ order.id }}</td>
            <td class="px-4 py-2">
                <ul class="list-disc pl-4">
                <li v-for="item in order.items" :key="item.id">
                    {{ item.product.name }} (x{{ item.quantity }})
                </li>
                </ul>
            </td>
            <td class="px-4 py-2 font-semibold">₦ {{ order.total_price }}</td>
            <td class="px-4 py-2 font-semibold">₦ {{ order.remaining_balance }}</td>
            <td class="px-4 py-2">
                <span
                :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    order.status === 'Completed'
                    ? 'bg-green-100 text-green-600'
                    : order.status === 'Pending'
                    ? 'bg-yellow-100 text-yellow-600'
                    : order.status === 'Processing'
                    ? 'bg-blue-100 text-blue-600'
                    : order.status === 'Rejected'
                    ? 'bg-red-100 text-red-600'
                    : 'bg-gray-200 text-gray-600'
                ]"
                >
                {{ order.status }}
                </span>
            </td>
            <td class="px-4 py-2">
                {{ new Date(order.created_at).toLocaleDateString() }}
            </td>
            <td class="px-4 py-2 text-right">
                <Link
                :href="route('order.show', order.id)"
                class="text-primary hover:underline"
                >
                View
                </Link>
            </td>
            </tr>
            <tr v-if="orders.length === 0">
            <td colspan="7" class="text-center py-6 text-gray-500">
                No orders found.
            </td>
            </tr>
        </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4"
        >
        <div class="flex justify-between items-center mb-2">
            <h4 class="font-semibold">#{{ order.id }}</h4>
            <span
            :class="[
                'px-2 py-1 rounded-full text-xs font-medium',
                order.status === 'Completed'
                ? 'bg-green-100 text-green-600'
                : order.status === 'Pending'
                ? 'bg-yellow-100 text-yellow-600'
                : order.status === 'Processing'
                ? 'bg-blue-100 text-blue-600'
                : order.status === 'Rejected'
                ? 'bg-red-100 text-red-600'
                : 'bg-gray-200 text-gray-600'
            ]"
            >
            {{ order.status }}
            </span>
        </div>
        <p class="text-sm text-gray-500 mb-2">
            Date: {{ new Date(order.created_at).toLocaleDateString() }}
        </p>
        <div class="mb-2">
            <p class="font-medium">Items:</p>
            <ul class="list-disc list-inside text-sm">
            <li v-for="item in order.items" :key="item.id">
                {{ item.product.name }} (x{{ item.quantity }})
            </li>
            </ul>
        </div>
        <div class="flex justify-between text-sm font-medium">
            <p>Total: ₦ {{ order.total_price }}</p>
            <p>Balance: ₦ {{ order.remaining_balance }}</p>
        </div>
        <div class="mt-3 text-right">
            <Link
            :href="route('order.show', order.id)"
            class="text-primary hover:underline text-sm"
            >
            View
            </Link>
        </div>
        </div>

        <p v-if="orders.length === 0" class="text-center py-6 text-gray-500">
        No orders found.
        </p>
    </div>
    </div>


  </div>
</template>
