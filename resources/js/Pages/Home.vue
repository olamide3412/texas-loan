<script setup>
import HeroSection from '@/Components/Home/HeroSection.vue';
import IntroductionSection from '@/Components/Home/IntroductionSection.vue';
import WhyChooseUsSection from '@/Components/WhyChooseUsSection.vue';
import ServicesSection from '@/Components/ServicesSection.vue';
import Footer from '@/Components/Footer.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

</script>

<template>
    <Head :title=" ` | ${$page.component}` "/>
    <div >
        <ThemeToggle/>
        <HeroSection/>
        <IntroductionSection/>
        <WhyChooseUsSection/>
        <ServicesSection/>
        <Footer/>
    </div>
</template>
