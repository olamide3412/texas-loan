<script setup>
import TextInput from '@/Components/Forms/TextInput.vue'
import SelectInput from '@/Components/Forms/SelectInput.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, nextTick, onMounted  } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const emit = defineEmits(['change-active-tab'])

const genderEnums = usePage().props.enums.genders;
const occupationEnums = usePage().props.enums.occupations;
const stateEnums = usePage().props.enums.states;

const turnstileWidgetId = ref(null);
const isTurnstileLoaded = ref(false);
const turnstileError = ref('');

const previewUrl = ref(null)
const activeSection = ref(1) // Track active section for step navigation
const sections = [
  { id: 1, title: "Basic Info", icon: "👤" },
  { id: 2, title: "Additional Details", icon: "🏠" },
  { id: 3, title: "Verification", icon: "📷" }
]

const form = useForm({
  // Required basic info
  last_name: '',
  first_name: '',
  phone_number: '',
  password: '',
  password_confirmation: '',

  // Optional personal info
  other_name: '',
  email: '',
  date_of_birth: '',
  gender: '',

  // Optional address info
  residential_address: '',
  local_government: '',
  state: '',
  occupation: '',

  // Optional verification info
  nin: '',
  bvn: '',
  annual_income: '',
  photo: null,

  cf_turnstile_response: '',
})

function onPhotoChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  form.photo = file
  // preview
  if (previewUrl.value) URL.revokeObjectURL(previewUrl.value)
  previewUrl.value = URL.createObjectURL(file)
}

// Function to scroll to top
const scrollToTop = () => {
  nextTick(() => {
    // Smooth scroll to top of the form container
    const formContainer = document.querySelector('.container-xl');
    if (formContainer) {
      formContainer.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }

    // Alternative: Scroll to very top of page
    // window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// Navigation functions
const nextSection = () => {
  // Only require basic info for step 1
  if (activeSection.value === 1 && (!form.first_name || !form.last_name || !form.phone_number || !form.email || !form.password || !form.occupation)) {
    toast.error('Please fill in all required basic information')
    return
  }

  if (activeSection.value < sections.length) {
    activeSection.value++
    scrollToTop(); // Scroll to top after changing section
  }
}

const prevSection = () => {
  if (activeSection.value > 1) {
    activeSection.value--
    scrollToTop(); // Scroll to top after changing section
  }
}

// Load Cloudflare Turnstile script
const loadTurnstile = () => {
    if (window.turnstile) {
        renderTurnstile();
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
    script.async = true;
    script.defer = true;

    script.onload = () => {
        isTurnstileLoaded.value = true;
        renderTurnstile();
    };

    script.onerror = () => {
        turnstileError.value = 'Failed to load CAPTCHA. Please refresh the page.';
    };

    document.head.appendChild(script);
};
// Render Turnstile widget
const renderTurnstile = () => {
    if (window.turnstile && document.getElementById('cf-turnstile-widget')) {
        turnstileWidgetId.value = window.turnstile.render('#cf-turnstile-widget', {
            sitekey: usePage().props.turnstileSiteKey,
            callback: (token) => {
                form.cf_turnstile_response = token;
                turnstileError.value = '';
            },
            'expired-callback': () => {
                form.cf_turnstile_response = '';
                turnstileError.value = 'CAPTCHA expired. Please verify again.';
                resetTurnstile();
            },
            'error-callback': () => {
                form.cf_turnstile_response = '';
                turnstileError.value = 'CAPTCHA error. Please try again.';
                resetTurnstile();
            }
        });
    }
};
// Reset Turnstile widget
const resetTurnstile = () => {
    if (window.turnstile && turnstileWidgetId.value) {
        window.turnstile.reset(turnstileWidgetId.value);
    }
};
// Reload Turnstile
const reloadTurnstile = () => {
    resetTurnstile();
    form.cf_turnstile_response = '';
    turnstileError.value = '';
};
// Load Turnstile when component mounts
onMounted(() => {
    loadTurnstile();
});

const submit = () => {
    if (!form.cf_turnstile_response) {
        turnstileError.value = 'Please complete the CAPTCHA verification';
        return;
    }
  // No final validation required since most fields are optional
  form.post(route('client.register.store'), {
    forceFormData: true,
     onFinish: () => {;
        // Reset Turnstile after submission
        resetTurnstile();
    },
    onError: (errors) => {
      //toast.error('Validation error. Please check the highlighted fields.')

         // Show each error with a 800ms delay between them
      Object.values(errors).forEach((error, index) => {
        setTimeout(() => {
          toast.error(error);
        }, 800 * index);
      });

       // Reset Turnstile on error
        if (errors.cf_turnstile_response) {
            resetTurnstile();
            form.cf_turnstile_response = '';
        }

    },
    onSuccess: () => {
      if (previewUrl.value) URL.revokeObjectURL(previewUrl.value)
      previewUrl.value = null
      form.reset()
      activeSection.value = 1
      emit('change-active-tab')
    },
  })
}
</script>

<template>
  <div class="container-xl lg:container m-auto p-5">
    <h1 class="title dark:text-white mb-6">Create Your Account</h1>

    <!-- Progress Indicator -->
    <div class="mb-6">
      <div class="flex justify-between items-center relative">
        <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 transform -translate-y-1/2 -z-10"></div>
        <div
          class="absolute top-1/2 left-0 h-1 bg-blue-500 transform -translate-y-1/2 -z-10 transition-all duration-300"
          :style="{ width: `${((activeSection - 1) / (sections.length - 1)) * 100}%` }"
        ></div>

        <div
          v-for="section in sections"
          :key="section.id"
          class="flex flex-col items-center relative"
          :class="activeSection >= section.id ? 'text-blue-600' : 'text-gray-500'"
        >
          <div
            class="w-8 h-8 rounded-full flex items-center justify-center border-2 text-sm"
            :class="{
              'bg-blue-500 text-white border-blue-500': activeSection >= section.id,
              'bg-white border-gray-300': activeSection < section.id
            }"
          >
            {{ section.icon }}
          </div>
          <span class="text-xs mt-1 font-medium hidden sm:block">
            {{ section.title }}
          </span>
        </div>
      </div>
    </div>

    <form @submit.prevent="submit">
      <!-- Section 1: Basic Information (Required) -->
      <div v-if="activeSection === 1" class="mb-6">
        <h2 class="text-md font-semibold mb-3 flex items-center">
          <span class="mr-2">👤</span> Basic Information
        </h2>
        <p class="text-sm text-gray-500 mb-4">Fields marked with * are required</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3 rounded-lg">
          <TextInput
            name="last_name"
            label="Last Name"
            v-model="form.last_name"
            :message="form.errors.last_name"
            :required="true"
            placeholder="e.g. Musa"
          />
          <TextInput
            name="first_name"
            label="First Name"
            v-model="form.first_name"
            :message="form.errors.first_name"
            :required="true"
            placeholder="e.g. Aliyu"
          />
          <TextInput
            name="other_name"
            label="Other Name"
            v-model="form.other_name"
            :message="form.errors.other_name"
            placeholder="Optional"
          />
          <TextInput
            name="phone_number"
            label="Phone Number"
            type="tel"
            inputmode="numeric"
            v-model="form.phone_number"
            :message="form.errors.phone_number"
            placeholder="11+ digits"
            :required="true"
          />
          <TextInput
            name="email"
            label="Email"
            type="email"
            v-model="form.email"
            :message="form.errors.email"
            placeholder="name@example.com"
            :required="true"
          />
        <SelectInput
            name="occupation"
            label="Occupation"
            v-model="form.occupation"
            :options="occupationEnums"
            :message="form.errors.occupation"
            :required="true"
          />
          <TextInput
            name="password"
            label="Password"
            type="password"
            v-model="form.password"
            :message="form.errors.password"
            :required="true"
            placeholder="Create a secure password"
          />
          <TextInput
            name="password_confirmation"
            label="Confirm Password"
            type="password"
            v-model="form.password_confirmation"
            :message="form.errors.password_confirmation"
            :required="true"
            placeholder="Confirm your password"
          />
        </div>
      </div>

      <!-- Section 2: Additional Details (Optional) -->
      <div v-if="activeSection === 2" class="mb-6">
        <h2 class="text-md font-semibold mb-3 flex items-center">
          <span class="mr-2">🏠</span> Additional Details
        </h2>
        <p class="text-sm text-gray-500 mb-4">All fields in this section are optional</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3 rounded-lg">
          <TextInput
            name="date_of_birth"
            label="Date of Birth"
            type="date"
            v-model="form.date_of_birth"
            :message="form.errors.date_of_birth"
          />
          <SelectInput
            name="gender"
            label="Gender"
            v-model="form.gender"
            :options="genderEnums"
            :message="form.errors.gender"
          />
          <TextInput
            name="residential_address"
            label="Residential Address"
            v-model="form.residential_address"
            :message="form.errors.residential_address"
            class="md:col-span-2"
          />
          <TextInput
            name="local_government"
            label="Local Government"
            v-model="form.local_government"
            :message="form.errors.local_government"
          />
          <SelectInput
            name="state"
            label="State"
            v-model="form.state"
            :options="stateEnums"
            :message="form.errors.state"
          />

        </div>
      </div>

      <!-- Section 3: Verification (Optional) -->
      <div v-if="activeSection === 3" class="mb-6">
        <h2 class="text-md font-semibold mb-3 flex items-center">
          <span class="mr-2">📷</span> Verification
        </h2>
        <p class="text-sm text-gray-500 mb-4">All fields in this section are optional. These help with faster approval.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3 rounded-lg">
          <TextInput
            name="nin"
            label="NIN"
            v-model="form.nin"
            inputmode="numeric"
            :message="form.errors.nin"
            placeholder="11 digits (optional)"
          />
          <TextInput
            name="bvn"
            label="BVN"
            v-model="form.bvn"
            inputmode="numeric"
            :message="form.errors.bvn"
            placeholder="11 digits (optional)"
          />
          <TextInput
            name="annual_income"
            label="Annual Income"
            v-model="form.annual_income"
            inputmode="numeric"
            :message="form.errors.annual_income"
            placeholder="e.g. 1200000 (optional)"
            class="md:col-span-2"
          />

          <div class="md:col-span-2">
            <label class="text-sm font-medium block mb-2">Photo (optional, PNG/JPG, ≤ 5MB)</label>
            <input
              type="file"
              accept="image/png,image/jpeg"
              @change="onPhotoChange"
              class="block w-full text-sm file:mr-4 file:py-2 file:px-4
                    file:rounded file:border-0 file:bg-gray-100 dark:file:bg-gray-800
                    file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200
                    dark:hover:file:bg-gray-700"
            />
            <p v-if="form.errors.photo" class="text-red-600 text-xs mt-1">{{ form.errors.photo }}</p>

            <div v-if="previewUrl" class="mt-3 flex flex-col items-center">
              <img :src="previewUrl" alt="Preview" class="h-24 w-24 rounded object-cover ring-2 ring-gray-300" />
              <p class="text-xs text-gray-500 mt-1">Image Preview</p>
            </div>
          </div>

        </div>

      </div>

       <div class=" px-4">
            <!-- Cloudflare Turnstile Widget -->
            <div class="flex justify-left">
                <div id="cf-turnstile-widget" class="cf-turnstile"></div>
            </div>
            <div v-if="turnstileError" class="text-red-600 text-sm text-center">
                {{ turnstileError }}
            </div>
            <div v-if="form.errors.cf_turnstile_response" class="text-red-600 text-sm text-center">
                {{ form.errors.cf_turnstile_response }}
            </div>
            <div class=" text-left">
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500" @click.prevent="reloadTurnstile">
                    Reload CAPTCHA
                </a>
            </div>
        </div>

      <!-- Navigation Buttons -->
      <div class="flex justify-between pt-4">
        <button
          v-if="activeSection > 1"
          @click="prevSection"
          class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm"
          type="button"
        >
          ← Previous
        </button>
        <div v-else></div> <!-- Empty div for spacing -->

        <button
          v-if="activeSection < sections.length"
          @click="nextSection"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm"
          type="button"
        >
          Next →
        </button>

        <button
          v-if="activeSection === sections.length"
          class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm"
          :disabled="form.processing"
          type="submit"
        >
          <span v-if="form.processing">Creating Account...</span>
          <span v-else>Create Account</span>
        </button>
      </div>


      <p class="text-xs text-gray-500 mt-3">
        You can complete your profile later. Providing more information helps with faster verification.
      </p>
    </form>
  </div>
</template>

<style scoped>
.container-xl {
  max-width: 1200px;
}
</style>
