<script setup>
import { ref } from 'vue';

const products = [
  {
    icon: { type: 'fas', name: 'motorcycle' },
    title: "Motorcycles",
    description: "Durable motorcycles like CG 125, Bajaj, and Boxer available for outright purchase or on debt repayment plans.",
    link: '/products/motorcycles',
  },
  {
    icon: { type: 'fas', name: 'car' },
    title: "Keke & Bicycles",
    description: "Keke NAPEP and bicycles for personal or commercial use, with flexible debt payment options.",
    link: '/products/keke-bicycle',
  },
  {
    icon: { type: 'fas', name: 'box' },
    title: "Food Items",
    description: "Rice (25kg & 50kg), Macaroni & Spaghetti cartons supplied at affordable rates on credit or cash.",
    link: '/products/food-items',
  },
  {
    icon: { type: 'fas', name: 'seedling' },
    title: "Fertilizers",
    description: "Top-grade NPK 50kg and Urea 50kg fertilizers to boost productivity, available for sale on flexible terms.",
    link: '/products/fertilizers',
  },
  {
    icon: { type: 'fas', name: 'laptop' },
    title: "Electronics",
    description: "HP Laptops, Desktops, Fridges, Freezers, and Solar Systems available with installment payment options.",
    link: '/products/electronics',
  },
  {
    icon: { type: 'fas', name: 'battery-full' },
    title: "Lithium Batteries",
    description: "Reliable Lithium Batteries (5KVA, 7KVA, 10KVA) for homes and businesses with debt purchase options.",
    link: '/products/lithium-batteries',
  },
  {
    icon: { type: 'fas', name: 'house' },
    title: "Lands & Properties",
    description: "Plots of land (50x70, 70x70, 70x100) available for outright purchase or on installment plans.",
    link: '/products/lands',
  },
];

const scrollContainer = ref(null);

const scrollLeft = () => {
  scrollContainer.value.scrollBy({ left: -300, behavior: 'smooth' });
};

const scrollRight = () => {
  scrollContainer.value.scrollBy({ left: 300, behavior: 'smooth' });
};
</script>

<template>
  <section class="py-16 bg-primary">
    <div class="max-w-5xl mx-auto text-center px-0 md:px-2">
      <h5 class="text-lg font-medium text-white" data-aos="fade-left">
        Our Products
      </h5>
      <h2
        class="text-3xl font-bold text-gray-100 mt-2"
        data-aos="fade-right"
        data-aos-delay="100"
      >
        What Texas Trust Multidynamic Inspection LTD Offers
      </h2>
      <p class="mt-4 text-gray-50" data-aos="fade-up" data-aos-delay="200">
        We provide motorcycles, vehicles, food items, fertilizers, electronics,
        solar systems, batteries, and land for outright purchase or on flexible
        debt repayment plans.
      </p>

      <!-- Horizontal scroll container with cards -->
      <div class="relative mt-10">
        <!-- Left Arrow -->
        <button
          @click="scrollLeft"
          class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-gray-800 p-2 rounded-full shadow hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          ◀
        </button>

        <!-- Scrollable cards -->
        <div
          ref="scrollContainer"
          class="flex gap-6 overflow-x-auto scroll-smooth whitespace-nowrap px-10 py-2 scrollbar-hide"
        >
          <div
            v-for="(product, index) in products"
            :key="index"
            class="min-w-[280px] bg-gray-100 dark:bg-gray-900 p-6 rounded-lg shadow transition-transform duration-500 hover:scale-105 hover:shadow-xl whitespace-normal break-words"
            data-aos="zoom-in"
            :data-aos-delay="300 + index * 100"
          >
            <div class="text-4xl text-primary mb-4">
              <font-awesome-icon :icon="[product.icon.type, product.icon.name]" />
            </div>
            <h3 class="text-xl font-semibold mb-2">{{ product.title }}</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              {{ product.description }}
            </p>
            <a
              :href="product.link"
              class="inline-block bg-primary text-white px-4 py-2 rounded hover:bg-secondary-700 transition"
            >
              View Products
            </a>
          </div>
        </div>

        <!-- Right Arrow -->
        <button
          @click="scrollRight"
          class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-gray-800 p-2 rounded-full shadow hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          ▶
        </button>
      </div>
    </div>
  </section>
</template>
