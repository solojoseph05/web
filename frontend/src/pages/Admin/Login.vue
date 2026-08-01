<template>
  <div class="min-h-screen bg-gray-100 px-4 py-24 dark:bg-gray-950">
    <div class="mx-auto max-w-md rounded-3xl bg-white p-8 shadow-xl dark:bg-gray-900 dark:border dark:border-gray-700">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Acceso administrador</h1>
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
        Inicia sesión para administrar vacantes, precios, imágenes y contenido del landing.
      </p>

      <form class="mt-6 space-y-4" @submit.prevent="login">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Correo</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none"
          />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Contraseña</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none"
          />
        </div>
        <button
          type="submit"
          :disabled="submitting"
          class="w-full rounded-xl bg-red-600 px-4 py-3 font-semibold text-white transition hover:bg-red-700 disabled:bg-red-300"
        >
          {{ submitting ? 'Ingresando...' : 'Ingresar' }}
        </button>
      </form>

      <button
        type="button"
        class="mt-4 text-sm font-medium text-red-600 hover:text-red-700"
        @click="showBootstrap = !showBootstrap"
      >
        {{ showBootstrap ? 'Ocultar creación inicial' : 'Crear administrador inicial' }}
      </button>

      <form v-if="showBootstrap" class="mt-4 space-y-4 rounded-2xl border border-red-100 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-950/30" @submit.prevent="bootstrapAdmin">
        <p class="text-sm text-gray-700 dark:text-gray-200">Úsalo solo la primera vez si aún no existe un administrador.</p>
        <input
          v-model="bootstrapForm.name"
          type="text"
          placeholder="Nombre"
          required
          class="w-full rounded-xl border border-red-200 px-4 py-3 focus:border-red-500 focus:outline-none"
        />
        <input
          v-model="bootstrapForm.email"
          type="email"
          placeholder="Correo"
          required
          class="w-full rounded-xl border border-red-200 px-4 py-3 focus:border-red-500 focus:outline-none"
        />
        <input
          v-model="bootstrapForm.password"
          type="password"
          placeholder="Contraseña"
          required
          minlength="8"
          class="w-full rounded-xl border border-red-200 px-4 py-3 focus:border-red-500 focus:outline-none"
        />
        <button
          type="submit"
          :disabled="submittingBootstrap"
          class="w-full rounded-xl bg-gray-900 px-4 py-3 font-semibold text-white transition hover:bg-gray-800 disabled:bg-gray-500"
        >
          {{ submittingBootstrap ? 'Creando...' : 'Crear administrador' }}
        </button>
      </form>

      <p v-if="message" class="mt-4 rounded-xl bg-green-50 px-3 py-2 text-sm font-medium text-green-700">
        {{ message }}
      </p>
      <p v-if="error" class="mt-4 rounded-xl bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/plugins/axios'
import { setAdminToken } from '@/composables/useAdminAuth'

const router = useRouter()

const email = ref('')
const password = ref('')
const message = ref('')
const error = ref('')
const submitting = ref(false)
const showBootstrap = ref(false)
const submittingBootstrap = ref(false)
const bootstrapForm = ref({
  name: '',
  email: '',
  password: '',
})

const login = async () => {
  submitting.value = true
  message.value = ''
  error.value = ''

  try {
    const response = await api.post('/admin/auth/login', {
      email: email.value.trim(),
      password: password.value,
    })

    setAdminToken(response.data.token)
    await router.push('/admin')
  } catch (requestError) {
    error.value = requestError.userMessage || 'No se pudo iniciar sesión.'
  } finally {
    submitting.value = false
  }
}

const bootstrapAdmin = async () => {
  submittingBootstrap.value = true
  message.value = ''
  error.value = ''

  try {
    await api.post('/admin/auth/bootstrap', {
      name: bootstrapForm.value.name.trim(),
      email: bootstrapForm.value.email.trim(),
      password: bootstrapForm.value.password,
    })

    message.value = 'Administrador inicial creado. Ahora puedes iniciar sesión.'
    showBootstrap.value = false
    bootstrapForm.value = { name: '', email: '', password: '' }
  } catch (requestError) {
    error.value = requestError.userMessage || 'No se pudo crear el administrador inicial.'
  } finally {
    submittingBootstrap.value = false
  }
}
</script>
