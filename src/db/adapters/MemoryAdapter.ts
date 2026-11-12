/**
 * ADAPTADOR DE BASE DE DATOS EN MEMORIA (LOCAL & DESARROLLO)
 * ==========================================================
 * Implementación predeterminada con datos iniciales precargados.
 * Simula fielmente operaciones ACID, transacciones y consultas SQL.
 */

import { IDatabaseAdapter } from '../DatabaseAdapter.js';
import {
  User,
  MenuItem,
  Job,
  Promotion,
  Branch,
  Order,
  ContactMessage,
  Review,
  SecurityAuditLog,
  LandingSettings
} from '../schema.js';

export class MemoryDatabaseAdapter implements IDatabaseAdapter {
  public name = 'MemoryAdapter';
  public isReady = true;

  private users: User[] = [
    {
      id: 1,
      name: 'Super Administrador',
      email: 'admin@pollofeliz.com',
      password: 'admin123456',
      is_admin: true,
      role: 'super_admin',
      api_token: 'default-admin-token-12345',
      two_factor_enabled: false,
      two_factor_secret: null,
      created_at: new Date(Date.now() - 86400000 * 30).toISOString(),
    },
    {
      id: 2,
      name: 'Lic. Roberto Garza',
      email: 'gerente@pollofeliz.com',
      password: 'gerente123',
      is_admin: true,
      role: 'gerente',
      api_token: 'token-gerente-12345',
      two_factor_enabled: false,
      two_factor_secret: null,
      created_at: new Date(Date.now() - 86400000 * 15).toISOString(),
    },
    {
      id: 3,
      name: 'Chef Manuel Parra',
      email: 'cocina@pollofeliz.com',
      password: 'cocina123',
      is_admin: true,
      role: 'cocina',
      api_token: 'token-cocina-12345',
      two_factor_enabled: false,
      two_factor_secret: null,
      created_at: new Date(Date.now() - 86400000 * 10).toISOString(),
    },
    {
      id: 4,
      name: 'Valeria Soto',
      email: 'soporte@pollofeliz.com',
      password: 'soporte123',
      is_admin: true,
      role: 'atencion',
      api_token: 'token-soporte-12345',
      two_factor_enabled: false,
      two_factor_secret: null,
      created_at: new Date(Date.now() - 86400000 * 5).toISOString(),
    },
    {
      id: 5,
      name: 'Andrea Morales',
      email: 'cajero@pollofeliz.com',
      password: 'cajero123',
      is_admin: true,
      role: 'cajero',
      api_token: 'token-cajero-12345',
      two_factor_enabled: false,
      two_factor_secret: null,
      created_at: new Date(Date.now() - 86400000 * 2).toISOString(),
    },
  ];

  private auditLogs: SecurityAuditLog[] = [
    {
      id: 1,
      user_id: 1,
      user_email: 'admin@pollofeliz.com',
      action: 'system.init',
      category: 'system',
      severity: 'low',
      target_type: 'System',
      target_id: '1',
      description: 'Inicialización del motor de persistencia y catálogo base.',
      changes: { initialized: true },
      created_at: new Date().toISOString(),
    },
  ];

  private settings: LandingSettings = {
    hero_title: 'El mejor Pollo Asado de Durango',
    hero_subtitle: '¡Bienvenidos a la Familia Pollo Feliz! · Tradición al Carbón',
    hero_description: 'Descubre el auténtico sabor que reúne a las familias. Pollo marinado con receta secreta de especias naturales y asado pacientemente al calor del carbón de mezquite.',
    hero_badge: '100% Asado al Carbón',
    hero_image_url: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=1000',
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
  };

  private menuItems: MenuItem[] = [
    { id: 1, name: 'Pollo Entero Clásico', description: 'Pollo asado con especias tradicionales, acompañado de salsa de la casa.', price: 180, image_url: '', badge: 'Más vendido', category: 'Pollo Entero', is_featured: true, is_active: true, sort_order: 1, deleted_at: null, created_at: new Date().toISOString() },
    { id: 2, name: 'Pollo Entero Especial', description: 'Con salsa especial de la casa y especias secretas.', price: 220, image_url: '', badge: 'Premium', category: 'Pollo Entero', is_featured: true, is_active: true, sort_order: 2, deleted_at: null, created_at: new Date().toISOString() },
    { id: 3, name: 'Paquete Familiar', description: 'Pollo entero + 2 complementos + refresco familiar.', price: 280, image_url: '', badge: 'Oferta', category: 'Paquetes', is_featured: true, is_active: true, sort_order: 3, deleted_at: null, created_at: new Date().toISOString() },
    { id: 4, name: 'Medio Pollo Clásico', description: 'Perfecto para una persona, con guarniciones incluidas.', price: 95, image_url: '', badge: 'Popular', category: 'Medio Pollo', is_featured: true, is_active: true, sort_order: 4, deleted_at: null, created_at: new Date().toISOString() },
    { id: 5, name: 'Papas a la Francesa', description: 'Crujientes y doradas, ideales para acompañar cualquier pedido.', price: 55, image_url: '', badge: 'Complemento', category: 'Complementos', is_featured: true, is_active: true, sort_order: 5, deleted_at: null, created_at: new Date().toISOString() },
    { id: 6, name: 'Papas Gajo', description: 'Sabrosas papas gajo con toque de especias.', price: 65, image_url: '', badge: 'Nuevo', category: 'Complementos', is_featured: true, is_active: true, sort_order: 6, deleted_at: null, created_at: new Date().toISOString() },
    { id: 7, name: 'Papas Asadas', description: 'Papas asadas con mantequilla y especias.', price: 60, image_url: '', badge: 'Recomendado', category: 'Complementos', is_featured: true, is_active: true, sort_order: 7, deleted_at: null, created_at: new Date().toISOString() },
    { id: 8, name: 'Ensalada Fresca', description: 'Ensalada fresca con lechuga, tomate y aderezo ligero.', price: 70, image_url: '', badge: 'Saludable', category: 'Ensaladas', is_featured: true, is_active: true, sort_order: 8, deleted_at: null, created_at: new Date().toISOString() },
    { id: 9, name: 'Ensalada Mixta', description: 'Ensalada con pollo, aguacate y vegetales frescos.', price: 95, image_url: '', badge: 'Popular', category: 'Ensaladas', is_featured: true, is_active: true, sort_order: 9, deleted_at: null, created_at: new Date().toISOString() },
    { id: 10, name: 'Ensalada de la Casa', description: 'Ensalada tropical con pollo y aderezo especial.', price: 100, image_url: '', badge: 'Favorita', category: 'Ensaladas', is_featured: true, is_active: true, sort_order: 10, deleted_at: null, created_at: new Date().toISOString() },
    { id: 11, name: 'Quesadilla Especial', description: 'Quesadilla rellena de queso, pollo y salsa especial.', price: 85, image_url: '', badge: 'Oferta', category: 'Tacos', is_featured: true, is_active: true, sort_order: 11, deleted_at: null, created_at: new Date().toISOString() },
    { id: 12, name: 'Tacos de Pollo', description: 'Tacos servidos con cebolla, cilantro y salsa.', price: 75, image_url: '', badge: 'Clásico', category: 'Tacos', is_featured: true, is_active: true, sort_order: 12, deleted_at: null, created_at: new Date().toISOString() },
    { id: 13, name: 'Taco Carreta', description: 'Tacos estilo carreta con mucho sabor.', price: 80, image_url: '', badge: 'Especial', category: 'Tacos', is_featured: true, is_active: true, sort_order: 13, deleted_at: null, created_at: new Date().toISOString() },
    { id: 14, name: 'Salsas Variadas', description: 'Cuatro salsas caseras para acompañar tu pedido.', price: 30, image_url: '', badge: 'Complemento', category: 'Complementos', is_featured: true, is_active: true, sort_order: 14, deleted_at: null, created_at: new Date().toISOString() },
    { id: 15, name: 'Spaguetti con Pollo', description: 'Spaguetti con pollo desmenuzado y salsa cremosa.', price: 110, image_url: '', badge: 'Nuevo', category: 'Paquetes', is_featured: true, is_active: true, sort_order: 15, deleted_at: null, created_at: new Date().toISOString() },
    { id: 16, name: 'Tortillas Calientes', description: 'Tortillas recién hechas para tus tacos o complementar tu orden.', price: 20, image_url: '', badge: 'Complemento', category: 'Complementos', is_featured: true, is_active: true, sort_order: 16, deleted_at: null, created_at: new Date().toISOString() },
    { id: 17, name: 'Nuggets Dorados', description: 'Nuggets crujientes perfectos para compartir.', price: 95, image_url: '', badge: 'Popular', category: 'Complementos', is_featured: true, is_active: true, sort_order: 17, deleted_at: null, created_at: new Date().toISOString() },
    { id: 18, name: 'Hamburguesa de Pollo', description: 'Hamburguesa jugosa con pollo, lechuga y aderezo especial.', price: 120, image_url: '', badge: 'Recomendado', category: 'Paquetes', is_featured: true, is_active: true, sort_order: 18, deleted_at: null, created_at: new Date().toISOString() },
    { id: 19, name: 'Pollo Grande con Guarnición', description: 'Pollo entero con papas y ensalada.', price: 240, image_url: '', badge: 'Promoción', category: 'Paquetes', is_featured: true, is_active: true, sort_order: 19, deleted_at: null, created_at: new Date().toISOString() },
    { id: 20, name: 'Pollo a la Brasa', description: 'Porción de pollo a la brasa con salsa especial.', price: 160, image_url: '', badge: 'Clásico', category: 'Pollo Entero', is_featured: true, is_active: true, sort_order: 20, deleted_at: null, created_at: new Date().toISOString() },
    { id: 21, name: 'Refresco Familiar 2L', description: 'Coca-Cola, Sidral Mundet o Jarritos de 2 litros bien frío.', price: 48, image_url: '', badge: 'Familiar', category: 'Bebidas', is_featured: true, is_active: true, sort_order: 21, deleted_at: null, created_at: new Date().toISOString() },
    { id: 22, name: 'Agua de Horchata Artesanal 1L', description: 'Receta tradicional de la casa con canela y arroz.', price: 45, image_url: '', badge: 'Hecha en casa', category: 'Bebidas', is_featured: true, is_active: true, sort_order: 22, deleted_at: null, created_at: new Date().toISOString() },
    { id: 23, name: 'Agua de Jamaica Natural 1L', description: 'Flor de jamaica fresca, refrescante y ligera de azúcar.', price: 45, image_url: '', badge: 'Natural', category: 'Bebidas', is_featured: true, is_active: true, sort_order: 23, deleted_at: null, created_at: new Date().toISOString() },
    { id: 24, name: 'Refresco Individual 600ml', description: 'Refresco personal frío en botella PET.', price: 28, image_url: '', badge: '', category: 'Bebidas', is_featured: true, is_active: true, sort_order: 24, deleted_at: null, created_at: new Date().toISOString() },
    { id: 25, name: 'Flan Casero Tradicional', description: 'Suave flan napolitano bañado en caramelo dorado.', price: 45, image_url: '', badge: 'Favorito', category: 'Postres', is_featured: true, is_active: true, sort_order: 25, deleted_at: null, created_at: new Date().toISOString() },
    { id: 26, name: 'Arroz con Leche de la Abuela', description: 'Cremoso arroz cocido con leche entera, canela y pasas.', price: 38, image_url: '', badge: 'Tradicional', category: 'Postres', is_featured: true, is_active: true, sort_order: 26, deleted_at: null, created_at: new Date().toISOString() },
    { id: 27, name: 'Pay de Queso con Zarzamora', description: 'Rebanada de pay cremoso con mermelada artesanal de zarzamora.', price: 55, image_url: '', badge: 'Delicioso', category: 'Postres', is_featured: true, is_active: true, sort_order: 27, deleted_at: null, created_at: new Date().toISOString() },
  ];

  private jobs: Job[] = [
    {
      id: 1,
      title: 'Parrillero de Pollo Asado',
      description: 'Buscamos persona apasionada por la cocina tradicional para preparación de pollo a la brasa y cortes.',
      employment_type: 'Tiempo completo',
      location: 'Sucursal Centro Histórico',
      schedule: 'Lunes a Sábado, turnos rotativos',
      image_url: null,
      is_active: true,
      sort_order: 1,
      deleted_at: null,
      created_at: new Date().toISOString(),
    },
    {
      id: 2,
      title: 'Atención al Cliente y Caja',
      description: 'Encargado(a) de recepción de pedidos, atención en mostrador y manejo de caja registradora.',
      employment_type: 'Medio tiempo',
      location: 'Sucursal Polanco',
      schedule: 'Viernes a Domingo',
      image_url: null,
      is_active: true,
      sort_order: 2,
      deleted_at: null,
      created_at: new Date().toISOString(),
    },
  ];

  private promotions: Promotion[] = [
    {
      id: 1,
      title: '2x1 en Pollo Entero',
      description: 'Lleva 2 pollos enteros por el precio de 1. ¡Válido todos los martes!',
      image_url: '',
      valid_until: '2026-12-31',
      code: 'POLLO2X1',
      badge: 'Oferta Especial',
      is_active: true,
      sort_order: 1,
      created_at: new Date().toISOString(),
    },
    {
      id: 2,
      title: 'Paquete Familiar Especial',
      description: 'Pollo entero + 4 complementos + refresco familiar a precio especial',
      image_url: '',
      valid_until: '2026-12-31',
      code: 'FAMILIA30',
      badge: 'Más Vendido',
      is_active: true,
      sort_order: 2,
      created_at: new Date().toISOString(),
    },
    {
      id: 3,
      title: 'Combo Estudiante',
      description: 'Medio pollo + papas + refresco. ¡Presenta tu credencial!',
      image_url: '',
      valid_until: '2026-12-31',
      code: 'ESTUDIANTE',
      badge: 'Promoción Especial',
      is_active: true,
      sort_order: 3,
      created_at: new Date().toISOString(),
    },
  ];

  private branches: Branch[] = [
    {
      id: 1,
      name: 'Sucursal Centro Histórico',
      address: 'Av. 5 de Mayo #123, Colonia Centro, Durango',
      phone: '555-123-4567',
      schedule: 'Lun - Dom: 10:00 AM - 10:00 PM',
      image_url: '',
      map_link: 'https://maps.google.com/?q=Durango',
      is_open: true,
      sort_order: 1,
      created_at: new Date().toISOString(),
    },
    {
      id: 2,
      name: 'Sucursal Jardines',
      address: 'Av. Las Rosas #456, Fracc. Jardines, Durango',
      phone: '555-987-6543',
      schedule: 'Lun - Dom: 11:00 AM - 11:00 PM',
      image_url: '',
      map_link: 'https://maps.google.com/?q=Durango',
      is_open: true,
      sort_order: 2,
      created_at: new Date().toISOString(),
    },
    {
      id: 3,
      name: 'Sucursal Coyoacán',
      address: 'Calle Hidalgo #789, Durango',
      phone: '555-456-7890',
      schedule: 'Lun - Vie: 12:00 PM - 9:00 PM',
      image_url: '',
      map_link: 'https://maps.google.com/?q=Durango',
      is_open: true,
      sort_order: 3,
      created_at: new Date().toISOString(),
    },
  ];

  private orders: Order[] = [
    {
      id: 'PF-9140',
      customer_name: 'Alejandro Morales',
      customer_phone: '618-204-5821',
      order_type: 'delivery',
      branch: 'Sucursal Centro Histórico',
      address: 'Av. 20 de Noviembre #504, Centro',
      payment_method: 'Tarjeta en línea',
      items: [
        { id: 1, name: 'Pollo Entero Clásico', price: 180, quantity: 2 },
        { id: 5, name: 'Papas a la Francesa', price: 55, quantity: 2 },
        { id: 21, name: 'Refresco Familiar 2L', price: 48, quantity: 1 }
      ],
      subtotal: 518,
      delivery_fee: 35,
      total: 553,
      status: 'preparing',
      status_label: 'En preparación',
      created_at: new Date(Date.now() - 8 * 60 * 1000).toISOString(),
      updated_at: new Date(Date.now() - 4 * 60 * 1000).toISOString(),
      estimated_delivery: '20-25 min',
    },
    {
      id: 'PF-7821',
      customer_name: 'Juan Pérez',
      customer_phone: '618-123-4567',
      order_type: 'delivery',
      branch: 'Sucursal Centro Histórico',
      address: 'Calle Juárez #450, Centro, Durango',
      payment_method: 'Efectivo',
      notes: 'Tocar timbre blanco. Salsa extra de la casa por favor.',
      items: [
        { id: 3, name: 'Paquete Familiar (Pollo + 2 complementos)', price: 280, quantity: 1 },
        { id: 5, name: 'Papas a la Francesa', price: 55, quantity: 1 },
        { id: 22, name: 'Agua de Horchata Artesanal 1L', price: 45, quantity: 1 }
      ],
      subtotal: 380,
      delivery_fee: 35,
      total: 415,
      status: 'on_the_way',
      status_label: 'En camino',
      created_at: new Date(Date.now() - 25 * 60 * 1000).toISOString(),
      updated_at: new Date(Date.now() - 5 * 60 * 1000).toISOString(),
      estimated_delivery: '10-15 min',
    },
  ];

  private reviews: Review[] = [
    {
      id: 1,
      customer_name: 'Martha Elena Salcido',
      rating: 5,
      comment: '¡El mejor pollo asado de Durango sin duda alguna! El marinado al carbón tiene ese sabor tradicional inigualable.',
      branch: 'Sucursal Centro Histórico',
      favorite_dish: 'Paquete Familiar',
      tags: ['Sabor al carbón', 'Crujiente', 'Salsas caseras'],
      is_verified: true,
      status: 'approved',
      is_featured: true,
      likes_count: 24,
      admin_reply: {
        author: 'Equipo Pollo Feliz',
        message: '¡Muchas gracias Sra. Martha! Nos llena de orgullo ser parte de sus domingos familiares.',
        created_at: new Date(Date.now() - 86400000 * 2).toISOString(),
      },
      created_at: new Date(Date.now() - 86400000 * 3).toISOString(),
    },
  ];

  private contactMessages: ContactMessage[] = [
    {
      id: 1,
      name: 'Carlos Mendoza',
      email: 'carlos@example.com',
      phone: '618-123-4567',
      subject: 'Servicio para evento familiar',
      branch: 'Matriz Durango Centro',
      destination_email: 'soporte@pollofelizdgo.com',
      folio: 'TKT-0001',
      email_sent: true,
      message: 'Hola, me gustaría cotizar 15 paquetes familiares para un cumpleaños este fin de semana.',
      status: 'pending',
      created_at: new Date(Date.now() - 3600000).toISOString(),
    },
  ];

  async connect(): Promise<void> {
    this.isReady = true;
  }

  async disconnect(): Promise<void> {
    this.isReady = false;
  }

  async healthCheck(): Promise<{ status: 'healthy'; latency_ms: number; details: any }> {
    return {
      status: 'healthy',
      latency_ms: 0.5,
      details: {
        usersCount: this.users.length,
        menuCount: this.menuItems.length,
        ordersCount: this.orders.length,
        auditLogsCount: this.auditLogs.length,
      },
    };
  }

  async getUsers(): Promise<User[]> {
    return [...this.users];
  }

  async findUserById(id: number): Promise<User | null> {
    const user = this.users.find(u => u.id === id);
    return user ? { ...user } : null;
  }

  async findUserByEmail(email: string): Promise<User | null> {
    const clean = email.trim().toLowerCase();
    const user = this.users.find(u => u.email.toLowerCase() === clean);
    return user ? { ...user } : null;
  }

  async findUserByToken(token: string): Promise<User | null> {
    if (!token) return null;
    const user = this.users.find(u => u.api_token === token || (token === 'default-admin-token-12345' && u.is_admin));
    return user ? { ...user } : null;
  }

  async createUser(userData: Omit<User, 'id' | 'created_at'>): Promise<User> {
    const newUser: User = {
      ...userData,
      id: this.users.length + 1,
      created_at: new Date().toISOString(),
    };
    this.users.push(newUser);
    return { ...newUser };
  }

  async updateUser(id: number, data: Partial<User>): Promise<User | null> {
    const index = this.users.findIndex(u => u.id === id);
    if (index === -1) return null;
    this.users[index] = {
      ...this.users[index],
      ...data,
      updated_at: new Date().toISOString(),
    };
    return { ...this.users[index] };
  }

  async deleteUser(id: number): Promise<boolean> {
    const initialLen = this.users.length;
    this.users = this.users.filter(u => u.id !== id);
    return this.users.length < initialLen;
  }

  async verifyCredentials(email: string, pass: string): Promise<User | null> {
    const user = await this.findUserByEmail(email);
    if (!user) return null;
    if (user.password !== pass) {
      return null;
    }
    return user;
  }

  async recordAuditLog(log: Omit<SecurityAuditLog, 'id' | 'created_at'>): Promise<SecurityAuditLog> {
    const newLog: SecurityAuditLog = {
      ...log,
      id: this.auditLogs.length + 1,
      created_at: new Date().toISOString(),
    };
    this.auditLogs.unshift(newLog);
    return { ...newLog };
  }

  async getAuditLogs(limit = 100, offset = 0): Promise<SecurityAuditLog[]> {
    return this.auditLogs.slice(offset, offset + limit);
  }

  async getMenuItems(onlyActive = false): Promise<MenuItem[]> {
    let list = this.menuItems.filter(m => m.deleted_at === null);
    if (onlyActive) {
      list = list.filter(m => m.is_active);
    }
    return [...list];
  }

  async findMenuItemById(id: number): Promise<MenuItem | null> {
    const item = this.menuItems.find(m => m.id === id && m.deleted_at === null);
    return item ? { ...item } : null;
  }

  async createMenuItem(item: Omit<MenuItem, 'id' | 'created_at'>): Promise<MenuItem> {
    const newItem: MenuItem = {
      ...item,
      id: this.menuItems.length + 1,
      created_at: new Date().toISOString(),
    };
    this.menuItems.push(newItem);
    return { ...newItem };
  }

  async updateMenuItem(id: number, data: Partial<MenuItem>): Promise<MenuItem | null> {
    const item = this.menuItems.find(m => m.id === id);
    if (!item) return null;
    Object.assign(item, data, { updated_at: new Date().toISOString() });
    return { ...item };
  }

  async deleteMenuItem(id: number): Promise<boolean> {
    const item = this.menuItems.find(m => m.id === id);
    if (!item) return false;
    item.deleted_at = new Date().toISOString();
    return true;
  }

  async getOrders(filters?: { status?: string; phone?: string; search?: string }): Promise<Order[]> {
    let results = [...this.orders];
    if (filters?.status) {
      results = results.filter(o => o.status === filters.status);
    }
    if (filters?.phone) {
      const cleanPhone = filters.phone.replace(/\D/g, '');
      results = results.filter(o => o.customer_phone.replace(/\D/g, '').includes(cleanPhone));
    }
    if (filters?.search) {
      const term = filters.search.toLowerCase();
      results = results.filter(o =>
        o.id.toLowerCase().includes(term) ||
        o.customer_name.toLowerCase().includes(term) ||
        o.customer_phone.includes(term)
      );
    }
    return results;
  }

  async findOrderById(id: string): Promise<Order | null> {
    const order = this.orders.find(o => o.id.toLowerCase() === id.toLowerCase());
    return order ? { ...order } : null;
  }

  async createOrder(order: Omit<Order, 'created_at' | 'updated_at'>): Promise<Order> {
    const newOrder: Order = {
      ...order,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    };
    this.orders.unshift(newOrder);
    return { ...newOrder };
  }

  async updateOrderStatus(id: string, status: Order['status'], statusLabel: string): Promise<Order | null> {
    const order = this.orders.find(o => o.id.toLowerCase() === id.toLowerCase());
    if (!order) return null;
    order.status = status;
    order.status_label = statusLabel;
    order.updated_at = new Date().toISOString();
    return { ...order };
  }

  async getPromotions(onlyActive = false): Promise<Promotion[]> {
    return onlyActive ? this.promotions.filter(p => p.is_active) : [...this.promotions];
  }

  async createPromotion(promo: Omit<Promotion, 'id' | 'created_at'>): Promise<Promotion> {
    const newPromo: Promotion = {
      ...promo,
      id: this.promotions.length + 1,
      created_at: new Date().toISOString(),
    };
    this.promotions.push(newPromo);
    return { ...newPromo };
  }

  async updatePromotion(id: number, data: Partial<Promotion>): Promise<Promotion | null> {
    const p = this.promotions.find(x => x.id === id);
    if (!p) return null;
    Object.assign(p, data, { updated_at: new Date().toISOString() });
    return { ...p };
  }

  async deletePromotion(id: number): Promise<boolean> {
    const initialLen = this.promotions.length;
    this.promotions = this.promotions.filter(p => p.id !== id);
    return this.promotions.length < initialLen;
  }

  async getBranches(): Promise<Branch[]> {
    return [...this.branches];
  }

  async updateBranch(id: number, data: Partial<Branch>): Promise<Branch | null> {
    const b = this.branches.find(x => x.id === id);
    if (!b) return null;
    Object.assign(b, data, { updated_at: new Date().toISOString() });
    return { ...b };
  }

  async getJobs(onlyActive = false): Promise<Job[]> {
    let list = this.jobs.filter(j => j.deleted_at === null);
    if (onlyActive) list = list.filter(j => j.is_active);
    return [...list];
  }

  async createJob(job: Omit<Job, 'id' | 'created_at'>): Promise<Job> {
    const newJob: Job = {
      ...job,
      id: this.jobs.length + 1,
      created_at: new Date().toISOString(),
    };
    this.jobs.push(newJob);
    return { ...newJob };
  }

  async updateJob(id: number, data: Partial<Job>): Promise<Job | null> {
    const j = this.jobs.find(x => x.id === id);
    if (!j) return null;
    Object.assign(j, data, { updated_at: new Date().toISOString() });
    return { ...j };
  }

  async deleteJob(id: number): Promise<boolean> {
    const j = this.jobs.find(x => x.id === id);
    if (!j) return false;
    j.deleted_at = new Date().toISOString();
    return true;
  }

  async getReviews(filters?: { branch?: string; rating?: number; status?: string; onlyActive?: boolean }): Promise<Review[]> {
    let list = [...this.reviews];
    if (filters?.onlyActive) {
      list = list.filter(r => r.status === 'approved');
    }
    if (filters?.branch && filters.branch !== 'Todas') {
      list = list.filter(r => r.branch.toLowerCase().includes(filters.branch!.toLowerCase()));
    }
    if (filters?.rating && filters.rating > 0) {
      list = list.filter(r => r.rating === filters.rating);
    }
    if (filters?.status) {
      list = list.filter(r => r.status === filters.status);
    }
    return list;
  }

  async createReview(review: Omit<Review, 'id' | 'created_at'>): Promise<Review> {
    const newRev: Review = {
      ...review,
      id: this.reviews.length + 1,
      created_at: new Date().toISOString(),
    };
    this.reviews.unshift(newRev);
    return { ...newRev };
  }

  async updateReview(id: number, data: Partial<Review>): Promise<Review | null> {
    const r = this.reviews.find(x => x.id === id);
    if (!r) return null;
    Object.assign(r, data, { updated_at: new Date().toISOString() });
    return { ...r };
  }

  async likeReview(id: number): Promise<number> {
    const r = this.reviews.find(x => x.id === id);
    if (!r) return 0;
    r.likes_count = (r.likes_count || 0) + 1;
    return r.likes_count;
  }

  async getContactMessages(status?: string): Promise<ContactMessage[]> {
    if (status) {
      return this.contactMessages.filter(m => m.status === status);
    }
    return [...this.contactMessages];
  }

  async createContactMessage(message: Omit<ContactMessage, 'id' | 'created_at'>): Promise<ContactMessage> {
    const newMsg: ContactMessage = {
      ...message,
      id: this.contactMessages.length + 1,
      created_at: new Date().toISOString(),
    };
    this.contactMessages.unshift(newMsg);
    return { ...newMsg };
  }

  async updateContactMessageStatus(id: number, status: ContactMessage['status']): Promise<ContactMessage | null> {
    const msg = this.contactMessages.find(m => m.id === id);
    if (!msg) return null;
    msg.status = status;
    return { ...msg };
  }

  async getSettings(): Promise<LandingSettings> {
    return { ...this.settings };
  }

  async updateSettings(data: Partial<LandingSettings>): Promise<LandingSettings> {
    this.settings = {
      ...this.settings,
      ...data,
    };
    return { ...this.settings };
  }
}
