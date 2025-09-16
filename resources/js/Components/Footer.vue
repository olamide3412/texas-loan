<script setup>
import Logo from '../../images/farm-logo.png' // Replace with Maibere Ventures logo
</script>

<template>
  <section class="relative pb-16 bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <!-- Optional Background Overlay -->
    <div class="absolute inset-0 bg-black opacity-5 pointer-events-none"></div>

    <div class="relative container mx-auto px-4 py-12">
      <div class="flex flex-col md:flex-row gap-10">

        <!-- Left Column: Logo + Company Description -->
        <div class="md:w-1/3" data-aos="fade-up" data-aos-delay="100">
          <img :src="Logo" alt="Texas Trust Retail & Credit Marketplace Logo" class="h-16 mb-4" />
          <p class="text-sm leading-relaxed">
            Texas Trust Retail & Credit Marketplace provides a reliable platform for Nigerians to shop
            essential products such as household items, electronics, and food supplies. Customers can
            pay <strong>cash or on flexible credit</strong>, making quality goods more accessible to all.
          </p>
        </div>

        <!-- Middle Column: Navigation -->
        <nav class="md:w-1/3" data-aos="fade-up" data-aos-delay="200" aria-label="Site Links">
          <h4 class="font-semibold text-lg mb-4">Quick Links</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:underline">Home</a></li>
            <li><a href="#" class="hover:underline">About Us</a></li>
            <li><a href="#" class="hover:underline">Products</a></li>
            <li><a href="#" class="hover:underline">Credit Options</a></li>
            <li><a href="#" class="hover:underline">How It Works</a></li>
            <li><a href="#" class="hover:underline">Contact Us</a></li>
          </ul>
        </nav>

        <!-- Right Column: Contact Info -->
        <address class="md:w-1/3 not-italic text-sm space-y-2" data-aos="fade-up" data-aos-delay="300">
          <h4 class="font-semibold text-lg mb-4">Contact</h4>
          <div class="flex items-start">
            <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="mr-2 text-primary" />
            <span>No. 25 Retail Plaza, Kano, Nigeria</span>
          </div>
          <div class="flex items-center">
            <font-awesome-icon :icon="['fas', 'phone']" class="mr-2 text-primary" />
            <span>+234 803 3351 418</span>
          </div>
          <div class="flex items-center">
            <font-awesome-icon :icon="['fas', 'envelope']" class="mr-2 text-primary" />
            <a href="mailto:support@texastrust.com" class="hover:underline">support@texastrust.com</a>
          </div>
        </address>
      </div>

      <!-- Divider -->
      <hr class="my-8 border-t border-gray-300 dark:border-gray-700" />

      <!-- Footer Note -->
      <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-600 dark:text-gray-400">
        <div>
          © {{ new Date().getFullYear() }} Texas Trust Retail & Credit Marketplace. All rights reserved.
        </div>
        <div class="flex space-x-4 mt-4 md:mt-0">
          <a href="#" class="hover:underline">Privacy Policy</a>
          <a href="#" class="hover:underline">Terms of Use</a>
        </div>
      </div>
    </div>
  </section>
</template>
