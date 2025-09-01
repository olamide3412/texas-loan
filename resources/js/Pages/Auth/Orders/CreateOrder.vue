<script setup>
import { reactive, ref, computed, onMounted } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import TextInput from "@/Components/Forms/TextInput.vue";
import SelectInput from "@/Components/Forms/SelectInput.vue";
import { useToast } from 'vue-toastification'

const props = defineProps({
  client: Object,       // passed from controller (the client profile)
  products: Array       // passed from controller (all available products)
});

// Payment types from enum (sync with backend)
const paymentTypes = [
  { value: "instant", label: "Instant" },
  { value: "installment", label: "Installment" },
  { value: "on-site-collection", label: "On-Site Collection" }
];

const frequencies = [
  { value: "monthly", label: "Monthly" },
  { value: "weekly", label: "Weekly" }
];

const toast = useToast();

// Cart state
const cart = ref([]);
const searchQuery = ref('');

// Guarantors management
const guarantors = ref([]);

// Filter products based on search
const filteredProducts = computed(() => {
  if (!searchQuery.value) return props.products;
  return props.products.filter(product =>
    product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Add product to cart
const addToCart = (product) => {
  const existingItem = cart.value.find(item => item.id === product.id);
  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.value.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1
    });
  }
  toast.success(`${product.name} added to cart`);
};

// Remove product from cart
const removeFromCart = (productId) => {
  const index = cart.value.findIndex(item => item.id === productId);
  if (index !== -1) {
    cart.value.splice(index, 1);
    toast.info('Product removed from cart');
  }
};

// Update quantity in cart
const updateQuantity = (productId, newQuantity) => {
  const item = cart.value.find(item => item.id === productId);
  if (item) {
    if (newQuantity <= 0) {
      removeFromCart(productId);
    } else {
      item.quantity = newQuantity;
    }
  }
};

// Add a new guarantor
const addGuarantor = () => {
  guarantors.value.push({
    name: '',
    phone: '',
    bvn: '',
    nin: '',
    relationship: '',
    address: ''
  });
};

// Remove a guarantor
const removeGuarantor = (index) => {
  guarantors.value.splice(index, 1);
};

// Convert guarantors to JSON format before submitting
const formatGuarantorsForSubmission = () => {
  const formattedGuarantors = {};

  guarantors.value.forEach((guarantor, index) => {
    // Only include guarantors with at least a name
    if (guarantor.name.trim()) {
      formattedGuarantors[index + 1] = {
        name: guarantor.name,
        phone: guarantor.phone,
        bvn: guarantor.bvn,
        nin: guarantor.nin,
        relationship: guarantor.relationship,
        address: guarantor.address
      };
    }
  });

  return Object.keys(formattedGuarantors).length > 0
    ? JSON.stringify(formattedGuarantors)
    : null;
};

// form state
const form = useForm({
  client_id: props.client.id,
  products: [],
  payment_type: "instant",
  repayment_frequency: "monthly",
  repayment_term: 1,
  employment: {
    monthly_salary: "",
    department: "",
    work_number_id: "",
    employer: "",
    address: "",
    guarantors: ""
  }
});

// Calculate total
const totalPrice = computed(() => {
  return cart.value.reduce((sum, item) => {
    return sum + (item.quantity * item.price);
  }, 0);
});

// Check if cart is empty
const isCartEmpty = computed(() => cart.value.length === 0);

// Submit
const submit = () => {
  // Prepare products data from cart
  form.products = cart.value.map(item => ({
    id: item.id,
    quantity: item.quantity
  }));

  // Format guarantors as JSON
  form.employment.guarantors = formatGuarantorsForSubmission();

  // Validate if cart is empty
  if (isCartEmpty.value) {
    toast.error('Please add at least one product to the cart');
    return;
  }

  form.post(route("order.store"), {
    onSuccess: () => {
      form.reset();
      cart.value = []; // Clear cart after successful order
      guarantors.value = []; // Clear guarantors
      toast.success('Order created successfully!!!.');
    },
    onError: () => {
      toast.error('Validation error. Please check the highlighted fields.');
    },
  });
};
</script>

<template>
  <div class="p-6 bg-white dark:bg-gray-900 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Create Order for {{ client.full_name }}</h2>

    <!-- Product Search and Selection -->
    <div class="mb-6">
      <h3 class="font-semibold mb-3">Add Products to Cart</h3>

      <!-- Search Bar -->
     <div class="mb-4 flex justify-center w-full px-4">
        <div class="w-full max-w-md">
            <TextInput
            label="Search Products"
            v-model="searchQuery"
            placeholder="Type to search products..."
            class="w-full"
            />
        </div>
    </div>

      <!-- Product List -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="border p-3 rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition"
          @click="addToCart(product)"
        >
          <p class="font-medium">{{ product.name }}</p>
          <p class="text-sm text-gray-600">₦{{ product.price }}</p>
          <p class="text-xs text-blue-500 mt-1">Click to add to cart</p>
        </div>
      </div>
    </div>

    <!-- Cart -->
    <div class="space-y-4 mb-6" v-if="!isCartEmpty">
      <h3 class="font-semibold">Shopping Cart</h3>

      <div v-for="item in cart" :key="item.id" class="flex items-center justify-between border p-3 rounded">
        <div class="flex-1">
          <p class="font-medium">{{ item.name }}</p>
          <p class="text-sm text-gray-600">₦{{ item.price }} each</p>
        </div>

        <div class="flex items-center space-x-2">
          <button
            @click="updateQuantity(item.id, item.quantity - 1)"
            class="px-2 py-1 bg-gray-200 rounded"
          >-</button>

          <input
            type="number"
            min="1"
            v-model.number="item.quantity"
            @change="updateQuantity(item.id, item.quantity)"
            class="w-16 border rounded p-1 text-center"
          />

          <button
            @click="updateQuantity(item.id, item.quantity + 1)"
            class="px-2 py-1 bg-gray-200 rounded"
          >+</button>

          <button
            @click="removeFromCart(item.id)"
            class="ml-3 px-2 py-1 bg-red-100 text-red-700 rounded text-sm"
          >Remove</button>
        </div>

        <div class="ml-4 text-right">
          <p class="font-medium">₦{{ item.quantity * item.price }}</p>
        </div>
      </div>

      <div class="flex justify-between items-center mt-4 pt-4 border-t">
        <p class="font-bold">Total: ₦{{ totalPrice }}</p>
        <button
          @click="cart = []"
          class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm"
        >Clear Cart</button>
      </div>
    </div>

    <div v-else class="mb-6 p-4 bg-gray-100 dark:bg-gray-800 rounded text-center">
      <p class="text-gray-500">Your cart is empty. Search for products above and click to add them to your cart.</p>
    </div>

    <!-- Payment Type -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <SelectInput name="Payment Type" v-model="form.payment_type" :options="paymentTypes" />
      <SelectInput name="Repayment Frequency" v-model="form.repayment_frequency" :options="frequencies" v-if="form.payment_type !== 'instant'" />
      <TextInput label="Repayment Term (months)" type="number" v-model="form.repayment_term" v-if="form.payment_type !== 'instant'" />
    </div>

    <!-- Employment Details (only if installment / on-site-collection) -->
    <div v-if="form.payment_type !== 'instant'" class="border-t pt-4 mt-4">
      <h3 class="font-semibold mb-3">Employment Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <TextInput label="Monthly Salary" type="number" v-model="form.employment.monthly_salary" />
        <TextInput label="Department" v-model="form.employment.department" />
        <TextInput label="Work Number ID" v-model="form.employment.work_number_id" />
        <TextInput label="Employer" v-model="form.employment.employer" />
        <TextInput label="Address" v-model="form.employment.address" />
      </div>

      <!-- Guarantors Section -->
      <div class="mt-6">
        <div class="flex justify-between items-center mb-4">
          <h4 class="font-semibold">Guarantors</h4>
          <button
            type="button"
            @click="addGuarantor"
            class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm"
          >
            + Add Guarantor
          </button>
        </div>

        <div v-if="guarantors.length === 0" class="p-4 bg-gray-100 rounded text-center mb-4">
          <p class="text-gray-500">No guarantors added yet. Click "Add Guarantor" to add one.</p>
        </div>

        <div v-for="(guarantor, index) in guarantors" :key="index" class="border p-4 rounded mb-4 relative">
          <button
            type="button"
            @click="removeGuarantor(index)"
            class="absolute top-2 right-2 text-red-500 hover:text-red-700"
            title="Remove guarantor"
          >
            &times;
          </button>

          <h5 class="font-medium mb-3">Guarantor {{ index + 1 }}</h5>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <TextInput
              label="Full Name"
              v-model="guarantor.name"
              :required="true"
            />
            <TextInput
              label="Phone Number"
              v-model="guarantor.phone"
              :required="true"
            />
            <TextInput
              label="BVN"
              v-model="guarantor.bvn"
            />
            <TextInput
              label="NIN"
              v-model="guarantor.nin"
            />
            <TextInput
              label="Relationship"
              v-model="guarantor.relationship"
              :required="true"
              placeholder="e.g., Brother, Sister, Friend, etc."
            />
            <TextInput
              label="Address"
              v-model="guarantor.address"
              :required="true"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Submit -->
    <div class="flex justify-center py-6">
      <button
        class="btn-primary"
        :disabled="form.processing || isCartEmpty"
        @click="submit"
        :class="{ 'opacity-50 cursor-not-allowed': isCartEmpty }"
      >
        <span v-if="form.processing">Saving...</span>
        <span v-else>Create Order ({{ cart.length }} items)</span>
      </button>
    </div>
  </div>
</template>
