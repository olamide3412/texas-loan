<script setup>
import { ref } from 'vue'
import FaqItem from './FaqItem.vue'

// FAQ Data for Texas Trust Multidynamic Inspection LTD
const faqs = [
  {
    question: "What does Texas Trust Multidynamic Inspection LTD offer?",
    answer: "We provide a trusted retail and credit marketplace where you can buy motorcycles, food items, electronics, land, solar systems, and more — either on cash or on credit."
  },
  {
    question: "Can I buy products on credit?",
    answer: "Yes, we offer flexible purchase options. Customers can buy products on cash or credit, depending on their eligibility and agreement terms."
  },
  {
    question: "How do I order products?",
    answer: "Browse our catalog, add items like motorcycles, rice, laptops, or land to your cart, and place an order. You can choose pickup or request delivery."
  },
  {
    question: "Do you offer delivery services?",
    answer: "Yes, we partner with reliable logistics providers to deliver products nationwide."
  },
  {
    question: "Which products are available?",
    answer: "We sell motorcycles, bicycles, tricycles (Keke NAPEP), rice, pasta, fertilizers, plots of land, laptops, batteries, solar systems, fridges, freezers, and cars."
  }
]
</script>

<template>
  <section class="bg-white py-16" data-aos="fade-up" data-aos-duration="800">
    <div class="container mx-auto px-4">
      <!-- Section Title -->
      <div class="text-center mb-12">
        <h3 class="text-3xl font-bold text-gray-800">Frequently Asked Questions</h3>
        <div class="w-20 h-1 bg-green-600 mx-auto my-3"></div>
      </div>

      <div class="flex flex-col lg:flex-row gap-12">
        <!-- FAQ Accordion -->
        <div class="lg:w-1/2 space-y-4">
          <FaqItem
            v-for="(faq, index) in faqs"
            :key="index"
            :question="faq.question"
            :answer="faq.answer"
          />
        </div>

        <!-- Call to Action -->
        <div class="lg:w-1/2 bg-gray-50 text-gray-700 p-8 rounded-lg shadow-md">
          <h4 class="text-2xl font-semibold mb-4">Need More Help?</h4>
          <p class="mb-2">Our customer service team is ready to assist with any questions about cash or credit purchases.</p>
          <p class="font-bold text-lg mb-6">
            Email:
            <a href="mailto:info@texastrust.com" class="text-green-600 underline">
              info@texastrust.com
            </a>
          </p>
          <a
            href="/contact"
            class="inline-block bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded transition-transform transform hover:scale-105"
          >
            Contact Us
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
