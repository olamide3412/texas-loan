<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

// Import your images (make sure to add these images to your project)
import HeroImage1 from '../../../images/products/laptop.png';
import HeroImage2 from '../../../images/products/car.png'; // Add this image
import HeroImage3 from '../../../images/products/solar.png'; // Add this image
import HeroImage4 from '../../../images/products/bike.png'; // Add this image

// Array of hero images
const heroImages = [
  HeroImage1,
  HeroImage2,
  HeroImage3,
  HeroImage4
];

// Current image index for slider
const currentImageIndex = ref(0);

// Auto slide functionality
let slideInterval;

const startSlider = () => {
  slideInterval = setInterval(() => {
    nextImage();
  }, 4000); // Change image every 4 seconds
};

const nextImage = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % heroImages.length;
};

const prevImage = () => {
  currentImageIndex.value = (currentImageIndex.value - 1 + heroImages.length) % heroImages.length;
};

const goToImage = (index) => {
  currentImageIndex.value = index;
};

// Start slider on component mount
onMounted(() => {
  startSlider();
});

// Clean up interval on component unmount
onUnmounted(() => {
  clearInterval(slideInterval);
});
</script>

<template>
  <!-- ✅ Mobile View -->
  <section class="block md:hidden px-4 py-6">
    <!-- Image Slider -->
    <div data-aos="zoom-out" data-aos-delay="500" data-aos-duration="1000" class="mb-6 relative overflow-hidden rounded-lg">
      <div class="relative h-64 w-full">
        <div
          v-for="(image, index) in heroImages"
          :key="index"
          class="absolute inset-0 w-full h-full transition-transform duration-700 ease-in-out"
          :class="{
            'translate-x-0': index === currentImageIndex,
            'translate-x-full': index > currentImageIndex,
            '-translate-x-full': index < currentImageIndex
          }"
        >
          <img :src="image" :alt="'Hero Image ' + (index + 1)" class="w-full h-full object-cover" />
        </div>
      </div>

      <!-- Navigation Dots -->
      <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
        <button
          v-for="(_, index) in heroImages"
          :key="index"
          @click="goToImage(index)"
          class="w-3 h-3 rounded-full transition-all duration-300"
          :class="{
            'bg-green-600': index === currentImageIndex,
            'bg-white bg-opacity-50': index !== currentImageIndex
          }"
          :aria-label="'Go to slide ' + (index + 1)"
        />
      </div>

      <!-- Navigation Arrows -->
      <button
        @click="prevImage"
        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full"
        aria-label="Previous image"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <button
        @click="nextImage"
        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full"
        aria-label="Next image"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Text Below -->
    <div data-aos="fade-up" data-aos-duration="1000" data-aos-delay="0" class="space-y-4">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center">
        Shop Products Cash or On Credit
      </h1>
      <p class="text-base text-gray-600 dark:text-gray-300 text-center">
        Texas Trust Multidynamic Inspection LTD — your one‑stop shop for motorcycles, food items, fertilizers, electronics, solar systems, real estate and more.
      </p>
      <div class="flex flex-col space-y-3 px-5 text-center">
        <Link href="/products" class="bg-green-600 text-white px-6 py-3 rounded-full shadow hover:bg-green-700 transition">
          Browse Products
        </Link>
        <Link href="/credit" class="border border-green-600 text-green-600 px-6 py-3 rounded-full hover:bg-green-600 hover:text-white transition dark:bg-gray-300">
          Buy on Credit
        </Link>
      </div>
    </div>
  </section>

  <!-- ✅ Desktop View -->
  <section class="hidden md:flex min-h-screen items-center justify-between px-16 py-0">
    <!-- Text Left -->
    <div data-aos="fade-up" data-aos-duration="1000" data-aos-delay="0" class="w-1/2 space-y-6 other-element">
      <h1 class="text-5xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
        Flexible Shopping for Every Need
      </h1>
      <p class="text-lg text-gray-600 dark:text-gray-300">
        Motorcycles, rice, fertilizers, laptops, batteries, solar systems, fridges, freezers, land plots — all available for cash or on credit.
      </p>
      <div class="flex space-x-4">
        <Link href="/products" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 transition">
          Browse Products
        </Link>
        <Link href="/credit" class="border border-green-600 text-green-600 px-6 py-3 rounded-lg hover:bg-green-600 hover:text-white transition dark:bg-gray-300">
          Buy on Credit
        </Link>
      </div>
    </div>

    <!-- Image Slider Right -->
    <div data-aos="zoom-out" data-aos-delay="500" data-aos-duration="1000" data-aos-anchor=".other-element" class="w-1/2 flex justify-center">
      <div class="relative w-full max-w-md overflow-hidden rounded-lg">
        <div class="relative h-96">
          <div
            v-for="(image, index) in heroImages"
            :key="index"
            class="absolute inset-0 w-full h-full transition-transform duration-700 ease-in-out"
            :class="{
              'translate-x-0': index === currentImageIndex,
              'translate-x-full': index > currentImageIndex,
              '-translate-x-full': index < currentImageIndex
            }"
          >
            <img :src="image" :alt="'Hero Image ' + (index + 1)" class="w-full h-full object-cover rounded-lg custom-bounce" />
          </div>
        </div>

        <!-- Navigation Dots -->
        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
          <button
            v-for="(_, index) in heroImages"
            :key="index"
            @click="goToImage(index)"
            class="w-3 h-3 rounded-full transition-all duration-300"
            :class="{
              'bg-green-600': index === currentImageIndex,
              'bg-white bg-opacity-50': index !== currentImageIndex
            }"
            :aria-label="'Go to slide ' + (index + 1)"
          />
        </div>

        <!-- Navigation Arrows -->
        <button
          @click="prevImage"
          class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full"
          aria-label="Previous image"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          @click="nextImage"
          class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full"
          aria-label="Next image"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
  </section>
</template>

<style scoped>
@keyframes small-bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.custom-bounce {
  animation: small-bounce 1s infinite;
}

/* Smooth transitions for slider */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.7s ease-in-out;
}
</style>
