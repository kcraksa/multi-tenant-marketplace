<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Tenants Management</h2>
      <button
        @click="showCreateModal = true"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
      >
        + Add Tenant
      </button>
    </div>

    <!-- Tenants List -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Loading tenants...</p>
    </div>

    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tenant
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Domain
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Phone
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Plan
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
          <tr v-for="tenant in tenants" :key="tenant.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                {{ tenant.name }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ tenant.domains?.[0]?.domain || '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ tenant.email }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ tenant.phone || '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ tenant.plan }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="tenant.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ tenant.status ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editTenant(tenant)"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Edit
              </button>
              <button
                @click="deleteTenant(tenant.id)"
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
      v-if="showCreateModal || editingTenant"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingTenant ? 'Edit Tenant' : 'Create Tenant' }}
        </h3>

        <form @submit.prevent="saveTenant" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tenant Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
            <input
              v-model="form.domain"
              type="text"
              :required="!editingTenant"
              :disabled="editingTenant !== null"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100"
              placeholder="example.localhost"
            />
            <p class="mt-1 text-xs text-gray-500">Domain cannot be changed after creation</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input
              v-model="form.phone"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea
              v-model="form.address"
              rows="2"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
              <select
                v-model="form.plan"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="basic">Basic</option>
                <option value="pro">Pro</option>
                <option value="enterprise">Enterprise</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>
          </div>

          <div v-if="!editingTenant">
            <label class="flex items-center">
              <input
                v-model="form.seed"
                type="checkbox"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              />
              <span class="ml-2 text-sm text-gray-700">Seed initial data</span>
            </label>
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
import { tenantAPI } from '../../api'

const tenants = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const editingTenant = ref(null)
const saving = ref(false)

const form = ref({
  name: '',
  domain: '',
  email: '',
  phone: '',
  address: '',
  plan: 'basic',
  status: true,
  seed: false,
})

const fetchTenants = async () => {
  loading.value = true
  try {
    const response = await tenantAPI.getAll()
    tenants.value = response.data.data
  } catch (error) {
    console.error('Error fetching tenants:', error)
    alert('Failed to fetch tenants')
  } finally {
    loading.value = false
  }
}

const editTenant = (tenant) => {
  editingTenant.value = tenant
  form.value = {
    name: tenant.name,
    domain: tenant.domains?.[0]?.domain || '',
    email: tenant.email,
    phone: tenant.phone || '',
    address: tenant.address || '',
    plan: tenant.plan,
    status: tenant.status,
    seed: false,
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingTenant.value = null
  form.value = {
    name: '',
    domain: '',
    email: '',
    phone: '',
    address: '',
    plan: 'basic',
    status: true,
    seed: false,
  }
}

const saveTenant = async () => {
  saving.value = true
  try {
    if (editingTenant.value) {
      // Don't send domain when updating
      const { domain, seed, ...updateData } = form.value
      await tenantAPI.update(editingTenant.value.id, updateData)
      alert('Tenant updated successfully!')
    } else {
      await tenantAPI.create(form.value)
      alert('Tenant created successfully!')
    }

    closeModal()
    await fetchTenants()
  } catch (error) {
    console.error('Error saving tenant:', error)
    alert(error.response?.data?.message || 'Failed to save tenant')
  } finally {
    saving.value = false
  }
}

const deleteTenant = async (id) => {
  if (!confirm('Are you sure you want to delete this tenant? This will permanently delete the tenant database and all associated data.')) {
    return
  }

  try {
    await tenantAPI.delete(id)
    alert('Tenant deleted successfully!')
    await fetchTenants()
  } catch (error) {
    console.error('Error deleting tenant:', error)
    alert('Failed to delete tenant')
  }
}

onMounted(() => {
  fetchTenants()
})
</script>
