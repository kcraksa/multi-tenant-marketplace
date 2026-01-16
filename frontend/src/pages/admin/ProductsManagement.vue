<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Products Management</h2>
      <button
        @click="showCreateModal = true"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
      >
        + Add Product
      </button>
    </div>

    <!-- Products List -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Loading products...</p>
    </div>

    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Product
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              SKU
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Price
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Stock
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 flex-shrink-0">
                  <img
                    v-if="product.image"
                    :src="product.image"
                    :alt="product.name"
                    class="h-10 w-10 rounded object-cover"
                  />
                  <div v-else class="h-10 w-10 rounded bg-gray-200"></div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ product.name }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ product.sku || '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              ${{ product.price }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ product.stock }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ product.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editProduct(product)"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Edit
              </button>
              <button
                @click="deleteProduct(product.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showCreateModal || editingProduct"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingProduct ? 'Edit Product' : 'Create Product' }}
        </h3>

        <form @submit.prevent="saveProduct" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
              <input
                v-model.number="form.price"
                type="number"
                step="0.01"
                required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
              <input
                v-model.number="form.stock"
                type="number"
                required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
              <input
                v-model="form.sku"
                type="text"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="form.is_active"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>
          </div>

          <div>
            <label class="flex items-center">
              <input
                v-model="form.is_featured"
                type="checkbox"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              />
              <span class="ml-2 text-sm text-gray-700">Featured Product</span>
            </label>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
            <input
              v-model="form.image"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="https://example.com/image.jpg"
            />
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ saving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { productAPI } from '../../api'

const products = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const editingProduct = ref(null)
const saving = ref(false)

const form = ref({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  sku: '',
  is_active: true,
  is_featured: false,
  image: '',
})

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await productAPI.getAll()
    products.value = response.data.data
  } catch (error) {
    console.error('Error fetching products:', error)
    alert('Failed to fetch products')
  } finally {
    loading.value = false
  }
}

const editProduct = (product) => {
  editingProduct.value = product
  form.value = {
    name: product.name,
    description: product.description || '',
    price: product.price,
    stock: product.stock,
    sku: product.sku || '',
    is_active: product.is_active,
    is_featured: product.is_featured || false,
    image: product.image || '',
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingProduct.value = null
  form.value = {
    name: '',
    description: '',
    price: 0,
    stock: 0,
    sku: '',
    is_active: true,
    is_featured: false,
    image: '',
  }
}

const saveProduct = async () => {
  saving.value = true
  try {
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key])
      }
    })

    if (editingProduct.value) {
      await productAPI.update(editingProduct.value.id, formData)
      alert('Product updated successfully!')
    } else {
      await productAPI.create(formData)
      alert('Product created successfully!')
    }

    closeModal()
    await fetchProducts()
  } catch (error) {
    console.error('Error saving product:', error)
    alert('Failed to save product')
  } finally {
    saving.value = false
  }
}

const deleteProduct = async (id) => {
  if (!confirm('Are you sure you want to delete this product?')) {
    return
  }

  try {
    await productAPI.delete(id)
    alert('Product deleted successfully!')
    await fetchProducts()
  } catch (error) {
    console.error('Error deleting product:', error)
    alert('Failed to delete product')
  }
}

onMounted(() => {
  fetchProducts()
})
</script>
