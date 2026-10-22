<template>
  <SessionTimeout @logout="logout" />
  <div class="min-h-screen bg-gray-100 px-4 py-24">
    <div class="mx-auto max-w-[1600px] rounded-3xl bg-white shadow-xl">
      <div class="border-b border-gray-200 px-6 py-5 sm:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-red-600">Panel interno</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Administración del landing</h1>
            <p class="mt-1 text-sm text-gray-600">
              Edita vacantes, menú, imágenes, textos, dirección y usuarios administradores.
            </p>
          </div>

          <div class="flex flex-wrap gap-2">
            <button
              class="rounded-full border border-gray-200 px-5 py-2 text-sm font-semibold text-gray-700 hover:border-red-200 hover:text-red-600"
              @click="toggleTheme"
            >
              {{ theme === 'dark' ? 'Modo claro' : 'Modo oscuro' }}
            </button>
            <router-link
              to="/"
              class="rounded-full border border-gray-200 px-5 py-2 text-sm font-semibold text-gray-700 hover:border-red-200 hover:text-red-600"
            >
              Ver sitio
            </router-link>
            <button
              class="rounded-full bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-black"
              @click="logout"
            >
              Cerrar sesión
            </button>
          </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-6">
          <div
            v-for="card in dashboardCards"
            :key="card.label"
            :class="`rounded-2xl border border-gray-200 px-4 py-3 ${card.color}`"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">{{ card.label }}</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ card.value }}</p>
                <p v-if="card.subtext" class="mt-1 text-xs text-gray-600">{{ card.subtext }}</p>
              </div>
              <span class="text-2xl opacity-50">{{ card.icon }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="grid gap-0 lg:grid-cols-[260px_minmax(0,1fr)_360px]">
        <aside class="border-b border-gray-200 bg-gray-50 p-5 lg:border-b-0 lg:border-r lg:sticky lg:top-6 lg:h-[calc(100vh-3rem)] lg:overflow-auto">
          <div class="space-y-2">
            <button
              v-for="section in sections"
              :key="section.id"
              class="flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left text-sm font-semibold transition"
              :class="activeSection === section.id ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-100'"
              @click="scrollToSection(section.id)"
            >
              <span>{{ section.label }}</span>
              <span class="text-xs opacity-80">{{ section.shortcut }}</span>
            </button>
          </div>

          <div class="mt-6 rounded-2xl bg-white p-4">
            <p class="text-sm font-semibold text-gray-900">Atajos</p>
            <ul class="mt-3 space-y-2 text-sm text-gray-600">
              <li>• Guardar ajustes principales</li>
              <li>• Crear o editar vacantes</li>
              <li>• Editar menú y subir imágenes</li>
              <li>• Filtrar mensajes por estado</li>
              <li>• Crear nuevos administradores</li>
            </ul>
          </div>
        </aside>

        <main class="min-w-0 p-5 sm:p-8">
          <p v-if="statusMessage" class="rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ statusMessage }}
          </p>
          <p v-if="statusError" class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ statusError }}
          </p>
          <p v-if="lastDraftSavedAt" class="mt-3 text-xs font-medium text-gray-500">
            Borrador local guardado: {{ formatDateTime(lastDraftSavedAt) }}
          </p>
          <div v-if="systemAlerts.pending_approvals || systemAlerts.pending_messages" class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <p class="font-semibold">Alertas internas</p>
            <p class="mt-1">
              Aprobaciones pendientes: {{ systemAlerts.pending_approvals }} · Mensajes pendientes: {{ systemAlerts.pending_messages }}
            </p>
          </div>

          <div v-if="loading" class="mt-8 rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-600">
            Cargando información del panel...
          </div>

          <template v-else>
            <GlobalSearch
              :jobs="jobs"
              :menu-items="menuItems"
              :promotions="promotions"
              :branches="branches"
              :messages="contactMessages"
              @select="handleSearchSelect"
            />

            <section id="overview" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Vista rápida</h2>
                  <p class="text-sm text-gray-600">Previsualiza cómo se verá el contenido público antes de publicarlo.</p>
                </div>
                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="livePreviewEnabled" type="checkbox" class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                  Vista previa en vivo
                </label>
              </div>

              <div class="mt-5 grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-2xl bg-gray-50 p-5">
                  <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Hero</p>
                  <h3 class="mt-2 text-3xl font-bold text-gray-900">
                    {{ settings.hero_title || 'Título principal' }}
                  </h3>
                  <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-600">
                    {{ settings.hero_description || 'Descripción principal del landing.' }}
                  </p>
                  <p class="mt-4 text-sm font-medium text-gray-700">
                    Dirección: <span class="font-semibold">{{ settings.company_address || 'Dirección no definida' }}</span>
                  </p>
                </div>

                <AdminOverviewSummary :summary="dashboardSummary" />
              </div>
            </section>

            <section id="content" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Contenido principal</h2>
                  <p class="text-sm text-gray-600">Ajusta lo que se muestra en la cabecera y el contacto del sitio.</p>
                </div>
                <button class="rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManageSettings" @click="saveSettings">
                  Guardar contenido principal
                </button>
              </div>
              <p v-if="!canManageLanding" class="mt-3 rounded-xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                Tu rol no tiene permiso para editar contenido del landing.
              </p>

              <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="text-sm font-medium text-gray-700">
                  Título principal
                  <input
                    v-model="settings.hero_title"
                    type="text"
                    class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none"
                  />
                </label>
                <label class="text-sm font-medium text-gray-700">
                  Dirección
                  <input
                    v-model="settings.company_address"
                    type="text"
                    class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none"
                  />
                </label>
              </div>
              <label class="mt-4 block text-sm font-medium text-gray-700">
                Descripción principal
                <textarea
                  v-model="settings.hero_description"
                  rows="4"
                  class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none"
                />
              </label>
              <div
                class="mt-4 rounded-2xl border border-dashed border-gray-300 p-4"
                @dragenter.prevent
                @dragover.prevent
                @drop="onImageDropped($event, settings, 'hero_image_url', 'hero')"
              >
                <div class="flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">Imagen principal del hero</p>
                    <p class="text-xs text-gray-500">Arrastra un archivo aquí o selecciónalo desde tu equipo. Se optimizará automáticamente.</p>
                  </div>
                  <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    <span>Subir imagen</span>
                    <input
                      type="file"
                      accept="image/png,image/jpeg,image/webp,image/jpg"
                      class="sr-only"
                      @change="onImageSelected($event, settings, 'hero_image_url', 'hero')"
                    />
                  </label>
                </div>

                <div v-if="settings.__uploadPreviewUrl" class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3">
                  <div class="flex flex-wrap items-center gap-3">
                    <img :src="settings.__uploadPreviewUrl" alt="Vista previa del hero" class="h-28 w-44 rounded-lg object-cover" width="176" height="112" loading="lazy" decoding="async" fetchpriority="low" />
                    <div class="min-w-0">
                      <p class="text-sm font-semibold text-gray-800">Vista previa lista para confirmar</p>
                      <p class="text-xs text-gray-500">La imagen se aplicará al guardar los cambios del landing.</p>
                    </div>
                  </div>
                  <div class="mt-3 flex flex-wrap gap-2">
                    <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(settings)">Aplicar imagen</button>
                    <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(settings)">Cancelar</button>
                  </div>
                </div>

                <div v-if="settings.hero_image_url && !settings.__uploadPreviewUrl" class="mt-4 flex flex-wrap items-center gap-3 rounded-xl bg-gray-50 p-3">
                  <img :src="settings.hero_image_url" alt="Hero preview" class="h-28 w-44 rounded-lg object-cover" width="176" height="112" loading="lazy" decoding="async" fetchpriority="low" />
                  <div>
                    <p class="text-sm font-semibold text-gray-800">Imagen actual del hero</p>
                    <p class="text-xs text-gray-500">Se mostrará en la cabecera pública del landing.</p>
                  </div>
                  <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="removeCurrentImage(settings, 'hero_image_url')">Quitar imagen</button>
                </div>

                <div v-if="imageGallery.length" class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-3">
                  <p class="text-sm font-semibold text-gray-900">Galería reutilizable</p>
                  <div class="mt-3 flex flex-wrap gap-3">
                    <button
                      v-for="image in imageGallery"
                      :key="image.path || image.url"
                      class="rounded-xl border border-gray-200 bg-white p-2 text-left"
                      @click="applyGalleryImage(settings, 'hero_image_url', image.url)"
                    >
                      <img :src="image.url" :alt="image.name || 'Imagen de galería'" class="h-20 w-28 rounded-lg object-cover" width="112" height="80" loading="lazy" decoding="async" fetchpriority="low" />
                    </button>
                  </div>
                </div>

                <p v-else-if="!settings.hero_image_url && !settings.__uploadPreviewUrl" class="mt-3 text-sm text-gray-500">Aún no hay imagen cargada para el hero.</p>
              </div>
            </section>

            <section id="jobs" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Vacantes</h2>
                  <p class="text-sm text-gray-600">Edita, oculta o crea oportunidades laborales desde aquí.</p>
                </div>
                <div class="flex gap-2">
                  <input
                    v-model="jobSearch"
                    type="search"
                    placeholder="Buscar vacante..."
                    class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none"
                  />
                </div>
              </div>

              <div class="mt-4 space-y-4">
                <div v-for="job in filteredJobs" :key="job.id" class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                  <div class="grid gap-3 md:grid-cols-2">
                    <input v-model="job.title" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="job.employment_type" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="job.location" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="job.schedule" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  </div>
                  <div
                    class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3"
                    @dragenter.prevent
                    @dragover.prevent
                    @drop="onImageDropped($event, job, 'image_url', 'job')"
                  >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                      <div>
                        <p class="text-sm font-semibold text-gray-900">Imagen de vacante</p>
                        <p class="text-xs text-gray-500">Arrastra una imagen o selecciónala para previsualizarla antes de guardar.</p>
                      </div>
                      <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        <span>Subir imagen</span>
                        <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, job, 'image_url', 'job')" />
                      </label>
                    </div>
                    <div v-if="job.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2">
                      <div class="flex flex-wrap items-center gap-3">
                        <img :src="job.__uploadPreviewUrl" :alt="job.title" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                        <div>
                          <p class="text-sm font-semibold text-gray-800">Vista previa lista</p>
                          <p class="text-xs text-gray-500">Se aplicará cuando guardes la vacante.</p>
                        </div>
                      </div>
                      <div class="mt-2 flex flex-wrap gap-2">
                        <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(job)">Aplicar</button>
                        <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(job)">Cancelar</button>
                      </div>
                    </div>
                    <div v-else-if="job.image_url" class="mt-3 flex items-center gap-3 rounded-xl bg-white p-2">
                      <img :src="job.image_url" :alt="job.title" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                      <p class="text-sm text-gray-600">Imagen cargada correctamente.</p>
                      <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="removeCurrentImage(job, 'image_url')">Quitar imagen</button>
                    </div>
                  </div>
                  <textarea v-model="job.description" rows="2" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <div class="mt-3 flex flex-wrap items-center gap-3">
                    <label class="text-sm text-gray-700">
                      <input v-model="job.is_active" type="checkbox" class="mr-2" />
                      Activa
                    </label>
                    <button class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black disabled:cursor-not-allowed disabled:bg-gray-400" :disabled="!canManageJobs" @click="saveJob(job)">Guardar</button>
                    <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManageJobs" @click="deleteJob(job.id)">Eliminar</button>
                  </div>
                </div>
              </div>

              <div v-if="trashedJobs.length" class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <h3 class="text-sm font-semibold text-amber-900">Papelera de vacantes</h3>
                    <p class="text-sm text-amber-700">Recupera o elimina de forma definitiva elementos archivados.</p>
                  </div>
                </div>
                <div class="mt-3 space-y-2">
                  <div v-for="job in trashedJobs" :key="job.id" class="flex items-center justify-between rounded-xl border border-amber-200 bg-white px-3 py-2">
                    <span class="text-sm font-medium text-gray-800">{{ job.title || 'Vacante eliminada' }}</span>
                    <button class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-amber-700" @click="restoreJob(job.id)">
                      Restaurar
                    </button>
                  </div>
                </div>
              </div>

              <div class="mt-5 rounded-2xl border border-dashed border-gray-300 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Nueva vacante</h3>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <input v-model="newJob.title" type="text" placeholder="Título" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newJob.employment_type" type="text" placeholder="Tipo (Tiempo completo)" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newJob.location" type="text" placeholder="Ubicación" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newJob.schedule" type="text" placeholder="Horario" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                </div>
                <div
                  class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3"
                  @dragenter.prevent
                  @dragover.prevent
                  @drop="onImageDropped($event, newJob, 'image_url', 'job')"
                >
                  <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">Imagen de la nueva vacante</p>
                      <p class="text-xs text-gray-500">Arrastra una imagen o selecciónala para previsualizarla antes de crear la vacante.</p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                      <span>Subir imagen</span>
                      <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, newJob, 'image_url', 'job')" />
                    </label>
                  </div>
                  <div v-if="newJob.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2">
                    <div class="flex flex-wrap items-center gap-3">
                      <img :src="newJob.__uploadPreviewUrl" alt="Nueva vacante" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                      <div>
                        <p class="text-sm font-semibold text-gray-800">Vista previa lista</p>
                        <p class="text-xs text-gray-500">Se aplicará al crear la vacante.</p>
                      </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                      <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(newJob)">Aplicar</button>
                      <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(newJob)">Cancelar</button>
                    </div>
                  </div>
                  <div v-else-if="newJob.image_url" class="mt-3 flex items-center gap-3 rounded-xl bg-white p-2">
                    <img :src="newJob.image_url" alt="Nueva vacante" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                    <p class="text-sm text-gray-600">Imagen lista para guardar.</p>
                  </div>
                </div>
                <textarea v-model="newJob.description" rows="2" placeholder="Descripción" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                <button class="mt-3 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManageJobs" @click="createJob">Crear vacante</button>
              </div>
            </section>

            <section id="menu" class="mt-8 rounded-2xl border border-gray-200 p-5 dark:border-gray-700 dark:bg-gray-800">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900 dark:text-white">Menú</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Edita precios, imágenes, descripciones y estados de publicación. Haz clic en cualquier tarjeta para editarla.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-if="!bulkMode"
                    class="rounded-lg border border-blue-300 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 dark:border-blue-600 dark:text-blue-300 dark:hover:bg-blue-900"
                    @click="bulkMode = true; bulkSelectedIds = []"
                  >
                    📋 Modo masivo
                  </button>
                  <button
                    v-else
                    class="rounded-lg border border-blue-300 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 dark:bg-blue-900 dark:border-blue-600 dark:text-blue-300"
                    @click="bulkMode = false; bulkSelectedIds = []"
                  >
                    ✓ Cancelar masivo
                  </button>
                  <button
                    class="rounded-lg border border-amber-300 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-600 dark:text-amber-300 dark:hover:bg-amber-900"
                    @click="restoreDefaultMenu"
                    v-if="canManageMenu"
                  >
                    ↻ Restaurar 20 default
                  </button>
                  <input
                    v-model="menuSearch"
                    type="search"
                    placeholder="Buscar producto..."
                    class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                </div>
              </div>

              <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(0,400px)]">
                <div>
                  <div class="mb-3 flex flex-wrap items-center justify-between rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-700">
                    <div>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">Todas las cards ({{ menuItems.length }})</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ bulkMode ? bulkSelectedIds.length + ' seleccionadas' : filteredMenuItems.length + ' visibles' }}</p>
                    </div>
                    <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                      <p class="font-semibold">{{ Math.ceil((filteredMenuItems.length) / 20) }} páginas</p>
                    </div>
                  </div>

                  <div v-if="filteredMenuItems.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                      v-for="item in filteredMenuItems"
                      :key="item.id"
                      class="group relative overflow-hidden rounded-2xl border bg-white shadow-sm transition hover:shadow-md dark:bg-gray-700 dark:border-gray-600"
                      :class="{
                        'border-red-500 ring-2 ring-red-200 dark:ring-red-800': selectedMenuItemId === item.id && !bulkMode,
                        'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800': bulkSelectedIds.includes(item.id),
                        'border-gray-200 dark:border-gray-600': !selectedMenuItemId || selectedMenuItemId !== item.id,
                      }"
                    >
                      <div v-if="bulkMode" class="absolute top-2 left-2 z-10">
                        <input
                          type="checkbox"
                          :checked="bulkSelectedIds.includes(item.id)"
                          @change="toggleBulkSelect(item.id)"
                          class="rounded-md w-5 h-5 cursor-pointer"
                        />
                      </div>
                      <img
                        :src="item.image_url || fallbackProductImage"
                        :alt="item.name"
                        class="h-28 w-full object-cover"
                        width="240"
                        height="112"
                        loading="lazy"
                        decoding="async"
                        fetchpriority="low"
                      />
                      <div class="space-y-2 p-2">
                        <div class="flex items-start justify-between gap-1">
                          <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-gray-900 dark:text-white">{{ item.name }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ item.category }}</p>
                          </div>
                          <span class="shrink-0 rounded-full bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-900 dark:text-red-200">${{ Number(item.price || 0).toFixed(2) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1 text-[9px] font-medium">
                          <span :class="item.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300'" class="rounded-full px-1.5 py-0.5">{{ item.is_active ? '✓' : '✗' }}</span>
                          <span :class="item.is_featured ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300'" class="rounded-full px-1.5 py-0.5">{{ item.is_featured ? '⭐' : '○' }}</span>
                        </div>
                      </div>
                      <div v-if="!bulkMode" class="hidden absolute inset-0 bg-black/30 group-hover:flex flex-col items-center justify-center gap-2 group-hover:bg-opacity-50 transition">
                        <button
                          @click.stop="selectMenuItem(item.id)"
                          class="rounded-lg bg-white px-3 py-1 text-xs font-semibold text-gray-900 hover:bg-gray-50 dark:bg-gray-800 dark:text-white"
                        >
                          Editar
                        </button>
                        <button
                          @click.stop="deleteMenuItem(item.id)"
                          v-if="canManageMenu"
                          class="rounded-lg bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700"
                        >
                          Eliminar
                        </button>
                      </div>
                    </div>
                  </div>

                  <div v-else class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400">
                    No hay productos que coincidan con la búsqueda.
                  </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                  <template v-if="selectedMenuItem && !bulkMode">
                    <div class="flex items-start justify-between gap-3">
                      <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Editar card</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Los cambios se aplican a esta tarjeta del menú.</p>
                      </div>
                      <button
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-white dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600"
                        type="button"
                        @click="selectMenuItem(menuItems[0]?.id || null)"
                      >
                        Ir a la primera
                      </button>
                    </div>

                    <div class="mt-4 grid gap-3">
                      <input v-model="selectedMenuItem.name" type="text" placeholder="Nombre" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none dark:border-gray-600 dark:bg-gray-600 dark:text-white" />
                      <input v-model="selectedMenuItem.category" type="text" placeholder="Categoría" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none dark:border-gray-600 dark:bg-gray-600 dark:text-white" />
                      <input v-model.number="selectedMenuItem.price" type="number" step="0.01" min="0" placeholder="Precio" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none dark:border-gray-600 dark:bg-gray-600 dark:text-white" />
                      <input v-model="selectedMenuItem.badge" type="text" placeholder="Badge (ej: Nuevo, Hot)" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none dark:border-gray-600 dark:bg-gray-600 dark:text-white" />
                    </div>

                    <div
                      class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3 dark:border-gray-600"
                      @dragenter.prevent
                      @dragover.prevent
                      @drop="onImageDropped($event, selectedMenuItem, 'image_url', 'menu')"
                    >
                      <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                          <p class="text-sm font-semibold text-gray-900 dark:text-white">Imagen de la card</p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">600×450 px recomendado. Max 10MB.</p>
                        </div>
                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                          <span>Subir imagen</span>
                          <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, selectedMenuItem, 'image_url', 'menu')" />
                        </label>
                      </div>
                      <div v-if="selectedMenuItem.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2 dark:border-red-900 dark:bg-red-900">
                        <div class="flex flex-wrap items-center gap-3">
                          <img :src="selectedMenuItem.__uploadPreviewUrl" :alt="selectedMenuItem.name" class="h-24 w-32 rounded-lg object-cover" width="128" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                          <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">Vista previa lista</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Se aplicará al guardar la card.</p>
                          </div>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                          <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(selectedMenuItem)">✓ Aplicar</button>
                          <button class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700" @click="openImageCropper(selectedMenuItem)">✂ Recortar</button>
                          <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600" @click="cancelPendingUpload(selectedMenuItem)">✕ Cancelar</button>
                        </div>
                      </div>
                      <div v-else-if="selectedMenuItem.image_url" class="mt-3 flex items-center gap-3 rounded-xl bg-white p-2 dark:bg-gray-600">
                        <img :src="selectedMenuItem.image_url" :alt="selectedMenuItem.name" class="h-24 w-32 rounded-lg object-cover" width="128" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                        <div class="flex-1">
                          <p class="text-sm text-gray-600 dark:text-gray-300">Imagen actual</p>
                          <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-500 dark:text-gray-300 dark:hover:bg-gray-500 mt-1" @click="removeCurrentImage(selectedMenuItem, 'image_url')">Quitar imagen</button>
                        </div>
                      </div>
                    </div>

                    <textarea v-model="selectedMenuItem.description" rows="3" placeholder="Descripción" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none dark:border-gray-600 dark:bg-gray-600 dark:text-white" />

                    <div class="mt-3 flex flex-wrap items-center gap-3">
                      <label class="text-sm text-gray-700 dark:text-gray-300"><input v-model="selectedMenuItem.is_featured" type="checkbox" class="mr-2" />⭐ Destacado</label>
                      <label class="text-sm text-gray-700 dark:text-gray-300"><input v-model="selectedMenuItem.is_active" type="checkbox" class="mr-2" />✓ Activo</label>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                      <button class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black disabled:cursor-not-allowed disabled:bg-gray-400 dark:bg-gray-900 dark:hover:bg-gray-800" :disabled="!canManageMenu" @click="saveMenuItem(selectedMenuItem)">💾 Guardar cambios</button>
                      <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManageMenu" @click="deleteMenuItem(selectedMenuItem.id)">🗑 Eliminar card</button>
                    </div>
                  </template>

                  <template v-else-if="bulkMode && bulkSelectedIds.length">
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-900">
                      <h3 class="text-base font-bold text-blue-900 dark:text-blue-100">Acciones masivas</h3>
                      <p class="text-sm text-blue-700 dark:text-blue-300">{{ bulkSelectedIds.length }} producto(s) seleccionado(s)</p>
                      <div class="mt-4 grid gap-3">
                        <button
                          @click="bulkActionChangeStatus(true)"
                          class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700"
                          v-if="canManageMenu"
                        >
                          ✓ Activar todos
                        </button>
                        <button
                          @click="bulkActionChangeStatus(false)"
                          class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                          v-if="canManageMenu"
                        >
                          ✗ Desactivar todos
                        </button>
                        <button
                          @click="bulkActionToggleFeatured"
                          class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-700"
                          v-if="canManageMenu"
                        >
                          ⭐ Destacar todos
                        </button>
                      </div>
                    </div>
                  </template>

                  <div v-else class="rounded-2xl border border-dashed border-gray-300 bg-white p-5 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400">
                    Selecciona una card para editarla.
                  </div>
                </div>
              </div>

              <div v-if="trashedMenuItems.length" class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <h3 class="text-sm font-semibold text-amber-900">Papelera de productos</h3>
                    <p class="text-sm text-amber-700">Recupera productos ocultados del menú público.</p>
                  </div>
                </div>
                <div class="mt-3 space-y-2">
                  <div v-for="item in trashedMenuItems" :key="item.id" class="flex items-center justify-between rounded-xl border border-amber-200 bg-white px-3 py-2">
                    <span class="text-sm font-medium text-gray-800">{{ item.name || 'Producto eliminado' }}</span>
                    <button class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-amber-700" @click="restoreMenuItem(item.id)">
                      Restaurar
                    </button>
                  </div>
                </div>
              </div>

              <div class="mt-5 rounded-2xl border border-dashed border-gray-300 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Nuevo producto</h3>
                <div class="mt-3 grid gap-3 md:grid-cols-3">
                  <input v-model="newMenuItem.name" type="text" placeholder="Nombre" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newMenuItem.category" type="text" placeholder="Categoría" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model.number="newMenuItem.price" type="number" step="0.01" min="0" placeholder="Precio" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newMenuItem.badge" type="text" placeholder="Badge" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                </div>
                <div
                  class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3"
                  @dragenter.prevent
                  @dragover.prevent
                  @drop="onImageDropped($event, newMenuItem, 'image_url', 'menu')"
                >
                  <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">Imagen del nuevo producto</p>
                      <p class="text-xs text-gray-500">Arrastra una imagen o selecciónala para previsualizarla antes de crear el producto. Mínimo: 300×200 px. Recomendado: 600×450 px.</p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                      <span>Subir imagen</span>
                      <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, newMenuItem, 'image_url', 'menu')" />
                    </label>
                  </div>
                  <div v-if="newMenuItem.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2">
                    <div class="flex flex-wrap items-center gap-3">
                      <img :src="newMenuItem.__uploadPreviewUrl" alt="Nueva imagen" class="h-24 w-32 rounded-lg object-cover" width="128" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                      <div>
                        <p class="text-sm font-semibold text-gray-800">Vista previa lista</p>
                        <p class="text-xs text-gray-500">Se aplicará al crear el producto.</p>
                      </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                      <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(newMenuItem)">Aplicar</button>
                      <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(newMenuItem)">Cancelar</button>
                    </div>
                  </div>
                  <div v-else-if="newMenuItem.image_url" class="mt-3 flex items-center gap-3 rounded-xl bg-white p-2">
                    <img :src="newMenuItem.image_url" alt="Nueva imagen" class="h-24 w-32 rounded-lg object-cover" width="128" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                    <p class="text-sm text-gray-600">Listo para guardar el producto.</p>
                  </div>
                </div>
                <textarea v-model="newMenuItem.description" rows="2" placeholder="Descripción" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                <button class="mt-3 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManageMenu" @click="createMenuItem">
                  Crear producto
                </button>
              </div>
            </section>

            <section id="promotions" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Promociones</h2>
                  <p class="text-sm text-gray-600">Administra ofertas visibles en el landing y su imagen destacada.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <input
                    v-model="promotionSearch"
                    type="search"
                    placeholder="Buscar promoción..."
                    class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none"
                  />
                  <select v-model="promotionStatusFilter" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none">
                    <option value="all">Todas</option>
                    <option value="active">Activas</option>
                    <option value="inactive">Inactivas</option>
                  </select>
                </div>
              </div>
              <p class="mt-3 text-xs text-gray-500">Arrastra y suelta tarjetas para reordenar cómo se muestran en el landing.</p>

              <div class="mt-4 space-y-4">
                <div
                  v-for="promotion in filteredPromotions"
                  :key="promotion.id"
                  class="rounded-2xl border border-gray-200 bg-gray-50 p-4"
                  :draggable="canManagePromotions"
                  @dragstart="onPromotionDragStart(promotion.id)"
                  @dragend="draggedPromotionId = null"
                  @dragover.prevent
                  @drop="onPromotionDrop(promotion.id)"
                >
                  <div class="mb-3 flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Promoción #{{ promotion.id }}</p>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700">↕ Arrastrar</span>
                  </div>
                  <div class="grid gap-3 md:grid-cols-2">
                    <input v-model="promotion.title" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="promotion.code" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="promotion.badge" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  </div>
                  <label class="mt-3 block text-sm font-medium text-gray-700">
                    Fecha válida
                    <input v-model="promotion.valid_until" type="date" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  </label>
                  <textarea v-model="promotion.description" rows="3" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <div
                    class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3"
                    @dragenter.prevent
                    @dragover.prevent
                    @drop="onImageDropped($event, promotion, 'image_url', 'promotion')"
                  >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                      <div>
                        <p class="text-sm font-semibold text-gray-900">Imagen de la promoción</p>
                        <p class="text-xs text-gray-500">Arrastra una imagen o selecciónala para previsualizarla antes de guardar.</p>
                      </div>
                      <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        <span>Subir imagen</span>
                        <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, promotion, 'image_url', 'promotion')" />
                      </label>
                    </div>
                    <div v-if="promotion.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2">
                      <div class="flex flex-wrap items-center gap-3">
                        <img :src="promotion.__uploadPreviewUrl" :alt="promotion.title" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                        <div>
                          <p class="text-sm font-semibold text-gray-800">Vista previa lista</p>
                          <p class="text-xs text-gray-500">Se aplicará al guardar la promoción.</p>
                        </div>
                      </div>
                      <div class="mt-2 flex flex-wrap gap-2">
                        <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(promotion)">Aplicar</button>
                        <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(promotion)">Cancelar</button>
                      </div>
                    </div>
                    <div v-else-if="promotion.image_url" class="mt-3 flex flex-wrap items-center gap-3 rounded-xl bg-white p-2">
                      <img :src="promotion.image_url" :alt="promotion.title" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                      <div class="flex flex-wrap gap-2">
                        <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="removeCurrentImage(promotion, 'image_url')">Quitar imagen</button>
                      </div>
                    </div>
                  </div>
                  <div class="mt-3 flex flex-wrap items-center gap-3">
                    <label class="text-sm text-gray-700"><input v-model="promotion.is_active" type="checkbox" class="mr-2" />Activa</label>
                    <button class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black disabled:cursor-not-allowed disabled:bg-gray-400" :disabled="!canManagePromotions" @click="savePromotion(promotion)">Guardar</button>
                    <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManagePromotions" @click="deletePromotion(promotion.id)">Eliminar</button>
                  </div>
                </div>
              </div>

              <div class="mt-5 rounded-2xl border border-dashed border-gray-300 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Nueva promoción</h3>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <input v-model="newPromotion.title" type="text" placeholder="Título" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newPromotion.code" type="text" placeholder="Código" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newPromotion.badge" type="text" placeholder="Badge" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                </div>
                <label class="mt-3 block text-sm font-medium text-gray-700">
                  Fecha válida
                  <input v-model="newPromotion.valid_until" type="date" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                </label>
                <textarea v-model="newPromotion.description" rows="3" placeholder="Descripción" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                <div
                  class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3"
                  @dragenter.prevent
                  @dragover.prevent
                  @drop="onImageDropped($event, newPromotion, 'image_url', 'promotion')"
                >
                  <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">Imagen de la nueva promoción</p>
                      <p class="text-xs text-gray-500">Arrastra una imagen o selecciónala para la tarjeta promocional.</p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                      <span>Subir imagen</span>
                      <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, newPromotion, 'image_url', 'promotion')" />
                    </label>
                  </div>
                  <div v-if="newPromotion.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2">
                    <div class="flex flex-wrap items-center gap-3">
                      <img :src="newPromotion.__uploadPreviewUrl" alt="Nueva promoción" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                      <div>
                        <p class="text-sm font-semibold text-gray-800">Vista previa lista</p>
                        <p class="text-xs text-gray-500">Se aplicará al crear la promoción.</p>
                      </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                      <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(newPromotion)">Aplicar</button>
                      <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(newPromotion)">Cancelar</button>
                    </div>
                  </div>
                  <div v-else-if="newPromotion.image_url" class="mt-3 flex items-center gap-3 rounded-xl bg-white p-2">
                    <img :src="newPromotion.image_url" alt="Nueva promoción" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                    <p class="text-sm text-gray-600">Listo para guardar.</p>
                  </div>
                </div>
                <button class="mt-3 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManagePromotions" @click="createPromotion">Crear promoción</button>
              </div>
            </section>

            <section id="branches" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Sucursales</h2>
                  <p class="text-sm text-gray-600">Edita direcciones, horarios, teléfono y estado abierto/cerrado.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <input
                    v-model="branchSearch"
                    type="search"
                    placeholder="Buscar sucursal..."
                    class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none"
                  />
                  <select v-model="branchStatusFilter" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none">
                    <option value="all">Todas</option>
                    <option value="open">Abiertas</option>
                    <option value="closed">Cerradas</option>
                  </select>
                </div>
              </div>
              <p class="mt-3 text-xs text-gray-500">Arrastra y suelta tarjetas para cambiar el orden de aparición en la landing.</p>

              <div class="mt-4 space-y-4">
                <div
                  v-for="branch in filteredBranches"
                  :key="branch.id"
                  class="rounded-2xl border border-gray-200 bg-gray-50 p-4"
                  :draggable="canManageBranches"
                  @dragstart="onBranchDragStart(branch.id)"
                  @dragend="draggedBranchId = null"
                  @dragover.prevent
                  @drop="onBranchDrop(branch.id)"
                >
                  <div class="mb-3 flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Sucursal #{{ branch.id }}</p>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700">↕ Arrastrar</span>
                  </div>
                  <div class="grid gap-3 md:grid-cols-2">
                    <input v-model="branch.name" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="branch.phone" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="branch.address" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                    <input v-model="branch.schedule" type="text" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  </div>
                  <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <input v-model="branch.map_link" type="text" placeholder="URL de Google Maps" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  </div>
                  <div
                    class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3"
                    @dragenter.prevent
                    @dragover.prevent
                    @drop="onImageDropped($event, branch, 'image_url', 'branch')"
                  >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                      <div>
                        <p class="text-sm font-semibold text-gray-900">Imagen de la sucursal</p>
                        <p class="text-xs text-gray-500">Arrastra una imagen o selecciónala para esta ubicación.</p>
                      </div>
                      <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        <span>Subir imagen</span>
                        <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, branch, 'image_url', 'branch')" />
                      </label>
                    </div>
                    <div v-if="branch.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2">
                      <div class="flex flex-wrap items-center gap-3">
                        <img :src="branch.__uploadPreviewUrl" :alt="branch.name" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                        <div>
                          <p class="text-sm font-semibold text-gray-800">Vista previa lista</p>
                          <p class="text-xs text-gray-500">Se aplicará al guardar la sucursal.</p>
                        </div>
                      </div>
                      <div class="mt-2 flex flex-wrap gap-2">
                        <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(branch)">Aplicar</button>
                        <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(branch)">Cancelar</button>
                      </div>
                    </div>
                    <div v-else-if="branch.image_url" class="mt-3 flex flex-wrap items-center gap-3 rounded-xl bg-white p-2">
                      <img :src="branch.image_url" :alt="branch.name" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                      <div class="flex flex-wrap gap-2">
                        <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="removeCurrentImage(branch, 'image_url')">Quitar imagen</button>
                      </div>
                    </div>
                  </div>
                  <div class="mt-3 flex flex-wrap items-center gap-3">
                    <label class="text-sm text-gray-700"><input v-model="branch.is_open" type="checkbox" class="mr-2" />Abierta</label>
                    <button class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black disabled:cursor-not-allowed disabled:bg-gray-400" :disabled="!canManageBranches" @click="saveBranch(branch)">Guardar</button>
                    <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManageBranches" @click="deleteBranch(branch.id)">Eliminar</button>
                  </div>
                </div>
              </div>

              <div class="mt-5 rounded-2xl border border-dashed border-gray-300 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Nueva sucursal</h3>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <input v-model="newBranch.name" type="text" placeholder="Nombre" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newBranch.phone" type="text" placeholder="Teléfono" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newBranch.address" type="text" placeholder="Dirección" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                  <input v-model="newBranch.schedule" type="text" placeholder="Horario" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                </div>
                <input v-model="newBranch.map_link" type="text" placeholder="URL de Google Maps" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                <div
                  class="mt-3 rounded-2xl border border-dashed border-gray-300 p-3"
                  @dragenter.prevent
                  @dragover.prevent
                  @drop="onImageDropped($event, newBranch, 'image_url', 'branch')"
                >
                  <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">Imagen de la nueva sucursal</p>
                      <p class="text-xs text-gray-500">Arrastra una imagen o selecciónala para la tarjeta de la sucursal.</p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                      <span>Subir imagen</span>
                      <input type="file" accept="image/png,image/jpeg,image/webp,image/jpg" class="sr-only" @change="onImageSelected($event, newBranch, 'image_url', 'branch')" />
                    </label>
                  </div>
                  <div v-if="newBranch.__uploadPreviewUrl" class="mt-3 rounded-xl border border-red-200 bg-red-50 p-2">
                    <div class="flex flex-wrap items-center gap-3">
                      <img :src="newBranch.__uploadPreviewUrl" alt="Nueva sucursal" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                      <div>
                        <p class="text-sm font-semibold text-gray-800">Vista previa lista</p>
                        <p class="text-xs text-gray-500">Se aplicará al crear la sucursal.</p>
                      </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                      <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmPendingUpload(newBranch)">Aplicar</button>
                      <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="cancelPendingUpload(newBranch)">Cancelar</button>
                    </div>
                  </div>
                  <div v-else-if="newBranch.image_url" class="mt-3 flex items-center gap-3 rounded-xl bg-white p-2">
                    <img :src="newBranch.image_url" alt="Nueva sucursal" class="h-24 w-36 rounded-lg object-cover" width="144" height="96" loading="lazy" decoding="async" fetchpriority="low" />
                    <p class="text-sm text-gray-600">Listo para guardar.</p>
                  </div>
                </div>
                <button class="mt-3 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300" :disabled="!canManageBranches" @click="createBranch">Crear sucursal</button>
              </div>
            </section>

            <section id="messages" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Mensajes de contacto</h2>
                  <p class="text-sm text-gray-600">Filtra, pagina y agrega notas de seguimiento.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                  <input
                    v-model="messageSearch"
                    type="search"
                    placeholder="Buscar mensaje..."
                    class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none"
                  />
                  <select v-model="messageStatusFilter" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none">
                    <option value="all">Todos</option>
                    <option value="pending">Pendiente</option>
                    <option value="attended">Atendido</option>
                    <option value="discarded">Descartado</option>
                  </select>
                </div>
              </div>

              <div class="mt-4 overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-y-3 text-left text-sm">
                  <thead class="text-xs uppercase text-gray-500">
                    <tr>
                      <th class="px-3 py-2">Nombre</th>
                      <th class="px-3 py-2">Correo</th>
                      <th class="px-3 py-2">Mensaje</th>
                      <th class="px-3 py-2">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="message in paginatedMessages" :key="message.id" class="rounded-2xl bg-gray-50 align-top">
                      <td class="rounded-l-2xl px-3 py-3">
                        <div class="font-semibold text-gray-900">{{ message.name }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ formatDate(message.created_at) }}</div>
                      </td>
                      <td class="px-3 py-3">
                        <div>{{ message.email }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ message.phone || 'Sin teléfono' }}</div>
                      </td>
                      <td class="px-3 py-3 text-gray-600">
                        <p class="line-clamp-3">{{ message.message }}</p>
                        <textarea
                          v-model="message.notes"
                          rows="2"
                          placeholder="Notas de seguimiento"
                          class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none"
                        />
                      </td>
                      <td class="rounded-r-2xl px-3 py-3">
                        <select v-model="message.status" class="w-full rounded-lg border border-gray-300 px-3 py-2" @change="saveContactMessage(message)">
                          <option value="pending">Pendiente</option>
                          <option value="attended">Atendido</option>
                          <option value="discarded">Descartado</option>
                        </select>
                        <button class="mt-2 w-full rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black disabled:cursor-not-allowed disabled:bg-gray-400" :disabled="!canManageMessages" @click="saveContactMessage(message)">
                          Guardar
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-600">
                  Mostrando {{ paginatedMessages.length }} de {{ filteredMessages.length }} mensajes filtrados.
                </p>
                <div class="flex items-center gap-2">
                  <button
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 disabled:opacity-40"
                    :disabled="messagePage === 1"
                    @click="messagePage--"
                  >
                    Anterior
                  </button>
                  <span class="text-sm font-semibold text-gray-700">
                    Página {{ messagePage }} de {{ messagePageCount }}
                  </span>
                  <button
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 disabled:opacity-40"
                    :disabled="messagePage >= messagePageCount"
                    @click="messagePage++"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
            </section>

            <section id="users" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Crear usuario administrador</h2>
                  <p class="text-sm text-gray-600">Agrega cuentas nuevas para acceso interno al panel.</p>
                </div>
                <button v-if="canManageAdmins" class="rounded-xl bg-gray-900 px-4 py-2 font-semibold text-white hover:bg-black" @click="createAdminUser">
                  Crear administrador
                </button>
              </div>

              <p v-if="!canManageAdmins" class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                Tu rol actual ({{ currentAdmin.role || 'sin rol' }}) no permite crear usuarios administradores.
              </p>

              <div v-else class="mt-4 grid gap-3 md:grid-cols-4">
                <input v-model="newAdminUser.name" type="text" placeholder="Nombre" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                <input v-model="newAdminUser.email" type="email" placeholder="Correo" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                <input v-model="newAdminUser.password" type="password" placeholder="Contraseña (mín. 8)" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none" />
                <select v-model="newAdminUser.role" class="rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none">
                  <option value="content_manager">Content manager</option>
                  <option value="support_agent">Support agent</option>
                  <option value="super_admin">Super admin</option>
                </select>
              </div>

              <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Seguridad de cuenta (2FA)</h3>
                <p class="mt-1 text-xs text-gray-600">Estado actual: {{ currentAdmin.two_factor_enabled ? 'Activado' : 'Desactivado' }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                  <button class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black" @click="setupTwoFactor">
                    Generar secreto 2FA
                  </button>
                  <button v-if="!currentAdmin.two_factor_enabled" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700" @click="enableTwoFactor">
                    Activar 2FA
                  </button>
                  <button v-else class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="disableTwoFactor">
                    Desactivar 2FA
                  </button>
                </div>
                <input
                  v-model="twoFactorCode"
                  type="text"
                  maxlength="6"
                  placeholder="Código 2FA de 6 dígitos"
                  class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-red-500 focus:outline-none"
                />
                <p v-if="twoFactorSetupSecret" class="mt-2 break-all text-xs text-gray-700">
                  Secreto: <strong>{{ twoFactorSetupSecret }}</strong>
                </p>
                <p v-if="twoFactorSetupUrl" class="mt-1 break-all text-[11px] text-gray-500">
                  URL OTP: {{ twoFactorSetupUrl }}
                </p>
              </div>
            </section>

            <section id="history" class="mt-8 rounded-2xl border border-gray-200 p-5">
              <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 class="text-lg font-bold text-gray-900">Historial de cambios</h2>
                  <p class="text-sm text-gray-600">Revisa quién cambió qué y cuándo.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <input v-model="historySearch" type="search" placeholder="Buscar acción..." class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none" />
                  <select v-model="historyActionFilter" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none">
                    <option value="">Todas las acciones</option>
                    <option v-for="action in historyActionOptions" :key="action" :value="action">{{ action }}</option>
                  </select>
                  <select v-model="historyAdminFilter" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none">
                    <option value="">Todos los admins</option>
                    <option v-for="admin in historyAdminOptions" :key="admin.id" :value="admin.id">{{ admin.name }}</option>
                  </select>
                  <input v-model="historyDateFrom" type="date" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none" />
                  <input v-model="historyDateTo" type="date" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-red-500 focus:outline-none" />
                  <button class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="fetchChangeLogs(1)">
                    Filtrar
                  </button>
                  <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="exportChangeLogs">
                    Exportar CSV
                  </button>
                </div>
              </div>

              <p v-if="!canViewHistory" class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                Tu rol no tiene permiso para ver historial de cambios.
              </p>

              <div v-if="canApproveRequests" class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="mb-3 flex items-center justify-between">
                  <h3 class="text-sm font-semibold text-amber-900">Cambios pendientes de aprobación</h3>
                  <span class="text-xs font-semibold text-amber-800">{{ pendingApprovals.length }}</span>
                </div>
                <div v-if="pendingApprovals.length" class="space-y-2">
                  <div v-for="request in pendingApprovals" :key="request.id" class="rounded-xl bg-white p-3">
                    <p class="text-xs text-gray-500">{{ formatDateTime(request.created_at) }} · {{ request.admin_user?.name || 'Admin' }}</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ request.action }}</p>
                    <p class="text-xs text-gray-600">{{ request.description || 'Sin detalle.' }}</p>
                    <div class="mt-2 flex gap-2">
                      <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700" @click="approveRequest(request.id)">
                        Aprobar
                      </button>
                      <button class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700" @click="rejectRequest(request.id)">
                        Rechazar
                      </button>
                    </div>
                  </div>
                </div>
                <p v-else class="text-xs text-amber-800">No hay solicitudes pendientes.</p>
              </div>

              <div v-else class="mt-4 overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-y-2 text-left text-sm">
                  <thead class="text-xs uppercase text-gray-500">
                    <tr>
                      <th class="px-3 py-2">Fecha</th>
                      <th class="px-3 py-2">Usuario</th>
                      <th class="px-3 py-2">Acción</th>
                      <th class="px-3 py-2">Detalle</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="entry in changeLogs" :key="entry.id" class="rounded-xl bg-gray-50">
                      <td class="px-3 py-2 text-xs text-gray-500">{{ formatDateTime(entry.created_at) }}</td>
                      <td class="px-3 py-2">
                        <div class="font-semibold text-gray-900">{{ entry.admin_user?.name || 'Sistema' }}</div>
                        <div class="text-xs text-gray-500">{{ entry.admin_user?.role || '-' }}</div>
                      </td>
                      <td class="px-3 py-2">
                        <div class="font-medium text-gray-800">{{ entry.action }}</div>
                        <div class="text-xs text-gray-500">{{ entry.target_type }}{{ entry.target_id ? ` #${entry.target_id}` : '' }}</div>
                      </td>
                      <td class="px-3 py-2 text-gray-600">{{ entry.description || 'Sin descripción' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="canViewHistory" class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-600">
                  Mostrando {{ changeLogs.length }} registros en esta página.
                </p>
                <div class="flex items-center gap-2">
                  <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 disabled:opacity-40" :disabled="historyPage <= 1" @click="fetchChangeLogs(historyPage - 1)">
                    Anterior
                  </button>
                  <span class="text-sm font-semibold text-gray-700">
                    Página {{ historyPage }} de {{ historyPageCount }}
                  </span>
                  <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 disabled:opacity-40" :disabled="historyPage >= historyPageCount" @click="fetchChangeLogs(historyPage + 1)">
                    Siguiente
                  </button>
                </div>
              </div>
            </section>
          </template>
        </main>

        <aside class="border-t border-gray-200 bg-gray-50 p-5 lg:border-t-0 lg:border-l lg:sticky lg:top-6 lg:h-[calc(100vh-3rem)] lg:overflow-auto">
          <template v-if="livePreviewEnabled">
            <div class="rounded-2xl bg-white p-5 shadow-sm">
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Vista previa</p>
              <h3 class="mt-2 text-2xl font-bold text-gray-900">
                {{ settings.hero_title || 'Título principal' }}
              </h3>
              <p class="mt-3 text-sm leading-6 text-gray-600">
                {{ settings.hero_description || 'Descripción del landing.' }}
              </p>
              <p class="mt-4 text-sm font-medium text-gray-700">
                {{ settings.company_address || 'Dirección pendiente' }}
              </p>
            </div>

            <div class="mt-5 rounded-2xl bg-white p-5 shadow-sm">
              <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900">Vacantes activas</p>
                <span class="text-xs text-gray-500">{{ filteredJobs.length }}</span>
              </div>
              <div class="mt-3 space-y-3">
                <div v-for="job in previewJobs" :key="job.id" class="rounded-xl border border-gray-200 p-3">
                  <p class="font-semibold text-gray-900">{{ job.title }}</p>
                  <p class="mt-1 text-xs text-gray-500">{{ job.employment_type }} · {{ job.location || 'Ubicación por definir' }}</p>
                  <p class="mt-2 text-sm text-gray-600 line-clamp-3">{{ job.description }}</p>
                </div>
              </div>
            </div>

            <div class="mt-5 rounded-2xl bg-white p-5 shadow-sm">
              <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900">Productos destacados</p>
                <span class="text-xs text-gray-500">{{ filteredMenuItems.length }}</span>
              </div>
              <div class="mt-3 space-y-3">
                <div v-for="item in previewMenuItems" :key="item.id" class="flex gap-3 rounded-xl border border-gray-200 p-3">
                  <img
                    :src="item.image_url || item.image || fallbackProductImage"
                    :alt="item.name"
                    class="h-16 w-16 rounded-lg object-cover"
                    width="64"
                    height="64"
                  />
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-900">{{ item.name }}</p>
                    <p class="text-xs text-gray-500">{{ item.category }}</p>
                    <p class="mt-1 text-sm text-red-600 font-semibold">${{ Number(item.price || 0).toFixed(2) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-5 rounded-2xl bg-white p-5 shadow-sm">
              <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900">Promociones visibles</p>
                <span class="text-xs text-gray-500">{{ filteredPromotions.length }}</span>
              </div>
              <div class="mt-3 space-y-3">
                <div v-for="promotion in previewPromotions" :key="promotion.id" class="rounded-xl border border-gray-200 p-3">
                  <img
                    v-if="promotion.image_url"
                    :src="promotion.image_url"
                    :alt="promotion.title"
                    class="h-20 w-full rounded-lg object-cover"
                    width="320"
                    height="80"
                  />
                  <p class="mt-2 font-semibold text-gray-900">{{ promotion.title }}</p>
                  <p class="mt-1 text-xs text-gray-500">{{ promotion.is_active ? 'Activa' : 'Inactiva' }}</p>
                </div>
              </div>
            </div>

            <div class="mt-5 rounded-2xl bg-white p-5 shadow-sm">
              <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900">Sucursales</p>
                <span class="text-xs text-gray-500">{{ filteredBranches.length }}</span>
              </div>
              <div class="mt-3 space-y-3">
                <div v-for="branch in previewBranches" :key="branch.id" class="rounded-xl border border-gray-200 p-3">
                  <img
                    v-if="branch.image_url"
                    :src="branch.image_url"
                    :alt="branch.name"
                    class="h-20 w-full rounded-lg object-cover"
                    width="320"
                    height="80"
                  />
                  <p class="mt-2 font-semibold text-gray-900">{{ branch.name }}</p>
                  <p class="mt-1 text-xs text-gray-500">{{ branch.phone || 'Sin teléfono' }}</p>
                  <p class="text-xs" :class="branch.is_open ? 'text-green-700' : 'text-red-700'">
                    {{ branch.is_open ? 'Abierta' : 'Cerrada' }}
                  </p>
                </div>
              </div>
            </div>
          </template>


          <div v-else class="rounded-2xl bg-white p-5 shadow-sm text-sm text-gray-600">
            La vista previa en vivo está desactivada.
          </div>
        </aside>
      </div>
    </div>
  </div>

  <ConfirmDialog
    ref="confirmDialog"
    title="Confirmar acción"
    message="¿Estás seguro de que deseas continuar?"
    @confirm="executeDeleteAction"
  />

  <LoadingSpinner ref="loadingSpinner" />
  <ImageCropper ref="imageCropper" @crop="onImageCropped" @cancel="cropTarget = null" />

  <div class="fixed bottom-6 right-6 z-30 flex flex-col gap-3">
    <div class="relative">
      <button
        class="rounded-full bg-linear-to-r from-green-600 to-green-700 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition hover:scale-105 dark:from-green-700 dark:to-green-800"
        title="Exportar datos"
        @click="showExportMenu = !showExportMenu"
      >
        📥 Exportar
      </button>
      <div v-if="showExportMenu" class="absolute bottom-full right-0 mb-2 rounded-lg bg-white shadow-xl border border-gray-200 overflow-hidden dark:bg-gray-800 dark:border-gray-700 min-w-40">
        <button
          @click="exportData('menu')"
          class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white"
        >
          📋 Menú
        </button>
        <button
          @click="exportData('jobs')"
          class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white"
        >
          💼 Vacantes
        </button>
        <button
          @click="exportData('promotions')"
          class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white"
        >
          🎉 Promociones
        </button>
        <button
          @click="exportData('branches')"
          class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white"
        >
          🏪 Sucursales
        </button>
        <button
          @click="exportData('all')"
          class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white border-t border-gray-200 dark:border-gray-700 font-bold"
        >
          📦 Todo
        </button>
      </div>
    </div>

    <button
      v-if="canViewHistory"
      class="rounded-full bg-linear-to-r from-red-600 to-red-700 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition hover:scale-105 dark:from-red-700 dark:to-red-800"
      :title="lastOperation ? 'Deshacer: ' + lastOperation : 'Sin cambios para deshacer'"
      :disabled="!lastOperation"
      @click="undoLastChange"
    >
      ↶ Deshacer
    </button>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/plugins/axios'
import { clearAdminToken } from '@/composables/useAdminAuth'
import { useLandingContent } from '@/composables/useLandingContent'
import { useToasts } from '@/composables/useToasts'
import { useTheme } from '@/composables/useTheme'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'
import GlobalSearch from '@/components/admin/GlobalSearch.vue'
import ModalEditor from '@/components/admin/ModalEditor.vue'
import LoadingSpinner from '@/components/admin/LoadingSpinner.vue'
import AdminOverviewSummary from '@/components/admin/AdminOverviewSummary.vue'
import MenuCardGrid from '@/components/admin/MenuCardGrid.vue'
import ImageCropper from '@/components/admin/ImageCropper.vue'
import SessionTimeout from '@/components/admin/SessionTimeout.vue'
import fallbackProductImage from '@/assets/images/3-sm.webp'

const router = useRouter()
const { loadLandingContent } = useLandingContent()
const { addToast } = useToasts()
const { theme, toggleTheme } = useTheme()

const confirmDialog = ref(null)
const deleteAction = ref(null)
const modalEditor = ref(null)
const loadingSpinner = ref(null)
const imageCropper = ref(null)
const cropTarget = ref(null)
const lastOperation = ref(null)
const showExportMenu = ref(false)
const bulkMode = ref(false)
const bulkSelectedIds = ref([])
const lastDraftSavedAt = ref('')
const hasRestoredDrafts = ref(false)

const loading = ref(true)
const settings = ref({
  hero_title: '',
  hero_description: '',
  hero_image_url: '',
  company_address: '',
})
const dashboardSummary = ref({
  active_jobs: 0,
  active_menu_items: 0,
  contact_messages: 0,
  pending_contact_messages: 0,
  admin_users: 0,
  change_logs: 0,
})
const jobs = ref([])
const menuItems = ref([])
const contactMessages = ref([])
const changeLogs = ref([])
const statusMessage = ref('')
const statusError = ref('')
const livePreviewEnabled = ref(true)
const activeSection = ref('overview')
const jobSearch = ref('')
const menuSearch = ref('')
const promotionSearch = ref('')
const promotionStatusFilter = ref('all')
const branchSearch = ref('')
const branchStatusFilter = ref('all')
const messageSearch = ref('')
const messageStatusFilter = ref('all')
const messagePage = ref(1)
const messagePageSize = ref(6)
const historySearch = ref('')
const historyActionFilter = ref('')
const historyDateFrom = ref('')
const historyDateTo = ref('')
const historyAdminFilter = ref('')
const historyPage = ref(1)
const historyPageCount = ref(1)
const trashedJobs = ref([])
const trashedMenuItems = ref([])
let scrollFrame = null

const sections = [
  { id: 'overview', label: 'Vista rápida', shortcut: '01' },
  { id: 'content', label: 'Contenido', shortcut: '02' },
  { id: 'jobs', label: 'Vacantes', shortcut: '03' },
  { id: 'menu', label: 'Menú', shortcut: '04' },
  { id: 'promotions', label: 'Promos', shortcut: '05' },
  { id: 'branches', label: 'Sucursales', shortcut: '06' },
  { id: 'messages', label: 'Mensajes', shortcut: '07' },
  { id: 'users', label: 'Usuarios', shortcut: '08' },
  { id: 'history', label: 'Historial', shortcut: '09' },
]

const newJob = ref({
  title: '',
  description: '',
  employment_type: 'Tiempo completo',
  location: '',
  schedule: '',
  image_url: '',
})

const newMenuItem = ref({
  name: '',
  description: '',
  price: 0,
  image_url: '',
  badge: '',
  category: 'Paquetes',
})
const selectedMenuItemId = ref(null)

const newAdminUser = ref({
  name: '',
  email: '',
  password: '',
  role: 'content_manager',
})
const currentAdmin = ref({
  id: null,
  name: '',
  email: '',
  role: '',
  permissions: [],
  two_factor_enabled: false,
})
const twoFactorSetupSecret = ref('')
const twoFactorSetupUrl = ref('')
const twoFactorCode = ref('')
const systemAlerts = ref({
  pending_approvals: 0,
  pending_messages: 0,
  alerts: [],
  recent_pending_approvals: [],
})
const pendingApprovals = ref([])
let alertsIntervalId = null
const imageGallery = ref([])
const promotions = ref([])
const branches = ref([])
const draggedPromotionId = ref(null)
const draggedBranchId = ref(null)
const persistingPromotionOrder = ref(false)
const persistingBranchOrder = ref(false)
const newPromotion = ref({
  title: '',
  description: '',
  image_url: '',
  valid_until: '',
  code: '',
  badge: '',
  is_active: true,
})
const newBranch = ref({
  name: '',
  address: '',
  phone: '',
  schedule: '',
  image_url: '',
  map_link: '',
  is_open: true,
})

const revokePendingUpload = (target) => {
  if (!target) {
    return
  }

  if (target.__uploadPreviewUrl && String(target.__uploadPreviewUrl).startsWith('blob:')) {
    URL.revokeObjectURL(target.__uploadPreviewUrl)
  }

  delete target.__uploadPreviewUrl
  delete target.__uploadFile
  delete target.__uploadContext
  delete target.__uploadFieldName
  delete target.__uploading
}

const setPendingUpload = (target, file, fieldName, context) => {
  if (!target) {
    return
  }

  revokePendingUpload(target)
  target.__uploadFile = file
  target.__uploadFieldName = fieldName
  target.__uploadContext = context
  target.__uploadPreviewUrl = URL.createObjectURL(file)
  target.__uploading = false
}

const setSuccess = (message) => {
  statusMessage.value = message
  statusError.value = ''
  addToast(message, 'success')
}

const formatDateTime = (value) => {
  if (!value) {
    return '-'
  }

  return new Date(value).toLocaleString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const setError = (message) => {
  statusError.value = message
  statusMessage.value = ''
  addToast(message, 'error')
}

const formatDate = (value) => {
  if (!value) {
    return '-'
  }

  return new Date(value).toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

const dashboardCards = computed(() => {
  const jobsWithoutImage = jobs.value.filter(j => !j.image_url).length
  const itemsWithoutImage = menuItems.value.filter(m => !m.image_url).length
  const pendingMessages = dashboardSummary.value.pending_contact_messages || 0

  return [
    {
      label: 'Vacantes Activas',
      value: dashboardSummary.value.active_jobs,
      subtext: `${jobsWithoutImage} sin imagen`,
      color: 'bg-blue-50 border-blue-200',
      icon: '📋'
    },
    {
      label: 'Productos Activos',
      value: dashboardSummary.value.active_menu_items,
      subtext: `${itemsWithoutImage} sin imagen`,
      color: 'bg-green-50 border-green-200',
      icon: '🍗'
    },
    {
      label: 'Mensajes Totales',
      value: dashboardSummary.value.contact_messages,
      subtext: `${pendingMessages} pendientes`,
      color: 'bg-yellow-50 border-yellow-200',
      icon: '✉️'
    },
    {
      label: 'Usuarios Admin',
      value: dashboardSummary.value.admin_users,
      subtext: 'Con acceso al panel',
      color: 'bg-purple-50 border-purple-200',
      icon: '👥'
    },
    {
      label: 'Cambios Registrados',
      value: dashboardSummary.value.change_logs || 0,
      subtext: 'En historial de auditoría',
      color: 'bg-orange-50 border-orange-200',
      icon: '📝'
    },
    {
      label: 'Estado',
      value: currentAdmin.value?.name || 'Admin',
      subtext: currentAdmin.value?.role ? `Rol: ${currentAdmin.value.role}` : 'Conectado',
      color: 'bg-gray-50 border-gray-200',
      icon: '✅'
    }
  ]
})
const hasPermission = (permission) => currentAdmin.value.permissions.includes(permission)
const canManageAdmins = computed(() => hasPermission('admin.manage'))
const canManageSettings = computed(() => hasPermission('landing.settings.manage') || hasPermission('landing.manage'))
const canManageJobs = computed(() => hasPermission('landing.jobs.manage') || hasPermission('landing.manage'))
const canManageMenu = computed(() => hasPermission('landing.menu.manage') || hasPermission('landing.manage'))
const canManagePromotions = computed(() => hasPermission('landing.promotions.manage') || hasPermission('landing.manage'))
const canManageBranches = computed(() => hasPermission('landing.branches.manage') || hasPermission('landing.manage'))
const canManageLanding = computed(() => (
  canManageSettings.value
  || canManageJobs.value
  || canManageMenu.value
  || canManagePromotions.value
  || canManageBranches.value
))
const canManageMessages = computed(() => hasPermission('messages.manage'))
const canViewHistory = computed(() => hasPermission('history.view'))
const canApproveRequests = computed(() => hasPermission('approval.manage'))
const historyActionOptions = computed(() => {
  return Array.from(new Set(changeLogs.value.map((entry) => entry.action).filter(Boolean)))
})
const historyAdminOptions = computed(() => {
  const admins = changeLogs.value
    .map((entry) => entry.admin_user)
    .filter(Boolean)

  return Array.from(new Map(admins.map((admin) => [admin.id, admin])).values())
})

const filteredJobs = computed(() => {
  const query = jobSearch.value.trim().toLowerCase()
  return jobs.value.filter((job) => {
    if (!query) {
      return true
    }

    return [job.title, job.description, job.location, job.schedule, job.employment_type]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(query))
  })
})

const filteredMenuItems = computed(() => {
  const query = menuSearch.value.trim().toLowerCase()
  return menuItems.value.filter((item) => {
    if (!query) {
      return true
    }

    return [item.name, item.description, item.category, item.badge]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(query))
  })
})

const filteredPromotions = computed(() => {
  const query = promotionSearch.value.trim().toLowerCase()
  return promotions.value.filter((promotion) => {
    const statusMatches = promotionStatusFilter.value === 'all'
      || (promotionStatusFilter.value === 'active' && Boolean(promotion.is_active))
      || (promotionStatusFilter.value === 'inactive' && !Boolean(promotion.is_active))

    if (!statusMatches) {
      return false
    }

    if (!query) {
      return true
    }

    return [promotion.title, promotion.description, promotion.code, promotion.badge]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(query))
  })
})

const filteredBranches = computed(() => {
  const query = branchSearch.value.trim().toLowerCase()
  return branches.value.filter((branch) => {
    const statusMatches = branchStatusFilter.value === 'all'
      || (branchStatusFilter.value === 'open' && Boolean(branch.is_open))
      || (branchStatusFilter.value === 'closed' && !Boolean(branch.is_open))

    if (!statusMatches) {
      return false
    }

    if (!query) {
      return true
    }

    return [branch.name, branch.address, branch.phone, branch.schedule]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(query))
  })
})

const filteredMessages = computed(() => {
  const query = messageSearch.value.trim().toLowerCase()
  return contactMessages.value.filter((message) => {
    const statusMatches = messageStatusFilter.value === 'all' || message.status === messageStatusFilter.value

    if (!statusMatches) {
      return false
    }

    if (!query) {
      return true
    }

    return [message.name, message.email, message.phone, message.message, message.notes]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(query))
  })
})

const messagePageCount = computed(() => Math.max(1, Math.ceil(filteredMessages.value.length / messagePageSize.value)))

const paginatedMessages = computed(() => {
  const start = (messagePage.value - 1) * messagePageSize.value
  return filteredMessages.value.slice(start, start + messagePageSize.value)
})

const previewJobs = computed(() => filteredJobs.value.slice(0, 2))
const previewMenuItems = computed(() => filteredMenuItems.value.slice(0, 3))
const previewPromotions = computed(() => filteredPromotions.value.slice(0, 2))
const previewBranches = computed(() => filteredBranches.value.slice(0, 2))
const selectedMenuItem = computed(() => menuItems.value.find((item) => item.id === selectedMenuItemId.value) || null)
const MAX_ADMIN_IMAGE_SIZE_BYTES = 10 * 1024 * 1024
const ADMIN_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp']
const ADMIN_IMAGE_MIN_DIMENSIONS = {
  hero: { width: 700, height: 500 },
  job: { width: 500, height: 350 },
  menu: { width: 300, height: 200 },
  promotion: { width: 600, height: 400 },
  branch: { width: 600, height: 400 },
  general: { width: 400, height: 300 },
}
const DRAFT_STORAGE_KEY = 'admin_panel_drafts_v1'

watch(menuItems, (items) => {
  if (!items.length) {
    selectedMenuItemId.value = null
    return
  }

  const hasSelectedItem = items.some((item) => item.id === selectedMenuItemId.value)
  if (!hasSelectedItem) {
    selectedMenuItemId.value = items[0].id
  }
}, { immediate: true })

const getImageDimensions = (file) => new Promise((resolve, reject) => {
  const url = URL.createObjectURL(file)
  const image = new Image()
  image.onload = () => {
    const width = image.width
    const height = image.height
    URL.revokeObjectURL(url)
    resolve({ width, height })
  }
  image.onerror = () => {
    URL.revokeObjectURL(url)
    reject(new Error('No se pudieron leer las dimensiones de la imagen.'))
  }
  image.src = url
})

const validateImageFile = async (file, context = 'general') => {
  if (!ADMIN_IMAGE_TYPES.includes(file.type)) {
    setError('Usa una imagen JPG, PNG o WebP.')
    return false
  }

  if (file.size > MAX_ADMIN_IMAGE_SIZE_BYTES) {
    setError('La imagen no debe pesar más de 10 MB.')
    return false
  }

  try {
    const { width, height } = await getImageDimensions(file)
    const minimum = ADMIN_IMAGE_MIN_DIMENSIONS[context] || ADMIN_IMAGE_MIN_DIMENSIONS.general
    if (width < minimum.width || height < minimum.height) {
      setError(`La imagen es muy pequeña para ${context}. Mínimo ${minimum.width}x${minimum.height}px.`)
      return false
    }
  } catch (requestError) {
    setError(requestError.message || 'No se pudo validar la imagen seleccionada.')
    return false
  }

  return true
}

const persistDrafts = () => {
  const payload = {
    settings: sanitizePayload(settings.value),
    newJob: sanitizePayload(newJob.value),
    newMenuItem: sanitizePayload(newMenuItem.value),
    newPromotion: sanitizePayload(newPromotion.value),
    newBranch: sanitizePayload(newBranch.value),
    timestamp: new Date().toISOString(),
  }
  localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(payload))
  lastDraftSavedAt.value = payload.timestamp
}

const restoreDrafts = () => {
  const raw = localStorage.getItem(DRAFT_STORAGE_KEY)
  if (!raw) {
    return
  }

  try {
    const data = JSON.parse(raw)
    if (data.settings) {
      settings.value = { ...settings.value, ...data.settings }
    }
    if (data.newJob) {
      newJob.value = { ...newJob.value, ...data.newJob }
    }
    if (data.newMenuItem) {
      newMenuItem.value = { ...newMenuItem.value, ...data.newMenuItem }
    }
    if (data.newPromotion) {
      newPromotion.value = { ...newPromotion.value, ...data.newPromotion }
    }
    if (data.newBranch) {
      newBranch.value = { ...newBranch.value, ...data.newBranch }
    }
    lastDraftSavedAt.value = data.timestamp || ''
  } catch (requestError) {
    localStorage.removeItem(DRAFT_STORAGE_KEY)
    lastDraftSavedAt.value = ''
  }
}

const fetchImageGallery = async () => {
  if (!canManageLanding.value) {
    imageGallery.value = []
    return
  }

  try {
    const response = await api.get('/admin/uploads/gallery')
    imageGallery.value = response.data?.images || []
  } catch (requestError) {
    imageGallery.value = []
  }
}

const fetchAll = async () => {
  loading.value = true
  statusError.value = ''

  try {
    const meResponse = await api.get('/admin/auth/me')
    currentAdmin.value = {
      id: meResponse.data?.user?.id || null,
      name: meResponse.data?.user?.name || '',
      email: meResponse.data?.user?.email || '',
      role: meResponse.data?.user?.role || '',
      permissions: meResponse.data?.user?.permissions || [],
      two_factor_enabled: Boolean(meResponse.data?.user?.two_factor_enabled),
    }

    const shouldLoadMessages = canManageMessages.value
    const shouldLoadHistory = canViewHistory.value
    const shouldLoadTrash = canManageJobs.value || canManageMenu.value

    const requests = [api.get('/admin/dashboard-content')]
    if (canManageLanding.value) {
      requests.push(api.get('/admin/uploads/gallery'))
    }
    if (shouldLoadTrash) {
      requests.push(api.get('/admin/jobs/trash'))
      requests.push(api.get('/admin/menu-items/trash'))
    }
    if (shouldLoadMessages) {
      requests.push(api.get('/admin/contact-messages'))
    }
    if (shouldLoadHistory) {
      requests.push(fetchChangeLogs(1))
    }
    requests.push(api.get('/admin/system-alerts'))
    if (canApproveRequests.value) {
      requests.push(api.get('/admin/approvals?status=pending'))
    }

    const responses = await Promise.all(requests)
    const dashboardResponse = responses[0]
    let responseIndex = 1
    const galleryResponse = canManageLanding.value ? responses[responseIndex++] : null
    const jobsTrashResponse = shouldLoadTrash ? responses[responseIndex++] : null
    const menuTrashResponse = shouldLoadTrash ? responses[responseIndex++] : null
    const messagesResponse = shouldLoadMessages ? responses[responseIndex++] : null
    const alertsResponse = responses[responseIndex++]
    const approvalsResponse = canApproveRequests.value ? responses[responseIndex++] : null

    settings.value = {
      hero_title: dashboardResponse.data?.settings?.hero_title || '',
      hero_description: dashboardResponse.data?.settings?.hero_description || '',
      hero_image_url: dashboardResponse.data?.settings?.hero_image_url || '',
      company_address: dashboardResponse.data?.settings?.company_address || '',
    }

    dashboardSummary.value = dashboardResponse.data?.summary || dashboardSummary.value
    jobs.value = dashboardResponse.data?.jobs || []
    menuItems.value = dashboardResponse.data?.menu_items || []
    promotions.value = dashboardResponse.data?.promotions || []
    branches.value = dashboardResponse.data?.branches || []
    imageGallery.value = galleryResponse?.data?.images || []
    trashedJobs.value = jobsTrashResponse?.data?.jobs || []
    trashedMenuItems.value = menuTrashResponse?.data?.items || []
    contactMessages.value = shouldLoadMessages ? (messagesResponse?.data || []) : []
    systemAlerts.value = alertsResponse?.data || systemAlerts.value
    pendingApprovals.value = approvalsResponse?.data?.requests || []
    if (!shouldLoadHistory) {
      changeLogs.value = []
      historyPage.value = 1
      historyPageCount.value = 1
    }

    if (!hasRestoredDrafts.value) {
      restoreDrafts()
      hasRestoredDrafts.value = true
    }
  } catch (requestError) {
    if (requestError.response?.status === 401) {
      clearAdminToken()
      await router.push('/admin/login')
      return
    }
    setError(requestError.userMessage || 'No se pudo cargar la información del panel.')
  } finally {
    loading.value = false
  }
}

const fetchChangeLogs = async (page = 1) => {
  if (!canViewHistory.value) {
    return
  }

  try {
    const response = await api.get('/admin/change-logs', {
      params: {
        page,
        per_page: 10,
        search: historySearch.value || undefined,
        action: historyActionFilter.value || undefined,
        admin_user_id: historyAdminFilter.value || undefined,
        date_from: historyDateFrom.value || undefined,
        date_to: historyDateTo.value || undefined,
      },
    })

    changeLogs.value = response.data?.data || []
    historyPage.value = response.data?.current_page || page
    historyPageCount.value = response.data?.last_page || 1
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo cargar el historial de cambios.')
  }
}

const exportChangeLogs = async () => {
  if (!canViewHistory.value) {
    return
  }

  try {
    const response = await api.get('/admin/change-logs/export', {
      params: {
        search: historySearch.value || undefined,
        action: historyActionFilter.value || undefined,
        admin_user_id: historyAdminFilter.value || undefined,
        date_from: historyDateFrom.value || undefined,
        date_to: historyDateTo.value || undefined,
      },
      responseType: 'blob',
    })

    const link = document.createElement('a')
    const blob = new Blob([response.data], { type: 'text/csv;charset=utf-8;' })
    const url = window.URL.createObjectURL(blob)
    link.href = url
    link.download = 'admin-change-history.csv'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    setSuccess('Historial exportado correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo exportar el historial.')
  }
}

const validateSettings = () => {
  if (!settings.value.hero_title?.trim()) {
    return 'El título principal no puede quedar vacío.'
  }
  if (!settings.value.hero_description?.trim()) {
    return 'La descripción principal no puede quedar vacía.'
  }
  if (!settings.value.company_address?.trim()) {
    return 'La dirección no puede quedar vacía.'
  }
  return ''
}

const validateJob = (job) => {
  if (!job.title?.trim()) {
    return 'La vacante necesita un título.'
  }
  if (!job.description?.trim()) {
    return 'La vacante necesita una descripción.'
  }
  if (!job.employment_type?.trim()) {
    return 'La vacante necesita un tipo de empleo.'
  }
  return ''
}

const validateMenuItem = (item) => {
  if (!item.name?.trim()) {
    return 'El producto necesita un nombre.'
  }
  if (!item.description?.trim()) {
    return 'El producto necesita una descripción.'
  }
  if (!item.category?.trim()) {
    return 'El producto necesita una categoría.'
  }
  if (Number.isNaN(Number(item.price)) || Number(item.price) < 0) {
    return 'El precio debe ser un número válido mayor o igual a 0.'
  }
  return ''
}

const selectMenuItem = (itemId) => {
  selectedMenuItemId.value = itemId
}

const validatePromotion = (promotion) => {
  if (!promotion.title?.trim()) {
    return 'La promoción necesita un título.'
  }
  if (!promotion.description?.trim()) {
    return 'La promoción necesita una descripción.'
  }
  return ''
}

const validateBranch = (branch) => {
  if (!branch.name?.trim()) {
    return 'La sucursal necesita un nombre.'
  }
  if (!branch.address?.trim()) {
    return 'La sucursal necesita una dirección.'
  }
  return ''
}

const validateAdminUser = () => {
  if (!newAdminUser.value.name?.trim()) {
    return 'El usuario admin necesita un nombre.'
  }
  if (!newAdminUser.value.email?.trim()) {
    return 'El usuario admin necesita un correo.'
  }
  if ((newAdminUser.value.password || '').length < 8) {
    return 'La contraseña debe tener al menos 8 caracteres.'
  }
  if (!newAdminUser.value.role) {
    return 'Selecciona un rol para el administrador.'
  }
  return ''
}

const finalizePendingUpload = async (target, fieldName = 'image_url') => {
  if (!target?.__uploadFile) {
    return false
  }

  try {
    const uploadedUrl = await uploadImage(target.__uploadFile, target.__uploadContext || 'general')
    if (uploadedUrl) {
      target[fieldName] = uploadedUrl
      revokePendingUpload(target)
      return true
    }
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo subir la imagen seleccionada.')
    revokePendingUpload(target)
    return false
  }

  return false
}

const sanitizePayload = (payload) => {
  if (!payload || typeof payload !== 'object') {
    return payload
  }

  const sanitized = { ...payload }
  delete sanitized.__uploadFile
  delete sanitized.__uploadFieldName
  delete sanitized.__uploadContext
  delete sanitized.__uploadPreviewUrl
  delete sanitized.__uploading
  return sanitized
}

const saveSettings = async () => {
  if (!canManageSettings.value) {
    setError('Tu rol no tiene permisos para editar el landing.')
    return
  }

  const validationError = validateSettings()
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (settings.value.__uploadFile) {
      await finalizePendingUpload(settings.value, 'hero_image_url')
    }
    await api.put('/admin/settings', sanitizePayload(settings.value))
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Contenido principal actualizado.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudieron guardar los cambios del contenido principal.')
  }
}

const createJob = async () => {
  if (!canManageJobs.value) {
    setError('Tu rol no tiene permisos para crear vacantes.')
    return
  }

  const validationError = validateJob(newJob.value)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (newJob.value.__uploadFile) {
      await finalizePendingUpload(newJob.value, 'image_url')
    }
    const response = await api.post('/admin/jobs', { ...sanitizePayload(newJob.value), is_active: true, sort_order: jobs.value.length + 1 })
    jobs.value.push(response.data.job)
    newJob.value = { title: '', description: '', employment_type: 'Tiempo completo', location: '', schedule: '', image_url: '' }
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Vacante creada correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo crear la vacante.')
  }
}

const saveJob = async (job) => {
  if (!canManageJobs.value) {
    setError('Tu rol no tiene permisos para editar vacantes.')
    return
  }

  const validationError = validateJob(job)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (job.__uploadFile) {
      await finalizePendingUpload(job, 'image_url')
    }
    await api.patch(`/admin/jobs/${job.id}`, sanitizePayload(job))
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Vacante actualizada.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo actualizar la vacante.')
  }
}

const promptDeleteConfirmation = (action, title = 'Confirmar acción') => {
  deleteAction.value = action
  confirmDialog.value?.show(title, '')
}

const executeDeleteAction = async () => {
  if (!deleteAction.value) {
    return
  }

  const action = deleteAction.value
  deleteAction.value = null

  try {
    await action()
  } catch (requestError) {
    setError(requestError?.userMessage || 'No se pudo completar la acción solicitada.')
  }
}

const deleteJob = async (jobId) => {
  if (!canManageJobs.value) {
    setError('Tu rol no tiene permisos para eliminar vacantes.')
    return
  }

  promptDeleteConfirmation(async () => {
    try {
      await api.delete(`/admin/jobs/${jobId}`)
      jobs.value = jobs.value.filter((job) => job.id !== jobId)
      trashedJobs.value = await api.get('/admin/jobs/trash').then((response) => response.data?.jobs || [])
      await loadLandingContent(true)
      setSuccess('Vacante enviada a la papelera.')
    } catch (requestError) {
      setError(requestError.userMessage || 'No se pudo eliminar la vacante.')
    }
  }, '¿Enviar vacante a la papelera?')
}

const restoreJob = async (jobId) => {
  if (!canManageJobs.value) {
    setError('Tu rol no tiene permisos para restaurar vacantes.')
    return
  }

  try {
    await api.post(`/admin/jobs/${jobId}/restore`)
    trashedJobs.value = await api.get('/admin/jobs/trash').then((response) => response.data?.jobs || [])
    jobs.value = await api.get('/admin/dashboard-content').then((response) => response.data?.jobs || [])
    await loadLandingContent(true)
    setSuccess('Vacante restaurada.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo restaurar la vacante.')
  }
}

const uploadImage = async (file, context = 'general') => {
  const formData = new FormData()
  formData.append('image', file)
  formData.append('context', context)

  const response = await api.post('/admin/uploads/image', formData)

  return response.data?.url || ''
}

const onImageSelected = async (event, target, fieldName = 'image_url', context = 'general') => {
  const contextPermissions = {
    hero: canManageSettings.value,
    job: canManageJobs.value,
    menu: canManageMenu.value,
    promotion: canManagePromotions.value,
    branch: canManageBranches.value,
    general: canManageLanding.value,
  }

  if (!contextPermissions[context]) {
    setError('Tu rol no tiene permisos para subir imágenes.')
    return
  }

  const [file] = event.target.files || []
  if (!file) {
    return
  }
  if (!(await validateImageFile(file, context))) {
    event.target.value = ''
    return
  }

  setPendingUpload(target, file, fieldName, context)
  setSuccess('Vista previa lista. Confirma para aplicar la imagen.')
  event.target.value = ''
}

const onImageDropped = async (event, target, fieldName = 'image_url', context = 'general') => {
  event.preventDefault()
  const contextPermissions = {
    hero: canManageSettings.value,
    job: canManageJobs.value,
    menu: canManageMenu.value,
    promotion: canManagePromotions.value,
    branch: canManageBranches.value,
    general: canManageLanding.value,
  }

  if (!contextPermissions[context]) {
    setError('Tu rol no tiene permisos para subir imágenes.')
    return
  }

  const [file] = event.dataTransfer?.files || []
  if (!file) {
    return
  }
  if (!(await validateImageFile(file, context))) {
    return
  }

  setPendingUpload(target, file, fieldName, context)
  setSuccess('Imagen lista para confirmar.')
}

const confirmPendingUpload = async (target) => {
  if (!target?.__uploadFile) {
    return
  }

  await finalizePendingUpload(target, target.__uploadFieldName || 'image_url')
}

const cancelPendingUpload = (target) => {
  revokePendingUpload(target)
  setSuccess('Se canceló la selección de imagen.')
}

const applyGalleryImage = (target, fieldName = 'image_url', imageUrl) => {
  if (!target || !imageUrl) {
    return
  }

  revokePendingUpload(target)
  target[fieldName] = imageUrl
  setSuccess('Se aplicó una imagen de la galería.')
}

const removeCurrentImage = async (target, fieldName = 'image_url') => {
  if (!canManageLanding.value) {
    setError('Tu rol no tiene permisos para quitar imágenes.')
    return
  }

  const currentUrl = target?.[fieldName]
  if (!currentUrl) {
    return
  }

  try {
    await api.delete('/admin/uploads/image', {
      data: { url: currentUrl },
    })
    target[fieldName] = ''
    setSuccess('Imagen removida correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo remover la imagen.')
  }
}

const createMenuItem = async () => {
  if (!canManageMenu.value) {
    setError('Tu rol no tiene permisos para crear productos.')
    return
  }

  const validationError = validateMenuItem(newMenuItem.value)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (newMenuItem.value.__uploadFile) {
      await finalizePendingUpload(newMenuItem.value, 'image_url')
    }
    const response = await api.post('/admin/menu-items', {
      ...sanitizePayload(newMenuItem.value),
      is_featured: true,
      is_active: true,
      sort_order: menuItems.value.length + 1,
    })
    menuItems.value.push(response.data.item)
    selectedMenuItemId.value = response.data.item.id
    newMenuItem.value = { name: '', description: '', price: 0, image_url: '', badge: '', category: 'Paquetes' }
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Producto creado correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo crear el producto.')
  }
}

const saveMenuItem = async (item, skipToast = false) => {
  if (!canManageMenu.value) {
    setError('Tu rol no tiene permisos para editar productos.')
    return
  }

  const validationError = validateMenuItem(item)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (item.__uploadFile) {
      await finalizePendingUpload(item, 'image_url')
    }
    await api.patch(`/admin/menu-items/${item.id}`, sanitizePayload(item))
    await loadLandingContent(true)
    await fetchImageGallery()
    if (!skipToast) {
      setSuccess('Producto actualizado.')
    }
    await loadLastOperation()
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo actualizar el producto.')
  }
}

const deleteMenuItem = async (itemId) => {
  if (!canManageMenu.value) {
    setError('Tu rol no tiene permisos para eliminar productos.')
    return
  }

  promptDeleteConfirmation(async () => {
    try {
      await api.delete(`/admin/menu-items/${itemId}`)
      menuItems.value = menuItems.value.filter((item) => item.id !== itemId)
      trashedMenuItems.value = await api.get('/admin/menu-items/trash').then((response) => response.data?.items || [])
      await loadLandingContent(true)
      setSuccess('Producto enviado a la papelera.')
    } catch (requestError) {
      setError(requestError.userMessage || 'No se pudo eliminar el producto.')
    }
  }, '¿Enviar producto a la papelera?')
}

const restoreMenuItem = async (itemId) => {
  if (!canManageMenu.value) {
    setError('Tu rol no tiene permisos para restaurar productos.')
    return
  }

  try {
    await api.post(`/admin/menu-items/${itemId}/restore`)
    trashedMenuItems.value = await api.get('/admin/menu-items/trash').then((response) => response.data?.items || [])
    menuItems.value = await api.get('/admin/dashboard-content').then((response) => response.data?.menu_items || [])
    await loadLandingContent(true)
    setSuccess('Producto restaurado.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo restaurar el producto.')
  }
}

const restoreDefaultMenu = async () => {
  if (!canManageMenu.value) {
    setError('Tu rol no tiene permisos para restaurar el menú.')
    return
  }

  promptDeleteConfirmation(async () => {
    try {
      const response = await api.post('/admin/menu-items/restore-default')
      addToast('Menú restaurado exitosamente', 'success')
      await loadLandingContent(true)
      await fetchImageGallery()
      menuItems.value = await api.get('/admin/dashboard-content').then((response) => response.data?.menu_items || [])
    } catch (requestError) {
      setError(requestError.userMessage || 'No se pudo restaurar el menú predeterminado.')
    }
  }, '¿Restaurar los 20 productos por defecto? Se añadirán al menú actual.')
}

const reorderById = (collection, sourceId, targetId) => {
  const sourceIndex = collection.findIndex((item) => item.id === sourceId)
  const targetIndex = collection.findIndex((item) => item.id === targetId)
  if (sourceIndex < 0 || targetIndex < 0 || sourceIndex === targetIndex) {
    return collection
  }

  const next = [...collection]
  const [sourceItem] = next.splice(sourceIndex, 1)
  next.splice(targetIndex, 0, sourceItem)
  return next
}

const persistPromotionSortOrder = async () => {
  if (persistingPromotionOrder.value) {
    return
  }

  persistingPromotionOrder.value = true
  try {
    const requests = promotions.value.map((promotion, index) => {
      const payload = sanitizePayload(promotion)
      if (!payload.valid_until) {
        payload.valid_until = null
      }
      payload.sort_order = index + 1
      return api.patch(`/admin/promotions/${promotion.id}`, payload)
    })
    await Promise.all(requests)
    await loadLandingContent(true)
    setSuccess('Orden de promociones actualizado.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo guardar el nuevo orden de promociones.')
  } finally {
    persistingPromotionOrder.value = false
  }
}

const persistBranchSortOrder = async () => {
  if (persistingBranchOrder.value) {
    return
  }

  persistingBranchOrder.value = true
  try {
    const requests = branches.value.map((branch, index) => {
      const payload = sanitizePayload(branch)
      if (!payload.map_link) {
        payload.map_link = null
      }
      payload.sort_order = index + 1
      return api.patch(`/admin/branches/${branch.id}`, payload)
    })
    await Promise.all(requests)
    await loadLandingContent(true)
    setSuccess('Orden de sucursales actualizado.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo guardar el nuevo orden de sucursales.')
  } finally {
    persistingBranchOrder.value = false
  }
}

const onPromotionDragStart = (promotionId) => {
  draggedPromotionId.value = promotionId
}

const onPromotionDrop = async (targetPromotionId) => {
  if (!canManageLanding.value || draggedPromotionId.value === null) {
    return
  }

  const before = promotions.value
  const reordered = reorderById(before, draggedPromotionId.value, targetPromotionId)
  draggedPromotionId.value = null
  if (reordered === before) {
    return
  }

  promotions.value = reordered
  await persistPromotionSortOrder()
}

const onBranchDragStart = (branchId) => {
  draggedBranchId.value = branchId
}

const onBranchDrop = async (targetBranchId) => {
  if (!canManageLanding.value || draggedBranchId.value === null) {
    return
  }

  const before = branches.value
  const reordered = reorderById(before, draggedBranchId.value, targetBranchId)
  draggedBranchId.value = null
  if (reordered === before) {
    return
  }

  branches.value = reordered
  await persistBranchSortOrder()
}

const createPromotion = async () => {
  if (!canManagePromotions.value) {
    setError('Tu rol no tiene permisos para crear promociones.')
    return
  }

  const validationError = validatePromotion(newPromotion.value)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (newPromotion.value.__uploadFile) {
      await finalizePendingUpload(newPromotion.value, 'image_url')
    }
    const payload = sanitizePayload(newPromotion.value)
    if (!payload.valid_until) {
      payload.valid_until = null
    }
    const response = await api.post('/admin/promotions', {
      ...payload,
      sort_order: promotions.value.length + 1,
    })
    promotions.value.push(response.data.promotion)
    newPromotion.value = { title: '', description: '', image_url: '', valid_until: '', code: '', badge: '', is_active: true }
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Promoción creada correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo crear la promoción.')
  }
}

const savePromotion = async (promotion) => {
  if (!canManagePromotions.value) {
    setError('Tu rol no tiene permisos para editar promociones.')
    return
  }

  const validationError = validatePromotion(promotion)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (promotion.__uploadFile) {
      await finalizePendingUpload(promotion, 'image_url')
    }
    const payload = sanitizePayload(promotion)
    if (!payload.valid_until) {
      payload.valid_until = null
    }
    await api.patch(`/admin/promotions/${promotion.id}`, payload)
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Promoción actualizada.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo actualizar la promoción.')
  }
}

const deletePromotion = async (promotionId) => {
  if (!canManagePromotions.value) {
    setError('Tu rol no tiene permisos para eliminar promociones.')
    return
  }

  promptDeleteConfirmation(async () => {
    try {
      await api.delete(`/admin/promotions/${promotionId}`)
      promotions.value = promotions.value.filter((promotion) => promotion.id !== promotionId)
      await loadLandingContent(true)
      setSuccess('Promoción eliminada.')
    } catch (requestError) {
      setError(requestError.userMessage || 'No se pudo eliminar la promoción.')
    }
  }, '¿Eliminar esta promoción?')
}

const createBranch = async () => {
  if (!canManageBranches.value) {
    setError('Tu rol no tiene permisos para crear sucursales.')
    return
  }

  const validationError = validateBranch(newBranch.value)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (newBranch.value.__uploadFile) {
      await finalizePendingUpload(newBranch.value, 'image_url')
    }
    const payload = sanitizePayload(newBranch.value)
    if (!payload.map_link) {
      payload.map_link = null
    }
    const response = await api.post('/admin/branches', {
      ...payload,
      sort_order: branches.value.length + 1,
    })
    branches.value.push(response.data.branch)
    newBranch.value = { name: '', address: '', phone: '', schedule: '', image_url: '', map_link: '', is_open: true }
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Sucursal creada correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo crear la sucursal.')
  }
}

const saveBranch = async (branch) => {
  if (!canManageBranches.value) {
    setError('Tu rol no tiene permisos para editar sucursales.')
    return
  }

  const validationError = validateBranch(branch)
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    if (branch.__uploadFile) {
      await finalizePendingUpload(branch, 'image_url')
    }
    const payload = sanitizePayload(branch)
    if (!payload.map_link) {
      payload.map_link = null
    }
    await api.patch(`/admin/branches/${branch.id}`, payload)
    await loadLandingContent(true)
    await fetchImageGallery()
    setSuccess('Sucursal actualizada.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo actualizar la sucursal.')
  }
}

const deleteBranch = async (branchId) => {
  if (!canManageBranches.value) {
    setError('Tu rol no tiene permisos para eliminar sucursales.')
    return
  }

  promptDeleteConfirmation(async () => {
    try {
      await api.delete(`/admin/branches/${branchId}`)
      branches.value = branches.value.filter((branch) => branch.id !== branchId)
      await loadLandingContent(true)
      setSuccess('Sucursal eliminada.')
    } catch (requestError) {
      setError(requestError.userMessage || 'No se pudo eliminar la sucursal.')
    }
  }, '¿Eliminar esta sucursal?')
}

const saveContactMessage = async (message) => {
  if (!canManageMessages.value) {
    setError('Tu rol no tiene permisos para gestionar mensajes.')
    return
  }

  try {
    await api.patch(`/admin/contact-messages/${message.id}`, {
      status: message.status,
      notes: message.notes || null,
    })
    setSuccess('Mensaje actualizado con notas de seguimiento.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo actualizar el estado del mensaje.')
  }
}

const createAdminUser = async () => {
  if (!canManageAdmins.value) {
    setError('Tu rol no tiene permisos para crear usuarios administradores.')
    return
  }

  const validationError = validateAdminUser()
  if (validationError) {
    setError(validationError)
    return
  }

  try {
    await api.post('/admin/users', newAdminUser.value)
    newAdminUser.value = { name: '', email: '', password: '', role: 'content_manager' }
    setSuccess('Usuario administrador creado.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo crear el usuario administrador.')
  }
}

const fetchSystemAlerts = async () => {
  if (!canViewHistory.value) {
    return
  }

  try {
    const response = await api.get('/admin/system-alerts')
    systemAlerts.value = response.data || systemAlerts.value
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudieron actualizar las alertas internas.')
  }
}

const fetchPendingApprovals = async () => {
  if (!canApproveRequests.value) {
    pendingApprovals.value = []
    return
  }

  try {
    const response = await api.get('/admin/approvals?status=pending')
    pendingApprovals.value = response.data?.requests || []
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudieron cargar las aprobaciones pendientes.')
  }
}

const approveRequest = async (requestId) => {
  try {
    await api.post(`/admin/approvals/${requestId}/approve`)
    await fetchPendingApprovals()
    await fetchSystemAlerts()
    setSuccess('Solicitud aprobada.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo aprobar la solicitud.')
  }
}

const rejectRequest = async (requestId) => {
  try {
    await api.post(`/admin/approvals/${requestId}/reject`)
    await fetchPendingApprovals()
    await fetchSystemAlerts()
    setSuccess('Solicitud rechazada.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo rechazar la solicitud.')
  }
}

const setupTwoFactor = async () => {
  try {
    const response = await api.post('/admin/auth/2fa/setup')
    twoFactorSetupSecret.value = response.data?.secret || ''
    twoFactorSetupUrl.value = response.data?.otpauth_url || ''
    setSuccess('Se generó el secreto 2FA. Ingresa un código para activarlo.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo iniciar la configuración 2FA.')
  }
}

const enableTwoFactor = async () => {
  try {
    await api.post('/admin/auth/2fa/enable', { code: twoFactorCode.value.trim() })
    twoFactorCode.value = ''
    twoFactorSetupSecret.value = ''
    twoFactorSetupUrl.value = ''
    currentAdmin.value.two_factor_enabled = true
    setSuccess('2FA activado correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo activar 2FA.')
  }
}

const disableTwoFactor = async () => {
  try {
    await api.post('/admin/auth/2fa/disable', { code: twoFactorCode.value.trim() })
    twoFactorCode.value = ''
    twoFactorSetupSecret.value = ''
    twoFactorSetupUrl.value = ''
    currentAdmin.value.two_factor_enabled = false
    setSuccess('2FA desactivado correctamente.')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo desactivar 2FA.')
  }
}

const logout = async () => {
  try {
    await api.post('/admin/auth/logout')
  } catch (requestError) {
    setError(requestError.userMessage || 'No se pudo cerrar la sesión remota, pero se cerrará localmente.')
  } finally {
    clearAdminToken()
    await router.push('/admin/login')
  }
}

const scrollToSection = (sectionId) => {
  activeSection.value = sectionId
  const target = document.getElementById(sectionId)
  if (target) {
    target.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const handleSearchSelect = (result) => {
  if (result.type === 'job') {
    scrollToSection('jobs')
  } else if (result.type === 'menuItem') {
    scrollToSection('menu')
    selectMenuItem(result.data.id)
  } else if (result.type === 'promotion') {
    scrollToSection('promotions')
  } else if (result.type === 'branch') {
    scrollToSection('branches')
  } else if (result.type === 'message') {
    scrollToSection('messages')
    messagePage.value = 1
  }
}

const cleanupPendingUploads = () => {
  [settings.value, newJob.value, newMenuItem.value, newPromotion.value, newBranch.value].forEach((target) => revokePendingUpload(target))
  jobs.value.forEach((job) => revokePendingUpload(job))
  menuItems.value.forEach((item) => revokePendingUpload(item))
  promotions.value.forEach((promotion) => revokePendingUpload(promotion))
  branches.value.forEach((branch) => revokePendingUpload(branch))
}

const updateActiveSection = () => {
  if (scrollFrame !== null) {
    return
  }

  scrollFrame = requestAnimationFrame(() => {
    const sectionElements = sections
      .map((section) => document.getElementById(section.id))
      .filter(Boolean)

    const scrollPosition = window.scrollY + 180
    let current = 'overview'

    sectionElements.forEach((section) => {
      if (section.offsetTop <= scrollPosition) {
        current = section.id
      }
    })

    activeSection.value = current
    scrollFrame = null
  })
}

watch([messageSearch, messageStatusFilter], () => {
  messagePage.value = 1
})

watch(filteredMessages, () => {
  if (messagePage.value > messagePageCount.value) {
    messagePage.value = messagePageCount.value
  }
})

watch([historySearch, historyActionFilter, historyDateFrom, historyDateTo, historyAdminFilter], () => {
  if (canViewHistory.value) {
    fetchChangeLogs(1)
  }
})

watch(
  [settings, newJob, newMenuItem, newPromotion, newBranch],
  () => {
    if (hasRestoredDrafts.value) {
      persistDrafts()
    }
  },
  { deep: true }
)

const undoLastChange = async () => {
  if (!canViewHistory.value) {
    setError('No tienes permisos para deshacer cambios.')
    return
  }

  loadingSpinner.value?.show()
  try {
    const response = await api.post('/admin/change-logs/revert-last')
    lastOperation.value = null
    loadingSpinner.value?.hide()
    addToast(`Cambio deshecho: ${response.data.last_change?.description}`, 'success')
    await loadLandingContent(true)
    await fetchChangeLogs(1)
  } catch (error) {
    loadingSpinner.value?.hide()
    setError(error.userMessage || 'No se pudo deshacer el cambio.')
  }
}

const loadLastOperation = async () => {
  try {
    const response = await api.get('/admin/change-logs', { params: { per_page: 1 } })
    if (response.data?.data?.[0]) {
      lastOperation.value = response.data.data[0].description
    }
  } catch (error) {
    lastOperation.value = null
  }
}

const exportData = async (type) => {
  try {
    const response = await api.get('/admin/export-data', {
      params: { type },
      responseType: 'blob'
    })
    // Axios returns Blob payload in response.data; using response object breaks downloads.
    const url = window.URL.createObjectURL(response.data)
    const link = document.createElement('a')
    link.href = url
    link.download = `admin-export-${type}-${new Date().toISOString().split('T')[0]}.csv`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    addToast(`Archivo ${type} exportado correctamente`, 'success')
  } catch (error) {
    setError('No se pudo exportar los datos.')
  } finally {
    showExportMenu.value = false
  }
}

const dataUrlToFile = (dataUrl, filename = 'cropped-image.png') => {
  // Convert cropper base64 output to File so existing upload pipeline can be reused.
  const [meta, base64] = dataUrl.split(',')
  const mime = (meta.match(/data:(.*?);base64/) || [])[1] || 'image/png'
  const binary = atob(base64)
  const bytes = new Uint8Array(binary.length)
  for (let i = 0; i < binary.length; i += 1) {
    bytes[i] = binary.charCodeAt(i)
  }
  return new File([bytes], filename, { type: mime })
}

const openImageCropper = (target, ratio = '4/3') => {
  if (!target?.__uploadPreviewUrl) {
    setError('Primero selecciona una imagen para recortar.')
    return
  }
  cropTarget.value = target
  imageCropper.value?.show(target.__uploadPreviewUrl, ratio)
}

const onImageCropped = (croppedImage) => {
  if (!cropTarget.value) {
    return
  }

  const target = cropTarget.value
  const file = dataUrlToFile(croppedImage, `cropped-${Date.now()}.png`)
  const fieldName = target.__uploadFieldName || 'image_url'
  const context = target.__uploadContext || 'general'
  setPendingUpload(target, file, fieldName, context)
  cropTarget.value = null
  addToast('Imagen recortada y lista para aplicar.', 'success')
}

const bulkActionChangeStatus = async (status) => {
  if (!canManageMenu.value) {
    setError('No tienes permisos para esta acción.')
    return
  }
  
  loadingSpinner.value?.show()
  try {
    for (const id of bulkSelectedIds.value) {
      const item = menuItems.value.find(m => m.id === id)
      if (item) {
        item.is_active = status
        await saveMenuItem(item, true)
      }
    }
    addToast(`${bulkSelectedIds.value.length} producto(s) actualizado(s)`, 'success')
    bulkMode.value = false
    bulkSelectedIds.value = []
    loadingSpinner.value?.hide()
  } catch (error) {
    loadingSpinner.value?.hide()
    setError('Error al actualizar los productos.')
  }
}

const bulkActionToggleFeatured = async () => {
  if (!canManageMenu.value) {
    setError('No tienes permisos para esta acción.')
    return
  }
  
  loadingSpinner.value?.show()
  try {
    for (const id of bulkSelectedIds.value) {
      const item = menuItems.value.find(m => m.id === id)
      if (item) {
        item.is_featured = !item.is_featured
        await saveMenuItem(item, true)
      }
    }
    addToast(`${bulkSelectedIds.value.length} producto(s) actualizado(s)`, 'success')
    bulkMode.value = false
    bulkSelectedIds.value = []
    loadingSpinner.value?.hide()
  } catch (error) {
    loadingSpinner.value?.hide()
    setError('Error al actualizar los productos.')
  }
}

const toggleBulkSelect = (itemId) => {
  const index = bulkSelectedIds.value.indexOf(itemId)
  if (index === -1) {
    bulkSelectedIds.value.push(itemId)
  } else {
    bulkSelectedIds.value.splice(index, 1)
  }
}

onMounted(async () => {
  await fetchAll()
  await loadLastOperation()
  if (canViewHistory.value) {
    alertsIntervalId = window.setInterval(() => {
      fetchSystemAlerts()
      if (canApproveRequests.value) {
        fetchPendingApprovals()
      }
    }, 60000)
  }
  window.addEventListener('scroll', updateActiveSection, { passive: true })
  updateActiveSection()
})

onBeforeUnmount(() => {
  cleanupPendingUploads()
  window.removeEventListener('scroll', updateActiveSection)
  if (alertsIntervalId !== null) {
    clearInterval(alertsIntervalId)
    alertsIntervalId = null
  }
  if (scrollFrame !== null) {
    cancelAnimationFrame(scrollFrame)
  }
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
