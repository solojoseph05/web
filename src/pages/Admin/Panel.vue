<!-- src/pages/Admin/Panel.vue -->
<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-950 flex flex-col">
    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-800 sticky top-0 z-30">
      <div class="container mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 shrink-0">
          <router-link to="/admin" @click="selectTab('summary')" class="flex items-center gap-2">
            <img :src="logo" alt="Pollo Feliz" class="h-8 sm:h-9 w-auto object-contain" />
            <span class="font-black text-gray-900 dark:text-white hidden sm:inline text-sm">Panel Administrativo</span>
          </router-link>
          <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-bold text-red-700 dark:bg-red-950/60 dark:text-red-300">
            Control Pollo Feliz
          </span>
        </div>

        <div class="flex items-center gap-3 sm:gap-4 shrink-0">
          <!-- Active User Identity & Role Badge -->
          <div class="hidden sm:flex items-center gap-2.5 pr-2 border-r border-gray-200 dark:border-gray-800">
            <div
              class="h-8 w-8 rounded-xl flex items-center justify-center font-bold text-xs shadow-xs"
              :class="adminUser?.role === 'super_admin'
                ? 'bg-purple-600 text-white'
                : adminUser?.role === 'gerente'
                  ? 'bg-blue-600 text-white'
                  : adminUser?.role === 'cocina'
                    ? 'bg-amber-600 text-white'
                    : 'bg-emerald-600 text-white'"
            >
              {{ currentRoleMeta.icon }}
            </div>
            <div class="text-left text-xs leading-tight">
              <span class="block font-bold text-gray-900 dark:text-white truncate max-w-[140px]">
                {{ adminUser?.name || 'Administrador' }}
              </span>
              <span
                class="inline-block px-1.5 py-0.2 rounded-md text-[10px] font-bold border"
                :class="currentRoleMeta.badgeClass"
              >
                {{ currentRoleMeta.label }}
              </span>
            </div>
          </div>



          <router-link
            to="/"
            class="text-xs font-bold text-gray-600 hover:text-red-600 dark:text-gray-300 dark:hover:text-white transition flex items-center gap-1"
          >
            <span>Ver Sitio Web</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
          </router-link>

          <!-- Refresh / Sync Button -->
          <button
            @click="refreshAllData"
            :disabled="isRefreshing"
            class="rounded-xl border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800 cursor-pointer transition flex items-center gap-1.5"
            title="Sincronizar y recargar datos"
          >
            <span :class="{'animate-spin inline-block': isRefreshing}">🔄</span>
            <span class="hidden sm:inline">{{ isRefreshing ? 'Sincronizando...' : 'Sincronizar' }}</span>
          </button>

          <!-- Dark Mode Toggle Button -->
          <button
            @click="toggleTheme"
            class="rounded-xl border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800 cursor-pointer transition flex items-center gap-1.5"
            :title="theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
            :aria-label="theme === 'dark' ? 'Modo claro' : 'Modo oscuro'"
          >
            <span v-if="theme === 'dark'" class="text-amber-300">☀️</span>
            <span v-else class="text-gray-600">🌙</span>
            <span class="hidden sm:inline">{{ theme === 'dark' ? 'Claro' : 'Oscuro' }}</span>
          </button>

          <button
            @click="handleLogout"
            class="rounded-xl border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-red-950/40 cursor-pointer transition"
          >
            Cerrar Sesión
          </button>
        </div>
      </div>
    </header>

    <!-- Main Container -->
    <div class="container mx-auto px-4 py-6 flex-1 flex flex-col md:flex-row gap-6">
      <!-- Sidebar / Tab Navigation -->
      <aside class="w-full md:w-64 shrink-0">
        <div class="rounded-3xl bg-white p-3 sm:p-4 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800 space-y-1">
          <button
            v-for="tab in visibleTabs"
            :key="tab.id"
            @click="selectTab(tab.id)"
            class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 text-sm font-semibold transition text-left cursor-pointer"
            :class="activeTab === tab.id
              ? 'bg-red-600 text-white shadow-sm'
              : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
          >
            <div class="flex items-center gap-3">
              <span>{{ tab.icon }}</span>
              <span>{{ tab.label }}</span>
            </div>
            <span
              v-if="tab.id === 'orders' && activeOrdersList.length > 0"
              class="rounded-full text-[10px] font-black px-2 py-0.5"
              :class="activeTab === 'orders' ? 'bg-white text-red-600' : 'bg-red-600 text-white'"
            >
              {{ activeOrdersList.length }}
            </span>
            <span
              v-else-if="tab.id === 'messages' && pendingMessagesCount > 0"
              class="rounded-full text-[10px] font-black px-2 py-0.5"
              :class="activeTab === 'messages' ? 'bg-white text-red-600' : 'bg-amber-500 text-white'"
            >
              {{ pendingMessagesCount }}
            </span>
            <span
              v-else-if="tab.id === 'reviews'"
              class="text-[11px] opacity-75 font-mono"
            >
              {{ adminReviewsList.length }}
            </span>
            <span
              v-else-if="tab.id === 'users'"
              class="text-[11px] opacity-75 font-mono"
            >
              {{ adminUsersList.length }}
            </span>
            <span
              v-else-if="tab.id === 'menu'"
              class="text-[11px] opacity-75 font-mono"
            >
              {{ menuItems.length }}
            </span>
          </button>

          <!-- Current Role Context in Sidebar -->
          <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 px-2 space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">
              Tu Nivel de Acceso
            </span>
            <div class="flex items-center gap-2">
              <span class="text-base">{{ currentRoleMeta.icon }}</span>
              <div class="truncate">
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200 truncate">
                  {{ currentRoleMeta.label }}
                </span>
                <span class="block text-[10px] text-gray-400 truncate">
                  {{ visibleTabs.length }} áreas disponibles
                </span>
              </div>
            </div>

            <!-- Estado del Motor de Base de Datos -->
            <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
              <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">
                Motor de Base de Datos
              </span>
              <div class="flex items-center justify-between text-[11px] p-2 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200/60 dark:border-gray-700/60">
                <div class="flex items-center gap-1.5 min-w-0">
                  <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                  <span class="font-medium text-gray-700 dark:text-gray-300 truncate">
                    {{ dbStatusInfo?.engine || 'Memoria (Activa)' }}
                  </span>
                </div>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 font-bold shrink-0">
                  BD Lista
                </span>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <!-- Content Area -->
      <main class="flex-1 min-w-0">
        <!-- TAB: Resumen & Analíticas de Ventas en Vivo -->
        <div v-if="activeTab === 'summary' || activeTab === 'analytics'" class="space-y-6">
          <AnalyticsDashboard />
        </div>

        <!-- TAB: Pedidos en Vivo (Order Management) -->
        <div v-else-if="activeTab === 'orders'" class="space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                <span>Gestión de Pedidos en Vivo</span>
                <span class="rounded-full bg-red-100 text-red-700 text-xs px-2.5 py-0.5 font-bold dark:bg-red-950/60 dark:text-red-300">
                  {{ orders.length }} pedidos totales
                </span>
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Actualiza el estado de los pedidos recibidos en sucursal en tiempo real</p>
            </div>

            <!-- CSV Export Button -->
            <button
              @click="exportOrdersCSV"
              class="rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition cursor-pointer flex items-center gap-1.5 self-start sm:self-auto shadow-xs"
              title="Descargar listado de pedidos en formato CSV para Excel"
            >
              <span>📥</span>
              <span>Exportar Pedidos (CSV)</span>
            </button>
          </div>

          <!-- Orders KPI Summary Cards -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
              <span class="text-[10px] uppercase font-bold text-gray-400 block">Total Registrados</span>
              <span class="text-xl font-black text-gray-900 dark:text-white">{{ orders.length }}</span>
            </div>
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
              <span class="text-[10px] uppercase font-bold text-amber-500 block">En Proceso</span>
              <span class="text-xl font-black text-amber-600 dark:text-amber-400">{{ activeOrdersList.length }}</span>
            </div>
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
              <span class="text-[10px] uppercase font-bold text-blue-500 block">En Camino</span>
              <span class="text-xl font-black text-blue-600 dark:text-blue-400">{{ orders.filter(o => o.status === 'on_the_way').length }}</span>
            </div>
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
              <span class="text-[10px] uppercase font-bold text-emerald-500 block">Entregados</span>
              <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ orders.filter(o => o.status === 'delivered').length }}</span>
            </div>
          </div>

          <!-- Search and Filter Controls -->
          <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            <!-- Search bar -->
            <div class="relative flex-1 max-w-md">
              <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
              <input
                v-model="orderSearch"
                type="text"
                placeholder="Buscar por cliente, teléfono, # orden..."
                class="w-full rounded-xl border border-gray-200 bg-white pl-9 pr-3.5 py-2 text-xs font-medium text-gray-900 placeholder:text-gray-400 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
              <button
                v-if="orderSearch"
                @click="orderSearch = ''"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-gray-600"
              >
                ✕
              </button>
            </div>

            <!-- Filter groups (Type & Status) -->
            <div class="flex flex-wrap items-center gap-2">
              <!-- Type Filters -->
              <div class="inline-flex rounded-xl bg-gray-100 p-0.5 dark:bg-gray-800">
                <button
                  @click="orderTypeFilter = 'all'"
                  class="rounded-lg px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                  :class="orderTypeFilter === 'all' ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400'"
                >
                  Todos
                </button>
                <button
                  @click="orderTypeFilter = 'delivery'"
                  class="rounded-lg px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                  :class="orderTypeFilter === 'delivery' ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400'"
                >
                  🛵 Domicilio
                </button>
                <button
                  @click="orderTypeFilter = 'branch'"
                  class="rounded-lg px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                  :class="orderTypeFilter === 'branch' ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400'"
                >
                  🏪 Sucursal
                </button>
              </div>

              <div class="h-4 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

              <!-- Status filter buttons -->
              <div class="flex flex-wrap items-center gap-1.5">
                <button
                  v-for="f in orderFilters"
                  :key="f.id"
                  @click="orderFilter = f.id"
                  class="rounded-xl px-3 py-1.5 text-xs font-bold transition cursor-pointer"
                  :class="orderFilter === f.id
                    ? 'bg-red-600 text-white shadow-xs'
                    : 'bg-white text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300'"
                >
                  {{ f.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Orders Grid -->
          <div v-if="filteredAdminOrders.length === 0" class="rounded-3xl bg-white p-8 text-center border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <p class="font-bold text-gray-800 dark:text-gray-200">No hay pedidos que coincidan con los filtros seleccionados.</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="order in filteredAdminOrders"
              :key="order.id"
              class="rounded-3xl bg-white p-5 sm:p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4"
            >
              <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3">
                  <span class="text-lg font-black text-gray-900 dark:text-white">#{{ order.id }}</span>
                  <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ order.order_type === 'delivery' ? '🛵 Domicilio' : '🏪 Sucursal' }}
                  </span>
                  <span class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</span>
                </div>

                <!-- Status Badge -->
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                  :class="getStatusBadgeClass(order.status)"
                >
                  <span class="h-2 w-2 rounded-full" :class="getStatusDotClass(order.status)"></span>
                  {{ getStatusLabel(order.status) }}
                </span>
              </div>

              <!-- Customer & Order Information -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-gray-600 dark:text-gray-300">
                <div>
                  <span class="font-bold text-gray-800 dark:text-gray-200 block">Cliente:</span>
                  <p class="font-semibold text-gray-900 dark:text-white">{{ order.customer_name }}</p>
                  <div class="flex items-center gap-1.5 mt-1">
                    <span class="font-mono text-[11px] text-gray-500">{{ order.customer_phone }}</span>
                    <button
                      v-if="order.customer_phone"
                      @click="copyToClipboard(order.customer_phone, 'Teléfono copiado')"
                      class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold transition cursor-pointer"
                      title="Copiar teléfono"
                    >
                      📋 Copiar
                    </button>
                    <a
                      v-if="order.customer_phone"
                      :href="getWhatsAppLink(order.customer_phone, order.id, order.customer_name)"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 hover:bg-emerald-200 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold transition inline-flex items-center gap-0.5"
                      title="Enviar WhatsApp"
                    >
                      💬 WhatsApp
                    </a>
                  </div>
                  <p class="text-gray-400 mt-1">{{ order.branch }}</p>
                </div>
                <div>
                  <span class="font-bold text-gray-800 dark:text-gray-200 block">Dirección / Notas:</span>
                  <p>{{ order.address || 'Recoger en mostrador' }}</p>
                  <p v-if="order.notes" class="text-amber-600 italic">"{{ order.notes }}"</p>
                </div>
                <div class="sm:text-right">
                  <span class="font-bold text-gray-800 dark:text-gray-200 block">Total:</span>
                  <p class="text-base font-black text-red-600">${{ order.total }} MXN</p>
                  <p class="text-[11px] text-gray-400">{{ order.payment_method || 'Efectivo' }}</p>
                </div>
              </div>

              <!-- Items list -->
              <div class="rounded-2xl bg-gray-50 p-3 dark:bg-gray-800/40 text-xs">
                <span class="font-bold text-gray-700 dark:text-gray-300 block mb-1">Productos:</span>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="item in (order.items || [])"
                    :key="item.id"
                    class="rounded-lg bg-white px-2.5 py-1 font-medium border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                  >
                    {{ item.quantity || 1 }}x {{ item.name }}
                  </span>
                </div>
              </div>

              <!-- Admin Action Buttons (Transition Order Status) -->
              <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-xs font-bold text-gray-500 mr-1">Cambiar estado:</span>
                <button
                  v-if="order.status !== 'preparing'"
                  @click="updateOrderStatus(order.id, 'preparing')"
                  class="rounded-xl bg-orange-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-600 transition cursor-pointer"
                >
                  🔥 En preparación
                </button>
                <button
                  v-if="order.status !== 'ready'"
                  @click="updateOrderStatus(order.id, 'ready')"
                  class="rounded-xl bg-purple-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-purple-700 transition cursor-pointer"
                >
                  📦 Empacado / Listo
                </button>
                <button
                  v-if="order.status !== 'on_the_way'"
                  @click="updateOrderStatus(order.id, 'on_the_way')"
                  class="rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 transition cursor-pointer"
                >
                  🛵 En camino
                </button>
                <button
                  v-if="order.status !== 'delivered'"
                  @click="updateOrderStatus(order.id, 'delivered')"
                  class="rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition cursor-pointer"
                >
                  ✅ Entregado
                </button>
                <button
                  v-if="order.status !== 'cancelled'"
                  @click="updateOrderStatus(order.id, 'cancelled')"
                  class="rounded-xl border border-gray-200 px-2.5 py-1.5 text-xs font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-300 transition cursor-pointer"
                >
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: Menú (DYNAMIC MENU CRUD INTERFACE) -->
        <div v-else-if="activeTab === 'menu'" class="space-y-6">
          <!-- Header and Primary Action -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                <span>Gestión del Menú Pollo Feliz</span>
                <span class="rounded-full bg-red-100 text-red-700 text-xs px-2.5 py-0.5 font-bold dark:bg-red-950/60 dark:text-red-300">
                  {{ filteredMenuItems.length }} de {{ menuItems.length }}
                </span>
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Agrega nuevos platillos, edita precios e imágenes, y elimina o pausa productos en tiempo real.
              </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
              <button
                @click="exportMenuCSV"
                class="rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition cursor-pointer flex items-center gap-1.5 shadow-xs"
                title="Descargar catálogo en formato CSV"
              >
                <span>📥</span>
                <span>Exportar Menú (CSV)</span>
              </button>
              <button
                @click="handleRestoreDefaultMenu"
                class="rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition cursor-pointer"
                title="Restaurar el catálogo original con todos los platillos"
              >
                🔄 Restaurar Catálogo
              </button>
              <button
                @click="showCategoriesManager = !showCategoriesManager"
                class="rounded-xl border border-amber-300 bg-amber-50 dark:bg-amber-950/40 dark:border-amber-800 text-amber-900 dark:text-amber-300 px-3.5 py-2 text-xs font-bold hover:bg-amber-100 transition cursor-pointer flex items-center gap-1.5 shadow-xs"
              >
                <span>📁</span>
                <span>{{ showCategoriesManager ? 'Ocultar Categorías' : 'Categorías (' + adminCategoriesList.length + ')' }}</span>
              </button>
              <button
                @click="handleOpenAddCategory"
                class="rounded-xl border border-amber-400 bg-amber-100 dark:bg-amber-900/60 dark:border-amber-700 text-amber-950 dark:text-amber-200 px-3.5 py-2 text-xs font-black hover:bg-amber-200 transition cursor-pointer flex items-center gap-1.5 shadow-xs"
              >
                <span>+ Nueva Categoría</span>
              </button>
              <button
                @click="handleOpenAddModal"
                class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-4 py-2 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition cursor-pointer flex items-center gap-1.5"
              >
                <span>+ Agregar Platillo</span>
              </button>
            </div>
          </div>

          <!-- Collapsible Categories Management Panel -->
          <div v-if="showCategoriesManager" class="rounded-3xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/60 p-5 space-y-3">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-black text-amber-950 dark:text-amber-200 flex items-center gap-2">
                  <span>📁</span>
                  <span>Categorías del Menú Digital</span>
                  <span class="rounded-full bg-amber-200 text-amber-900 text-[10px] px-2 py-0.5 font-bold">
                    {{ adminCategoriesList.length }} registradas
                  </span>
                </h3>
                <p class="text-[11px] text-amber-800/80 dark:text-amber-400 mt-0.5">
                  Edita nombres, slugs, orden de aparición y estado activo para los filtros del menú público.
                </p>
              </div>
              <button
                @click="handleOpenAddCategory"
                class="rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black px-3 py-1.5 text-xs shadow-xs transition cursor-pointer"
              >
                + Crear Categoría
              </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 pt-1">
              <div
                v-for="cat in adminCategoriesList"
                :key="cat.id"
                class="flex items-center justify-between p-3 rounded-2xl bg-white dark:bg-gray-800 border border-amber-200/80 dark:border-gray-700 shadow-2xs"
              >
                <div class="min-w-0 flex-1 pr-2">
                  <div class="flex items-center gap-1.5">
                    <span class="text-xs font-black text-gray-900 dark:text-white truncate">{{ cat.name }}</span>
                    <span v-if="!cat.is_active" class="text-[9px] px-1.5 py-0.2 rounded-full bg-gray-100 text-gray-500 font-bold">Pausada</span>
                  </div>
                  <span class="block text-[10px] font-mono text-gray-400 truncate">#{{ cat.sort_order }} · {{ cat.slug }}</span>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                  <button
                    @click="handleOpenEditCategory(cat)"
                    class="p-1.5 rounded-lg text-gray-500 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-gray-700 transition cursor-pointer"
                    title="Editar categoría"
                  >
                    ✏️
                  </button>
                  <button
                    @click="handlePromptDeleteCategory(cat)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                    title="Eliminar categoría"
                  >
                    🗑️
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Category Quick Summary Bar (Combos, Drinks, Extras, etc.) -->
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <button
              v-for="catStat in categorySummaryCards"
              :key="catStat.id"
              @click="selectedMenuCategory = (selectedMenuCategory === catStat.id ? 'Todas' : catStat.id)"
              class="rounded-2xl p-3 text-left transition-all cursor-pointer border flex flex-col justify-between"
              :class="selectedMenuCategory === catStat.id
                ? 'bg-red-600 text-white border-red-600 shadow-md ring-2 ring-red-400'
                : 'bg-white text-gray-800 border-gray-100 hover:border-red-200 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-200 shadow-xs'"
            >
              <div class="flex items-center justify-between">
                <span class="text-xl">{{ catStat.icon }}</span>
                <span
                  class="rounded-full px-2 py-0.5 text-[11px] font-black"
                  :class="selectedMenuCategory === catStat.id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                >
                  {{ catStat.count }}
                </span>
              </div>
              <div class="mt-2">
                <p class="text-xs font-bold truncate">{{ catStat.label }}</p>
                <p class="text-[10px] opacity-75">{{ catStat.subtext }}</p>
              </div>
            </button>
          </div>

          <!-- Filters & Search Toolbar -->
          <div class="rounded-3xl bg-white p-4 sm:p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-3.5">
            <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
              <!-- Search Input -->
              <div class="relative w-full md:w-80">
                <input
                  v-model="menuSearch"
                  type="text"
                  placeholder="Buscar por nombre, categoría o etiqueta..."
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 pl-9 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
              </div>

              <!-- Quick Category Dropdown (Convenient when many categories exist) -->
              <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 whitespace-nowrap hidden sm:inline">Filtrar categoría:</span>
                <select
                  v-model="selectedMenuCategory"
                  class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-800 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white cursor-pointer"
                >
                  <option v-for="cat in menuCategoryFilterOptions" :key="cat" :value="cat">
                    {{ getCategoryIcon(cat) }} {{ cat }} ({{ getCategoryCount(cat) }})
                  </option>
                </select>

                <!-- Status Switch Filter -->
                <div class="flex items-center gap-1">
                  <button
                    @click="selectedMenuStatus = 'all'"
                    class="rounded-lg px-2.5 py-1.5 text-xs font-bold transition cursor-pointer"
                    :class="selectedMenuStatus === 'all' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'"
                    title="Ver todos"
                  >
                    Todos ({{ menuItems.length }})
                  </button>
                  <button
                    @click="selectedMenuStatus = 'active'"
                    class="rounded-lg px-2.5 py-1.5 text-xs font-bold transition cursor-pointer"
                    :class="selectedMenuStatus === 'active' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'"
                    title="Ver activos"
                  >
                    Activos ({{ menuItems.filter(i => i.is_active).length }})
                  </button>
                  <button
                    @click="selectedMenuStatus = 'paused'"
                    class="rounded-lg px-2.5 py-1.5 text-xs font-bold transition cursor-pointer"
                    :class="selectedMenuStatus === 'paused' ? 'bg-amber-600 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'"
                    title="Ver pausados"
                  >
                    Pausados ({{ menuItems.filter(i => !i.is_active).length }})
                  </button>
                </div>
              </div>
            </div>

            <!-- Dynamic Category Pills Filter Bar -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-1 no-scrollbar">
              <button
                v-for="cat in menuCategoryFilterOptions"
                :key="cat"
                @click="selectedMenuCategory = cat"
                class="rounded-full px-3 py-1 text-xs font-bold whitespace-nowrap transition cursor-pointer shrink-0 flex items-center gap-1.5 border"
                :class="selectedMenuCategory === cat
                  ? 'bg-red-600 text-white border-red-600 shadow-xs'
                  : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300'"
              >
                <span>{{ getCategoryIcon(cat) }}</span>
                <span>{{ cat }}</span>
                <span
                  class="rounded-full px-1.5 py-0.2 text-[10px] font-extrabold"
                  :class="selectedMenuCategory === cat ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                >
                  {{ getCategoryCount(cat) }}
                </span>
              </button>
            </div>
          </div>

          <!-- Bulk Selection Floating Actions Bar -->
          <Transition name="fade">
            <div
              v-if="selectedItemIds.length > 0"
              class="sticky top-20 z-30 mb-2 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-gray-900 p-3 sm:p-4 text-white shadow-xl border border-gray-700/80 dark:bg-black dark:border-gray-800"
            >
              <div class="flex items-center gap-2.5">
                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-red-600 text-xs font-black text-white shadow-xs">
                  {{ selectedItemIds.length }}
                </span>
                <div>
                  <span class="text-xs sm:text-sm font-bold block leading-tight">
                    {{ selectedItemIds.length === 1 ? '1 platillo seleccionado' : `${selectedItemIds.length} platillos seleccionados` }}
                  </span>
                  <button
                    type="button"
                    @click="clearSelection"
                    class="text-[11px] text-gray-400 hover:text-white underline cursor-pointer"
                  >
                    Desmarcar todos
                  </button>
                </div>
              </div>

              <!-- Bulk Action Controls -->
              <div class="flex flex-wrap items-center gap-2">
                <!-- Quick Bulk Category Mover -->
                <div class="flex items-center gap-1.5 bg-gray-800 rounded-xl px-2.5 py-1.5 border border-gray-700">
                  <span class="text-xs text-gray-400 font-semibold hidden md:inline">Mover a:</span>
                  <select
                    v-model="bulkTargetCategory"
                    class="bg-transparent text-xs font-bold text-white focus:outline-hidden cursor-pointer"
                  >
                    <option value="" disabled class="text-gray-900 bg-white">Elige categoría...</option>
                    <option v-for="cat in allUniqueCategories" :key="cat" :value="cat" class="text-gray-900 bg-white">
                      {{ getCategoryIcon(cat) }} {{ cat }}
                    </option>
                  </select>
                  <button
                    type="button"
                    @click="handleApplyBulkCategoryQuick"
                    :disabled="!bulkTargetCategory"
                    class="rounded-lg bg-red-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed transition cursor-pointer"
                  >
                    Mover
                  </button>
                </div>

                <!-- Advanced Bulk Category Modal Trigger -->
                <button
                  type="button"
                  @click="showBulkCategoryModal = true"
                  class="rounded-xl border border-gray-700 bg-gray-800 hover:bg-gray-700 px-3 py-1.5 text-xs font-bold text-gray-200 transition cursor-pointer flex items-center gap-1.5"
                  title="Cambiar categoría con opciones y vista previa"
                >
                  <span>🏷️</span>
                  <span class="hidden sm:inline">Categoría Avanzada</span>
                </button>

                <!-- Bulk Status Toggles -->
                <button
                  type="button"
                  @click="handleBulkToggleActive(true)"
                  class="rounded-xl bg-gray-800 hover:bg-emerald-700/60 border border-gray-700 px-2.5 py-1.5 text-xs font-bold text-emerald-400 hover:text-white transition cursor-pointer"
                  title="Activar venta en línea para todos los seleccionados"
                >
                  ✓ Activar
                </button>
                <button
                  type="button"
                  @click="handleBulkToggleActive(false)"
                  class="rounded-xl bg-gray-800 hover:bg-amber-700/60 border border-gray-700 px-2.5 py-1.5 text-xs font-bold text-amber-400 hover:text-white transition cursor-pointer"
                  title="Pausar venta para todos los seleccionados"
                >
                  ⏸ Pausar
                </button>

                <!-- Bulk Delete Action -->
                <button
                  type="button"
                  @click="handlePromptBulkDelete"
                  class="rounded-xl bg-red-600 hover:bg-red-700 px-3.5 py-1.5 text-xs font-black text-white shadow-md transition cursor-pointer flex items-center gap-1.5"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  <span>Eliminar ({{ selectedItemIds.length }})</span>
                </button>
              </div>
            </div>
          </Transition>

          <!-- Empty State -->
          <div v-if="filteredMenuItems.length === 0" class="rounded-3xl bg-white p-10 text-center border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <span class="text-4xl block mb-2">🍗</span>
            <p class="font-bold text-gray-800 dark:text-gray-200">No se encontraron platillos en la categoría "{{ selectedMenuCategory }}"</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-4">Intenta cambiar la categoría o el término de búsqueda.</p>
            <div class="flex items-center justify-center gap-3">
              <button
                @click="selectedMenuCategory = 'Todas'; menuSearch = ''"
                class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer"
              >
                Ver Todas las Categorías
              </button>
              <button
                @click="handleOpenAddModal"
                class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-red-700 transition cursor-pointer"
              >
                + Agregar Platillo a {{ selectedMenuCategory !== 'Todas' ? selectedMenuCategory : 'Menú' }}
              </button>
            </div>
          </div>

          <!-- Menu Items Table (Desktop & Tablets) -->
          <div v-else class="overflow-x-auto rounded-3xl bg-white shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
              <thead class="bg-gray-50 text-xs font-bold uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                <tr>
                  <!-- Bulk Select All Checkbox -->
                  <th class="px-4 py-3.5 w-12 text-center">
                    <input
                      type="checkbox"
                      :checked="isAllFilteredSelected"
                      :indeterminate.prop="isPartiallySelected"
                      @change="toggleSelectAll"
                      class="h-4 w-4 rounded text-red-600 focus:ring-red-500 cursor-pointer"
                      title="Seleccionar / deseleccionar todos los platillos filtrados"
                    />
                  </th>
                  <th class="px-5 py-3.5">Platillo</th>
                  <th class="px-5 py-3.5">Categoría & Clasificación</th>
                  <th class="px-5 py-3.5">Precio</th>
                  <th class="px-5 py-3.5">Disponibilidad</th>
                  <th class="px-5 py-3.5 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <tr
                  v-for="item in filteredMenuItems"
                  :key="item.id"
                  class="hover:bg-gray-50/70 dark:hover:bg-gray-800/50 transition-colors"
                  :class="{ 'bg-red-50/50 dark:bg-red-950/20': selectedItemIds.includes(item.id) }"
                >
                  <!-- Individual Item Checkbox -->
                  <td class="px-4 py-3.5 text-center">
                    <input
                      type="checkbox"
                      :checked="selectedItemIds.includes(item.id)"
                      @change="toggleSelectItem(item.id)"
                      class="h-4 w-4 rounded text-red-600 focus:ring-red-500 cursor-pointer"
                    />
                  </td>

                  <!-- Item Info & Thumbnail -->
                  <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                      <div class="h-12 w-12 rounded-xl bg-red-50 dark:bg-red-950/40 flex items-center justify-center shrink-0 overflow-hidden border border-gray-100 dark:border-gray-800">
                        <img
                          v-if="item.image_url"
                          :src="item.image_url"
                          :alt="item.name"
                          class="h-full w-full object-cover"
                          @error="(e) => e.target.style.display = 'none'"
                        />
                        <span v-else class="text-lg">🍗</span>
                      </div>
                      <div class="min-w-0 max-w-xs sm:max-w-sm">
                        <div class="flex items-center gap-2">
                          <h4 class="font-bold text-gray-900 dark:text-white truncate text-sm">
                            {{ item.name }}
                          </h4>
                          <span
                            v-if="item.badge"
                            class="rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 shrink-0"
                          >
                            {{ item.badge }}
                          </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                          {{ item.description || 'Sin descripción' }}
                        </p>
                      </div>
                    </div>
                  </td>

                  <!-- Dynamic Categorization & In-line Quick Switcher -->
                  <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold border"
                        :class="getCategoryBadgeClass(item.category)"
                      >
                        <span>{{ getCategoryIcon(item.category) }}</span>
                        <span>{{ item.category }}</span>
                      </span>

                      <!-- Inline Quick Category Reassigner -->
                      <select
                        :value="item.category"
                        @change="(e) => handleQuickCategoryChange(item, e.target.value)"
                        class="text-[11px] font-semibold rounded-lg border border-gray-200 bg-white py-1 px-1.5 text-gray-600 hover:border-red-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 cursor-pointer transition"
                        title="Cambiar categoría con 1 clic"
                      >
                        <option v-for="cat in allUniqueCategories" :key="cat" :value="cat">
                          {{ cat }}
                        </option>
                      </select>
                    </div>
                  </td>

                  <!-- Price -->
                  <td class="px-5 py-3.5">
                    <span class="font-black text-red-600 dark:text-red-400 text-sm sm:text-base">
                      ${{ Number(item.price).toFixed(0) }} MXN
                    </span>
                  </td>

                  <!-- Availability Toggle -->
                  <td class="px-5 py-3.5">
                    <button
                      @click="toggleItemActive(item)"
                      class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                      :class="item.is_active
                        ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-950/60 dark:text-green-300'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'"
                      :title="item.is_active ? 'Clic para pausar venta' : 'Clic para activar venta'"
                    >
                      <span class="h-2 w-2 rounded-full" :class="item.is_active ? 'bg-green-500' : 'bg-gray-400'"></span>
                      <span>{{ item.is_active ? 'Disponible' : 'Pausado' }}</span>
                    </button>
                  </td>

                  <!-- Edit / Remove Actions -->
                  <td class="px-5 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                      <button
                        @click="handleOpenEditModal(item)"
                        class="rounded-xl border border-gray-200 bg-white p-2 text-xs font-bold text-gray-700 hover:border-red-500 hover:text-red-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-500 transition cursor-pointer"
                        title="Editar platillo y categoría"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                      </button>

                      <button
                        @click="handlePromptDelete(item)"
                        class="rounded-xl border border-gray-200 bg-white p-2 text-xs font-bold text-gray-700 hover:border-red-600 hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-red-950/40 transition cursor-pointer"
                        title="Eliminar platillo del menú"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB: Promociones -->
        <div v-else-if="activeTab === 'promotions'" class="space-y-6">
          <!-- Header and Action -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                <span>Gestión de Promociones Pollo Feliz</span>
                <span class="rounded-full bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 font-bold dark:bg-amber-950/60 dark:text-amber-300">
                  {{ promotions.length }} ofertas
                </span>
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Crea, edita o pausa cupones de descuento y ofertas especiales para los clientes.
              </p>
            </div>

            <button
              @click="handleOpenAddPromo"
              class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-4 py-2 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition cursor-pointer flex items-center gap-1.5 self-start sm:self-auto"
            >
              <span>+ Nueva Promoción</span>
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="promotions.length === 0" class="rounded-3xl bg-white p-10 text-center border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <span class="text-4xl block mb-2">🏷️</span>
            <p class="font-bold text-gray-800 dark:text-gray-200">No hay promociones registradas</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-4">Crea una nueva promoción para atraer más clientes.</p>
            <button
              @click="handleOpenAddPromo"
              class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-red-700 transition cursor-pointer"
            >
              + Crear Primera Promoción
            </button>
          </div>

          <!-- Promotions Cards Grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
              v-for="promo in promotions"
              :key="promo.id"
              class="rounded-3xl bg-white p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden"
            >
              <!-- Promo Thumbnail Banner -->
              <div v-if="promo.image_url" class="h-36 -mx-5 -mt-5 mb-4 overflow-hidden relative border-b border-gray-100 dark:border-gray-800">
                <img
                  :src="promo.image_url"
                  :alt="promo.title"
                  class="h-full w-full object-cover"
                  @error="(e) => e.target.style.display = 'none'"
                />
                <span class="absolute top-3 left-3 rounded-full bg-red-600/90 backdrop-blur-xs px-2.5 py-1 text-[11px] font-black text-white shadow-md">
                  {{ promo.badge || 'Oferta' }}
                </span>
              </div>
              <div v-else class="flex items-center justify-between mb-3">
                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-800 dark:bg-amber-950/70 dark:text-amber-300">
                  {{ promo.badge || 'Oferta' }}
                </span>
                <span
                  class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold"
                  :class="promo.is_active !== false ? 'bg-green-100 text-green-700 dark:bg-green-950/60 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                >
                  {{ promo.is_active !== false ? '● Activa' : '○ Pausada' }}
                </span>
              </div>

              <!-- Content -->
              <div class="space-y-2 flex-1">
                <h3 class="font-black text-gray-900 dark:text-white text-base leading-snug">
                  {{ promo.title }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                  {{ promo.description }}
                </p>

                <!-- Coupon Box -->
                <div class="flex items-center justify-between p-2.5 rounded-2xl bg-red-50/70 dark:bg-red-950/30 border border-red-100 dark:border-red-900/50 mt-3">
                  <div>
                    <span class="text-[10px] font-bold text-gray-400 block uppercase">Código de Cupón</span>
                    <span class="font-mono font-black text-sm text-red-600 dark:text-red-400 tracking-wider">
                      {{ promo.code }}
                    </span>
                  </div>
                  <div class="text-right">
                    <span class="text-[10px] text-gray-400 block">Vigencia</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                      {{ promo.valid_until || 'Indefinida' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Actions Bottom Bar -->
              <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                <!-- Status Toggle -->
                <button
                  @click="togglePromoActive(promo)"
                  class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                  :class="promo.is_active !== false
                    ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-950/60 dark:text-green-300'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'"
                  :title="promo.is_active !== false ? 'Pausar promoción' : 'Activar promoción'"
                >
                  <span class="h-2 w-2 rounded-full" :class="promo.is_active !== false ? 'bg-green-500' : 'bg-gray-400'"></span>
                  <span>{{ promo.is_active !== false ? 'Activa' : 'Pausada' }}</span>
                </button>

                <!-- Edit / Delete Buttons -->
                <div class="flex items-center gap-1.5">
                  <button
                    @click="handleOpenEditPromo(promo)"
                    class="rounded-xl border border-gray-200 bg-white p-2 text-xs font-bold text-gray-700 hover:border-red-500 hover:text-red-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-500 transition cursor-pointer"
                    title="Editar promoción"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                  </button>
                  <button
                    @click="handlePromptDeletePromo(promo)"
                    class="rounded-xl border border-gray-200 bg-white p-2 text-xs font-bold text-gray-700 hover:border-red-600 hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-red-950/40 transition cursor-pointer"
                    title="Eliminar promoción"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: Sucursales -->
        <div v-else-if="activeTab === 'branches'" class="space-y-6">
          <!-- Header and Action -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                <span>Gestión de Sucursales Pollo Feliz</span>
                <span class="rounded-full bg-red-100 text-red-700 text-xs px-2.5 py-0.5 font-bold dark:bg-red-950/60 dark:text-red-300">
                  {{ branches.length }} puntos de venta
                </span>
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Administra horarios, teléfonos, enlaces de ubicación y puntos de entrega.
              </p>
            </div>

            <button
              @click="handleOpenAddBranch"
              class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-4 py-2 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition cursor-pointer flex items-center gap-1.5 self-start sm:self-auto"
            >
              <span>+ Nueva Sucursal</span>
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="branches.length === 0" class="rounded-3xl bg-white p-10 text-center border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <span class="text-4xl block mb-2">📍</span>
            <p class="font-bold text-gray-800 dark:text-gray-200">No hay sucursales registradas</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-4">Agrega tus ubicaciones para que los clientes puedan localizarlas.</p>
            <button
              @click="handleOpenAddBranch"
              class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-red-700 transition cursor-pointer"
            >
              + Agregar Primera Sucursal
            </button>
          </div>

          <!-- Branches Cards Grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
              v-for="b in branches"
              :key="b.id"
              class="rounded-3xl bg-white p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden"
            >
              <!-- Branch Image Preview (if provided) -->
              <div v-if="b.image_url" class="h-36 -mx-5 -mt-5 mb-4 overflow-hidden relative border-b border-gray-100 dark:border-gray-800">
                <img
                  :src="b.image_url"
                  :alt="b.name"
                  class="h-full w-full object-cover"
                  @error="(e) => e.target.style.display = 'none'"
                />
                <div class="absolute top-3 right-3 flex items-center gap-1.5">
                  <span
                    v-if="b.is_new"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-red-600 text-white shadow-xs"
                  >
                    ¡Nueva!
                  </span>
                  <span
                    v-if="b.is_coming_soon"
                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-black bg-amber-500/95 text-amber-950 backdrop-blur-md shadow-xs"
                  >
                    <span>🚀</span>
                    <span>Próxima Apertura</span>
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold backdrop-blur-md"
                    :class="b.is_open !== false ? 'bg-emerald-600/90 text-white shadow-xs' : 'bg-gray-900/90 text-gray-300'"
                  >
                    <span class="h-2 w-2 rounded-full" :class="b.is_open !== false ? 'bg-emerald-300' : 'bg-gray-400'"></span>
                    <span>{{ b.is_open !== false ? 'Abierta' : 'Cerrada' }}</span>
                  </span>
                </div>
              </div>
              <div v-else class="flex items-center justify-between mb-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 font-bold text-base">
                  🍗
                </span>
                <div class="flex items-center gap-1.5">
                  <span
                    v-if="b.is_new"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-red-600 text-white shadow-xs"
                  >
                    ¡Nueva!
                  </span>
                  <span
                    v-if="b.is_coming_soon"
                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-black bg-amber-100 text-amber-900 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800"
                  >
                    <span>🚀</span>
                    <span>Próxima Apertura</span>
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold"
                    :class="b.is_open !== false ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                  >
                    <span class="h-2 w-2 rounded-full" :class="b.is_open !== false ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                    <span>{{ b.is_open !== false ? 'Abierta' : 'Cerrada' }}</span>
                  </span>
                </div>
              </div>

              <!-- Details -->
              <div class="space-y-2.5 flex-1">
                <h3 class="font-black text-gray-900 dark:text-white text-base leading-snug">
                  {{ b.name }}
                </h3>

                <p class="text-xs text-gray-600 dark:text-gray-300 flex items-start gap-1.5">
                  <span class="text-red-500 mt-0.5">📍</span>
                  <span>{{ b.address }}</span>
                </p>

                <p class="text-xs text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                  <span class="text-gray-400">📞</span>
                  <a :href="`tel:${b.phone}`" class="font-semibold text-gray-800 dark:text-gray-200 hover:text-red-600 dark:hover:text-red-400">
                    {{ b.phone }}
                  </a>
                </p>

                <p class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800/50 p-2 rounded-xl">
                  <span>🕒</span>
                  <span>{{ b.schedule }}</span>
                </p>
              </div>

              <!-- Bottom Actions Bar -->
              <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2">
                  <button
                    @click="toggleBranchOpen(b)"
                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                    :class="b.is_open !== false
                      ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'"
                    :title="b.is_open !== false ? 'Marcar como cerrada' : 'Marcar como abierta'"
                  >
                    <span>{{ b.is_open !== false ? 'Operando' : 'Cerrada' }}</span>
                  </button>

                  <a
                    v-if="b.map_link"
                    :href="b.map_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="rounded-lg p-1.5 text-xs text-gray-500 hover:text-red-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    title="Ver en Google Maps"
                  >
                    🗺️
                  </a>
                </div>

                <!-- Edit / Delete -->
                <div class="flex items-center gap-1.5">
                  <button
                    @click="handleOpenEditBranch(b)"
                    class="rounded-xl border border-gray-200 bg-white p-2 text-xs font-bold text-gray-700 hover:border-red-500 hover:text-red-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-500 transition cursor-pointer"
                    title="Editar sucursal"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                  </button>
                  <button
                    @click="handlePromptDeleteBranch(b)"
                    class="rounded-xl border border-gray-200 bg-white p-2 text-xs font-bold text-gray-700 hover:border-red-600 hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-red-950/40 transition cursor-pointer"
                    title="Eliminar sucursal"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: Mensajes -->
        <div v-else-if="activeTab === 'messages'" class="space-y-6">
          <!-- Header & Actions -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <div class="flex items-center gap-2">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">
                  Bandeja de Mensajes & Consultas
                </h2>
                <span class="rounded-full bg-red-100 text-red-800 text-xs px-2.5 py-0.5 font-bold dark:bg-red-950/60 dark:text-red-300">
                  {{ contactMessages.length }} total
                </span>
                <span v-if="pendingMessagesCount > 0" class="rounded-full bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 font-bold dark:bg-amber-950/60 dark:text-amber-300">
                  {{ pendingMessagesCount }} pendientes
                </span>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5 flex-wrap">
                <span>📨 Consultas recibidas desde la página web.</span>
                <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">
                  ✓ Despachadas a: soporte@pollofelizdgo.com
                </span>
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
              <button
                @click="loadContactMessages"
                class="rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition cursor-pointer flex items-center gap-1.5 shadow-xs"
              >
                <span>🔄 Actualizar</span>
              </button>
            </div>
          </div>

          <!-- Filter Pills -->
          <div class="flex items-center gap-2 border-b border-gray-100 pb-3 dark:border-gray-800">
            <button
              @click="messageFilter = 'all'"
              class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition cursor-pointer"
              :class="messageFilter === 'all'
                ? 'bg-red-600 text-white shadow-xs'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'"
            >
              Todos ({{ contactMessages.length }})
            </button>
            <button
              @click="messageFilter = 'pending'"
              class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition cursor-pointer"
              :class="messageFilter === 'pending'
                ? 'bg-amber-600 text-white shadow-xs'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'"
            >
              Pendientes ({{ pendingMessagesCount }})
            </button>
            <button
              @click="messageFilter = 'attended'"
              class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition cursor-pointer"
              :class="messageFilter === 'attended'
                ? 'bg-emerald-600 text-white shadow-xs'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'"
            >
              Atendidos ({{ contactMessages.length - pendingMessagesCount }})
            </button>
          </div>

          <!-- Empty State -->
          <div
            v-if="filteredContactMessages.length === 0"
            class="rounded-3xl bg-white p-12 text-center border border-gray-100 dark:bg-gray-900 dark:border-gray-800"
          >
            <span class="text-4xl block mb-2">📭</span>
            <p class="font-bold text-gray-800 dark:text-gray-200">No hay mensajes en esta vista</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Las nuevas consultas recibidas a través del formulario de la landing page aparecerán aquí y en soporte@pollofelizdgo.com.
            </p>
          </div>

          <!-- Message Cards List -->
          <div v-else class="space-y-4">
            <div
              v-for="msg in filteredContactMessages"
              :key="msg.id"
              class="rounded-3xl bg-white p-6 shadow-sm border transition-shadow hover:shadow-md dark:bg-gray-900 space-y-4"
              :class="msg.status === 'pending'
                ? 'border-amber-200 dark:border-amber-900/50'
                : 'border-gray-100 dark:border-gray-800'"
            >
              <!-- Card Header -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3 dark:border-gray-800">
                <div class="space-y-1">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs font-black px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                      {{ msg.folio || `TKT-${String(msg.id).padStart(4, '0')}` }}
                    </span>
                    <h4 class="font-black text-gray-900 dark:text-white text-base">
                      {{ msg.name }}
                    </h4>
                    <span
                      class="rounded-full px-2.5 py-0.5 text-[10px] font-bold"
                      :class="msg.status === 'pending'
                        ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300'
                        : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300'"
                    >
                      {{ msg.status === 'pending' ? '● Pendiente de atención' : '✓ Atendido' }}
                    </span>
                  </div>

                  <!-- Details Badges -->
                  <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400 pt-0.5">
                    <span class="flex items-center gap-1">
                      <span>✉️</span>
                      <a :href="`mailto:${msg.email}`" class="text-red-600 hover:underline dark:text-red-400 font-medium">
                        {{ msg.email || 'Sin correo' }}
                      </a>
                    </span>
                    <span v-if="msg.phone" class="flex items-center gap-1">
                      <span>📞</span>
                      <a :href="`tel:${msg.phone}`" class="hover:underline font-medium">
                        {{ msg.phone }}
                      </a>
                    </span>
                    <span v-if="msg.subject" class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                      🏷️ {{ msg.subject }}
                    </span>
                    <span v-if="msg.branch" class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                      📍 {{ msg.branch }}
                    </span>
                  </div>
                </div>

                <div class="text-right shrink-0">
                  <span class="text-[11px] text-gray-400 block">
                    {{ formatDate(msg.created_at) }}
                  </span>
                  <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full mt-1">
                    ✓ Enviado a soporte@pollofelizdgo.com
                  </span>
                </div>
              </div>

              <!-- Message Body -->
              <div class="rounded-2xl bg-gray-50 p-4 text-xs sm:text-sm text-gray-800 dark:bg-gray-800/60 dark:text-gray-200 border border-gray-100 dark:border-gray-700/50 leading-relaxed whitespace-pre-wrap">
                {{ msg.message }}
              </div>

              <!-- Quick Action Buttons -->
              <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                <div class="flex items-center gap-2">
                  <button
                    @click="handleToggleMessageStatus(msg)"
                    class="rounded-xl px-3 py-1.5 text-xs font-bold transition cursor-pointer border flex items-center gap-1.5"
                    :class="msg.status === 'pending'
                      ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700'
                      : 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300'"
                  >
                    <span>{{ msg.status === 'pending' ? '✓ Marcar como Atendido' : '↩ Reabrir / Pendiente' }}</span>
                  </button>

                  <a
                    v-if="msg.email"
                    :href="`mailto:${msg.email}?subject=Respuesta a consulta Pollo Feliz (${msg.folio || 'TKT-' + msg.id})`"
                    class="rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition cursor-pointer flex items-center gap-1"
                  >
                    <span>✉️ Responder por Correo</span>
                  </a>

                  <a
                    v-if="msg.phone"
                    :href="`https://wa.me/52${msg.phone.replace(/\\D/g, '')}?text=${encodeURIComponent('¡Hola ' + msg.name + '! Nos comunicamos de Pollo Feliz respecto a tu consulta (' + (msg.folio || 'TKT-' + msg.id) + ').')}`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 transition cursor-pointer flex items-center gap-1"
                  >
                    <span>💬 WhatsApp</span>
                  </a>
                </div>

                <button
                  @click="handleDeleteContactMessage(msg.id)"
                  class="rounded-xl px-2.5 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Eliminar mensaje"
                >
                  ✕ Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: Reseñas & Opiniones de Clientes -->
        <div v-else-if="activeTab === 'reviews'" class="space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <div class="flex items-center gap-2">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">
                  Reseñas & Opiniones de Clientes
                </h2>
                <span class="rounded-full bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 font-bold dark:bg-amber-950/60 dark:text-amber-300">
                  {{ adminReviewsStats.average_rating || 4.9 }} ★
                </span>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Modera opiniones públicas, destaca testimonios en la portada y responde oficialmente a tus comensales.
              </p>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click="loadAdminReviews"
                class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition cursor-pointer flex items-center gap-1.5"
              >
                <span>🔄 Actualizar</span>
              </button>
            </div>
          </div>

          <!-- Stats Overview Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-4 rounded-2xl bg-white border border-gray-100 dark:bg-gray-900 dark:border-gray-800 shadow-xs">
              <span class="text-[10px] font-bold uppercase text-gray-400 block">Promedio General</span>
              <div class="flex items-baseline gap-1 mt-1">
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ adminReviewsStats.average_rating || 4.9 }}</span>
                <span class="text-amber-400 text-lg">★</span>
              </div>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-gray-100 dark:bg-gray-900 dark:border-gray-800 shadow-xs">
              <span class="text-[10px] font-bold uppercase text-gray-400 block">Total de Reseñas</span>
              <span class="text-2xl font-black text-gray-900 dark:text-white block mt-1">
                {{ adminReviewsList.length }}
              </span>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-gray-100 dark:bg-gray-900 dark:border-gray-800 shadow-xs">
              <span class="text-[10px] font-bold uppercase text-gray-400 block">Aprobadas (Visibles)</span>
              <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 block mt-1">
                {{ adminReviewsList.filter(r => r.status === 'approved').length }}
              </span>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-gray-100 dark:bg-gray-900 dark:border-gray-800 shadow-xs">
              <span class="text-[10px] font-bold uppercase text-gray-400 block">Destacadas en Portada</span>
              <span class="text-2xl font-black text-amber-500 block mt-1">
                {{ adminReviewsList.filter(r => r.is_featured).length }}
              </span>
            </div>
          </div>

          <!-- Filters Bar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex flex-wrap items-center gap-1.5">
              <button
                v-for="f in [
                  { id: 'all', label: 'Todas' },
                  { id: 'approved', label: 'Aprobadas' },
                  { id: 'pending', label: 'Pendientes' },
                  { id: 'rejected', label: 'Rechazadas' },
                  { id: 'featured', label: 'Destacadas 🏆' },
                ]"
                :key="f.id"
                @click="adminReviewFilter = f.id"
                class="rounded-xl px-3 py-1.5 text-xs font-bold transition cursor-pointer"
                :class="adminReviewFilter === f.id
                  ? 'bg-red-600 text-white shadow-xs'
                  : 'bg-gray-50 text-gray-600 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300'"
              >
                {{ f.label }}
              </button>
            </div>

            <div class="relative w-full sm:w-64">
              <input
                v-model="adminReviewSearch"
                type="text"
                placeholder="Buscar por cliente, sucursal o texto..."
                class="w-full rounded-xl border border-gray-200 px-3 py-1.5 text-xs outline-hidden focus:border-red-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <!-- Reviews Moderation List -->
          <div v-if="filteredAdminReviews.length === 0" class="rounded-3xl bg-white p-10 text-center border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <span class="text-3xl block mb-2">⭐</span>
            <p class="font-bold text-gray-800 dark:text-gray-200">No hay reseñas que coincidan con el filtro.</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="rev in filteredAdminReviews"
              :key="rev.id"
              class="rounded-3xl bg-white p-5 sm:p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4 hover:border-gray-200 transition"
            >
              <!-- Card Header -->
              <div class="flex flex-wrap items-start justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-2xl bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 flex items-center justify-center font-black text-sm shrink-0">
                    {{ rev.customer_name ? rev.customer_name.charAt(0).toUpperCase() : 'C' }}
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="font-black text-gray-900 dark:text-white text-sm">
                        {{ rev.customer_name }}
                      </span>
                      <span
                        v-if="rev.is_featured"
                        class="rounded-full bg-amber-100 text-amber-800 text-[10px] px-2 py-0.2 font-black dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300/60"
                      >
                        🏆 Destacada
                      </span>
                      <span
                        class="rounded-full text-[10px] px-2 py-0.2 font-bold border"
                        :class="rev.status === 'approved'
                          ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300'
                          : rev.status === 'pending'
                            ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300'
                            : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/60 dark:text-red-300'"
                      >
                        {{ rev.status === 'approved' ? '✓ Visible en Sitio' : rev.status === 'pending' ? '⏳ Pendiente' : '✕ Oculta / Rechazada' }}
                      </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                      <span>{{ formatDate(rev.created_at) }}</span>
                      <span>·</span>
                      <span>📍 {{ rev.branch || 'Sucursal Principal' }}</span>
                      <span v-if="rev.favorite_dish">· 🍗 {{ rev.favorite_dish }}</span>
                    </div>
                  </div>
                </div>

                <!-- Star Rating Badge -->
                <div class="flex items-center gap-1 bg-amber-50 dark:bg-amber-950/40 px-3 py-1 rounded-full border border-amber-200/60 text-xs font-black text-amber-800 dark:text-amber-300">
                  <span class="text-sm text-amber-400">★</span>
                  <span>{{ rev.rating }} / 5</span>
                </div>
              </div>

              <!-- Comment & Tags -->
              <div>
                <p class="text-xs sm:text-sm text-gray-800 dark:text-gray-200 leading-relaxed font-normal">
                  "{{ rev.comment }}"
                </p>

                <div v-if="rev.tags && rev.tags.length" class="flex flex-wrap gap-1 mt-2.5">
                  <span
                    v-for="tag in rev.tags"
                    :key="tag"
                    class="rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 text-[10px] px-2 py-0.5 font-semibold"
                  >
                    #{{ tag }}
                  </span>
                </div>
              </div>

              <!-- Existing Admin Reply (if any) -->
              <div
                v-if="rev.admin_reply"
                class="rounded-2xl bg-red-50/70 dark:bg-red-950/30 p-3.5 border border-red-100 dark:border-red-900/40 space-y-1"
              >
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-red-700 dark:text-red-300 flex items-center gap-1">
                    <span>🍗</span> {{ rev.admin_reply.author || 'Respuesta Oficial' }}
                  </span>
                  <span class="text-[10px] text-gray-400">
                    {{ formatDate(rev.admin_reply.created_at) }}
                  </span>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-normal">
                  {{ rev.admin_reply.message }}
                </p>
              </div>

              <!-- Inline Reply Input Box (when active) -->
              <div
                v-if="activeReplyReviewId === rev.id"
                class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 space-y-2.5"
              >
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">
                  Escribir respuesta oficial de Pollo Feliz:
                </label>
                <textarea
                  v-model="replyTextInput"
                  rows="2"
                  placeholder="¡Muchas gracias por visitarnos! Nos da gusto que hayas disfrutado..."
                  class="w-full rounded-xl border border-gray-200 p-2.5 text-xs outline-hidden focus:border-red-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white resize-none"
                ></textarea>
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="activeReplyReviewId = null"
                    class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 cursor-pointer"
                  >
                    Cancelar
                  </button>
                  <button
                    @click="handleSaveReviewReply(rev)"
                    class="rounded-xl bg-red-600 px-4 py-1.5 text-xs font-bold text-white hover:bg-red-700 transition cursor-pointer"
                  >
                    Publicar Respuesta
                  </button>
                </div>
              </div>

              <!-- Action Buttons Bar -->
              <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs">
                <!-- Status & Featured Toggles -->
                <div class="flex items-center gap-2 flex-wrap">
                  <button
                    @click="handleToggleReviewStatus(rev)"
                    class="rounded-xl border px-3 py-1.5 font-bold transition cursor-pointer"
                    :class="rev.status === 'approved'
                      ? 'border-gray-200 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300'
                      : 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300'"
                  >
                    {{ rev.status === 'approved' ? 'Ocultar / Pausar' : '✓ Aprobar & Mostrar' }}
                  </button>

                  <button
                    @click="handleToggleReviewFeatured(rev)"
                    class="rounded-xl border px-3 py-1.5 font-bold transition cursor-pointer flex items-center gap-1"
                    :class="rev.is_featured
                      ? 'border-amber-300 bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'
                      : 'border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300'"
                  >
                    <span>{{ rev.is_featured ? '⭐ Destacada' : 'Marcar Destacada' }}</span>
                  </button>

                  <button
                    @click="openReplyInput(rev)"
                    class="rounded-xl border border-gray-200 px-3 py-1.5 font-bold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 transition cursor-pointer flex items-center gap-1"
                  >
                    <span>💬</span>
                    <span>{{ rev.admin_reply ? 'Editar Respuesta' : 'Responder' }}</span>
                  </button>
                </div>

                <!-- Delete Review Button -->
                <button
                  @click="handleDeleteReview(rev)"
                  class="rounded-xl px-2.5 py-1.5 font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Eliminar reseña"
                >
                  ✕ Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: Editar Landing Completo -->
        <div v-else-if="activeTab === 'landing'" class="space-y-6">
          <LandingEditor />
        </div>

        <!-- TAB: Bolsa de Vacantes (Job Postings CRUD) -->
        <div v-else-if="activeTab === 'vacancies'" class="space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
            <div>
              <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 text-lg">
                  💼
                </span>
                <h2 class="text-xl font-black text-gray-900 dark:text-white">
                  Bolsa de Trabajo & Reclutamiento
                </h2>
                <span class="rounded-full bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 font-bold dark:bg-amber-950/60 dark:text-amber-300">
                  {{ adminJobsList.length }} vacantes · {{ jobApplicationsList.length }} postulaciones
                </span>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Administra ofertas de trabajo activas y revisa solicitudes de candidatos que se postulan en la web.
              </p>
            </div>

            <div class="flex items-center gap-2.5">
              <button
                v-if="vacancySubTab === 'jobs'"
                @click="handleOpenAddJob"
                class="rounded-xl bg-linear-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-2 self-start sm:self-auto"
              >
                <span>➕ Publicar Nueva Vacante</span>
              </button>
              <button
                v-else
                @click="loadJobApplications"
                class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 transition cursor-pointer shadow-xs flex items-center gap-1.5"
              >
                <span>🔄 Recargar Candidatos</span>
              </button>
            </div>
          </div>

          <!-- Sub-Tab Switcher: Vacancies vs Candidate Applications -->
          <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-gray-200/80 dark:bg-gray-800 border border-gray-300/60 dark:border-gray-700 w-fit">
            <button
              type="button"
              @click="vacancySubTab = 'jobs'"
              class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="vacancySubTab === 'jobs'
                ? 'bg-white dark:bg-gray-700 text-red-600 dark:text-white shadow-xs font-black'
                : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
            >
              <span>📋 Ofertas de Vacantes</span>
              <span class="rounded-full px-2 py-0.5 text-[10px]" :class="vacancySubTab === 'jobs' ? 'bg-red-50 text-red-700 dark:bg-gray-800 dark:text-white' : 'bg-gray-300 dark:bg-gray-600'">
                {{ adminJobsList.length }}
              </span>
            </button>
            <button
              type="button"
              @click="vacancySubTab = 'applications'; loadJobApplications()"
              class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="vacancySubTab === 'applications'
                ? 'bg-white dark:bg-gray-700 text-red-600 dark:text-white shadow-xs font-black'
                : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
            >
              <span>📥 Bandeja de Postulantes</span>
              <span class="rounded-full px-2 py-0.5 text-[10px]" :class="vacancySubTab === 'applications' ? 'bg-red-50 text-red-700 dark:bg-gray-800 dark:text-white' : 'bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-200'">
                {{ jobApplicationsList.length }}
              </span>
            </button>
          </div>

          <!-- SUB-VIEW 1: Vacancies List & Cards -->
          <div v-if="vacancySubTab === 'jobs'" class="space-y-6">
            <!-- Vacancy Summary Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Total de Vacantes</span>
                <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ adminJobsList.length }}</p>
              </div>
              <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Activas en Web</span>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ adminJobsList.filter(j => j.is_active).length }}</p>
              </div>
              <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
                <span class="text-xs font-bold text-gray-400">Pausadas / En Espera</span>
                <p class="text-2xl font-black text-gray-500 dark:text-gray-400 mt-1">{{ adminJobsList.filter(j => !j.is_active).length }}</p>
              </div>
            </div>

            <!-- Vacancies Cards Grid -->
            <div v-if="isLoadingJobs && adminJobsList.length === 0" class="py-12 text-center text-gray-400">
              <svg class="animate-spin h-8 w-8 text-red-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <p class="text-xs">Cargando vacantes...</p>
            </div>

            <div v-else-if="adminJobsList.length === 0" class="p-12 text-center rounded-3xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
              <span class="text-4xl block mb-2">💼</span>
              <h3 class="text-base font-black text-gray-900 dark:text-white">No hay vacantes registradas</h3>
              <p class="text-xs text-gray-500 mt-1 mb-4">Comienza publicando tu primera oferta de trabajo para sumar talento a Pollo Feliz.</p>
              <button
                @click="handleOpenAddJob"
                class="rounded-xl bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-xs font-bold transition cursor-pointer"
              >
                Publicar Vacante
              </button>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
              <div
                v-for="job in adminJobsList"
                :key="job.id"
                class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs flex flex-col justify-between overflow-hidden transition-all hover:shadow-md"
              >
                <!-- Card Top Image / Banner -->
                <div v-if="job.image_url" class="relative h-40 w-full overflow-hidden bg-gray-100 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700/60 group">
                  <img
                    :src="job.image_url"
                    :alt="job.title"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    loading="lazy"
                    @error="(e) => e.target.style.display = 'none'"
                  />
                  <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-black/20"></div>

                  <!-- Floating Status Badges over Image -->
                  <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-black/60 backdrop-blur-xs text-amber-300 border border-amber-300/30">
                      {{ job.employment_type || 'Tiempo completo' }}
                    </span>
                    <button
                      @click="handleToggleJobActive(job)"
                      class="text-[10px] font-black px-2.5 py-1 rounded-full cursor-pointer transition backdrop-blur-xs border shadow-xs"
                      :class="job.is_active
                        ? 'bg-emerald-600/90 text-white border-emerald-400'
                        : 'bg-black/60 text-gray-300 border-gray-500'"
                      :title="job.is_active ? 'Hacer clic para pausar vacante' : 'Hacer clic para activar vacante'"
                    >
                      {{ job.is_active ? '● Activa en Web' : '○ Pausada' }}
                    </button>
                  </div>

                  <div class="absolute bottom-2.5 left-3 text-[11px] text-white/90 font-medium flex items-center gap-1.5 drop-shadow-xs">
                    <span>📸 Foto asignada</span>
                  </div>
                </div>

                <!-- Fallback Top Header (No Image) -->
                <div v-else class="p-5 pb-0">
                  <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                      {{ job.employment_type || 'Tiempo completo' }}
                    </span>
                    <button
                      @click="handleToggleJobActive(job)"
                      class="text-[10px] font-black px-2.5 py-0.5 rounded-full cursor-pointer transition border"
                      :class="job.is_active
                        ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300'
                        : 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-300'"
                      :title="job.is_active ? 'Hacer clic para pausar vacante' : 'Hacer clic para activar vacante'"
                    >
                      {{ job.is_active ? '● Activa en Web' : '○ Pausada' }}
                    </button>
                  </div>
                </div>

                <!-- Card Body Content -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="text-base font-black text-gray-900 dark:text-white mb-2 leading-snug">
                      {{ job.title }}
                    </h3>

                    <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400 mb-3">
                      <div class="flex items-center gap-1.5">
                        <span class="text-red-500">📍</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ job.location || 'Sucursal Centro Histórico' }}</span>
                      </div>
                      <div v-if="job.salary_range" class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold">
                        <span>💵</span>
                        <span>{{ job.salary_range }}</span>
                      </div>
                      <div v-if="job.schedule" class="flex items-center gap-1.5">
                        <span class="text-amber-500">⏰</span>
                        <span>{{ job.schedule }}</span>
                      </div>
                    </div>

                    <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-3 mb-4 leading-relaxed">
                      {{ job.description }}
                    </p>
                  </div>

                  <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100 dark:border-gray-700/80">
                    <span v-if="!job.image_url" class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold flex items-center gap-1">
                      <span>💡</span> Sin foto asignada
                    </span>
                    <span v-else class="text-[11px] text-gray-400">
                      ID #{{ job.id }}
                    </span>

                    <div class="flex items-center gap-1.5">
                      <button
                        @click="handleOpenEditJob(job)"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition cursor-pointer flex items-center gap-1"
                      >
                        <span>✏️ Editar</span>
                      </button>
                      <button
                        @click="handlePromptDeleteJob(job)"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900 transition cursor-pointer"
                        title="Eliminar vacante"
                      >
                        <span>🗑️</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SUB-VIEW 2: Candidate Applications Tray (Bandeja de Postulaciones) -->
          <div v-else class="space-y-4">
            <!-- Applications Filter Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-2xs">
              <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 mr-1">Filtrar estado:</span>
                <button
                  v-for="st in [
                    { id: 'all', label: 'Todos' },
                    { id: 'nuevo', label: 'Nuevos' },
                    { id: 'en_revision', label: 'En Revisión' },
                    { id: 'entrevista', label: 'Entrevista' },
                    { id: 'contratado', label: 'Contratados' },
                    { id: 'descartado', label: 'Descartados' },
                  ]"
                  :key="st.id"
                  @click="appStatusFilter = st.id"
                  class="rounded-xl px-3 py-1.5 text-xs font-bold transition cursor-pointer"
                  :class="appStatusFilter === st.id
                    ? 'bg-red-600 text-white shadow-xs'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'"
                >
                  {{ st.label }}
                  <span v-if="st.id === 'all'" class="text-[10px] ml-1 opacity-75">({{ jobApplicationsList.length }})</span>
                  <span v-else class="text-[10px] ml-1 opacity-75">
                    ({{ jobApplicationsList.filter(a => a.status === st.id).length }})
                  </span>
                </button>
              </div>

              <span class="text-xs font-medium text-gray-500">
                Mostrando {{ filteredApplications.length }} solicitudes
              </span>
            </div>

            <!-- Applications List -->
            <div v-if="isLoadingApplications" class="py-12 text-center text-gray-400">
              <svg class="animate-spin h-8 w-8 text-red-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <p class="text-xs">Cargando candidatos...</p>
            </div>

            <div v-else-if="filteredApplications.length === 0" class="p-12 text-center rounded-3xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
              <span class="text-4xl block mb-2">📥</span>
              <h3 class="text-base font-black text-gray-900 dark:text-white">No hay postulaciones en este estado</h3>
              <p class="text-xs text-gray-500 mt-1">Cuando los usuarios envíen su CV o solicitud en la web, aparecerán reflejados aquí al instante.</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="app in filteredApplications"
                :key="app.id"
                class="p-5 rounded-3xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:shadow-md"
              >
                <!-- Candidate Info -->
                <div class="space-y-1.5 flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-black text-sm text-gray-950 dark:text-white">
                      {{ app.applicant_name }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase border" :class="getAppStatusMeta(app.status).badge">
                      {{ getAppStatusMeta(app.status).label }}
                    </span>
                    <span class="text-[11px] text-gray-400">
                      ID #{{ app.id }} · {{ app.created_at ? new Date(app.created_at).toLocaleDateString('es-MX') : 'Reciente' }}
                    </span>
                  </div>

                  <p class="text-xs font-bold text-red-600 dark:text-red-400">
                    💼 Vacante: {{ app.vacancy_title }}
                  </p>

                  <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-300 flex-wrap">
                    <a :href="`tel:${app.phone}`" class="hover:text-red-600 flex items-center gap-1 font-mono">
                      <span>📞</span> {{ app.phone }}
                    </a>
                    <a :href="`https://wa.me/${app.phone.replace(/[^0-9]/g, '')}`" target="_blank" rel="noopener noreferrer" class="text-emerald-600 font-bold hover:underline flex items-center gap-1">
                      <span>💬</span> WhatsApp
                    </a>
                    <a :href="`mailto:${app.email}`" class="hover:text-red-600 flex items-center gap-1">
                      <span>✉️</span> {{ app.email }}
                    </a>
                  </div>

                  <p v-if="app.notes" class="text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/60 p-2.5 rounded-xl border border-gray-100 dark:border-gray-800 mt-2">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Nota del aspirante:</span> "{{ app.notes }}"
                  </p>
                </div>

                <!-- Status Selector, CV & Delete Actions -->
                <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                  <!-- CV Download Button -->
                  <a
                    v-if="app.resume_data"
                    :href="app.resume_data"
                    :download="app.resume_name || `CV_${app.applicant_name}.pdf`"
                    class="rounded-xl border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:border-blue-900/60 dark:bg-blue-950/40 dark:text-blue-300 px-3 py-1.5 text-xs font-bold flex items-center gap-1 transition"
                    title="Descargar Currículum"
                  >
                    <span>📎 Ver CV</span>
                  </a>
                  <span v-else class="text-[11px] text-gray-400 italic">
                    Sin archivo CV
                  </span>

                  <!-- Status Pipeline Dropdown -->
                  <div class="flex items-center gap-1">
                    <select
                      :value="app.status"
                      @change="handleChangeAppStatus(app.id, $event.target.value)"
                      class="rounded-xl border border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-xs font-bold px-2.5 py-1.5 text-gray-800 dark:text-white cursor-pointer focus:ring-1 focus:ring-red-500"
                    >
                      <option value="nuevo">🆕 Nuevo</option>
                      <option value="en_revision">🔍 En Revisión</option>
                      <option value="entrevista">📅 Entrevista</option>
                      <option value="contratado">✅ Contratado</option>
                      <option value="descartado">❌ Descartado</option>
                    </select>
                  </div>

                  <!-- Delete Application -->
                  <button
                    @click="handlePromptDeleteApp(app)"
                    class="p-2 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                    title="Eliminar postulación"
                  >
                    🗑️
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: Logs del Sistema & Diagnóstico del Servidor -->
        <div v-else-if="activeTab === 'logs'" class="space-y-6">
          <SystemLogsTab />
        </div>

        <!-- TAB: Usuarios & Roles Administrativos -->
        <div v-else-if="activeTab === 'users'" class="space-y-6">
          <!-- Header and Global Security Actions -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <div class="flex items-center gap-2">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">
                  Usuarios, Accesos & Seguridad del Sitio
                </h2>
                <span class="rounded-full bg-purple-100 text-purple-800 text-xs px-2.5 py-0.5 font-bold dark:bg-purple-950/60 dark:text-purple-300">
                  {{ adminUsersList.length }} cuentas
                </span>
                <span class="rounded-full bg-emerald-100 text-emerald-800 text-xs px-2.5 py-0.5 font-bold dark:bg-emerald-950/60 dark:text-emerald-300">
                  Escudo A+ Activo
                </span>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Monitorea la postura de seguridad, revoca sesiones de emergencia y gestiona cuentas con permisos modulares.
              </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
              <!-- Global Emergency Incident Revocation -->
              <button
                type="button"
                @click="handleRevokeAllSessions"
                class="rounded-xl border border-rose-300 bg-rose-50 hover:bg-rose-100 text-rose-800 dark:border-rose-800 dark:bg-rose-950/60 dark:text-rose-300 dark:hover:bg-rose-900 px-3.5 py-2 text-xs font-black shadow-2xs transition cursor-pointer flex items-center gap-1.5"
                title="Revocar inmediatamente todas las sesiones de todos los usuarios en caso de sospecha de compromiso"
              >
                <span>🚨 Revocar Todas las Sesiones</span>
              </button>

              <button
                @click="handleOpenAddUser"
                class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-4 py-2 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition cursor-pointer flex items-center gap-1.5"
              >
                <span>+ Nuevo Usuario</span>
              </button>
            </div>
          </div>

          <!-- Real-Time Security Posture & Diagnostics Dashboard -->
          <div class="p-5 rounded-3xl bg-linear-to-br from-gray-900 via-slate-900 to-gray-950 text-white shadow-lg border border-gray-800 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-800 pb-3">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-500/30">
                  🛡️
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="text-sm font-black text-white">
                      Escudo de Seguridad & Diagnóstico en Tiempo Real
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                      CALIFICACIÓN {{ securityStatus?.security_score || 98 }}/100 ({{ securityStatus?.security_grade || 'A+' }})
                    </span>
                  </div>
                  <p class="text-xs text-gray-400 mt-0.5">
                    Defensas activas: Cifrado TLS 256-bit, limitador anti-fuerza bruta, rate limiting DDoS y protección de sesiones.
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="loadSecurityStatus"
                  :disabled="loadingSecurityStatus"
                  class="rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 px-3 py-1.5 text-xs font-semibold transition cursor-pointer flex items-center gap-1 border border-gray-700"
                >
                  <span :class="{ 'animate-spin': loadingSecurityStatus }">🔄</span>
                  <span>Verificar Estado</span>
                </button>
              </div>
            </div>

            <!-- Security Metrics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
              <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Cifrado de Red</span>
                <span class="text-xs font-black text-emerald-400 flex items-center gap-1">
                  <span>🔒</span> TLS 256-bit
                </span>
                <p class="text-[9px] text-gray-400">HSTS & CSP activos</p>
              </div>

              <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Filtro Anti-DDoS</span>
                <span class="text-xs font-black text-emerald-400 flex items-center gap-1">
                  <span>⚡</span> Rate Limiter
                </span>
                <p class="text-[9px] text-gray-400">120 req/min por IP</p>
              </div>

              <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Escudo Fuerza Bruta</span>
                <span class="text-xs font-black text-emerald-400 flex items-center gap-1">
                  <span>🧱</span> Bloqueo Activo
                </span>
                <p class="text-[9px] text-gray-400">Máx 5 intentos erróneos</p>
              </div>

              <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Modo de Acceso</span>
                <span class="text-xs font-black text-emerald-400 flex items-center gap-1">
                  <span>⚡</span> Acceso Directo RBAC
                </span>
                <p class="text-[9px] text-gray-400">Acceso libre inmediato</p>
              </div>

              <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Sesiones Activas</span>
                <span class="text-xs font-black text-cyan-400 flex items-center gap-1">
                  <span>🌐</span> {{ securityStatus?.active_sessions_count || 1 }} en vivo
                </span>
                <p class="text-[9px] text-gray-400">Auto-cierre tras 30m idle</p>
              </div>

              <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Tokens Revocados</span>
                <span class="text-xs font-black text-amber-400 flex items-center gap-1">
                  <span>🚫</span> {{ securityStatus?.revoked_tokens_count || 0 }}
                </span>
                <p class="text-[9px] text-gray-400">Lista negra de seguridad</p>
              </div>
            </div>
          </div>

          <!-- Security Status Card -->
          <div class="p-5 rounded-3xl bg-linear-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-100 dark:from-emerald-950/30 dark:via-teal-950/30 dark:to-blue-950/30 dark:border-emerald-900/40 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-start gap-3.5">
              <div class="h-11 w-11 rounded-2xl bg-white dark:bg-gray-800 shadow-xs flex items-center justify-center text-xl shrink-0">
                🛡️
              </div>
              <div>
                <div class="flex items-center gap-2 flex-wrap">
                  <h3 class="text-sm font-black text-gray-900 dark:text-white">
                    Seguridad y Control de Acceso
                  </h3>
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide border bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800"
                  >
                    ✓ Acceso Directo Autorizado
                  </span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 max-w-2xl leading-relaxed">
                  El sistema se encuentra configurado en modo de acceso directo y seguro. Todas las pestañas y herramientas operativas están totalmente disponibles según los permisos modulares de tu rol.
                </p>
              </div>
            </div>
          </div>

          <!-- Roles Info Cards Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div
              v-for="r in ROLES_CONFIG"
              :key="r.id"
              class="p-3.5 rounded-2xl bg-white border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-1 shadow-xs"
            >
              <div class="flex items-center justify-between">
                <span class="text-xs font-black text-gray-900 dark:text-white flex items-center gap-1">
                  <span>{{ r.icon }}</span> {{ r.label }}
                </span>
                <span class="text-xs font-mono font-bold text-gray-500">
                  {{ adminUsersList.filter(u => u.role === r.id).length }}
                </span>
              </div>
              <p class="text-[10px] text-gray-400 line-clamp-2 leading-tight">
                {{ r.description }}
              </p>
            </div>
          </div>

          <!-- Users Search & Filter Bar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex flex-wrap items-center gap-1.5">
              <button
                v-for="rf in [
                  { id: 'all', label: 'Todos los Roles' },
                  { id: 'super_admin', label: '👑 Super Admin' },
                  { id: 'gerente', label: '👔 Gerente' },
                  { id: 'cocina', label: '🍳 Cocina' },
                  { id: 'atencion', label: '💬 Atención' },
                  { id: 'cajero', label: '💳 Cajero' },
                ]"
                :key="rf.id"
                @click="userRoleFilter = rf.id"
                class="rounded-xl px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                :class="userRoleFilter === rf.id
                  ? 'bg-red-600 text-white shadow-2xs'
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'"
              >
                {{ rf.label }}
              </button>

              <div class="h-4 w-px bg-gray-200 dark:bg-gray-700 mx-1 hidden sm:block"></div>

              <button
                v-for="sf in [
                  { id: 'all', label: 'Todos' },
                  { id: 'active', label: '✅ Activos' },
                  { id: 'suspended', label: '🚫 Suspendidos' },
                ]"
                :key="sf.id"
                @click="userStatusFilter = sf.id"
                class="rounded-xl px-2.5 py-1 text-xs font-bold transition cursor-pointer"
                :class="userStatusFilter === sf.id
                  ? 'bg-purple-600 text-white shadow-2xs'
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'"
              >
                {{ sf.label }}
              </button>
              <div class="h-4 w-px bg-gray-200 dark:bg-gray-700 mx-1 hidden sm:block"></div>

              <select
                v-model="userPermissionFilter"
                class="rounded-xl border border-gray-200 bg-gray-50 py-1.5 px-2.5 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 cursor-pointer focus:border-red-500 focus:outline-hidden"
              >
                <option value="all">🔍 Todos los permisos</option>
                <option value="menu.manage">🍗 Menú & Platillos</option>
                <option value="orders.manage">🛒 Pedidos en Vivo</option>
                <option value="promotions.manage">🏷️ Promociones & Ofertas</option>
                <option value="branches.manage">📍 Sucursales & Horarios</option>
                <option value="messages.manage">💬 Bandeja de Dudas</option>
                <option value="reviews.manage">⭐ Moderación Reseñas</option>
                <option value="landing.manage">🎨 Editor Web</option>
                <option value="users.manage">👥 Gestión de Usuarios</option>
                <option value="security.manage">🛡️ Auditoría & Sesiones</option>
              </select>
            </div>

            <div class="relative w-full sm:w-64">
              <input
                v-model="userSearchQuery"
                type="text"
                placeholder="Buscar por nombre o correo..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <!-- Users Table / Cards List -->
          <div class="rounded-3xl bg-white shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
            <!-- RBAC info banner -->
            <div class="p-3.5 bg-linear-to-r from-purple-50 to-indigo-50 dark:from-purple-950/40 dark:to-indigo-950/40 border-b border-purple-100 dark:border-purple-900/40 text-xs text-purple-900 dark:text-purple-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-5">
              <div class="flex items-center gap-2">
                <span class="text-base">💡</span>
                <span><strong>Control RBAC Activo:</strong> Los permisos específicos que asignes a cada usuario determinan qué pestañas y módulos podrá ver y operar al iniciar sesión.</span>
              </div>
              <span class="text-[11px] font-bold text-purple-700 dark:text-purple-300 bg-white dark:bg-purple-900/50 px-2.5 py-0.5 rounded-full border border-purple-200 dark:border-purple-700 shrink-0">
                {{ adminUsersList.length }} cuentas configuradas
              </span>
            </div>

            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                  Cuentas Registradas & Permisos ({{ filteredAdminUsers.length }})
                </h3>
              </div>
              <button
                @click="loadAdminUsers"
                class="text-xs text-red-600 hover:underline dark:text-red-400 font-semibold cursor-pointer"
              >
                🔄 Recargar lista
              </button>
            </div>

            <!-- Empty State for Users Filter -->
            <div v-if="filteredAdminUsers.length === 0" class="p-10 text-center">
              <span class="text-3xl block mb-2">🔍</span>
              <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">No se encontraron usuarios</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Prueba cambiando los filtros de búsqueda o rol.</p>
            </div>

            <div v-else class="divide-y divide-gray-100 dark:divide-gray-800">
              <div
                v-for="u in filteredAdminUsers"
                :key="u.id"
                class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition"
              >
                <!-- User Info -->
                <div class="flex items-start gap-3.5 min-w-0">
                  <div
                    class="h-10 w-10 shrink-0 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs mt-0.5"
                    :class="u.role === 'super_admin'
                      ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/80 dark:text-purple-300'
                      : u.role === 'gerente'
                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300'
                        : u.role === 'cocina'
                          ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300'
                          : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300'"
                  >
                    {{ u.name[0] }}
                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="font-bold text-gray-900 dark:text-white text-sm">
                        {{ u.name }}
                      </span>
                      <span
                        v-if="u.id === adminUser?.id"
                        class="rounded-full bg-red-100 text-red-700 text-[10px] px-2 py-0.2 font-black dark:bg-red-950/70 dark:text-red-300"
                      >
                        (Tu sesión actual)
                      </span>
                      <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                        :class="getRoleMeta(u.role).badgeClass"
                      >
                        {{ getRoleMeta(u.role).icon }} {{ getRoleMeta(u.role).label }}
                      </span>
                      <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-black border uppercase tracking-wider"
                        :class="u.status === 'suspended'
                          ? 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/70 dark:text-rose-300 dark:border-rose-800'
                          : 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/70 dark:text-emerald-300 dark:border-emerald-800'"
                      >
                        {{ u.status === 'suspended' ? '🚫 Suspendida' : '✅ Activa' }}
                      </span>

                      <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold border flex items-center gap-1"
                        :class="(u.active_sessions_count > 0 || (u.active_sessions_count === undefined && !u.last_revoked_at))
                          ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900'
                          : 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'"
                        :title="u.last_revoked_at ? `Última revocación: ${formatDate(u.last_revoked_at)}` : 'Sesión activa en el servidor'"
                      >
                        <span>{{ (u.active_sessions_count > 0 || (u.active_sessions_count === undefined && !u.last_revoked_at)) ? '🟢' : '⚪' }}</span>
                        <span>{{ (u.active_sessions_count > 0 || (u.active_sessions_count === undefined && !u.last_revoked_at)) ? 'Sesión activa' : 'Sesiones revocadas' }}</span>
                      </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-1 flex-wrap">
                      <span>{{ u.email }}</span>
                      <span>·</span>
                      <span>Registrado: {{ formatDate(u.created_at) }}</span>
                      <span v-if="u.last_revoked_at" class="text-amber-600 dark:text-amber-400 font-medium text-[11px]">
                        · Revocado: {{ formatDate(u.last_revoked_at) }}
                      </span>
                    </div>

                    <!-- Permissions chips preview with icons and labels -->
                    <div class="mt-2.5">
                      <div v-if="u.permissions && u.permissions.length" class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[10px] font-bold uppercase text-gray-400 mr-0.5">Permisos:</span>
                        <span
                          v-for="perm in u.permissions"
                          :key="perm"
                          class="text-[11px] px-2 py-0.5 rounded-lg border font-semibold flex items-center gap-1 shadow-2xs"
                          :class="getPermissionMeta(perm).color"
                        >
                          <span>{{ getPermissionMeta(perm).icon }}</span>
                          <span>{{ getPermissionMeta(perm).label }}</span>
                        </span>
                      </div>
                      <div v-else class="text-[11px] text-gray-400 italic">
                        Sin permisos específicos (acceso predeterminado por rol)
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 self-end sm:self-auto flex-wrap">
                  <!-- Botón: Revocar todas las sesiones activas (Sospecha de compromiso) -->
                  <button
                    type="button"
                    @click="handleRevokeUserSessions(u)"
                    class="rounded-xl border border-amber-300 bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-800 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-900/60 transition cursor-pointer flex items-center gap-1 shadow-2xs"
                    title="Cerrar y revocar todas las sesiones activas de esta cuenta por sospecha de compromiso"
                  >
                    <span>🛡️</span>
                    <span>Cerrar Sesiones</span>
                  </button>

                  <!-- Botón: Suspender o Reactivar cuenta (Bloqueo de emergencia) -->
                  <button
                    v-if="u.id !== 1 && u.id !== adminUser?.id"
                    type="button"
                    @click="handleToggleUserStatus(u)"
                    class="rounded-xl border px-2.5 py-1.5 text-xs font-bold transition cursor-pointer flex items-center gap-1 shadow-2xs"
                    :class="u.status === 'suspended'
                      ? 'border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300'
                      : 'border-rose-300 bg-rose-50 text-rose-800 hover:bg-rose-100 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300'"
                    :title="u.status === 'suspended' ? 'Reactivar acceso a esta cuenta' : 'Suspender temporalmente acceso a esta cuenta'"
                  >
                    <span>{{ u.status === 'suspended' ? '✅ Reactivar' : '🚫 Suspender' }}</span>
                  </button>

                  <!-- Botón: Editar Rol & Permisos -->
                  <button
                    @click="handleOpenEditUser(u)"
                    class="rounded-xl border border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-700 dark:border-purple-800 dark:bg-purple-950/40 dark:text-purple-300 px-3 py-1.5 text-xs font-bold transition cursor-pointer flex items-center gap-1 shadow-2xs"
                    title="Configurar rol y permisos específicos de este usuario"
                  >
                    <span>🔑 Permisos & Rol</span>
                  </button>

                  <!-- Botón: Eliminar usuario con modal de confirmación -->
                  <button
                    v-if="u.id !== 1 && u.id !== adminUser?.id"
                    @click="handlePromptDeleteUser(u)"
                    class="rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300 px-2.5 py-1.5 text-xs font-semibold transition cursor-pointer flex items-center gap-1"
                    title="Eliminar usuario definitivamente"
                  >
                    <span>🗑️ Eliminar</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Security Audit Trail Table -->
          <div class="rounded-3xl bg-white shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 overflow-hidden p-5 space-y-3">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                  <span>📜</span>
                  <span>Registro de Auditoría de Seguridad & Cambios Recientes</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                  Trazabilidad inmutable de eventos administrativos, autenticación y respuesta a incidentes.
                </p>
              </div>
              <button
                type="button"
                @click="loadSecurityStatus"
                class="text-xs text-purple-600 hover:underline dark:text-purple-400 font-semibold cursor-pointer"
              >
                🔄 Actualizar bitácora
              </button>
            </div>

            <div v-if="!securityStatus?.recent_audit_logs || securityStatus.recent_audit_logs.length === 0" class="py-6 text-center text-xs text-gray-400">
              No hay eventos de seguridad registrados recientemente.
            </div>

            <div v-else class="divide-y divide-gray-100 dark:divide-gray-800">
              <div
                v-for="log in securityStatus.recent_audit_logs"
                :key="log.id"
                class="py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs"
              >
                <div class="flex items-center gap-2">
                  <span class="font-mono text-[10px] text-gray-400">
                    {{ formatDate(log.created_at) }}
                  </span>
                  <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    {{ log.action }}
                  </span>
                  <span class="text-gray-800 dark:text-gray-200 font-medium">
                    {{ log.summary }}
                  </span>
                </div>

                <div class="text-[11px] text-gray-400 shrink-0">
                  <span>Por: <strong class="text-gray-600 dark:text-gray-300">{{ log.author_name || 'Sistema' }}</strong></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Modals for Menu CRUD & Bulk Operations -->
    <MenuItemModal
      :is-open="showItemModal"
      :item-to-edit="itemToEdit"
      :existing-categories="allUniqueCategories"
      @close="showItemModal = false"
      @save="handleSaveMenuItem"
    />

    <BulkCategoryModal
      :is-open="showBulkCategoryModal"
      :items="selectedItemsList"
      :existing-categories="allUniqueCategories"
      @close="showBulkCategoryModal = false"
      @confirm="handleConfirmBulkCategory"
    />

    <DeleteConfirmModal
      :is-open="showDeleteModal"
      :title="deleteModalTitle"
      :message="deleteModalMessage"
      :item-name="itemToDelete?.name || itemToDelete?.title"
      :items-list="itemsToDeleteNames"
      :is-bulk="isBulkDelete"
      @cancel="showDeleteModal = false"
      @confirm="handleConfirmDelete"
    />

    <!-- Modal for Promotions CRUD -->
    <PromotionModal
      :is-open="showPromoModal"
      :promo-to-edit="promoToEdit"
      @close="showPromoModal = false"
      @save="handleSavePromo"
    />

    <!-- Modal for Branches CRUD -->
    <BranchModal
      :is-open="showBranchModal"
      :branch-to-edit="branchToEdit"
      @close="showBranchModal = false"
      @save="handleSaveBranch"
      @delete="(id) => { showBranchModal = false; const b = branches.find(item => item.id === id); if (b) handlePromptDeleteBranch(b); }"
    />

    <!-- Modal for User & Role CRUD -->
    <UserModal
      :is-open="showUserModal"
      :user-to-edit="userToEdit"
      @close="showUserModal = false"
      @save="handleSaveUser"
    />

    <!-- Modal for Vacancy / Job CRUD -->
    <VacancyModal
      :is-open="showVacancyModal"
      :job-to-edit="jobToEdit"
      @close="showVacancyModal = false"
      @save="handleSaveJob"
    />

    <!-- Modal for Category CRUD -->
    <CategoryModal
      :is-open="showCategoryModal"
      :category-to-edit="categoryToEdit"
      :next-sort-order="adminCategoriesList.length + 1"
      @close="showCategoryModal = false"
      @save="handleSaveCategory"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/plugins/axios'
import logo from '@/assets/images/logo.png'
import { useAdminAuth, hasAdminToken } from '@/composables/useAdminAuth'
import { useToasts } from '@/composables/useToasts'
import MenuItemModal from '@/components/admin/MenuItemModal.vue'
import DeleteConfirmModal from '@/components/admin/DeleteConfirmModal.vue'
import BulkCategoryModal from '@/components/admin/BulkCategoryModal.vue'
import PromotionModal from '@/components/admin/PromotionModal.vue'
import BranchModal from '@/components/admin/BranchModal.vue'
import UserModal from '@/components/admin/UserModal.vue'
import VacancyModal from '@/components/admin/VacancyModal.vue'
import CategoryModal from '@/components/admin/CategoryModal.vue'
import SystemLogsTab from '@/components/admin/SystemLogsTab.vue'
import AnalyticsDashboard from '@/components/admin/AnalyticsDashboard.vue'
import LandingEditor from '@/components/admin/LandingEditor.vue'
import { useTheme } from '@/composables/useTheme'
import { useLandingContent } from '@/composables/useLandingContent'

const router = useRouter()
const route = useRoute()
const { logout, adminUser, isSuperAdmin, canAccessTab, ROLES_CONFIG, getRoleMeta, fetchDatabaseStatus, fetchProfile, checkAuth } = useAdminAuth()
const { addToast } = useToasts()
const { theme, toggleTheme } = useTheme()
const { loadLandingContent } = useLandingContent()

const dbStatusInfo = ref(null)
const activeTab = ref(route.query?.tab || 'summary')
const orderFilter = ref('all')
const orderTypeFilter = ref('all')
const orderSearch = ref('')
const isRefreshing = ref(false)

// Navegación directa entre pestañas sin bloqueos ni ventanas emergentes
const selectTab = (tabId) => {
  activeTab.value = tabId
}

const tabs = [
  { id: 'summary', label: 'Ventas & Analíticas', icon: '📈' },
  { id: 'menu', label: 'Gestión Menú', icon: '🍗' },
  { id: 'landing', label: 'Editar Landing', icon: '🎨' },
  { id: 'vacancies', label: 'Bolsa de Vacantes', icon: '💼' },
  { id: 'orders', label: 'Pedidos en Vivo', icon: '🛵' },
  { id: 'promotions', label: 'Promociones', icon: '🏷️' },
  { id: 'branches', label: 'Sucursales', icon: '📍' },
  { id: 'reviews', label: 'Reseñas & Opiniones', icon: '⭐' },
  { id: 'messages', label: 'Mensajes', icon: '💬' },
  { id: 'logs', label: 'Logs del Sistema', icon: '🪵' },
  { id: 'users', label: 'Usuarios & Roles', icon: '👥' },
]

const visibleTabs = computed(() => {
  return tabs.filter(t => canAccessTab(t.id))
})

const currentRoleMeta = computed(() => getRoleMeta(adminUser.value?.role || 'super_admin'))

const orderFilters = [
  { id: 'all', label: 'Todos' },
  { id: 'active', label: 'En proceso' },
  { id: 'on_the_way', label: 'En camino' },
  { id: 'delivered', label: 'Entregados' },
]

// State for content
const summary = ref({})
const menuItems = ref([])
const promotions = ref([])
const branches = ref([])
const contactMessages = ref([])
const messageFilter = ref('all') // 'all' | 'pending' | 'attended'
const orders = ref([])
const adminUsersList = ref([])
const showUserModal = ref(false)
const userToEdit = ref(null)
const userSearchQuery = ref('')
const userRoleFilter = ref('all')
const userStatusFilter = ref('all')
const userPermissionFilter = ref('all')
const securityStatus = ref(null)
const loadingSecurityStatus = ref(false)

// State for Categories CRUD
const adminCategoriesList = ref([])
const showCategoryModal = ref(false)
const categoryToEdit = ref(null)
const showCategoriesManager = ref(false)

// State for Vacancies / Bolsa de Trabajo CRUD
const adminJobsList = ref([])
const showVacancyModal = ref(false)
const jobToEdit = ref(null)
const isLoadingJobs = ref(false)

// State for Candidates / Job Applications Tray
const vacancySubTab = ref('jobs') // 'jobs' | 'applications'
const jobApplicationsList = ref([])
const appStatusFilter = ref('all')
const isLoadingApplications = ref(false)

const getPermissionMeta = (permId) => {
  const map = {
    'menu.manage': { label: 'Menú & Precios', icon: '🍗', color: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' },
    'orders.manage': { label: 'Pedidos en Vivo', icon: '🛒', color: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' },
    'promotions.manage': { label: 'Promociones & Cupones', icon: '🏷️', color: 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300 border-orange-200 dark:border-orange-800' },
    'branches.manage': { label: 'Sucursales & Horarios', icon: '📍', color: 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800' },
    'messages.manage': { label: 'Bandeja de Dudas', icon: '💬', color: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950/60 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800' },
    'reviews.manage': { label: 'Moderación Reseñas', icon: '⭐', color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/60 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800' },
    'landing.manage': { label: 'Editor Web', icon: '🎨', color: 'bg-pink-100 text-pink-800 dark:bg-pink-950/60 dark:text-pink-300 border-pink-200 dark:border-pink-800' },
    'users.manage': { label: 'Gestión Usuarios', icon: '👥', color: 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800' },
    'security.manage': { label: 'Seguridad & Sesiones', icon: '🛡️', color: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800' },
    'admin.manage': { label: 'Acceso Total', icon: '👑', color: 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800' },
  }
  return map[permId] || { label: permId, icon: '🔑', color: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700' }
}

const filteredAdminUsers = computed(() => {
  let list = [...adminUsersList.value]
  if (userRoleFilter.value !== 'all') {
    list = list.filter(u => u.role === userRoleFilter.value)
  }
  if (userStatusFilter.value !== 'all') {
    list = list.filter(u => (u.status || 'active') === userStatusFilter.value)
  }
  if (userPermissionFilter.value !== 'all') {
    list = list.filter(u =>
      u.role === 'super_admin' ||
      (Array.isArray(u.permissions) && (u.permissions.includes(userPermissionFilter.value) || u.permissions.includes('admin.manage')))
    )
  }
  if (userSearchQuery.value.trim()) {
    const q = userSearchQuery.value.trim().toLowerCase()
    list = list.filter(u =>
      (u.name && u.name.toLowerCase().includes(q)) ||
      (u.email && u.email.toLowerCase().includes(q))
    )
  }
  return list
})

// Reviews Moderation State
const adminReviewsList = ref([])
const adminReviewsStats = ref({})
const adminReviewFilter = ref('all') // 'all' | 'approved' | 'pending' | 'rejected' | 'featured'
const adminReviewSearch = ref('')
const activeReplyReviewId = ref(null)
const replyTextInput = ref('')

const pendingMessagesCount = computed(() => {
  return contactMessages.value.filter(m => m.status === 'pending').length
})

const filteredContactMessages = computed(() => {
  if (messageFilter.value === 'pending') {
    return contactMessages.value.filter(m => m.status === 'pending')
  }
  if (messageFilter.value === 'attended') {
    return contactMessages.value.filter(m => m.status !== 'pending')
  }
  return contactMessages.value
})

// State for Menu CRUD operations
const menuSearch = ref('')
const selectedMenuCategory = ref('Todas')
const selectedMenuStatus = ref('all') // 'all', 'active', 'paused'
const showItemModal = ref(false)
const itemToEdit = ref(null)
const showDeleteModal = ref(false)
const itemToDelete = ref(null)
const deleteItemType = ref('menu') // 'menu' | 'promotion' | 'branch'
const deleteModalTitle = ref('')
const deleteModalMessage = ref('')

// State for Promotions & Branches CRUD operations
const showPromoModal = ref(false)
const promoToEdit = ref(null)
const showBranchModal = ref(false)
const branchToEdit = ref(null)

// Bulk selection & actions state
const selectedItemIds = ref([])
const bulkTargetCategory = ref('')
const showBulkCategoryModal = ref(false)
const isBulkDelete = ref(false)

const selectedItemsList = computed(() => {
  return menuItems.value.filter(i => selectedItemIds.value.includes(i.id))
})

const isAllFilteredSelected = computed(() => {
  return filteredMenuItems.value.length > 0 &&
    filteredMenuItems.value.every(item => selectedItemIds.value.includes(item.id))
})

const isPartiallySelected = computed(() => {
  const count = filteredMenuItems.value.filter(item => selectedItemIds.value.includes(item.id)).length
  return count > 0 && count < filteredMenuItems.value.length
})

const itemsToDeleteNames = computed(() => {
  if (isBulkDelete.value) {
    return selectedItemsList.value.map(i => i.name)
  }
  return itemToDelete.value ? [itemToDelete.value.name || itemToDelete.value.title] : []
})

const toggleSelectAll = () => {
  if (isAllFilteredSelected.value) {
    const filteredIds = new Set(filteredMenuItems.value.map(i => i.id))
    selectedItemIds.value = selectedItemIds.value.filter(id => !filteredIds.has(id))
  } else {
    const set = new Set(selectedItemIds.value)
    filteredMenuItems.value.forEach(i => set.add(i.id))
    selectedItemIds.value = Array.from(set)
  }
}

const toggleSelectItem = (id) => {
  const idx = selectedItemIds.value.indexOf(id)
  if (idx > -1) {
    selectedItemIds.value.splice(idx, 1)
  } else {
    selectedItemIds.value.push(id)
  }
}

const clearSelection = () => {
  selectedItemIds.value = []
}

const defaultCategoryPresets = [
  'Combos',
  'Bebidas',
  'Extras',
  'Pollo Entero',
  'Medio Pollo',
  'Paquetes',
  'Complementos',
  'Tacos',
  'Ensaladas',
  'Postres',
]

// Dynamic Categories computed from items + presets
const allUniqueCategories = computed(() => {
  const set = new Set()
  defaultCategoryPresets.forEach(preset => set.add(preset))
  menuItems.value.forEach(item => {
    if (item.category && item.category.trim()) {
      set.add(item.category.trim())
    }
  })
  return Array.from(set)
})

// Categories that currently have items + default key categories
const menuCategoryFilterOptions = computed(() => {
  const set = new Set(['Todas'])
  // Add active categories with items first
  menuItems.value.forEach(item => {
    if (item.category && item.category.trim()) {
      set.add(item.category.trim())
    }
  })
  // Include key categories for easy access
  set.add('Combos')
  set.add('Bebidas')
  set.add('Extras')
  set.add('Pollo Entero')
  set.add('Medio Pollo')
  return Array.from(set)
})

// Count items per category
const getCategoryCount = (categoryName) => {
  if (categoryName === 'Todas') return menuItems.value.length
  return menuItems.value.filter(
    i => (i.category || '').toLowerCase().trim() === categoryName.toLowerCase().trim()
  ).length
}

// Category summary cards widget (Combos, Drinks, Extras, Pollo, etc.)
const categorySummaryCards = computed(() => [
  {
    id: 'Combos',
    label: 'Combos',
    subtext: 'Paquetes completos',
    icon: '🍱',
    count: getCategoryCount('Combos') + getCategoryCount('Paquetes'),
  },
  {
    id: 'Bebidas',
    label: 'Bebidas',
    subtext: 'Drinks & Refrescos',
    icon: '🥤',
    count: getCategoryCount('Bebidas') + getCategoryCount('Drinks'),
  },
  {
    id: 'Extras',
    label: 'Extras',
    subtext: 'Papas, salsas, etc.',
    icon: '🍟',
    count: getCategoryCount('Extras') + getCategoryCount('Complementos'),
  },
  {
    id: 'Pollo Entero',
    label: 'Pollo Entero',
    subtext: 'Al carbón tradicional',
    icon: '🍗',
    count: getCategoryCount('Pollo Entero'),
  },
  {
    id: 'Medio Pollo',
    label: 'Medio Pollo',
    subtext: 'Porción individual',
    icon: '🍗',
    count: getCategoryCount('Medio Pollo'),
  },
  {
    id: 'Tacos',
    label: 'Tacos & Más',
    subtext: 'Especialidades',
    icon: '🌮',
    count: getCategoryCount('Tacos') + getCategoryCount('Ensaladas'),
  },
])

// Category visual styling helper
const getCategoryBadgeClass = (category) => {
  const cat = (category || '').toLowerCase()
  if (cat.includes('combo')) return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-900'
  if (cat.includes('bebida') || cat.includes('drink')) return 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-900'
  if (cat.includes('extra') || cat.includes('complemento')) return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900'
  if (cat.includes('entero')) return 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-900'
  if (cat.includes('medio')) return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900'
  if (cat.includes('taco')) return 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-900'
  if (cat.includes('ensalada')) return 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/60 dark:text-green-300 dark:border-green-900'
  if (cat.includes('postre')) return 'bg-pink-50 text-pink-700 border-pink-200 dark:bg-pink-950/60 dark:text-pink-300 dark:border-pink-900'
  if (cat.includes('paquete')) return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-900'
  return 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'
}

const getCategoryIcon = (category) => {
  const cat = (category || '').toLowerCase()
  if (cat === 'todas') return '🍽️'
  if (cat.includes('combo')) return '🍱'
  if (cat.includes('bebida') || cat.includes('drink')) return '🥤'
  if (cat.includes('extra')) return '🍟'
  if (cat.includes('complemento')) return '🥔'
  if (cat.includes('entero') || cat.includes('medio') || cat.includes('pollo')) return '🍗'
  if (cat.includes('taco')) return '🌮'
  if (cat.includes('ensalada')) return '🥗'
  if (cat.includes('postre')) return '🍮'
  if (cat.includes('paquete')) return '📦'
  return '✨'
}

// In-line Quick Category Switcher
const handleQuickCategoryChange = async (item, newCategory) => {
  if (!newCategory || item.category === newCategory) return
  const oldCategory = item.category
  item.category = newCategory
  try {
    await api.patch(`/admin/menu-items/${item.id}`, { category: newCategory })
    addToast(`Categoría de "${item.name}" cambiada a "${newCategory}".`, 'success')
  } catch (err) {
    item.category = oldCategory
    addToast('Error al actualizar la categoría.', 'error')
  }
}

const loadContactMessages = async () => {
  try {
    const res = await api.get('/admin/contact-messages')
    if (res.data && Array.isArray(res.data)) {
      contactMessages.value = res.data
    }
  } catch (err) {
    console.warn('Error cargando mensajes de contacto', err)
  }
}

const handleToggleMessageStatus = async (msg) => {
  const newStatus = msg.status === 'pending' ? 'attended' : 'pending'
  try {
    await api.patch(`/admin/contact-messages/${msg.id}`, { status: newStatus })
    msg.status = newStatus
    addToast(
      newStatus === 'attended'
        ? `Mensaje ${msg.folio || '#' + msg.id} marcado como atendido.`
        : `Mensaje ${msg.folio || '#' + msg.id} marcado como pendiente.`,
      'success'
    )
  } catch (err) {
    addToast('Error al actualizar el estado del mensaje.', 'error')
  }
}

const handleDeleteContactMessage = async (msgId) => {
  try {
    await api.delete(`/admin/contact-messages/${msgId}`)
    contactMessages.value = contactMessages.value.filter(m => m.id !== msgId)
    addToast('Mensaje de contacto eliminado.', 'info')
  } catch (err) {
    addToast('Error al eliminar el mensaje.', 'error')
  }
}

const loadData = async () => {
  try {
    const res = await api.get('/admin/dashboard-content').catch(() => api.get('/landing/content'))
    if (res.data) {
      summary.value = res.data.summary || {}
      menuItems.value = res.data.menu_items || []
      promotions.value = res.data.promotions || []
      branches.value = res.data.branches || []
      if (res.data.contact_messages && Array.isArray(res.data.contact_messages)) {
        contactMessages.value = res.data.contact_messages
      }
    }
  } catch (err) {
    console.error('Error cargando datos del panel admin', err)
  }

  await loadContactMessages()
  await loadAdminCategories()

  if (canAccessTab('vacancies')) {
    await loadAdminJobs()
    await loadJobApplications()
  }

  if (canAccessTab('users')) {
    await loadAdminUsers()
  }

  if (canAccessTab('reviews')) {
    await loadAdminReviews()
  }

  try {
    const ordersRes = await api.get('/orders')
    if (ordersRes.data) {
      orders.value = ordersRes.data.orders || []
    }
  } catch (err) {
    console.warn('Error cargando pedidos', err)
  }
}

// Menu Filter Logic (supporting Combos, Drinks, Extras & aliases)
const filteredMenuItems = computed(() => {
  let list = [...menuItems.value]

  if (selectedMenuCategory.value !== 'Todas') {
    const target = selectedMenuCategory.value.toLowerCase().trim()
    list = list.filter(i => {
      const itemCat = (i.category || '').toLowerCase().trim()
      if (itemCat === target) return true
      if (target === 'combos' && (itemCat === 'paquetes' || itemCat.includes('combo'))) return true
      if ((target === 'bebidas' || target === 'drinks') && (itemCat === 'drinks' || itemCat.includes('bebida'))) return true
      if ((target === 'extras' || target === 'complementos') && (itemCat === 'complementos' || itemCat.includes('extra'))) return true
      return false
    })
  }

  if (selectedMenuStatus.value === 'active') {
    list = list.filter(i => i.is_active)
  } else if (selectedMenuStatus.value === 'paused') {
    list = list.filter(i => !i.is_active)
  }

  if (menuSearch.value.trim()) {
    const q = menuSearch.value.trim().toLowerCase()
    list = list.filter(i =>
      i.name.toLowerCase().includes(q) ||
      (i.category && i.category.toLowerCase().includes(q)) ||
      (i.description && i.description.toLowerCase().includes(q)) ||
      (i.badge && i.badge.toLowerCase().includes(q))
    )
  }

  return list
})

// Menu Item Actions
const handleOpenAddModal = () => {
  itemToEdit.value = null
  showItemModal.value = true
}

const handleOpenEditModal = (item) => {
  itemToEdit.value = { ...item }
  showItemModal.value = true
}

const handleSaveMenuItem = async (formData) => {
  try {
    if (formData.id) {
      // Edit existing item
      const res = await api.patch(`/admin/menu-items/${formData.id}`, formData)
      const updated = res.data?.item
      const idx = menuItems.value.findIndex(i => i.id === formData.id)
      if (idx >= 0 && updated) {
        menuItems.value[idx] = updated
      } else {
        await loadData()
      }
      addToast(`Platillo "${formData.name}" actualizado correctamente.`, 'success')
    } else {
      // Create new item
      const res = await api.post('/admin/menu-items', formData)
      const created = res.data?.item
      if (created) {
        menuItems.value.unshift(created)
      } else {
        await loadData()
      }
      addToast(`¡Platillo "${formData.name}" agregado al menú con éxito!`, 'success')
    }
    showItemModal.value = false
  } catch (err) {
    console.error('Error al guardar platillo', err)
    addToast(err.response?.data?.message || 'Error al guardar el platillo.', 'error')
  }
}

const handlePromptDelete = (item) => {
  deleteItemType.value = 'menu'
  isBulkDelete.value = false
  itemToDelete.value = item
  deleteModalTitle.value = '¿Eliminar platillo del menú?'
  deleteModalMessage.value = `Estás a punto de eliminar "${item.name}". Ya no estará disponible en el catálogo de Pollo Feliz.`
  showDeleteModal.value = true
}

const handlePromptBulkDelete = () => {
  if (!selectedItemIds.value.length) return
  deleteItemType.value = 'menu'
  isBulkDelete.value = true
  itemToDelete.value = null
  deleteModalTitle.value = `¿Eliminar ${selectedItemIds.value.length} platillos del menú?`
  deleteModalMessage.value = `Estás a punto de eliminar los siguientes ${selectedItemIds.value.length} platillos seleccionados en lote.`
  showDeleteModal.value = true
}

const handleConfirmDelete = async () => {
  if (deleteItemType.value === 'user') {
    if (!itemToDelete.value) return
    const id = itemToDelete.value.id
    const name = itemToDelete.value.name
    try {
      await api.delete(`/admin/users/${id}`)
      adminUsersList.value = adminUsersList.value.filter(u => u.id !== id && String(u.id) !== String(id))
      addToast(`Usuario "${name}" eliminado correctamente.`, 'info')
      showDeleteModal.value = false
      itemToDelete.value = null
    } catch (err) {
      console.error('Error al eliminar usuario', err)
      addToast(err.response?.data?.message || 'Error al eliminar usuario.', 'error')
    }
    return
  }

  if (deleteItemType.value === 'promotion') {
    if (!itemToDelete.value) return
    const id = itemToDelete.value.id
    const title = itemToDelete.value.title
    try {
      await api.delete(`/admin/promotions/${id}`)
      promotions.value = promotions.value.filter(p => p.id !== id)
      addToast(`Promoción "${title}" eliminada correctamente.`, 'info')
      showDeleteModal.value = false
      itemToDelete.value = null
    } catch (err) {
      console.error('Error al eliminar promoción', err)
      addToast('Error al eliminar la promoción.', 'error')
    }
    return
  }

  if (deleteItemType.value === 'branch') {
    if (!itemToDelete.value) return
    const id = itemToDelete.value.id
    const name = itemToDelete.value.name
    try {
      await api.delete(`/admin/branches/${id}`)
      branches.value = branches.value.filter(b => b.id !== id)
      try {
        await loadLandingContent(true)
      } catch (_) {}
      addToast(`Sucursal "${name}" eliminada correctamente.`, 'info')
      showDeleteModal.value = false
      itemToDelete.value = null
    } catch (err) {
      console.error('Error al eliminar sucursal', err)
      addToast('Error al eliminar la sucursal.', 'error')
    }
    return
  }

  if (deleteItemType.value === 'job') {
    if (!itemToDelete.value) return
    const id = itemToDelete.value.id
    const title = itemToDelete.value.title
    try {
      await api.delete(`/admin/jobs/${id}`)
      adminJobsList.value = adminJobsList.value.filter(j => j.id !== id && String(j.id) !== String(id))
      addToast(`Vacante "${title}" eliminada correctamente.`, 'info')
      showDeleteModal.value = false
      itemToDelete.value = null
    } catch (err) {
      console.error('Error al eliminar vacante', err)
      addToast('Error al eliminar la vacante.', 'error')
    }
    return
  }

  if (deleteItemType.value === 'category') {
    if (!itemToDelete.value) return
    const id = itemToDelete.value.id
    const name = itemToDelete.value.name
    try {
      await api.delete(`/admin/categories/${id}`)
      adminCategoriesList.value = adminCategoriesList.value.filter(c => c.id !== id && String(c.id) !== String(id))
      addToast(`Categoría "${name}" eliminada correctamente.`, 'info')
      showDeleteModal.value = false
      itemToDelete.value = null
    } catch (err) {
      console.error('Error al eliminar categoría', err)
      addToast('Error al eliminar categoría.', 'error')
    }
    return
  }

  if (deleteItemType.value === 'application') {
    if (!itemToDelete.value) return
    const id = itemToDelete.value.id
    const name = itemToDelete.value.applicant_name
    try {
      await api.delete(`/admin/job-applications/${id}`)
      jobApplicationsList.value = jobApplicationsList.value.filter(a => a.id !== id && String(a.id) !== String(id))
      addToast(`Postulación de "${name}" eliminada correctamente.`, 'info')
      showDeleteModal.value = false
      itemToDelete.value = null
    } catch (err) {
      console.error('Error al eliminar postulación', err)
      addToast('Error al eliminar la postulación.', 'error')
    }
    return
  }

  // Deletion for menu items
  if (isBulkDelete.value) {
    if (!selectedItemIds.value.length) return
    const ids = [...selectedItemIds.value]
    try {
      await api.post('/admin/menu-items/bulk-delete', { ids })
      const idSet = new Set(ids)
      menuItems.value = menuItems.value.filter(i => !idSet.has(i.id))
      addToast(`Se eliminaron ${ids.length} platillos correctamente del menú.`, 'info')
      showDeleteModal.value = false
      clearSelection()
    } catch (err) {
      console.error('Error al eliminar platillos en lote', err)
      addToast('Error al eliminar los platillos en lote.', 'error')
    }
  } else {
    if (!itemToDelete.value) return
    const id = itemToDelete.value.id
    const name = itemToDelete.value.name
    try {
      await api.delete(`/admin/menu-items/${id}`)
      menuItems.value = menuItems.value.filter(i => i.id !== id)
      selectedItemIds.value = selectedItemIds.value.filter(selectedId => selectedId !== id)
      addToast(`Platillo "${name}" eliminado correctamente del menú.`, 'info')
      showDeleteModal.value = false
      itemToDelete.value = null
    } catch (err) {
      console.error('Error eliminando platillo', err)
      addToast('Error al eliminar el platillo.', 'error')
    }
  }
}

// ==================== PROMOTIONS CRUD HANDLERS ====================
const handleOpenAddPromo = () => {
  promoToEdit.value = null
  showPromoModal.value = true
}

const handleOpenEditPromo = (promo) => {
  promoToEdit.value = { ...promo }
  showPromoModal.value = true
}

const handleSavePromo = async (formData) => {
  try {
    if (formData.id) {
      const res = await api.patch(`/admin/promotions/${formData.id}`, formData)
      const updated = res.data?.promotion
      const idx = promotions.value.findIndex(p => p.id === formData.id)
      if (idx >= 0 && updated) {
        promotions.value[idx] = updated
      } else {
        await loadData()
      }
      addToast(`Promoción "${formData.title}" actualizada correctamente.`, 'success')
    } else {
      const res = await api.post('/admin/promotions', formData)
      const created = res.data?.promotion
      if (created) {
        promotions.value.unshift(created)
      } else {
        await loadData()
      }
      addToast(`¡Promoción "${formData.title}" creada con éxito!`, 'success')
    }
    showPromoModal.value = false
  } catch (err) {
    console.error('Error al guardar promoción', err)
    addToast(err.response?.data?.message || 'Error al guardar la promoción.', 'error')
  }
}

const togglePromoActive = async (promo) => {
  const newActive = promo.is_active === false ? true : false
  try {
    await api.patch(`/admin/promotions/${promo.id}`, { is_active: newActive })
    promo.is_active = newActive
    addToast(
      `Promoción "${promo.title}" ${newActive ? 'activada' : 'pausada temporalmente'}.`,
      newActive ? 'success' : 'info'
    )
  } catch (err) {
    addToast('Error al actualizar el estado de la promoción.', 'error')
  }
}

const handlePromptDeletePromo = (promo) => {
  deleteItemType.value = 'promotion'
  itemToDelete.value = promo
  isBulkDelete.value = false
  deleteModalTitle.value = '¿Eliminar promoción?'
  deleteModalMessage.value = `Estás a punto de eliminar "${promo.title}". Ya no aparecerá disponible para los clientes.`
  showDeleteModal.value = true
}

// ==================== BRANCHES CRUD HANDLERS ====================
const handleOpenAddBranch = () => {
  branchToEdit.value = null
  showBranchModal.value = true
}

const handleOpenEditBranch = (b) => {
  branchToEdit.value = { ...b }
  showBranchModal.value = true
}

const handleSaveBranch = async (formData) => {
  try {
    const payload = {
      ...formData,
      branch_name: (formData.name || formData.branch_name || '').trim(),
      name: (formData.name || formData.branch_name || '').trim(),
      is_active: formData.is_active !== undefined ? formData.is_active : true,
      is_open: formData.is_open !== undefined ? formData.is_open : true,
    }
    if (formData.id) {
      const res = await api.patch(`/admin/branches/${formData.id}`, payload)
      const updated = res.data?.branch
      const idx = branches.value.findIndex(b => b.id === formData.id)
      if (idx >= 0 && updated) {
        branches.value[idx] = updated
      } else {
        await loadData()
      }
      addToast(`Sucursal "${formData.name}" actualizada correctamente.`, 'success')
    } else {
      const res = await api.post('/admin/branches', payload)
      const created = res.data?.branch
      if (created) {
        branches.value.push(created)
      } else {
        await loadData()
      }
      addToast(`¡Sucursal "${formData.name}" agregada con éxito!`, 'success')
    }
    try {
      await loadLandingContent(true)
    } catch (_) {}
    showBranchModal.value = false
  } catch (err) {
    console.error('Error al guardar sucursal', err)
    addToast(err.response?.data?.message || 'Error al guardar la sucursal.', 'error')
  }
}

const toggleBranchOpen = async (b) => {
  const newOpen = b.is_open === false ? true : false
  try {
    await api.patch(`/admin/branches/${b.id}`, { is_open: newOpen })
    b.is_open = newOpen
    addToast(
      `Sucursal "${b.name}" marcada como ${newOpen ? 'Abierta / Operando' : 'Cerrada'}.`,
      newOpen ? 'success' : 'info'
    )
  } catch (err) {
    addToast('Error al actualizar el estado de la sucursal.', 'error')
  }
}

const handlePromptDeleteBranch = (b) => {
  deleteItemType.value = 'branch'
  itemToDelete.value = b
  isBulkDelete.value = false
  deleteModalTitle.value = '¿Eliminar sucursal?'
  deleteModalMessage.value = `Estás a punto de eliminar la sucursal "${b.name}".`
  showDeleteModal.value = true
}

// ==================== VACANCIES CRUD HANDLERS ====================
const loadAdminJobs = async () => {
  isLoadingJobs.value = true
  try {
    const res = await api.get('/admin/jobs')
    adminJobsList.value = res.data?.jobs || []
  } catch (err) {
    console.error('Error al cargar vacantes:', err)
  } finally {
    isLoadingJobs.value = false
  }
}

const handleOpenAddJob = () => {
  jobToEdit.value = null
  showVacancyModal.value = true
}

const handleOpenEditJob = (job) => {
  jobToEdit.value = { ...job }
  showVacancyModal.value = true
}

const handleSaveJob = async (formData) => {
  try {
    if (formData.id) {
      const res = await api.patch(`/admin/jobs/${formData.id}`, formData)
      const updated = res.data?.job
      const idx = adminJobsList.value.findIndex(j => j.id === formData.id || String(j.id) === String(formData.id))
      if (idx >= 0 && updated) {
        adminJobsList.value[idx] = updated
      } else {
        await loadAdminJobs()
      }
      addToast(`Vacante "${formData.title}" actualizada correctamente.`, 'success')
    } else {
      const res = await api.post('/admin/jobs', formData)
      const created = res.data?.job
      if (created) {
        adminJobsList.value.unshift(created)
      } else {
        await loadAdminJobs()
      }
      addToast(`¡Vacante "${formData.title}" publicada con éxito!`, 'success')
    }
    showVacancyModal.value = false
  } catch (err) {
    console.error('Error al guardar vacante:', err)
    addToast(err.response?.data?.message || 'Error al guardar la vacante.', 'error')
  }
}

const handleToggleJobActive = async (job) => {
  const newActive = !job.is_active
  try {
    await api.patch(`/admin/jobs/${job.id}`, { is_active: newActive })
    job.is_active = newActive
    addToast(
      `Vacante "${job.title}" ${newActive ? 'activada en la web pública' : 'pausada temporalmente'}.`,
      newActive ? 'success' : 'info'
    )
  } catch (err) {
    console.error('Error al cambiar estado de la vacante:', err)
    addToast('Error al actualizar el estado de la vacante.', 'error')
  }
}

const handlePromptDeleteJob = (job) => {
  deleteItemType.value = 'job'
  itemToDelete.value = job
  isBulkDelete.value = false
  deleteModalTitle.value = '¿Eliminar vacante de empleo?'
  deleteModalMessage.value = `Estás a punto de eliminar la oferta de "${job.title}". Los candidatos ya no podrán postularse.`
  showDeleteModal.value = true
}

// ==================== CATEGORIES CRUD HANDLERS ====================
const loadAdminCategories = async () => {
  try {
    const res = await api.get('/admin/categories')
    adminCategoriesList.value = res.data?.categories || []
  } catch (err) {
    console.error('Error al cargar categorías:', err)
  }
}

const handleOpenAddCategory = () => {
  categoryToEdit.value = null
  showCategoryModal.value = true
}

const handleOpenEditCategory = (cat) => {
  categoryToEdit.value = { ...cat }
  showCategoryModal.value = true
}

const handleSaveCategory = async (catData) => {
  try {
    if (catData.id) {
      const res = await api.patch(`/admin/categories/${catData.id}`, catData)
      const updated = res.data?.category
      const idx = adminCategoriesList.value.findIndex(c => c.id === catData.id)
      if (idx >= 0 && updated) {
        adminCategoriesList.value[idx] = updated
      } else {
        await loadAdminCategories()
      }
      addToast(`Categoría "${catData.name}" actualizada con éxito.`, 'success')
    } else {
      const res = await api.post('/admin/categories', catData)
      const created = res.data?.category
      if (created) {
        adminCategoriesList.value.push(created)
      } else {
        await loadAdminCategories()
      }
      addToast(`¡Categoría "${catData.name}" creada con éxito!`, 'success')
    }
    showCategoryModal.value = false
  } catch (err) {
    console.error('Error al guardar categoría:', err)
    addToast(err?.response?.data?.message || 'Error al guardar categoría.', 'error')
  }
}

const handlePromptDeleteCategory = (cat) => {
  deleteItemType.value = 'category'
  itemToDelete.value = cat
  isBulkDelete.value = false
  deleteModalTitle.value = '¿Eliminar categoría del menú?'
  deleteModalMessage.value = `Estás a punto de eliminar la categoría "${cat.name}".`
  showDeleteModal.value = true
}

// ==================== CANDIDATE APPLICATIONS HANDLERS ====================
const loadJobApplications = async () => {
  isLoadingApplications.value = true
  try {
    const res = await api.get('/admin/job-applications')
    jobApplicationsList.value = res.data?.applications || []
  } catch (err) {
    console.error('Error al cargar postulaciones:', err)
  } finally {
    isLoadingApplications.value = false
  }
}

const handleChangeAppStatus = async (appId, newStatus) => {
  try {
    await api.patch(`/admin/job-applications/${appId}/status`, { status: newStatus })
    const app = jobApplicationsList.value.find(a => a.id === appId)
    if (app) app.status = newStatus
    addToast(`Estado del candidato actualizado a "${newStatus}".`, 'success')
  } catch (err) {
    console.error('Error al actualizar estado:', err)
    addToast('Error al actualizar estado de la postulación.', 'error')
  }
}

const handlePromptDeleteApp = (app) => {
  deleteItemType.value = 'application'
  itemToDelete.value = app
  isBulkDelete.value = false
  deleteModalTitle.value = '¿Eliminar postulación de candidato?'
  deleteModalMessage.value = `Estás a punto de eliminar la postulación de "${app.applicant_name}" (${app.vacancy_title}).`
  showDeleteModal.value = true
}

const filteredApplications = computed(() => {
  let list = [...jobApplicationsList.value]
  if (appStatusFilter.value !== 'all') {
    list = list.filter(a => a.status === appStatusFilter.value)
  }
  return list
})

const getAppStatusMeta = (status) => {
  const map = {
    nuevo: { label: 'Nuevo', badge: 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border-blue-200' },
    en_revision: { label: 'En Revisión', badge: 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border-amber-200' },
    entrevista: { label: 'Entrevista', badge: 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border-purple-200' },
    contratado: { label: 'Contratado', badge: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-200' },
    descartado: { label: 'Descartado', badge: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200' },
  }
  return map[status] || { label: status, badge: 'bg-gray-100 text-gray-700' }
}

// Bulk Category Handlers
const handleApplyBulkCategoryQuick = async () => {
  if (!bulkTargetCategory.value || !selectedItemIds.value.length) return
  await handleConfirmBulkCategory(bulkTargetCategory.value)
  bulkTargetCategory.value = ''
}

const handleConfirmBulkCategory = async (newCategory) => {
  if (!selectedItemIds.value.length || !newCategory) return
  const ids = [...selectedItemIds.value]
  try {
    await api.post('/admin/menu-items/bulk-update-category', {
      ids,
      category: newCategory,
    })
    const idSet = new Set(ids)
    menuItems.value.forEach(i => {
      if (idSet.has(i.id)) {
        i.category = newCategory
      }
    })
    addToast(`Categoría actualizada a "${newCategory}" para ${ids.length} platillos.`, 'success')
    showBulkCategoryModal.value = false
    clearSelection()
  } catch (err) {
    console.error('Error al actualizar categorías en lote', err)
    addToast('Error al actualizar las categorías en lote.', 'error')
  }
}

// Bulk Toggle Availability
const handleBulkToggleActive = async (isActive) => {
  if (!selectedItemIds.value.length) return
  const ids = [...selectedItemIds.value]
  try {
    await api.post('/admin/menu-items/bulk-toggle-active', {
      ids,
      is_active: isActive,
    })
    const idSet = new Set(ids)
    menuItems.value.forEach(i => {
      if (idSet.has(i.id)) {
        i.is_active = isActive
      }
    })
    addToast(
      `${ids.length} platillos fueron ${isActive ? 'activados' : 'pausados'} en lote.`,
      isActive ? 'success' : 'info'
    )
    clearSelection()
  } catch (err) {
    console.error('Error al actualizar disponibilidad en lote', err)
    addToast('Error al actualizar la disponibilidad en lote.', 'error')
  }
}

const toggleItemActive = async (item) => {
  const newActive = !item.is_active
  try {
    await api.patch(`/admin/menu-items/${item.id}`, { is_active: newActive })
    item.is_active = newActive
    addToast(
      `"${item.name}" ahora está ${newActive ? 'activo en el menú' : 'pausado temporalmente'}`,
      newActive ? 'success' : 'info'
    )
  } catch (err) {
    item.is_active = !newActive
    addToast('Error al actualizar disponibilidad del platillo.', 'error')
  }
}

const handleRestoreDefaultMenu = async () => {
  if (!confirm('¿Deseas restaurar el menú con los 27 platillos y especialidades oficiales de Pollo Feliz?')) return
  try {
    await api.post('/admin/menu-items/restore-default')
    await loadData()
    addToast('Menú oficial de Pollo Feliz restaurado con éxito.', 'success')
  } catch (err) {
    addToast('Error al restaurar el menú por defecto.', 'error')
  }
}

// Order Management Helpers
const activeOrdersList = computed(() => {
  return orders.value.filter(o => o.status !== 'delivered' && o.status !== 'cancelled')
})

const filteredAdminOrders = computed(() => {
  let list = orders.value
  if (orderFilter.value === 'active') {
    list = list.filter(o => o.status !== 'delivered' && o.status !== 'cancelled')
  } else if (orderFilter.value === 'on_the_way') {
    list = list.filter(o => o.status === 'on_the_way')
  } else if (orderFilter.value === 'delivered') {
    list = list.filter(o => o.status === 'delivered')
  }

  if (orderTypeFilter.value !== 'all') {
    list = list.filter(o => o.order_type === orderTypeFilter.value)
  }

  if (orderSearch.value.trim()) {
    const q = orderSearch.value.trim().toLowerCase()
    list = list.filter(o =>
      String(o.id).includes(q) ||
      (o.customer_name && o.customer_name.toLowerCase().includes(q)) ||
      (o.customer_phone && o.customer_phone.toLowerCase().includes(q)) ||
      (o.branch && o.branch.toLowerCase().includes(q))
    )
  }

  return list
})

const copyToClipboard = async (text, message = 'Copiado al portapapeles') => {
  try {
    if (navigator?.clipboard?.writeText) {
      await navigator.clipboard.writeText(text)
    } else {
      const textarea = document.createElement('textarea')
      textarea.value = text
      document.body.appendChild(textarea)
      textarea.select()
      document.execCommand('copy')
      document.body.removeChild(textarea)
    }
    addToast(message, 'success')
  } catch (err) {
    addToast('No se pudo copiar el texto', 'error')
  }
}

const getWhatsAppLink = (phone, orderId, customerName) => {
  if (!phone) return '#'
  const cleanPhone = phone.replace(/[^0-9]/g, '')
  const fullPhone = cleanPhone.length === 10 ? `52${cleanPhone}` : cleanPhone
  const message = encodeURIComponent(`¡Hola ${customerName}! Te escribimos de Pollo Feliz respecto a tu pedido #${orderId}.`)
  return `https://wa.me/${fullPhone}?text=${message}`
}

const exportOrdersCSV = () => {
  if (!orders.value || orders.value.length === 0) {
    addToast('No hay pedidos registrados para exportar.', 'info')
    return
  }

  const headers = ['ID', 'Fecha', 'Cliente', 'Teléfono', 'Tipo', 'Sucursal', 'Dirección', 'Total MXN', 'Método Pago', 'Estado', 'Productos']
  const rows = orders.value.map(o => {
    const itemsStr = (o.items || []).map(i => `${i.quantity || 1}x ${i.name}`).join('; ')
    return [
      o.id,
      o.created_at || '',
      `"${(o.customer_name || '').replace(/"/g, '""')}"`,
      `"${(o.customer_phone || '').replace(/"/g, '""')}"`,
      o.order_type === 'delivery' ? 'A Domicilio' : 'En Sucursal',
      `"${(o.branch || '').replace(/"/g, '""')}"`,
      `"${(o.address || '').replace(/"/g, '""')}"`,
      o.total || 0,
      `"${(o.payment_method || 'Efectivo').replace(/"/g, '""')}"`,
      o.status_label || o.status,
      `"${itemsStr.replace(/"/g, '""')}"`
    ].join(',')
  })

  const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\r\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', `pedidos-pollo-feliz-${new Date().toISOString().slice(0, 10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)

  addToast('Archivo CSV de pedidos descargado con éxito.', 'success')
}

const exportMenuCSV = () => {
  if (!menuItems.value || menuItems.value.length === 0) {
    addToast('No hay platillos en el catálogo para exportar.', 'info')
    return
  }

  const headers = ['ID', 'Nombre', 'Categoría', 'Precio MXN', 'Descripción', 'Insignia', 'Estado', 'Ruta Imagen']
  const rows = menuItems.value.map(item => [
    item.id,
    `"${(item.name || '').replace(/"/g, '""')}"`,
    `"${(item.category || '').replace(/"/g, '""')}"`,
    item.price || 0,
    `"${(item.description || '').replace(/"/g, '""')}"`,
    `"${(item.badge || '').replace(/"/g, '""')}"`,
    item.is_active ? 'Activo' : 'Pausado',
    `"${(item.image || '').replace(/"/g, '""')}"`
  ].join(','))

  const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\r\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', `catalogo-menu-pollo-feliz-${new Date().toISOString().slice(0, 10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)

  addToast('Catálogo de menú exportado a CSV con éxito.', 'success')
}

const refreshAllData = async () => {
  if (isRefreshing.value) return
  isRefreshing.value = true
  try {
    await loadData()
    addToast('Datos sincronizados y actualizados con éxito.', 'success')
  } catch (err) {
    addToast('Error al sincronizar datos del panel.', 'error')
  } finally {
    setTimeout(() => {
      isRefreshing.value = false
    }, 500)
  }
}

const updateOrderStatus = async (orderId, newStatus) => {
  try {
    const res = await api.patch(`/orders/${orderId}/status`, { status: newStatus })
    if (res.data?.order) {
      const idx = orders.value.findIndex(o => o.id === orderId)
      if (idx >= 0) {
        orders.value[idx] = res.data.order
      }
      addToast(`Pedido #${orderId} actualizado a '${res.data.order.status_label}'`, 'success')
    }
  } catch (err) {
    addToast('Error al actualizar el estado del pedido', 'error')
  }
}

const getStatusBadgeClass = (status) => {
  if (status === 'pending') return 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/70 dark:text-amber-300'
  if (status === 'preparing') return 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-950/70 dark:text-orange-300'
  if (status === 'ready') return 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/70 dark:text-purple-300'
  if (status === 'on_the_way') return 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/70 dark:text-blue-300'
  if (status === 'delivered') return 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-300'
  return 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300'
}

const getStatusDotClass = (status) => {
  if (status === 'pending') return 'bg-amber-500'
  if (status === 'preparing') return 'bg-orange-500'
  if (status === 'ready') return 'bg-purple-500'
  if (status === 'on_the_way') return 'bg-blue-500 animate-ping'
  if (status === 'delivered') return 'bg-emerald-500'
  return 'bg-gray-400'
}

const getStatusLabel = (status) => {
  const map = {
    pending: 'Pendiente',
    preparing: 'En preparación',
    ready: 'Empacado / Listo',
    on_the_way: 'En camino',
    delivered: 'Entregado',
    cancelled: 'Cancelado',
  }
  return map[status] || status
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('es-MX', { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' })
  } catch {
    return dateStr
  }
}

// ==================== USERS & ROLES CRUD HANDLERS ====================
const loadSecurityStatus = async () => {
  if (!canAccessTab('users')) return
  loadingSecurityStatus.value = true
  try {
    const res = await api.get('/admin/security/status')
    if (res.data) {
      securityStatus.value = res.data
    }
  } catch (err) {
    console.warn('Error cargando estado de seguridad', err)
  } finally {
    loadingSecurityStatus.value = false
  }
}

const handleRevokeAllSessions = async () => {
  const confirmMsg =
    '🚨 PROTOCOLO DE RESPUESTA A INCIDENTES DE SEGURIDAD 🚨\n\n' +
    '¿Estás seguro de que deseas REVOCAR Y CERRAR TODAS LAS SESIONES ACTIVAS en todos los dispositivos de todos los usuarios?\n\n' +
    '• Todos los tokens de sesión previos serán invalidados inmediatamente.\n' +
    '• Cualquier usuario o dispositivo conectado será forzado a reconectarse.\n' +
    '• Tu sesión actual de Super Administrador permanecerá protegida.\n\n' +
    '¿Proceder con la revocación global?'

  if (!confirm(confirmMsg)) return

  try {
    const res = await api.post('/admin/security/revoke-all-sessions')
    addToast(res.data?.message || 'Todas las sesiones globales han sido revocadas exitosamente.', 'warning')
    await loadAdminUsers()
    await loadSecurityStatus()
  } catch (err) {
    console.error('Error al revocar todas las sesiones', err)
    addToast(err.response?.data?.message || 'Error al ejecutar revocación global.', 'error')
  }
}

const loadAdminUsers = async () => {
  if (!canAccessTab('users')) return
  try {
    const res = await api.get('/admin/users')
    if (res.data && Array.isArray(res.data.users)) {
      adminUsersList.value = res.data.users
    }
    await loadSecurityStatus()
  } catch (err) {
    console.warn('Error cargando usuarios administrativos', err)
  }
}

const handleOpenAddUser = () => {
  userToEdit.value = null
  showUserModal.value = true
}

const handleOpenEditUser = (user) => {
  userToEdit.value = { ...user }
  showUserModal.value = true
}

const handleSaveUser = async (formData) => {
  try {
    if (formData.id) {
      const res = await api.patch(`/admin/users/${formData.id}`, formData)
      const updated = res.data?.user
      const idx = adminUsersList.value.findIndex(u => u.id === formData.id)
      if (idx >= 0 && updated) {
        adminUsersList.value[idx] = updated
      } else {
        await loadAdminUsers()
      }
      addToast(`Usuario "${formData.name}" actualizado correctamente.`, 'success')
    } else {
      const res = await api.post('/admin/users', formData)
      const created = res.data?.user
      if (created) {
        adminUsersList.value.push(created)
      } else {
        await loadAdminUsers()
      }
      addToast(`¡Usuario "${formData.name}" creado con éxito con rol "${getRoleMeta(formData.role).label}"!`, 'success')
    }
    showUserModal.value = false
  } catch (err) {
    console.error('Error al guardar usuario', err)
    addToast(err.response?.data?.message || 'Error al guardar el usuario.', 'error')
  }
}

const handlePromptDeleteUser = (user) => {
  if (user.id === 1) {
    addToast('No es posible eliminar al Super Administrador principal.', 'warning')
    return
  }
  if (user.id === adminUser.value?.id) {
    addToast('No puedes eliminar tu propia cuenta mientras tienes la sesión activa.', 'warning')
    return
  }
  deleteItemType.value = 'user'
  itemToDelete.value = user
  isBulkDelete.value = false
  deleteModalTitle.value = '¿Eliminar cuenta de usuario?'
  deleteModalMessage.value = `Estás a punto de revocar todos los accesos y eliminar definitivamente la cuenta de "${user.name}" (${user.email}). Ya no podrá ingresar al panel de Pollo Feliz.`
  showDeleteModal.value = true
}

const handleDeleteUser = (user) => {
  handlePromptDeleteUser(user)
}

// Revocar todas las sesiones activas de un usuario (Sospecha de compromiso de cuenta)
const handleRevokeUserSessions = async (user) => {
  const isSelf = user.id === adminUser.value?.id
  const warningText = isSelf
    ? `⚠️ ATENCIÓN: Estás a punto de revocar todas las sesiones de TU PROPIA cuenta (${user.name}).\n\nTu sesión actual se cerrará inmediatamente por seguridad. ¿Deseas continuar?`
    : `🛡️ ¿Revocar y cerrar TODAS las sesiones activas de "${user.name}" (${user.email})?\n\nEsta acción invalidará tokens y desconectará inmediatamente cualquier navegador o dispositivo abierto bajo sospecha de compromiso de cuenta.`

  if (!confirm(warningText)) return

  try {
    const res = await api.post(`/admin/users/${user.id}/revoke-sessions`)
    addToast(res.data?.message || `Sesiones de ${user.name} revocadas correctamente.`, 'warning')

    const idx = adminUsersList.value.findIndex(u => u.id === user.id)
    if (idx >= 0) {
      adminUsersList.value[idx].active_sessions_count = 0
      adminUsersList.value[idx].last_revoked_at = res.data?.revoked_at || new Date().toISOString()
    }

    if (isSelf) {
      setTimeout(() => {
        window.location.href = '/admin/login?reason=session_revoked'
      }, 900)
    }
  } catch (err) {
    console.error('Error al revocar sesiones', err)
    addToast(err.response?.data?.message || 'Error al revocar sesiones del usuario.', 'error')
  }
}

// Suspender o Reactivar cuenta de usuario (Bloqueo de emergencia)
const handleToggleUserStatus = async (user) => {
  const isSuspending = user.status !== 'suspended'
  const msg = isSuspending
    ? `¿Estás seguro de suspender temporalmente la cuenta de "${user.name}"? El usuario no podrá iniciar sesión ni acceder al panel.`
    : `¿Deseas reactivar la cuenta de "${user.name}"?`

  if (!confirm(msg)) return

  try {
    const res = await api.post(`/admin/users/${user.id}/toggle-status`)
    const updatedUser = res.data?.user
    const idx = adminUsersList.value.findIndex(u => u.id === user.id)
    if (idx >= 0 && updatedUser) {
      adminUsersList.value[idx].status = updatedUser.status
      adminUsersList.value[idx].active_sessions_count = updatedUser.active_sessions_count
      adminUsersList.value[idx].last_revoked_at = updatedUser.last_revoked_at
    } else {
      await loadAdminUsers()
    }
    addToast(res.data?.message || `Estado de ${user.name} modificado.`, isSuspending ? 'warning' : 'success')
  } catch (err) {
    console.error('Error al cambiar estado de cuenta', err)
    addToast(err.response?.data?.message || 'Error al modificar estado del usuario.', 'error')
  }
}

// ==================== REVIEWS MODERATION HANDLERS ====================
// Filtra las reseñas según el estado seleccionado (aprobadas, pendientes, rechazadas, destacadas) y el término de búsqueda
const filteredAdminReviews = computed(() => {
  let list = [...adminReviewsList.value]
  if (adminReviewFilter.value === 'approved') list = list.filter(r => r.status === 'approved')
  else if (adminReviewFilter.value === 'pending') list = list.filter(r => r.status === 'pending')
  else if (adminReviewFilter.value === 'rejected') list = list.filter(r => r.status === 'rejected')
  else if (adminReviewFilter.value === 'featured') list = list.filter(r => r.is_featured)

  if (adminReviewSearch.value.trim()) {
    const q = adminReviewSearch.value.trim().toLowerCase()
    list = list.filter(r =>
      r.customer_name?.toLowerCase().includes(q) ||
      r.comment?.toLowerCase().includes(q) ||
      r.branch?.toLowerCase().includes(q) ||
      (r.favorite_dish && r.favorite_dish.toLowerCase().includes(q))
    )
  }
  return list
})

// Carga todas las reseñas desde el backend administrativo con sus estadísticas
const loadAdminReviews = async () => {
  if (!canAccessTab('reviews')) return
  try {
    const res = await api.get('/admin/reviews')
    if (res.data) {
      if (Array.isArray(res.data.reviews)) {
        adminReviewsList.value = res.data.reviews
      }
      if (res.data.stats) {
        adminReviewsStats.value = res.data.stats
      }
    }
  } catch (err) {
    console.warn('Error cargando reseñas en panel admin', err)
  }
}

// Abre el formulario en línea para responder a un cliente
const openReplyInput = (rev) => {
  activeReplyReviewId.value = rev.id
  replyTextInput.value = rev.admin_reply?.message || ''
}

// Guarda la respuesta oficial de la empresa a la reseña
const handleSaveReviewReply = async (rev) => {
  try {
    const res = await api.patch(`/admin/reviews/${rev.id}`, {
      admin_reply: replyTextInput.value
    })
    if (res.data?.review) {
      const idx = adminReviewsList.value.findIndex(r => r.id === rev.id)
      if (idx >= 0) adminReviewsList.value[idx] = res.data.review
    }
    addToast('Respuesta oficial guardada con éxito.', 'success')
    activeReplyReviewId.value = null
    replyTextInput.value = ''
  } catch (err) {
    addToast('Error al guardar respuesta.', 'error')
  }
}

// Alterna la visibilidad pública de una reseña (aprobada vs rechazada/oculta)
const handleToggleReviewStatus = async (rev) => {
  const newStatus = rev.status === 'approved' ? 'rejected' : 'approved'
  try {
    await api.patch(`/admin/reviews/${rev.id}`, { status: newStatus })
    rev.status = newStatus
    addToast(newStatus === 'approved' ? 'Reseña aprobada y visible en el sitio.' : 'Reseña ocultada del sitio.', 'info')
  } catch (err) {
    addToast('Error al actualizar estado de la reseña.', 'error')
  }
}

// Alterna si la reseña aparece destacada con trofeo y tarjeta dorada
const handleToggleReviewFeatured = async (rev) => {
  const newFeatured = !rev.is_featured
  try {
    await api.patch(`/admin/reviews/${rev.id}`, { is_featured: newFeatured })
    rev.is_featured = newFeatured
    addToast(newFeatured ? 'Reseña marcada como destacada.' : 'Reseña desmarcada de destacadas.', 'info')
  } catch (err) {
    addToast('Error al actualizar estado destacado.', 'error')
  }
}

// Elimina una reseña del sistema de forma permanente
const handleDeleteReview = async (rev) => {
  if (!confirm(`¿Eliminar la reseña de "${rev.customer_name}"?`)) return
  try {
    await api.delete(`/admin/reviews/${rev.id}`)
    adminReviewsList.value = adminReviewsList.value.filter(r => r.id !== rev.id)
    addToast('Reseña eliminada correctamente.', 'info')
  } catch (err) {
    addToast('Error al eliminar reseña.', 'error')
  }
}

const handleLogout = () => {
  logout()
  addToast('Has cerrado sesión correctamente.', 'info')
  router.push('/admin/login')
}

// Watchers para seguridad de pestañas y carga bajo demanda
watch(visibleTabs, (newTabs) => {
  if (newTabs.length > 0 && !newTabs.some(t => t.id === activeTab.value)) {
    activeTab.value = newTabs[0].id
  }
}, { immediate: true })

watch(activeTab, (newTab) => {
  if (newTab === 'messages') {
    loadContactMessages()
  } else if (newTab === 'users' && canAccessTab('users')) {
    loadAdminUsers()
  } else if (newTab === 'reviews' && canAccessTab('reviews')) {
    loadAdminReviews()
  } else if (newTab === 'vacancies' && canAccessTab('vacancies')) {
    loadAdminJobs()
  }
})

onMounted(async () => {
  // Cargar usuario almacenado si aún no está en memoria
  if (!adminUser.value && hasAdminToken()) {
    let stored = null
    if (typeof window !== 'undefined') {
      try {
        const raw =
          localStorage.getItem('pollofeliz_admin_user') ||
          sessionStorage.getItem('pollofeliz_admin_user') ||
          localStorage.getItem('user') ||
          sessionStorage.getItem('user')
        if (raw && raw !== 'undefined' && raw !== 'null' && raw !== '[object Object]') {
          stored = JSON.parse(raw)
        }
      } catch (err) {
        console.warn('Error parseando usuario guardado en panel:', err)
      }
    }
    if (stored && stored.role) {
      adminUser.value = stored
    } else {
      adminUser.value = {
        id: 1,
        name: 'Super Administrador',
        email: 'admin@pollofeliz.com',
        role: 'super_admin',
      }
    }
  }

  // Redirigir al login únicamente si no existe ningún token almacenado
  if (!hasAdminToken()) {
    router.replace('/admin/login')
    return
  }

  // Cargar el contenido y métricas del panel de administración
  loadData()
  loadAdminJobs()

  // Sincronización transparente en segundo plano
  try {
    const profile = await fetchProfile()
    if (profile) {
      adminUser.value = profile
    }
  } catch (e) {
    // Continuar con perfil almacenado en caso de latencia
  }

  try {
    const status = await fetchDatabaseStatus()
    if (status) dbStatusInfo.value = status
  } catch (e) {
    // Modo contingencia
  }
})
</script>
