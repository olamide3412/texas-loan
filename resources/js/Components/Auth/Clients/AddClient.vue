<script setup>
import TextInput from '@/Components/Forms/TextInput.vue'
import SelectInput from '@/Components/Forms/SelectInput.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const emit = defineEmits(['change-active-tab'])


const genderEmuns = usePage().props.enums.genders;
const occupationEnums = usePage().props.enums.occupations;

const previewUrl = ref(null)

const form = useForm({
  // names
  last_name: '',
  first_name: '',
  other_name: '',
  // contact
  email: '',
  phone_number: '',
  // biodata
  date_of_birth: '',
  gender: '',
  // address
  residential_address: '',
  local_government: '',
  state: '',
  occupation: '',
  // IDs
  nin: '',
  bvn: '',
  // finance
  annual_income: '',
  // file
  photo: null,
})

function onPhotoChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  form.photo = file
  // preview
  if (previewUrl.value) URL.revokeObjectURL(previewUrl.value)
  previewUrl.value = URL.createObjectURL(file)
}

const submit = () => {
  // keep commas if user typed them; backend strips them (you already merge)
  form.post(route('client.store'), {
    forceFormData: true, // VERY important for file upload
    onError: () => {
      toast.error('Validation error. Please check the highlighted fields.')
    },
    onSuccess: () => {
      toast.success('Client registered successfully!')
      // cleanup preview + reset form
      if (previewUrl.value) URL.revokeObjectURL(previewUrl.value)
      previewUrl.value = null
      form.reset()
      emit('change-active-tab')
    },
  })
}
</script>

<template>
  <div class="container-xl lg:container m-auto p-0">
    <h1 class="title dark:text-white">Register Client</h1>

    <form @submit.prevent="submit">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

        <!-- Names (OneWord rule server-side; hint the user) -->
        <TextInput
          name="last_name"
          label="Last Name"
          v-model="form.last_name"
          :message="form.errors.last_name"
          :required="true"
          placeholder="e.g. musa"
        />
        <TextInput
          name="first_name"
          label="First Name"
          v-model="form.first_name"
          :message="form.errors.first_name"
          :required="true"
          placeholder="e.g. aliyu"
        />
        <TextInput
          name="other_name"
          label="Other Name (optional)"
          v-model="form.other_name"
          :message="form.errors.other_name"
          placeholder="Single word only"
        />

        <!-- Contact -->
        <TextInput
          name="email"
          label="Email (optional)"
          type="email"
          v-model="form.email"
          :message="form.errors.email"
          placeholder="name@example.com"
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

        <!-- Biodata -->
        <TextInput
          name="date_of_birth"
          label="Date of Birth"
          type="date"
          v-model="form.date_of_birth"
          :message="form.errors.date_of_birth"
          :required="true"
        />
        <SelectInput
          name="gender"
          label="Gender"
          v-model="form.gender"
          :options="genderEmuns"
          :message="form.errors.gender"
          :required="true"
        />

        <!-- Address -->
        <TextInput
          name="residential_address"
          label="Residential Address"
          v-model="form.residential_address"
          :message="form.errors.residential_address"
          :required="true"
        />
        <TextInput
          name="local_government"
          label="Local Government"
          v-model="form.local_government"
          :message="form.errors.local_government"
          :required="true"
        />
        <TextInput
          name="state"
          label="State"
          v-model="form.state"
          :message="form.errors.state"
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

        <!-- IDs -->
        <TextInput
          name="nin"
          label="NIN"
          v-model="form.nin"
          inputmode="numeric"
          :message="form.errors.nin"
          :required="true"
          placeholder="11+ digits"
        />
        <TextInput
          name="bvn"
          label="BVN"
          v-model="form.bvn"
          inputmode="numeric"
          :message="form.errors.bvn"
          :required="true"
          placeholder="11 digits"
        />

        <!-- Finance -->
        <TextInput
          name="annual_income"
          label="Annual Income"
          v-model="form.annual_income"
          inputmode="numeric"
          :message="form.errors.annual_income"
          :required="true"
          placeholder="e.g. 1,200,000"
        />

        <!-- Photo -->
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Photo (PNG/JPG, ≤ 5MB)</label>
          <input
            type="file"
            accept="image/png,image/jpeg"
            @change="onPhotoChange"
            class="block w-full text-sm file:mr-4 file:py-2 file:px-4
                   file:rounded file:border-0 file:bg-gray-100 dark:file:bg-gray-800
                   file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200
                   dark:hover:file:bg-gray-700"
          />
          <p v-if="form.errors.photo" class="text-red-600 text-xs">{{ form.errors.photo }}</p>

          <div v-if="previewUrl" class="mt-2">
            <img :src="previewUrl" alt="Preview" class="h-24 w-24 rounded object-cover ring-1 ring-gray-200 dark:ring-gray-700" />
          </div>
        </div>

      </div>

      <div class="flex justify-center py-6">
        <button class="btn-primary" :disabled="form.processing">
          <span v-if="form.processing">Saving...</span>
          <span v-else>Register Client</span>
        </button>
      </div>

      <p class="text-xs text-gray-500 mt-2">
        Note: First/Last/Other Name must be a single word (no spaces). You may type commas in Annual Income—server will normalize it.
      </p>
    </form>
  </div>
</template>
