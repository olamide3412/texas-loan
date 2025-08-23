<script setup>
import FarmCover from '../../../images/farm-cover.png' // Replace with actual farming image
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
          :src="FarmCover"
          alt="Maibere Ventures Nigeria Ltd - Farming & Livestock"
          width="1024"
          height="879"
          class="w-full h-auto rounded-lg shadow-md"
        />
      </div>

      <!-- Right Column: Content -->
      <div
        class="md:w-1/2"
        data-aos="fade-left"
        data-aos-duration="800"
        data-aos-delay="200"
      >
        <h5 class="text-green-600 font-semibold text-sm uppercase mb-2">
          Welcome to Maibere Ventures Nigeria Ltd
        </h5>

        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 leading-tight">
          Fresh Farm Produce, Livestock & Agricultural Stock at Your Fingertips
        </h2>

        <div class="space-y-4 text-gray-600 mb-8">
          <p>
            Maibere Ventures Nigeria Ltd connects farmers and buyers, offering a trusted
            platform to buy and sell fresh farm produce, quality livestock, and essential
            agricultural products.
          </p>
          <p>
            We partner with verified farmers to ensure you get the freshest products at
            fair prices while supporting local agriculture.
          </p>
          <p class="font-bold">
            Join our marketplace and experience the best of farming, delivered to you.
          </p>
        </div>

        <!-- CTA Button -->
        <a
          href="/products"
          class="inline-block bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded transition-transform duration-300 transform hover:scale-105"
        >
          Browse Products
        </a>
      </div>
    </div>
  </section>
</template>
