<script setup>

</script>

<template>
  <section class="py-10">
    <div class="container mx-auto px-4">
      <!-- Heading -->
      <div data-aos="fade-up">
        <h2 class="text-3xl font-bold text-center mb-4">
          Welcome to Texas Trust Multidynamic Inspection LTD
        </h2>
      </div>
      <div data-aos="fade-up" data-aos-delay="100">
        <h3 class="text-2xl text-center mb-6">
          Your Trusted Partner for Products & Credit Purchases
        </h3>
      </div>
      <div
        data-aos="fade-up"
        data-aos-delay="200"
        class="text-center mb-12 max-w-2xl mx-auto"
      >
        <p>
          Shop motorcycles, food items, fertilizers, electronics, solar systems,
          real estate, and more — pay cash or conveniently buy on credit. We
          value flexibility, trust, and affordability.
        </p>
      </div>

      <!-- Three Feature Boxes -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Flexible Payments -->
        <div
          class="text-center bg-white shadow-lg rounded-xl p-6"
          data-aos="fade-up"
          data-aos-delay="300"
        >
          <div class="mb-4 text-5xl text-green-600">
            <font-awesome-icon :icon="['fas','credit-card']" />
          </div>
          <h3 class="text-xl font-semibold mb-2 text-green-600">
            Flexible Payments
          </h3>
          <p class="text-black dark:text-white">
            Pay upfront or choose our credit plan and spread your payments
            easily.
          </p>
        </div>

        <!-- Verified Products -->
        <div
          class="text-center bg-green-600 text-white shadow-lg rounded-xl p-6"
          data-aos="fade-up"
          data-aos-delay="400"
        >
          <div class="mb-4 text-5xl">
            <font-awesome-icon :icon="['fas','check-circle']" />
          </div>
          <h3 class="text-xl font-semibold mb-2">Verified Products</h3>
          <p>
            All items — from motorcycles to land plots — are verified for
            quality and authenticity.
          </p>
        </div>

        <!-- Fair Pricing -->
        <div
          class="text-center bg-white shadow-lg rounded-xl p-6"
          data-aos="fade-up"
          data-aos-delay="500"
        >
          <div class="mb-4 text-5xl text-green-600">
            <font-awesome-icon :icon="['fas','hand-holding-usd']" />
          </div>
          <h3 class="text-xl font-semibold mb-2 text-green-600">
            Fair Pricing
          </h3>
          <p class="text-black dark:text-white">
            Enjoy competitive prices while maintaining high product standards.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.container {
  max-width: 1200px;
}
</style>
