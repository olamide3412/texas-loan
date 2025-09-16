<script setup>

defineEmits(['close']);
const liClasses = 'block px-2 py-0 text-gray-700 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-700';
</script>

<template>
  <li>
    <Link :href="route('dashboard')" :class="liClasses" @click="$emit('close')">
      Dashboard
    </Link>
  </li>
  <li>
    <Link :href="route('client.index')" :class="liClasses" @click="$emit('close')">
      Clients
    </Link>
  </li>
  <li>
    <Link :href="route('product.index')" :class="liClasses" @click="$emit('close')">
      Manage Products
    </Link>
  </li>
  <li>
    <Link :href="route('order.index')" :class="liClasses" @click="$emit('close')">
      Orders
    </Link>
  </li>
  <li>
    <Link :href="route('staff.index')" :class="liClasses" @click="$emit('close')">
      Staff
    </Link>
  </li>
  <li>
    <Link :href="route('log.index')" :class="liClasses" @click="$emit('close')">
      Logs
    </Link>
  </li>
  <li>
    <Link :href="route('reports.payments')" :class="liClasses" @click="$emit('close')">
      Report
    </Link>
  </li>
  <li>
    <Link :href="route('staff.profile')" :class="liClasses" @click="$emit('close')">
      Profile
    </Link>
  </li>
  <li>
    <Link
      :href="route('logout')"
      as="button"
      method="post"
      class="block px-4 py-2 text-red-500 hover:bg-gray-100 w-full dark:hover:bg-gray-700"
      @click="$emit('close')"
    >
      Logout
    </Link>
  </li>
</template>
