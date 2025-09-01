<script setup>
import ProductImage from '../../images/tricycle.jpg'; // Replace with an actual product image (e.g., motorcycles, rice bags, or solar)
</script>

<template>
  <section class="py-12">
    <div class="container mx-auto flex flex-col md:flex-row items-center px-4">

      <!-- Text Column -->
      <div class="w-full md:w-1/2 mb-10 md:mb-0" data-aos="fade-right">
        <h5 class="text-sm uppercase text-gray-500 dark:text-gray-300 mb-2">
          Why Choose Texas Trust Multidynamic Inspection LTD
        </h5>
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          Quality Products & Flexible Credit Options You Can Rely On
        </h2>
        <p class="text-gray-700 dark:text-gray-200">
          At <strong>Texas Trust Multidynamic Inspection LTD</strong>, we provide a wide range of
          essential products — from <em>motorcycles, bicycles, and keke NAPEP</em> to
          <em>rice, macaroni, spaghetti, fertilizers, and plots of land</em>.
          We also supply <em>laptops, desktops, lithium batteries, solar systems, fridges, freezers, and cars</em>.
          <br><br>
          Whether you want to <strong>buy in cash or on credit</strong>, we ensure reliability, affordability,
          and trust in every transaction. Our flexible debt system makes ownership easier while supporting
          your growth and lifestyle needs.
        </p>
      </div>

      <!-- Image Column -->
      <div class="w-full md:w-1/2" data-aos="fade-left">
        <img
          :src="ProductImage"
          alt="Texas Trust Products"
          class="w-full h-auto rounded-lg shadow-md"
        />
      </div>
    </div>
  </section>
</template>
