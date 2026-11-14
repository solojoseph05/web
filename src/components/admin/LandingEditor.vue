<!-- src/components/admin/LandingEditor.vue -->
<template>
  <div class="admin-panel space-y-6">
    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-5 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xs">
      <div>
        <div class="flex items-center gap-2.5">
          <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
            Editor Completo del Sitio Web (Landing)
          </h2>
          <span class="rounded-full bg-red-100 text-red-700 text-xs px-2.5 py-0.5 font-bold dark:bg-red-950/60 dark:text-red-300">
            En Vivo
          </span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Modifica titulares, descripciones, estadísticas de confianza, imágenes de portada y datos de contacto de todo el sitio.
        </p>
      </div>

      <div class="flex items-center gap-2.5 self-start sm:self-auto">
        <router-link
          to="/"
          target="_blank"
          class="rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer transition flex items-center gap-1.5"
        >
          <span>👁️ Ver Sitio Web</span>
        </router-link>

        <button
          @click="handleSave"
          :disabled="isSaving"
          class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-5 py-2 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition cursor-pointer flex items-center gap-1.5 disabled:opacity-50"
        >
          <svg v-if="isSaving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          <span>{{ isSaving ? 'Guardando...' : '💾 Guardar Cambios' }}</span>
        </button>
      </div>
    </div>

    <!-- Section Navigation Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-800 pb-2">
      <button
        v-for="section in editorSections"
        :key="section.id"
        @click="activeSection = section.id"
        class="flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-bold transition cursor-pointer"
        :class="activeSection === section.id
          ? 'bg-red-600 text-white shadow-xs'
          : 'bg-white text-gray-600 hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-800'"
      >
        <span>{{ section.icon }}</span>
        <span>{{ section.label }}</span>
      </button>
    </div>

    <!-- MAIN FORM BODY -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Left Column: Form Controls (7 cols) -->
      <div class="lg:col-span-7 space-y-6">
        <!-- SECTION 1: HERO PRINCIPAL -->
        <div v-show="activeSection === 'hero'" class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
          <h3 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2">
            <span>🚀 Portada Principal (Hero)</span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Configura el primer impacto visual que ven los clientes al entrar a la página.
          </p>

          <div class="space-y-4 pt-2">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Título Principal de la Portada
              </label>
              <input
                v-model="form.hero_title"
                type="text"
                placeholder="El mejor Pollo Asado de Durango"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 font-semibold transition"
              />
              <span class="text-[11px] text-gray-400 mt-0.5 block">Tip: Puedes incluir 'de Durango' para que aparezca con el estilo destacado rojo.</span>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Lema / Kicker Superior de Bienvenida
              </label>
              <input
                v-model="form.hero_subtitle"
                type="text"
                placeholder="¡Bienvenidos a la Familia Pollo Feliz! · Tradición al Carbón"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Descripción de Bienvenida
              </label>
              <textarea
                v-model="form.hero_description"
                rows="3"
                placeholder="Descubre el auténtico sabor que reúne a las familias..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 leading-relaxed resize-none transition"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Texto Botón Primario
                </label>
                <input
                  v-model="form.hero_cta_primary"
                  type="text"
                  placeholder="Ver Menú Completo"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Texto Botón Secundario
                </label>
                <input
                  v-model="form.hero_cta_secondary"
                  type="text"
                  placeholder="Ordenar en Línea"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
            </div>

            <!-- Hero Image Picker -->
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                URL de la Imagen Destacada del Hero
              </label>
              <div class="flex gap-2">
                <input
                  v-model="form.hero_image_url"
                  type="url"
                  placeholder="https://..."
                  class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 font-mono transition"
                />
                <button
                  type="button"
                  @click="useRandomHeroImage"
                  class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer"
                  title="Elegir una foto sugerida aleatoria"
                >
                  🎲 Sugerida
                </button>
              </div>

              <!-- Suggested Photos Quick Select -->
              <div class="mt-2.5 space-y-1.5">
                <span class="text-[11px] font-bold text-gray-400 block">Fotografías Recomendadas:</span>
                <div class="grid grid-cols-4 gap-2">
                  <div
                    v-for="(img, idx) in SUGGESTED_HERO_IMAGES"
                    :key="idx"
                    @click="form.hero_image_url = img"
                    class="h-14 rounded-xl overflow-hidden border-2 cursor-pointer transition relative group"
                    :class="form.hero_image_url === img ? 'border-red-600 ring-2 ring-red-400/40' : 'border-transparent hover:border-gray-300'"
                  >
                    <img :src="img" alt="Foto sugerida" class="h-full w-full object-cover group-hover:scale-110 transition duration-300" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: ESTADÍSTICAS Y CONFIANZA -->
        <div v-show="activeSection === 'stats'" class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
          <h3 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2">
            <span>🏆 Estadísticas de Confianza (Social Proof)</span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Estos 3 indicadores aparecen en la portada para dar respaldo y confianza a los clientes.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
            <!-- Stat 1 -->
            <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
              <span class="text-[10px] font-black uppercase text-gray-400">Métrica 1</span>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Valor</label>
                <input
                  v-model="form.stat_1_value"
                  type="text"
                  placeholder="100%"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-900 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Etiqueta</label>
                <input
                  v-model="form.stat_1_label"
                  type="text"
                  placeholder="Asado al Carbón"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
            </div>

            <!-- Stat 2 -->
            <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
              <span class="text-[10px] font-black uppercase text-red-500">Métrica 2 (Destacada)</span>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Valor</label>
                <input
                  v-model="form.stat_2_value"
                  type="text"
                  placeholder="4.9 ★"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-red-600 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-red-400 dark:placeholder-slate-400 transition"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Etiqueta</label>
                <input
                  v-model="form.stat_2_label"
                  type="text"
                  placeholder="+2,500 Reseñas"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
            </div>

            <!-- Stat 3 -->
            <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
              <span class="text-[10px] font-black uppercase text-gray-400">Métrica 3</span>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Valor</label>
                <input
                  v-model="form.stat_3_value"
                  type="text"
                  placeholder="3+"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-900 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Etiqueta</label>
                <input
                  v-model="form.stat_3_label"
                  type="text"
                  placeholder="Sucursales Activas"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 3: SECCIONES DEL SITIO (MENÚ, PROMOS, SUCURSALES) -->
        <div v-show="activeSection === 'sections'" class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-5">
          <h3 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2">
            <span>🍗 Encabezados de Secciones</span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Controla los títulos y lemas explicativos de cada bloque de la página de inicio.
          </p>

          <!-- Menú Section -->
          <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
            <span class="text-xs font-black text-gray-900 dark:text-white flex items-center gap-1.5">
              <span>🍗</span> Bloque de Menú Destacado
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Título</label>
                <input
                  v-model="form.menu_section_title"
                  type="text"
                  placeholder="Platos Destacados"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-900 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Subtítulo</label>
                <input
                  v-model="form.menu_section_subtitle"
                  type="text"
                  placeholder="Descubre nuestros platillos más populares..."
                  class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
            </div>
          </div>

          <!-- Promociones Section -->
          <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
            <span class="text-xs font-black text-gray-900 dark:text-white flex items-center gap-1.5">
              <span>🏷️</span> Bloque de Promociones
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Título</label>
                <input
                  v-model="form.promotions_section_title"
                  type="text"
                  placeholder="Promociones Imperdibles"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-900 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Subtítulo</label>
                <input
                  v-model="form.promotions_section_subtitle"
                  type="text"
                  placeholder="Aprovecha nuestras promociones especiales..."
                  class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
            </div>
          </div>

          <!-- Sucursales Section -->
          <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
            <span class="text-xs font-black text-gray-900 dark:text-white flex items-center gap-1.5">
              <span>📍</span> Bloque de Sucursales
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Título</label>
                <input
                  v-model="form.branches_section_title"
                  type="text"
                  placeholder="Nuestras Sucursales"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-900 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-0.5">Subtítulo</label>
                <input
                  v-model="form.branches_section_subtitle"
                  type="text"
                  placeholder="Visítanos o llámanos directamente..."
                  class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 4: CONTACTO Y DATOS DE EMPRESA -->
        <div v-show="activeSection === 'contact'" class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
          <h3 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2">
            <span>📞 Datos de Contacto y Redes</span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Afecta a la sección de contacto, pie de página (footer) y enlaces directos de llamada/WhatsApp.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Nombre de la Empresa
              </label>
              <input
                v-model="form.company_name"
                type="text"
                placeholder="Pollo Feliz Durango"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 font-semibold transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Teléfono de Atención
              </label>
              <input
                v-model="form.company_phone"
                type="text"
                placeholder="618-123-4567"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Correo Electrónico
              </label>
              <input
                v-model="form.company_email"
                type="email"
                placeholder="contacto@pollofeliz.com"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Número de WhatsApp
              </label>
              <input
                v-model="form.company_whatsapp"
                type="text"
                placeholder="618-123-4567"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Dirección Matriz
              </label>
              <input
                v-model="form.company_address"
                type="text"
                placeholder="Av. 5 de Mayo #123, Zona Centro, Durango, Dgo."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Horario de Servicio
              </label>
              <input
                v-model="form.schedule_weekday"
                type="text"
                placeholder="Lunes a Domingo: 10:00 AM - 10:00 PM"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Enlace de Facebook
              </label>
              <input
                v-model="form.social_facebook"
                type="url"
                placeholder="https://facebook.com/..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Enlace de Instagram
              </label>
              <input
                v-model="form.social_instagram"
                type="url"
                placeholder="https://instagram.com/..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
              />
            </div>
          </div>
        </div>

        <!-- Bottom Save & Reset Actions Bar -->
        <div class="flex items-center justify-between pt-2">
          <button
            type="button"
            @click="handleResetDefaults"
            class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 cursor-pointer transition"
          >
            Restaurar Valores Predeterminados
          </button>

          <button
            type="button"
            @click="handleSave"
            :disabled="isSaving"
            class="rounded-xl bg-red-600 px-6 py-2.5 text-xs font-black text-white shadow-md hover:bg-red-700 transition cursor-pointer flex items-center gap-2 disabled:opacity-50"
          >
            <span>{{ isSaving ? 'Guardando...' : 'Guardar y Publicar en el Sitio' }}</span>
          </button>
        </div>
      </div>

      <!-- Right Column: Interactive Live Preview Card (5 cols) -->
      <div class="lg:col-span-5 space-y-4">
        <div class="sticky top-20 rounded-3xl bg-white p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-gray-900 dark:text-white flex items-center gap-1.5">
              <span>📱</span> Vista Previa en Vivo (Hero)
            </span>
            <span class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold px-2 py-0.5 rounded-full">
              Sincronizado
            </span>
          </div>

          <!-- Mockup Card Container -->
          <div class="rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden bg-radial-[at_top_right] from-red-50/80 via-white to-orange-50/40 dark:from-red-950/30 dark:via-gray-950 dark:to-gray-950 p-4 space-y-3">
            <!-- Mock Welcoming Pill -->
            <div class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-white/90 px-2.5 py-1 text-[10px] font-semibold text-gray-700 dark:border-red-900/50 dark:bg-gray-900/80 dark:text-gray-200">
              <span class="h-2 w-2 rounded-full bg-red-600"></span>
              <span class="truncate">{{ form.hero_subtitle || '¡Bienvenidos a la Familia Pollo Feliz!' }}</span>
            </div>

            <!-- Mock Headline -->
            <h4 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white leading-tight">
              {{ form.hero_title || 'El mejor Pollo Asado de Durango' }}
            </h4>

            <!-- Mock Description -->
            <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-3 leading-relaxed">
              {{ form.hero_description || 'Descubre el auténtico sabor que reúne a la familia.' }}
            </p>

            <!-- Mock Buttons -->
            <div class="flex items-center gap-2 pt-1">
              <span class="rounded-full bg-red-600 px-3 py-1.5 text-[11px] font-bold text-white shadow-xs">
                {{ form.hero_cta_primary || 'Ver Menú' }}
              </span>
              <span class="rounded-full border border-red-200 bg-white px-3 py-1.5 text-[11px] font-bold text-red-600 dark:border-red-900 dark:bg-gray-900 dark:text-red-400">
                {{ form.hero_cta_secondary || 'Ordenar' }}
              </span>
            </div>

            <!-- Mock Image -->
            <div class="h-36 rounded-xl overflow-hidden mt-3 border border-gray-200 dark:border-gray-800 relative bg-gray-100 dark:bg-gray-800">
              <img
                v-if="form.hero_image_url"
                :src="form.hero_image_url"
                alt="Vista previa"
                class="h-full w-full object-cover"
                @error="(e) => e.target.style.display = 'none'"
              />
              <div v-else class="flex h-full w-full items-center justify-center text-xs text-gray-400">
                🍗 Foto de Pollo Asado
              </div>
            </div>

            <!-- Mock Stats Row -->
            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100 dark:border-gray-800 text-center">
              <div>
                <span class="font-black text-gray-900 dark:text-white text-xs block">{{ form.stat_1_value || '100%' }}</span>
                <span class="text-[9px] text-gray-400 truncate block">{{ form.stat_1_label || 'Carbón' }}</span>
              </div>
              <div>
                <span class="font-black text-red-600 dark:text-red-500 text-xs block">{{ form.stat_2_value || '4.9 ★' }}</span>
                <span class="text-[9px] text-gray-400 truncate block">{{ form.stat_2_label || 'Reseñas' }}</span>
              </div>
              <div>
                <span class="font-black text-gray-900 dark:text-white text-xs block">{{ form.stat_3_value || '3+' }}</span>
                <span class="text-[9px] text-gray-400 truncate block">{{ form.stat_3_label || 'Sucursales' }}</span>
              </div>
            </div>
          </div>

          <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-800/40 text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
            <span class="font-bold text-gray-800 dark:text-gray-200 block">💡 Publicación Inmediata</span>
            <p>Al hacer clic en "Guardar Cambios", los textos y enlaces se actualizarán de forma instantánea para todos los visitantes del sitio.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import { useToasts } from '@/composables/useToasts'
import { useLandingContent } from '@/composables/useLandingContent'

const { addToast } = useToasts()
const { landingSettings, updateLandingSettings } = useLandingContent()

const isSaving = ref(false)
const activeSection = ref('hero')

const editorSections = [
  { id: 'hero', label: 'Hero Principal', icon: '🚀' },
  { id: 'stats', label: 'Estadísticas & Confianza', icon: '🏆' },
  { id: 'sections', label: 'Títulos de Secciones', icon: '🍗' },
  { id: 'contact', label: 'Contacto & Redes', icon: '📞' },
]

const SUGGESTED_HERO_IMAGES = [
  'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=1000',
  'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=1000',
  'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=1000',
  'https://images.unsplash.com/photo-1594221708779-94832f4320d1?w=1000',
]

const form = ref({
  hero_title: '',
  hero_subtitle: '',
  hero_description: '',
  hero_badge: '',
  hero_image_url: '',
  hero_cta_primary: '',
  hero_cta_secondary: '',
  stat_1_value: '',
  stat_1_label: '',
  stat_2_value: '',
  stat_2_label: '',
  stat_3_value: '',
  stat_3_label: '',
  menu_section_title: '',
  menu_section_subtitle: '',
  promotions_section_title: '',
  promotions_section_subtitle: '',
  branches_section_title: '',
  branches_section_subtitle: '',
  company_name: '',
  company_phone: '',
  company_email: '',
  company_whatsapp: '',
  company_address: '',
  schedule_weekday: '',
  social_facebook: '',
  social_instagram: '',
})

const populateFormFromSettings = (data) => {
  if (!data) return
  form.value = {
    hero_title: data.hero_title || 'El mejor Pollo Asado de Durango',
    hero_subtitle: data.hero_subtitle || '¡Bienvenidos a la Familia Pollo Feliz! · Tradición al Carbón',
    hero_description: data.hero_description || 'Descubre el auténtico sabor que reúne a las familias.',
    hero_badge: data.hero_badge || '100% Asado al Carbón',
    hero_image_url: data.hero_image_url || SUGGESTED_HERO_IMAGES[0],
    hero_cta_primary: data.hero_cta_primary || 'Ver Menú Completo',
    hero_cta_secondary: data.hero_cta_secondary || 'Ordenar en Línea',
    stat_1_value: data.stat_1_value || '100%',
    stat_1_label: data.stat_1_label || 'Asado al Carbón',
    stat_2_value: data.stat_2_value || '4.9 ★',
    stat_2_label: data.stat_2_label || '+2,500 Reseñas',
    stat_3_value: data.stat_3_value || '3+',
    stat_3_label: data.stat_3_label || 'Sucursales Activas',
    menu_section_title: data.menu_section_title || 'Platos Destacados',
    menu_section_subtitle: data.menu_section_subtitle || 'Descubre nuestros platillos más populares...',
    promotions_section_title: data.promotions_section_title || 'Promociones Imperdibles',
    promotions_section_subtitle: data.promotions_section_subtitle || 'Aprovecha nuestras promociones especiales...',
    branches_section_title: data.branches_section_title || 'Nuestras Sucursales',
    branches_section_subtitle: data.branches_section_subtitle || 'Visítanos o llámanos directamente...',
    company_name: data.company_name || 'Pollo Feliz Durango',
    company_phone: data.company_phone || '618-123-4567',
    company_email: data.company_email || 'contacto@pollofeliz.com',
    company_whatsapp: data.company_whatsapp || '618-123-4567',
    company_address: data.company_address || 'Av. 5 de Mayo #123, Zona Centro, Durango, Dgo.',
    schedule_weekday: data.schedule_weekday || 'Lunes a Domingo: 10:00 AM - 10:00 PM',
    social_facebook: data.social_facebook || 'https://facebook.com/pollofelizdurango',
    social_instagram: data.social_instagram || 'https://instagram.com/pollofelizdurango',
  }
}

const useRandomHeroImage = () => {
  const current = form.value.hero_image_url
  const others = SUGGESTED_HERO_IMAGES.filter(img => img !== current)
  form.value.hero_image_url = others[Math.floor(Math.random() * others.length)]
}

const handleResetDefaults = () => {
  if (!confirm('¿Deseas restaurar todos los textos oficiales originales del landing de Pollo Feliz?')) return
  populateFormFromSettings({
    hero_title: 'El mejor Pollo Asado de Durango',
    hero_subtitle: '¡Bienvenidos a la Familia Pollo Feliz! · Tradición al Carbón',
    hero_description: 'Descubre el auténtico sabor que reúne a las familias. Pollo marinado con receta secreta de especias naturales y asado pacientemente al calor del carbón de mezquite.',
    hero_badge: '100% Asado al Carbón',
    hero_image_url: SUGGESTED_HERO_IMAGES[0],
    hero_cta_primary: 'Ver Menú Completo',
    hero_cta_secondary: 'Ordenar en Línea',
    stat_1_value: '100%',
    stat_1_label: 'Asado al Carbón',
    stat_2_value: '4.9 ★',
    stat_2_label: '+2,500 Reseñas',
    stat_3_value: '3+',
    stat_3_label: 'Sucursales Activas',
    menu_section_title: 'Platos Destacados',
    menu_section_subtitle: 'Descubre nuestros platillos más populares, preparados con la receta secreta que nos hace únicos.',
    promotions_section_title: 'Promociones Imperdibles',
    promotions_section_subtitle: 'Aprovecha nuestras promociones especiales y disfruta del mejor pollo asado con descuentos exclusivos.',
    branches_section_title: 'Nuestras Sucursales',
    branches_section_subtitle: 'Visítanos o llámanos directamente para disfrutar del mejor sabor al carbón recién salido de la parrilla.',
    company_name: 'Pollo Feliz Durango',
    company_phone: '618-123-4567',
    company_email: 'contacto@pollofeliz.com',
    company_whatsapp: '618-123-4567',
    company_address: 'Av. 5 de Mayo #123, Zona Centro, Durango, Dgo.',
    schedule_weekday: 'Lunes a Domingo: 10:00 AM - 10:00 PM',
    social_facebook: 'https://facebook.com/pollofelizdurango',
    social_instagram: 'https://instagram.com/pollofelizdurango',
  })
  addToast('Valores oficiales restablecidos en el formulario. Recuerda hacer clic en Guardar.', 'info')
}

const handleSave = async () => {
  isSaving.value = true
  try {
    const payload = { ...form.value }
    const res = await api.put('/admin/settings', payload)
    if (res.data?.settings) {
      updateLandingSettings(res.data.settings)
    } else {
      updateLandingSettings(payload)
    }
    addToast('¡Contenido del landing guardado y actualizado con éxito!', 'success')
  } catch (err) {
    console.error('Error al guardar ajustes del landing', err)
    addToast(err.response?.data?.message || 'Error al guardar la configuración del landing.', 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(async () => {
  try {
    const res = await api.get('/admin/dashboard-content').catch(() => api.get('/landing/content'))
    if (res.data?.settings) {
      populateFormFromSettings(res.data.settings)
    } else if (landingSettings.value) {
      populateFormFromSettings(landingSettings.value)
    }
  } catch {
    populateFormFromSettings(landingSettings.value)
  }
})
</script>
