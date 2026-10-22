<template>
  <div class="relative min-h-screen overflow-hidden bg-linear-to-br from-[#ff7a59] via-[#ff8e66] to-[#f36d5f] px-4 py-12">
    <div class="pointer-events-none absolute -bottom-20 -left-24 h-80 w-80 rounded-full bg-white/10 blur-3xl" />
    <div class="pointer-events-none absolute -top-16 right-10 h-72 w-72 rounded-full bg-white/10 blur-3xl" />

    <div class="relative mx-auto grid w-full max-w-6xl gap-8 lg:grid-cols-[1fr_1.1fr] lg:items-center">
      <section class="rounded-3xl border border-white/25 bg-white/15 p-8 text-white shadow-2xl backdrop-blur-md lg:p-10">
        <img :src="logoImage" alt="Pollo Feliz" class="h-16 w-16 rounded-2xl bg-white/90 p-1 shadow-md" />
        <p class="mt-8 text-sm font-semibold uppercase tracking-[0.28em] text-white/80">Panel administrativo</p>
        <h1 class="mt-3 text-3xl font-extrabold leading-tight md:text-5xl">
          ADMIN LOGIN
          <span class="mt-2 block rounded-xl bg-white px-4 py-2 text-[#f36d5f]">POLLO FELIZ</span>
        </h1>
        <p class="mt-5 max-w-lg text-sm leading-6 text-white/90 md:text-base">
          Inicia sesión para administrar vacantes, menú, promociones, sucursales y mensajes desde un solo lugar.
        </p>
        <img
          :src="chickenImage"
          alt="Pollo asado"
          class="mx-auto mt-8 h-52 w-auto drop-shadow-[0_15px_25px_rgba(0,0,0,0.25)] md:h-64"
          loading="lazy"
        />
      </section>

      <div class="relative lg:pl-12">
        <div class="absolute inset-y-7 left-0 hidden w-[88%] rounded-3xl border border-white/45 bg-white/35 shadow-xl backdrop-blur-sm lg:block" />
        <section class="relative rounded-3xl bg-white p-7 shadow-2xl md:p-8">
          <h2 class="text-3xl font-bold text-gray-900">Acceso administrador</h2>
          <p class="mt-2 text-sm text-gray-600">Usa tus credenciales para entrar al panel.</p>

          <form class="mt-6 space-y-4" @submit.prevent="login">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Correo</label>
              <input
                v-model="email"
                type="email"
                required
                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:border-red-500 focus:outline-none"
                placeholder="admin@correo.com"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Contraseña</label>
              <div class="relative">
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-24 text-gray-900 focus:border-red-500 focus:outline-none"
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-500 hover:text-gray-800"
                  @click="showPassword = !showPassword"
                >
                  {{ showPassword ? 'Ocultar' : 'Mostrar' }}
                </button>
              </div>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Código 2FA (si aplica)</label>
              <input
                v-model="twoFactorCode"
                type="text"
                maxlength="6"
                inputmode="numeric"
                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:border-red-500 focus:outline-none"
                placeholder="123456"
              />
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
              <input v-model="rememberSession" type="checkbox" class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
              Recordar sesión
            </label>
            <button
              type="submit"
              :disabled="submitting"
              class="w-full rounded-xl bg-[#d94f39] px-4 py-3 font-semibold text-white transition hover:bg-[#c2422f] disabled:bg-red-300"
            >
              {{ submitting ? 'Ingresando...' : 'Login' }}
            </button>
          </form>

          <button
            type="button"
            class="mt-4 text-sm font-medium text-red-600 hover:text-red-700"
            @click="showBootstrap = !showBootstrap"
          >
            {{ showBootstrap ? 'Ocultar creación inicial' : 'Crear administrador inicial' }}
          </button>

          <form v-if="showBootstrap" class="mt-4 space-y-3 rounded-2xl border border-red-100 bg-red-50 p-4" @submit.prevent="bootstrapAdmin">
            <p class="text-sm text-gray-700">Úsalo solo la primera vez si aún no existe un administrador.</p>
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
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/plugins/axios'
import { setAdminToken } from '@/composables/useAdminAuth'
import chickenImage from '@/assets/images/POLLO ENTERO.webp'
import logoImage from '@/assets/images/logo.jpg'

const router = useRouter()

const email = ref('')
const password = ref('')
const twoFactorCode = ref('')
const message = ref('')
const error = ref('')
const submitting = ref(false)
const showPassword = ref(false)
const rememberSession = ref(true)
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
      two_factor_code: twoFactorCode.value.trim() || undefined,
    })

    setAdminToken(response.data.token, rememberSession.value)
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
