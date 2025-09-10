<script setup>
import CompanyCover from '../../../images/tricycle.jpg'; // Replace with your actual image path
const laptopImagePath = '/images/products/laptop.png'
</script>

<template>
  <section class="py-12 md:py-20 bg-white">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center gap-10">

      <!-- Left Column: Image -->
      <div
        class="md:w-1/2"
        data-aos="fade-right"
        data-aos-duration="800"
        data-aos-delay="200"
      >
        <img
          :src="laptopImagePath"
          alt="Texas Trust Multidynamic Inspection LTD - Reliable Product Sales & Credit Services"
          width="1024"
          height="879"
          class="w-full h-auto rounded-lg shadow-md object-cover"
        />
      </div>

      <!-- Right Column: Content -->
      <div
        class="md:w-1/2"
        data-aos="fade-left"
        data-aos-duration="800"
        data-aos-delay="200"
      >
        <h5 class="text-blue-600 font-semibold text-sm uppercase mb-2">
          Welcome to Texas Trust Multidynamic Inspection LTD
        </h5>

        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 leading-tight">
          Quality Products, Flexible Credit – Trusted Solutions for Every Need
        </h2>

        <div class="space-y-4 text-gray-600 mb-8">
          <p>
            Texas Trust Multidynamic Inspection LTD offers a wide range of high-demand products — from motorcycles and solar systems to land, electronics, and essential commodities — all available for <strong>cash or credit</strong>.
          </p>
          <p>
            We provide reliable, transparent, and flexible purchasing options, empowering individuals and businesses to acquire what they need today and pay conveniently over time.
          </p>
          <p class="font-bold text-gray-700">
            Shop with confidence. Own today, pay smart tomorrow.
          </p>
        </div>

        <!-- CTA Button -->
        <a
          href="/products"
          class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded transition-transform duration-300 transform hover:scale-105"
        >
          Browse Products
        </a>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Optional: Ensure image maintains aspect ratio */
img {
  aspect-ratio: 4 / 3;
  object-fit: cover;
}
</style>
