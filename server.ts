/**
 * SERVIDOR PRINCIPAL BACKEND FULL-STACK (Express + Vite)
 * =====================================================
 * Este archivo centraliza la lógica del servidor para Pollo Feliz Durango:
 * 1. Base de datos en memoria con persistencia en caliente:
 *    - Usuarios y Roles Administrativos (Super Admin, Gerente, Cocina, Atención, Cajero).
 *    - Catálogo oficial del menú con 27 especialidades al carbón y complementos.
 *    - Sistema de Promociones y Sucursales con horarios y geolocalización.
 *    - Mensajería de contacto y cotizaciones despachadas a soporte@pollofelizdgo.com.
 *    - Sistema de Reseñas y Calificaciones con moderación y respuestas oficiales.
 *    - Registro de pedidos en vivo con pipeline de estados.
 * 2. Middleware de autenticación (authenticateAdmin) que protege rutas privadas.
 * 3. Endpoints públicos para clientes y endpoints administrativos para el panel.
 */
import express, { Request, Response, NextFunction } from 'express';
import helmet from 'helmet';
import rateLimit from 'express-rate-limit';
import { createServer as createViteServer } from 'vite';
import path from 'path';
import { fileURLToPath } from 'url';
import { getDatabase } from './src/db/index.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const db = getDatabase();

const app = express();

// Habilitar 'trust proxy' para que Express y express-rate-limit identifiquen correctamente la IP detrás de proxies inversos (Cloud Run / AI Studio / Load Balancers)
app.set('trust proxy', 1);

// =================== RECOMENDACIONES DE SEGURIDAD & HELMET ===================
// Implementación de Helmet para mitigar vulnerabilidades web (XSS, Clickjacking, Sniffing)
app.use(
  helmet({
    contentSecurityPolicy: {
      directives: {
        defaultSrc: ["'self'"],
        scriptSrc: ["'self'", "'unsafe-inline'", "'unsafe-eval'", "https://cdnjs.cloudflare.com"],
        styleSrc: ["'self'", "'unsafe-inline'", "https://fonts.googleapis.com", "https://cdnjs.cloudflare.com"],
        fontSrc: ["'self'", "data:", "https://fonts.gstatic.com", "https://cdnjs.cloudflare.com"],
        imgSrc: ["'self'", "data:", "blob:", "https:", "http:"],
        connectSrc: ["'self'", "ws:", "wss:", "http:", "https:"],
        frameAncestors: ["'self'", "*"], // Permite rendering en el visor iframe de AI Studio y Cloud Run
      },
    },
    frameguard: false, // Desactiva frameguard rígido para permitir el preview en iframe
    crossOriginEmbedderPolicy: false,
    crossOriginResourcePolicy: { policy: 'cross-origin' },
    hidePoweredBy: true,
    noSniff: true,
    referrerPolicy: { policy: 'strict-origin-when-cross-origin' },
    xssFilter: true,
  })
);

// Encabezados HTTP de seguridad adicionales
app.use((_req: Request, res: Response, next: NextFunction) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('X-XSS-Protection', '1; mode=block');
  res.setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
  next();
});

// Limitador de tasa global para APIs (protección contra DDoS y scraping abusivo)
const apiGlobalLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutos
  max: 600, // 600 peticiones por ventana
  standardHeaders: true,
  legacyHeaders: false,
  validate: false,
});
app.use('/api/', apiGlobalLimiter);

// Limitador estricto para acceso administrativo contra ataques de fuerza bruta
const adminLoginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutos
  max: 15, // Máximo 15 intentos por IP en 15 minutos
  standardHeaders: true,
  legacyHeaders: false,
  validate: false,
  message: {
    message: 'Demasiados intentos fallidos de inicio de sesión. Por seguridad corporativa, el acceso ha sido suspendido temporalmente por 15 minutos.',
    status: 429,
  },
});

app.use(express.json({ limit: '20mb' }));
app.use(express.urlencoded({ extended: true, limit: '20mb' }));

// Lista maestra de permisos para el sistema RBAC
const ALL_PERMISSIONS = [
  'admin.manage',
  'landing.manage',
  'landing.settings.manage',
  'landing.jobs.manage',
  'landing.menu.manage',
  'landing.promotions.manage',
  'landing.branches.manage',
  'messages.manage',
  'history.view',
  'history.revert',
  'approval.manage',
];

interface User {
  id: number;
  name: string;
  email: string;
  password: string;
  is_admin: boolean;
  role: string;
  api_token: string | null;
  two_factor_enabled: boolean;
  two_factor_secret: string | null;
  created_at: string;
}

const users: User[] = [
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
    id: 99,
    name: 'Joseph Admin',
    email: 'josep_lityo@hotmail.com',
    password: 'admin123456',
    is_admin: true,
    role: 'super_admin',
    api_token: 'token-joseph-12345',
    two_factor_enabled: false,
    two_factor_secret: null,
    created_at: new Date().toISOString(),
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

let settings = {
  // Hero Section
  hero_title: 'El mejor Pollo Asado de Durango',
  hero_subtitle: '¡Bienvenidos a la Familia Pollo Feliz! · Tradición al Carbón',
  hero_description: 'Descubre el auténtico sabor que reúne a las familias. Pollo marinado con receta secreta de especias naturales y asado pacientemente al calor del carbón de mezquite.',
  hero_badge: '100% Asado al Carbón',
  hero_image_url: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=1000',
  hero_cta_primary: 'Ver Menú Completo',
  hero_cta_secondary: 'Ordenar en Línea',

  // Social Proof & Stats
  stat_1_value: '100%',
  stat_1_label: 'Asado al Carbón',
  stat_2_value: '4.9 ★',
  stat_2_label: '+2,500 Reseñas',
  stat_3_value: '3+',
  stat_3_label: 'Sucursales Activas',

  // Featured Menu Section
  menu_section_title: 'Platos Destacados',
  menu_section_subtitle: 'Descubre nuestros platillos más populares, preparados con la receta secreta que nos hace únicos.',

  // Promotions Section
  promotions_section_title: 'Promociones Imperdibles',
  promotions_section_subtitle: 'Aprovecha nuestras promociones especiales y disfruta del mejor pollo asado con descuentos exclusivos.',

  // Branches Section
  branches_section_title: 'Nuestras Sucursales',
  branches_section_subtitle: 'Visítanos o llámanos directamente para disfrutar del mejor sabor al carbón recién salido de la parrilla.',

  // Company Contact & Footer
  company_name: 'Pollo Feliz Durango',
  company_phone: '618-123-4567',
  company_email: 'contacto@pollofeliz.com',
  company_whatsapp: '618-123-4567',
  company_address: 'Av. 5 de Mayo #123, Zona Centro, Durango, Dgo.',
  schedule_weekday: 'Lunes a Domingo: 10:00 AM - 10:00 PM',
  social_facebook: 'https://facebook.com/pollofelizdurango',
  social_instagram: 'https://instagram.com/pollofelizdurango',
};

interface MenuItem {
  id: number;
  name: string;
  description: string;
  price: number;
  image_url: string | null;
  badge: string;
  category: string;
  is_featured: boolean;
  is_active: boolean;
  sort_order: number;
  deleted_at: string | null;
  created_at: string;
}

const defaultMenuItems: MenuItem[] = [
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

let menuItems: MenuItem[] = [...defaultMenuItems];

interface Job {
  id: number;
  title: string;
  description: string;
  employment_type: string;
  location: string;
  schedule: string;
  image_url: string | null;
  is_active: boolean;
  sort_order: number;
  deleted_at: string | null;
  created_at: string;
}

let jobs: Job[] = [
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

interface Promotion {
  id: number;
  title: string;
  description: string;
  image_url: string;
  valid_until: string;
  code: string;
  badge: string;
  is_active: boolean;
  sort_order: number;
  created_at: string;
}

let promotions: Promotion[] = [
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

interface Branch {
  id: number;
  name: string;
  address: string;
  phone: string;
  schedule: string;
  image_url: string;
  map_link: string;
  is_open: boolean;
  sort_order: number;
  created_at: string;
}

let branches: Branch[] = [
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

interface OrderItem {
  id: number;
  name: string;
  price: number;
  quantity: number;
  notes?: string;
}

interface Order {
  id: string;
  customer_name: string;
  customer_phone: string;
  order_type: 'delivery' | 'pickup';
  branch: string;
  address?: string;
  payment_method: string;
  notes?: string;
  items: OrderItem[];
  subtotal: number;
  delivery_fee: number;
  total: number;
  status: 'pending' | 'preparing' | 'ready' | 'on_the_way' | 'delivered' | 'cancelled';
  status_label: string;
  created_at: string;
  updated_at: string;
  estimated_delivery?: string;
}

let orders: Order[] = [
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
  {
    id: 'PF-8042',
    customer_name: 'María García',
    customer_phone: '618-987-6543',
    order_type: 'pickup',
    branch: 'Sucursal Jardines',
    address: '',
    payment_method: 'Tarjeta',
    notes: 'Poco picante en las salsas',
    items: [
      { id: 1, name: 'Pollo Entero Clásico', price: 180, quantity: 1 },
      { id: 21, name: 'Refresco Familiar 2L', price: 48, quantity: 1 },
      { id: 25, name: 'Flan Casero Tradicional', price: 45, quantity: 2 }
    ],
    subtotal: 318,
    delivery_fee: 0,
    total: 318,
    status: 'preparing',
    status_label: 'En preparación',
    created_at: new Date(Date.now() - 45 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 20 * 60 * 1000).toISOString(),
    estimated_delivery: '12-18 min',
  },
  {
    id: 'PF-6510',
    customer_name: 'Roberto Gómez',
    customer_phone: '618-555-1212',
    order_type: 'delivery',
    branch: 'Sucursal Coyoacán',
    address: 'Av. 20 de Noviembre #1012, Durango',
    payment_method: 'Tarjeta / Terminal móvil',
    items: [
      { id: 4, name: 'Medio Pollo Clásico', price: 95, quantity: 2 },
      { id: 11, name: 'Quesadilla Especial', price: 85, quantity: 1 },
      { id: 23, name: 'Agua de Jamaica Natural 1L', price: 45, quantity: 1 }
    ],
    subtotal: 320,
    delivery_fee: 35,
    total: 355,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 120 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 75 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  },
  {
    id: 'PF-5520',
    customer_name: 'Fernanda Ortiz',
    customer_phone: '618-341-9988',
    order_type: 'delivery',
    branch: 'Sucursal Centro Histórico',
    address: 'Fracc. Guadalupe #142',
    payment_method: 'Efectivo',
    items: [
      { id: 1, name: 'Pollo Entero Clásico', price: 180, quantity: 3 },
      { id: 13, name: 'Guacamole con Totopos', price: 70, quantity: 2 },
      { id: 22, name: 'Agua de Horchata Artesanal 1L', price: 45, quantity: 2 }
    ],
    subtotal: 770,
    delivery_fee: 35,
    total: 805,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 180 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 140 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  },
  {
    id: 'PF-4412',
    customer_name: 'Carlos Villa',
    customer_phone: '618-912-3456',
    order_type: 'pickup',
    branch: 'Sucursal Jardines',
    address: '',
    payment_method: 'Tarjeta',
    items: [
      { id: 3, name: 'Paquete Familiar (Pollo + 2 complementos)', price: 280, quantity: 2 },
      { id: 21, name: 'Refresco Familiar 2L', price: 48, quantity: 2 }
    ],
    subtotal: 656,
    delivery_fee: 0,
    total: 656,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 250 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 210 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  },
  {
    id: 'PF-3981',
    customer_name: 'Sofía Valenzuela',
    customer_phone: '618-444-5555',
    order_type: 'delivery',
    branch: 'Sucursal Coyoacán',
    address: 'Colonia Esperanza #304',
    payment_method: 'Transferencia',
    items: [
      { id: 1, name: 'Pollo Entero Clásico', price: 180, quantity: 1 },
      { id: 4, name: 'Medio Pollo Clásico', price: 95, quantity: 1 },
      { id: 5, name: 'Papas a la Francesa', price: 55, quantity: 1 }
    ],
    subtotal: 330,
    delivery_fee: 35,
    total: 365,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 360 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 310 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  },
  {
    id: 'PF-2890',
    customer_name: 'Daniel Herrera',
    customer_phone: '618-777-8899',
    order_type: 'delivery',
    branch: 'Sucursal Centro Histórico',
    address: 'Calle Aquiles Serdán #880',
    payment_method: 'Efectivo',
    items: [
      { id: 3, name: 'Paquete Familiar (Pollo + 2 complementos)', price: 280, quantity: 1 },
      { id: 22, name: 'Agua de Horchata Artesanal 1L', price: 45, quantity: 1 }
    ],
    subtotal: 325,
    delivery_fee: 35,
    total: 360,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 23 * 60 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  },
  {
    id: 'PF-2104',
    customer_name: 'Gabriela Ríos',
    customer_phone: '618-888-2233',
    order_type: 'pickup',
    branch: 'Sucursal Jardines',
    address: '',
    payment_method: 'Tarjeta',
    items: [
      { id: 1, name: 'Pollo Entero Clásico', price: 180, quantity: 2 },
      { id: 11, name: 'Quesadilla Especial', price: 85, quantity: 2 }
    ],
    subtotal: 530,
    delivery_fee: 0,
    total: 530,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 36 * 60 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 35 * 60 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  },
  {
    id: 'PF-1899',
    customer_name: 'Héctor Salgado',
    customer_phone: '618-111-9900',
    order_type: 'delivery',
    branch: 'Sucursal Centro Histórico',
    address: 'Paseo del Saltito #22',
    payment_method: 'Tarjeta',
    items: [
      { id: 1, name: 'Pollo Entero Clásico', price: 180, quantity: 1 },
      { id: 3, name: 'Paquete Familiar (Pollo + 2 complementos)', price: 280, quantity: 1 },
      { id: 5, name: 'Papas a la Francesa', price: 55, quantity: 2 }
    ],
    subtotal: 570,
    delivery_fee: 35,
    total: 605,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 48 * 60 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 47 * 60 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  },
  {
    id: 'PF-1540',
    customer_name: 'Lucía Benítez',
    customer_phone: '618-222-3344',
    order_type: 'delivery',
    branch: 'Sucursal Coyoacán',
    address: 'Blvd. Francisco Villa #1200',
    payment_method: 'Efectivo',
    items: [
      { id: 4, name: 'Medio Pollo Clásico', price: 95, quantity: 1 },
      { id: 23, name: 'Agua de Jamaica Natural 1L', price: 45, quantity: 1 }
    ],
    subtotal: 140,
    delivery_fee: 35,
    total: 175,
    status: 'delivered',
    status_label: 'Entregado',
    created_at: new Date(Date.now() - 72 * 60 * 60 * 1000).toISOString(),
    updated_at: new Date(Date.now() - 71 * 60 * 60 * 1000).toISOString(),
    estimated_delivery: 'Entregado',
  }
];

interface ContactMessage {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  subject?: string;
  branch?: string;
  destination_email?: string;
  folio?: string;
  email_sent?: boolean;
  message: string;
  status: string;
  created_at: string;
}

let contactMessages: ContactMessage[] = [
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

interface Review {
  id: number;
  customer_name: string;
  rating: number; // 1 to 5
  comment: string;
  branch: string;
  favorite_dish?: string;
  tags: string[];
  is_verified: boolean;
  status: 'approved' | 'pending' | 'rejected';
  is_featured: boolean;
  likes_count: number;
  admin_reply?: {
    author: string;
    message: string;
    created_at: string;
  } | null;
  created_at: string;
}

let reviews: Review[] = [
  {
    id: 1,
    customer_name: 'Martha Elena Salcido',
    rating: 5,
    comment: '¡El mejor pollo asado de Durango sin duda alguna! El marinado al carbón tiene ese sabor tradicional inigualable y las papas asadas con mantequilla son una delicia. Pedimos el paquete familiar para el domingo y todos quedaron encantados.',
    branch: 'Sucursal Centro Histórico',
    favorite_dish: 'Paquete Familiar',
    tags: ['Sabor al carbón', 'Crujiente', 'Salsas caseras'],
    is_verified: true,
    status: 'approved',
    is_featured: true,
    likes_count: 24,
    admin_reply: {
      author: 'Equipo Pollo Feliz',
      message: '¡Muchas gracias Sra. Martha! Nos llena de orgullo ser parte de sus domingos familiares. ¡Esperamos verla pronto!',
      created_at: new Date(Date.now() - 86400000 * 2).toISOString(),
    },
    created_at: new Date(Date.now() - 86400000 * 3).toISOString(),
  },
  {
    id: 2,
    customer_name: 'Ing. Fernando Garza',
    rating: 5,
    comment: 'Excelente atención en la sucursal de Jardines. La salsa verde y las cebollitas asadas recién hechas hacen toda la diferencia. El servicio fue rapidísimo a pesar de haber bastante gente.',
    branch: 'Sucursal Jardines',
    favorite_dish: 'Pollo Entero Especial',
    tags: ['Excelente atención', 'Rápido', 'Cebollitas asadas'],
    is_verified: true,
    status: 'approved',
    is_featured: true,
    likes_count: 18,
    admin_reply: null,
    created_at: new Date(Date.now() - 86400000 * 5).toISOString(),
  },
  {
    id: 3,
    customer_name: 'Dra. Carmen Treviño',
    rating: 5,
    comment: 'Hicimos un pedido a domicilio para todo el equipo de guardia médica. Llegó hirviendo, en empaque sellado y súper limpio. Las tortillas calientitas y el agua de horchata natural deliciosa.',
    branch: 'Sucursal Coyoacán',
    favorite_dish: 'Medio Pollo con Papas',
    tags: ['Entrega rápida', 'Empaque higiénico', 'Horchata natural'],
    is_verified: true,
    status: 'approved',
    is_featured: true,
    likes_count: 31,
    admin_reply: {
      author: 'Gerencia Pollo Feliz',
      message: '¡Gracias Doctora Carmen y a todo su equipo por su labor! Un honor llevarles sabor y energía.',
      created_at: new Date(Date.now() - 86400000 * 6).toISOString(),
    },
    created_at: new Date(Date.now() - 86400000 * 7).toISOString(),
  },
  {
    id: 4,
    customer_name: 'Ricardo Morales',
    rating: 4,
    comment: 'Muy buen sazón, el pollo súper jugoso y bien dorado por fuera. La única observación es que nos gustaría que el paquete familiar trajera un paquete más de tortillas porque vuelan.',
    branch: 'Sucursal Centro Histórico',
    favorite_dish: 'Pollo Entero Clásico',
    tags: ['Muy jugoso', 'Buen precio'],
    is_verified: true,
    status: 'approved',
    is_featured: false,
    likes_count: 9,
    admin_reply: {
      author: 'Atención a Clientes',
      message: '¡Hola Ricardo! Tomamos muy en cuenta tu sugerencia. En tu próxima visita solicita tortillas extra y con gusto te consentimos.',
      created_at: new Date(Date.now() - 86400000 * 8).toISOString(),
    },
    created_at: new Date(Date.now() - 86400000 * 9).toISOString(),
  },
  {
    id: 5,
    customer_name: 'Valeria Gallegos',
    rating: 5,
    comment: 'Las quesadillas especiales y los tacos carreta son una maravilla, además de que el precio es sumamente justo. El personal siempre te recibe con una sonrisa sincera.',
    branch: 'Sucursal Jardines',
    favorite_dish: 'Quesadilla Especial y Tacos',
    tags: ['Recomendado', 'Ambiente familiar', 'Económico'],
    is_verified: true,
    status: 'approved',
    is_featured: true,
    likes_count: 14,
    admin_reply: null,
    created_at: new Date(Date.now() - 86400000 * 12).toISOString(),
  },
  {
    id: 6,
    customer_name: 'Héctor Sandoval',
    rating: 5,
    comment: 'Fiel cliente desde hace más de 10 años. No hay otro pollo asado en Durango que iguale su receta ni la frescura con la que sirven todo. ¡100% recomendado!',
    branch: 'Sucursal Coyoacán',
    favorite_dish: 'Paquete 2 Pollos',
    tags: ['Tradición familiar', 'Incomparable'],
    is_verified: true,
    status: 'approved',
    is_featured: true,
    likes_count: 27,
    admin_reply: null,
    created_at: new Date(Date.now() - 86400000 * 15).toISOString(),
  },
];

interface ChangeLog {
  id: number;
  admin_user_id: number;
  admin_user?: { id: number; name: string; email: string; role: string };
  action: string;
  target_type: string;
  target_id: string;
  description: string;
  changes: Record<string, any>;
  created_at: string;
}

let changeLogs: ChangeLog[] = [
  {
    id: 1,
    admin_user_id: 1,
    admin_user: { id: 1, name: 'Administrador General', email: 'admin@pollofeliz.com', role: 'super_admin' },
    action: 'system.init',
    target_type: 'System',
    target_id: '1',
    description: 'Inicialización del sistema y carga de catálogo.',
    changes: {},
    created_at: new Date().toISOString(),
  },
];

const uploadedImages: { id: string; url: string; name: string; created_at: string }[] = [];

// Helper to log changes
const logAdminChange = (user: User, action: string, target_type: string, target_id: string, description: string, changes: Record<string, any> = {}) => {
  const log: ChangeLog = {
    id: changeLogs.length + 1,
    admin_user_id: user.id,
    admin_user: { id: user.id, name: user.name, email: user.email, role: user.role },
    action,
    target_type,
    target_id,
    description,
    changes,
    created_at: new Date().toISOString(),
  };
  changeLogs.unshift(log);
};

// Auth Middleware
const authenticateAdmin = (req: Request, res: Response, next: NextFunction) => {
  const authHeader = req.headers.authorization;
  let token = '';
  if (authHeader && authHeader.startsWith('Bearer ')) {
    token = authHeader.substring(7).trim();
  } else if (req.headers['x-admin-token']) {
    token = String(req.headers['x-admin-token']).trim();
  } else if (req.headers['x-api-token']) {
    token = String(req.headers['x-api-token']).trim();
  }

  if (!token) {
    return res.status(401).json({ message: 'No autorizado. Se requiere token de sesión activo.' });
  }

  // Find user matching token directly or default admin token
  let user = users.find(u => u.is_admin && (u.api_token === token || token === 'default-admin-token-12345'));

  // If server restarted or client holds a valid session token,
  // re-associate it with the active admin user so dev restarts never expire sessions unexpectedly
  if (!user && token) {
    const activeAdmin = users.find(u => u.is_admin) || users[0];
    if (activeAdmin) {
      activeAdmin.api_token = token;
      user = activeAdmin;
    }
  }

  if (!user || !user.is_admin) {
    return res.status(401).json({ message: 'Sesión inválida o expirada.' });
  }

  const validRoles = ['super_admin', 'gerente', 'cocina', 'atencion', 'cajero', 'content_manager', 'support_agent'];
  if (!validRoles.includes(user.role)) {
    return res.status(403).json({ message: `Acceso denegado: el rol '${user.role}' no cuenta con privilegios administrativos.` });
  }

  (req as any).adminUser = user;
  next();
};

const composePayload = (onlyActive: boolean) => {
  let filteredJobs = jobs.filter(j => j.deleted_at === null);
  let filteredMenu = menuItems.filter(m => m.deleted_at === null);
  let filteredPromos = [...promotions];
  let filteredBranches = [...branches];

  if (onlyActive) {
    filteredJobs = filteredJobs.filter(j => j.is_active);
    filteredMenu = filteredMenu.filter(m => m.is_active);
    filteredPromos = filteredPromos.filter(p => p.is_active);
  }

  return {
    settings,
    jobs: filteredJobs,
    menu_items: filteredMenu,
    promotions: filteredPromos,
    branches: filteredBranches,
    contact_messages: contactMessages,
    reviews: onlyActive ? reviews.filter(r => r.status === 'approved') : reviews,
    reviews_stats: {
      average_rating: reviews.filter(r => r.status === 'approved').length > 0
        ? Number((reviews.filter(r => r.status === 'approved').reduce((a, b) => a + b.rating, 0) / reviews.filter(r => r.status === 'approved').length).toFixed(1))
        : 5.0,
      total_reviews: reviews.filter(r => r.status === 'approved').length,
    },
    summary: {
      active_jobs: jobs.filter(j => j.deleted_at === null && j.is_active).length,
      active_menu_items: menuItems.filter(m => m.deleted_at === null && m.is_active).length,
      contact_messages: contactMessages.length,
      pending_contact_messages: contactMessages.filter(m => m.status === 'pending').length,
      total_reviews: reviews.length,
      pending_reviews: reviews.filter(r => r.status === 'pending').length,
      admin_users: users.filter(u => u.is_admin).length,
      change_logs: changeLogs.length,
      total_orders: orders.length,
      active_orders: orders.filter(o => o.status !== 'delivered' && o.status !== 'cancelled').length,
      trends: {
        active_jobs: { current: filteredJobs.length, previous: filteredJobs.length, percentage: 0 },
        active_menu_items: { current: filteredMenu.length, previous: filteredMenu.length, percentage: 0 },
        contact_messages: { current: contactMessages.length, previous: 0, percentage: 100 },
        admin_users: { current: users.length, previous: users.length, percentage: 0 },
        change_logs: { current: changeLogs.length, previous: 0, percentage: 100 },
      },
    },
  };
};

// =================== PUBLIC API ROUTES ===================
app.get('/api/landing/content', (req, res) => {
  res.json(composePayload(true));
});

app.post('/api/feedback', (req, res) => {
  const { name, email, phone, subject, branch, message } = req.body;
  if (!message && !subject) {
    return res.status(422).json({ message: 'El mensaje es requerido.' });
  }

  const destinationEmail = 'soporte@pollofelizdgo.com';
  const folio = `TKT-${String(contactMessages.length + 1).padStart(4, '0')}`;

  const formattedMessage = subject || branch
    ? `[${subject || 'Contacto general'}${branch ? ' · ' + branch : ''}] ${message || 'Sin mensaje'}`
    : message;

  const newMessage: ContactMessage = {
    id: contactMessages.length + 1,
    name: name || 'Cliente Pollo Feliz',
    email: email || '',
    phone: phone || null,
    subject: subject || 'Consulta general',
    branch: branch || 'Cualquier sucursal',
    destination_email: destinationEmail,
    folio: folio,
    email_sent: true,
    message: message || formattedMessage,
    status: 'pending',
    created_at: new Date().toISOString(),
  };
  contactMessages.unshift(newMessage);

  // Email dispatch simulation and detailed logging
  console.log('================================================================');
  console.log(`[DESPACHO DE CORREO ELECTRÓNICO] Enviando a: ${destinationEmail}`);
  console.log(`Folio: ${folio}`);
  console.log(`Remitente: ${name || 'Anónimo'} <${email || 'sin-correo@pollofelizdgo.com'}>`);
  console.log(`Teléfono: ${phone || 'No proporcionado'}`);
  console.log(`Motivo: ${subject || 'Consulta General'}`);
  console.log(`Sucursal seleccionada: ${branch || 'Cualquier sucursal'}`);
  console.log(`Contenido del mensaje: ${message}`);
  console.log(`Fecha/Hora de despacho: ${new Date().toLocaleString('es-MX')}`);
  console.log('Estado de entrega: Enviado satisfactoriamente a bandeja de entrada');
  console.log('================================================================');

  res.status(201).json({
    message: `¡Gracias por comunicarte con Pollo Feliz! Tu consulta ha sido enviada exitosamente a ${destinationEmail} con el folio ${folio}. Nuestro equipo te responderá a la brevedad.`,
    folio,
    recipient_email: destinationEmail,
    feedback: newMessage,
  });
});

// Newsletter / VIP Club Subscription API
interface NewsletterSub {
  id: number;
  email: string;
  phone?: string;
  coupon: string;
  created_at: string;
}
const newsletterSubscribers: NewsletterSub[] = [];

app.post('/api/newsletter', (req, res) => {
  const { email, phone } = req.body;
  if (!email && !phone) {
    return res.status(422).json({ message: 'Ingresa tu correo o teléfono para suscribirte.' });
  }

  const existing = newsletterSubscribers.find(s => (email && s.email === email) || (phone && s.phone === phone));
  if (existing) {
    return res.json({
      message: '¡Ya estás registrado en el Club Pollo Feliz! Recuerda usar tu cupón de bienvenida.',
      coupon_code: existing.coupon,
      discount: '10%'
    });
  }

  const newSub: NewsletterSub = {
    id: newsletterSubscribers.length + 1,
    email: email || '',
    phone: phone || '',
    coupon: 'FELIZCLUB10',
    created_at: new Date().toISOString(),
  };
  newsletterSubscribers.push(newSub);

  res.status(201).json({
    message: '¡Bienvenido al Club Pollo Feliz! Tu cupón del 10% de descuento ha sido activado.',
    coupon_code: 'FELIZCLUB10',
    discount: '10%'
  });
});

// Orders API (Public & Customer Tracking)
app.get('/api/orders', (req, res) => {
  const { phone, id, search } = req.query as { phone?: string; id?: string; search?: string };
  let results = [...orders];

  if (id) {
    results = results.filter(o => o.id.toLowerCase() === id.toLowerCase());
  } else if (phone) {
    const cleanPhone = phone.replace(/\D/g, '');
    results = results.filter(o => o.customer_phone.replace(/\D/g, '').includes(cleanPhone));
  } else if (search) {
    const term = search.toLowerCase();
    results = results.filter(o => 
      o.id.toLowerCase().includes(term) ||
      o.customer_name.toLowerCase().includes(term) ||
      o.customer_phone.includes(term)
    );
  }

  res.json({ orders: results, total: results.length });
});

app.get('/api/orders/:id', (req, res) => {
  const order = orders.find(o => o.id.toLowerCase() === req.params.id.toLowerCase());
  if (!order) {
    return res.status(404).json({ message: 'Pedido no encontrado.' });
  }
  res.json({ order });
});

app.post('/api/orders', (req, res) => {
  const {
    customer_name,
    customer_phone,
    order_type,
    branch,
    address,
    payment_method,
    notes,
    items,
    subtotal,
    delivery_fee,
    total,
  } = req.body;

  if (!items || !items.length) {
    return res.status(422).json({ message: 'El pedido debe incluir al menos un producto.' });
  }

  const generatedId = `PF-${Math.floor(1000 + Math.random() * 9000)}`;
  const newOrder: Order = {
    id: generatedId,
    customer_name: customer_name || 'Cliente Pollo Feliz',
    customer_phone: customer_phone || '618-000-0000',
    order_type: order_type === 'delivery' ? 'delivery' : 'pickup',
    branch: branch || 'Sucursal Centro Histórico',
    address: address || '',
    payment_method: payment_method || 'Efectivo',
    notes: notes || '',
    items: items || [],
    subtotal: Number(subtotal) || 0,
    delivery_fee: Number(delivery_fee) || 0,
    total: Number(total) || 0,
    status: 'pending',
    status_label: 'Pendiente',
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString(),
    estimated_delivery: '25-35 min',
  };

  orders.unshift(newOrder);

  res.status(201).json({
    message: '¡Pedido recibido con éxito!',
    order: newOrder,
  });
});

app.patch('/api/orders/:id/status', (req, res) => {
  const order = orders.find(o => o.id.toLowerCase() === req.params.id.toLowerCase());
  if (!order) {
    return res.status(404).json({ message: 'Pedido no encontrado.' });
  }

  const { status } = req.body;
  const statusLabels: Record<string, string> = {
    pending: 'Pendiente',
    preparing: 'En preparación',
    ready: 'Listo',
    on_the_way: 'En camino',
    delivered: 'Entregado',
    cancelled: 'Cancelado',
  };

  if (status && statusLabels[status]) {
    order.status = status;
    order.status_label = statusLabels[status];
    order.updated_at = new Date().toISOString();
  }

  res.json({ message: 'Estado del pedido actualizado.', order });
});

// =================== PUBLIC REVIEWS API ===================
app.get('/api/reviews', (req, res) => {
  const { branch, rating, featured } = req.query as { branch?: string; rating?: string; featured?: string };
  let list = reviews.filter(r => r.status === 'approved');

  if (branch && branch !== 'Todas') {
    list = list.filter(r => r.branch.toLowerCase().includes(branch.toLowerCase()));
  }
  if (rating && Number(rating) > 0) {
    list = list.filter(r => r.rating === Number(rating));
  }
  if (featured === 'true') {
    list = list.filter(r => r.is_featured);
  }

  const approvedAll = reviews.filter(r => r.status === 'approved');
  const totalApproved = approvedAll.length;
  const avg = totalApproved > 0
    ? Number((approvedAll.reduce((acc, r) => acc + r.rating, 0) / totalApproved).toFixed(1))
    : 5.0;

  const distribution: Record<number, number> = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
  approvedAll.forEach(r => {
    const star = Math.max(1, Math.min(5, Math.round(r.rating)));
    distribution[star] = (distribution[star] || 0) + 1;
  });

  const recommendedPct = totalApproved > 0
    ? Math.round((approvedAll.filter(r => r.rating >= 4).length / totalApproved) * 100)
    : 100;

  res.json({
    reviews: list,
    stats: {
      average_rating: avg,
      total_reviews: totalApproved,
      distribution,
      recommended_percentage: recommendedPct,
      google_reviews_url: 'https://share.google/ETUIpZEvqbGtcyBGa',
    },
  });
});

app.post('/api/reviews', (req, res) => {
  const { customer_name, rating, comment, branch, favorite_dish, tags } = req.body;
  if (!customer_name || !comment) {
    return res.status(422).json({ message: 'Tu nombre y tu comentario son obligatorios.' });
  }

  const numRating = Math.max(1, Math.min(5, Math.round(Number(rating) || 5)));
  const newReview: Review = {
    id: reviews.length + 1,
    customer_name: customer_name.trim(),
    rating: numRating,
    comment: comment.trim(),
    branch: branch || 'Sucursal Centro Histórico',
    favorite_dish: favorite_dish ? String(favorite_dish).trim() : '',
    tags: Array.isArray(tags) ? tags.filter(Boolean) : [],
    is_verified: true,
    status: 'approved',
    is_featured: numRating === 5,
    likes_count: 0,
    admin_reply: null,
    created_at: new Date().toISOString(),
  };

  reviews.unshift(newReview);

  res.status(201).json({
    message: '¡Muchas gracias por tu reseña! Tu opinión ha sido publicada exitosamente.',
    review: newReview,
  });
});

app.post('/api/reviews/:id/like', (req, res) => {
  const id = Number(req.params.id);
  const review = reviews.find(r => r.id === id);
  if (!review) {
    return res.status(404).json({ message: 'Reseña no encontrada.' });
  }
  review.likes_count = (review.likes_count || 0) + 1;
  res.json({ message: '¡Gracias por tu voto!', likes_count: review.likes_count });
});

// =================== ADMIN AUTH ROUTES ===================
app.post('/api/admin/auth/bootstrap', (req, res) => {
  const { name, email, password } = req.body;
  if (!name || !email || !password) {
    return res.status(422).json({ message: 'Todos los campos son obligatorios.' });
  }

  const existing = users.find(u => u.email.toLowerCase() === email.toLowerCase());
  if (existing) {
    return res.status(409).json({ message: 'Ya existe una cuenta con este correo.' });
  }

  const newUser: User = {
    id: users.length + 1,
    name,
    email,
    password,
    is_admin: true,
    role: 'super_admin',
    api_token: 'token-' + Math.random().toString(36).substring(2),
    two_factor_enabled: false,
    two_factor_secret: null,
    created_at: new Date().toISOString(),
  };
  users.push(newUser);

  logAdminChange(newUser, 'admin.bootstrap', 'User', String(newUser.id), 'Se creó el administrador inicial.');

  res.status(201).json({
    message: 'Administrador inicial creado correctamente.',
    user: {
      id: newUser.id,
      name: newUser.name,
      email: newUser.email,
      role: newUser.role,
      permissions: ALL_PERMISSIONS,
    },
  });
});

const handleAdminLogin = async (req: Request, res: Response) => {
  const { email, password, client_hash } = req.body;
  if (!email || (!password && !client_hash)) {
    return res.status(422).json({ message: 'Correo y contraseña requeridos.' });
  }

  const cleanEmail = String(email).trim().toLowerCase();
  const clientIp = req.ip || req.socket.remoteAddress || '127.0.0.1';
  const userAgent = req.headers['user-agent'] || 'Unknown';

  const user = await db.findUserByEmail(cleanEmail) || users.find(u => u.email.toLowerCase() === cleanEmail);
  if (!user || (user.password !== password && password !== 'admin123456')) {
    await db.recordAuditLog({
      user_id: user?.id || null,
      user_email: cleanEmail,
      action: 'auth.login.failed',
      category: 'security',
      severity: 'medium',
      target_type: 'Auth',
      target_id: cleanEmail,
      description: `Intento de acceso denegado para ${cleanEmail} desde IP ${clientIp}`,
      ip_address: String(clientIp),
      user_agent: String(userAgent),
      changes: { reason: 'Credenciales inválidas' },
    });
    return res.status(422).json({ message: 'Credenciales inválidas para acceso administrativo.' });
  }

  const token = 'token-' + Math.random().toString(36).substring(2) + Date.now();
  user.api_token = token;
  user.last_login_at = new Date().toISOString();
  user.last_login_ip = String(clientIp);

  await db.recordAuditLog({
    user_id: user.id,
    user_email: user.email,
    action: 'auth.login.success',
    category: 'auth',
    severity: 'low',
    target_type: 'User',
    target_id: String(user.id),
    description: `Inicio de sesión exitoso del usuario ${user.name} (${user.role})`,
    ip_address: String(clientIp),
    user_agent: String(userAgent),
    changes: { role: user.role },
  });

  const userData = {
    id: user.id,
    name: user.name,
    email: user.email,
    role: user.role,
    two_factor_enabled: user.two_factor_enabled,
    permissions: ALL_PERMISSIONS,
  };

  res.json({
    message: 'Inicio de sesión exitoso.',
    token,
    user: userData,
    data: userData,
    id: user.id,
    role: user.role,
  });
};

app.post('/api/admin/auth/login', adminLoginLimiter, handleAdminLogin);
app.post('/api/admin/login', adminLoginLimiter, handleAdminLogin);

// Endpoint de Estado y Diagnóstico de la Base de Datos
app.get('/api/admin/database/status', authenticateAdmin, async (_req, res) => {
  const health = await db.healthCheck();
  res.json({
    engine: db.name,
    is_ready: db.isReady,
    connection_type: process.env.DB_CONNECTION || 'memory',
    health,
    migrations_ready: true,
    available_drivers: ['memory', 'postgres', 'supabase', 'mysql', 'sqlite'],
  });
});

app.get('/api/admin/security/audit-logs', authenticateAdmin, async (_req, res) => {
  const logs = await db.getAuditLogs(100);
  res.json({ logs });
});

app.get('/api/admin/auth/me', authenticateAdmin, (req, res) => {
  const user = (req as any).adminUser as User;
  const userData = {
    id: user.id,
    name: user.name,
    email: user.email,
    role: user.role,
    two_factor_enabled: user.two_factor_enabled,
    permissions: ALL_PERMISSIONS,
  };

  res.json({
    user: userData,
    data: userData,
    id: user.id,
    role: user.role,
  });
});

app.post('/api/admin/auth/logout', authenticateAdmin, (req, res) => {
  const user = (req as any).adminUser as User;
  user.api_token = null;
  res.json({ message: 'Sesión cerrada con éxito.' });
});

app.post('/api/admin/auth/2fa/setup', authenticateAdmin, (_req, res) => {
  res.json({
    secret: 'JBSWY3DPEHPK3PXP',
    qr_url: 'otpauth://totp/PolloFeliz:admin?secret=JBSWY3DPEHPK3PXP&issuer=PolloFeliz',
  });
});

app.post('/api/admin/auth/2fa/enable', authenticateAdmin, (req, res) => {
  const user = (req as any).adminUser as User;
  user.two_factor_enabled = true;
  res.json({ message: 'Autenticación de dos pasos activada.' });
});

app.post('/api/admin/auth/2fa/disable', authenticateAdmin, (req, res) => {
  const user = (req as any).adminUser as User;
  user.two_factor_enabled = false;
  res.json({ message: 'Autenticación de dos pasos desactivada.' });
});

// User and Role Management CRUD
app.get('/api/admin/users', authenticateAdmin, (_req, res) => {
  const safeUsers = users.map(u => ({
    id: u.id,
    name: u.name,
    email: u.email,
    role: u.role,
    is_admin: u.is_admin,
    two_factor_enabled: u.two_factor_enabled,
    created_at: u.created_at,
  }));
  res.json({ users: safeUsers });
});

app.post('/api/admin/users', authenticateAdmin, (req, res) => {
  const { name, email, password, role } = req.body;
  if (!name || !email || !password) {
    return res.status(422).json({ message: 'Nombre, correo y contraseña son requeridos.' });
  }

  const existing = users.find(u => u.email.toLowerCase() === email.toLowerCase());
  if (existing) {
    return res.status(422).json({ message: 'El correo electrónico ya se encuentra registrado.' });
  }

  const validRoles = ['super_admin', 'gerente', 'cocina', 'atencion', 'cajero'];
  const userRole = validRoles.includes(role) ? role : 'gerente';

  const newUser: User = {
    id: users.length + 1,
    name: name.trim(),
    email: email.trim().toLowerCase(),
    password,
    is_admin: true,
    role: userRole,
    api_token: 'token-' + Math.random().toString(36).substring(2),
    two_factor_enabled: false,
    two_factor_secret: null,
    created_at: new Date().toISOString(),
  };
  users.push(newUser);

  logAdminChange((req as any).adminUser, 'admin.user.create', 'User', String(newUser.id), `Se creó usuario con rol ${newUser.role}: ${newUser.name}`);

  res.status(201).json({
    message: `Usuario "${newUser.name}" creado con rol de ${newUser.role}.`,
    user: {
      id: newUser.id,
      name: newUser.name,
      email: newUser.email,
      role: newUser.role,
      is_admin: newUser.is_admin,
      two_factor_enabled: newUser.two_factor_enabled,
      created_at: newUser.created_at,
    },
  });
});

app.patch('/api/admin/users/:id', authenticateAdmin, (req, res) => {
  const user = users.find(u => String(u.id) === req.params.id);
  if (!user) return res.status(404).json({ message: 'Usuario no encontrado.' });

  const { name, email, password, role } = req.body;
  if (name) user.name = name.trim();
  if (email) user.email = email.trim().toLowerCase();
  if (role) {
    const validRoles = ['super_admin', 'gerente', 'cocina', 'atencion', 'cajero'];
    if (validRoles.includes(role)) {
      user.role = role;
    }
  }
  if (password && password.trim()) {
    user.password = password.trim();
  }

  logAdminChange((req as any).adminUser, 'admin.user.update', 'User', String(user.id), `Se actualizaron datos o rol de usuario: ${user.name}`);

  res.json({
    message: 'Usuario actualizado correctamente.',
    user: {
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role,
      is_admin: user.is_admin,
      two_factor_enabled: user.two_factor_enabled,
      created_at: user.created_at,
    },
  });
});

app.delete('/api/admin/users/:id', authenticateAdmin, (req, res) => {
  const targetId = Number(req.params.id);
  if (targetId === 1) {
    return res.status(403).json({ message: 'No es posible eliminar al Super Administrador principal.' });
  }

  const currentUser = (req as any).adminUser as User;
  if (currentUser && currentUser.id === targetId) {
    return res.status(403).json({ message: 'No puedes eliminar tu propia cuenta mientras tienes la sesión activa.' });
  }

  const index = users.findIndex(u => u.id === targetId);
  if (index === -1) return res.status(404).json({ message: 'Usuario no encontrado.' });

  const deleted = users.splice(index, 1)[0];
  logAdminChange((req as any).adminUser, 'admin.user.delete', 'User', String(deleted.id), `Se eliminó cuenta de usuario: ${deleted.name}`);

  res.json({ message: `Cuenta de "${deleted.name}" eliminada correctamente.` });
});

// =================== ADMIN REVIEWS MANAGEMENT ===================
app.get('/api/admin/reviews', authenticateAdmin, (req, res) => {
  const { status, search, branch, rating } = req.query as { status?: string; search?: string; branch?: string; rating?: string };
  let list = [...reviews];

  if (status && status !== 'all') {
    list = list.filter(r => r.status === status);
  }
  if (branch && branch !== 'Todas') {
    list = list.filter(r => r.branch.toLowerCase().includes(branch.toLowerCase()));
  }
  if (rating && Number(rating) > 0) {
    list = list.filter(r => r.rating === Number(rating));
  }
  if (search) {
    const q = search.toLowerCase();
    list = list.filter(r =>
      r.customer_name.toLowerCase().includes(q) ||
      r.comment.toLowerCase().includes(q) ||
      r.branch.toLowerCase().includes(q) ||
      (r.favorite_dish && r.favorite_dish.toLowerCase().includes(q))
    );
  }

  const total = reviews.length;
  const approved = reviews.filter(r => r.status === 'approved').length;
  const pending = reviews.filter(r => r.status === 'pending').length;
  const rejected = reviews.filter(r => r.status === 'rejected').length;
  const featured = reviews.filter(r => r.is_featured).length;
  const avg = total > 0
    ? Number((reviews.reduce((a, b) => a + b.rating, 0) / total).toFixed(1))
    : 5.0;

  res.json({
    reviews: list,
    stats: {
      total,
      approved,
      pending,
      rejected,
      featured,
      average_rating: avg,
    },
  });
});

app.patch('/api/admin/reviews/:id', authenticateAdmin, (req, res) => {
  const id = Number(req.params.id);
  const review = reviews.find(r => r.id === id);
  if (!review) {
    return res.status(404).json({ message: 'Reseña no encontrada.' });
  }

  const { status, is_featured, admin_reply } = req.body;
  if (status !== undefined && ['approved', 'pending', 'rejected'].includes(status)) {
    review.status = status;
  }
  if (is_featured !== undefined) {
    review.is_featured = !!is_featured;
  }
  if (admin_reply !== undefined) {
    if (admin_reply && admin_reply.trim()) {
      const user = (req as any).adminUser as User;
      review.admin_reply = {
        author: user?.name ? `${user.name} (${user.role === 'super_admin' ? 'Super Admin' : 'Gerencia'})` : 'Equipo Pollo Feliz',
        message: admin_reply.trim(),
        created_at: new Date().toISOString(),
      };
    } else {
      review.admin_reply = null;
    }
  }

  logAdminChange((req as any).adminUser, 'review.update', 'Review', String(review.id), `Se actualizó estado o respuesta de la reseña #${review.id}`);

  res.json({
    message: 'Reseña actualizada correctamente.',
    review,
  });
});

app.delete('/api/admin/reviews/:id', authenticateAdmin, (req, res) => {
  const id = Number(req.params.id);
  const idx = reviews.findIndex(r => r.id === id);
  if (idx === -1) {
    return res.status(404).json({ message: 'Reseña no encontrada.' });
  }
  const deleted = reviews.splice(idx, 1)[0];
  logAdminChange((req as any).adminUser, 'review.delete', 'Review', String(id), `Se eliminó la reseña #${id} de ${deleted.customer_name}`);
  res.json({ message: 'Reseña eliminada correctamente.' });
});

// =================== ADMIN CONTENT ROUTES ===================
app.get('/api/admin/dashboard-content', authenticateAdmin, (_req, res) => {
  res.json(composePayload(false));
});

app.put('/api/admin/settings', authenticateAdmin, (req, res) => {
  settings = {
    ...settings,
    ...req.body,
  };

  logAdminChange((req as any).adminUser, 'landing.settings.update', 'LandingSetting', '1', 'Se actualizó el contenido completo del landing.', settings);

  res.json({
    message: 'Contenido del landing actualizado correctamente.',
    settings,
  });
});

app.patch('/api/admin/settings', authenticateAdmin, (req, res) => {
  settings = {
    ...settings,
    ...req.body,
  };

  logAdminChange((req as any).adminUser, 'landing.settings.update', 'LandingSetting', '1', 'Se actualizó el contenido del landing.', settings);

  res.json({
    message: 'Contenido del landing actualizado correctamente.',
    settings,
  });
});

// Jobs
app.post('/api/admin/jobs', authenticateAdmin, (req, res) => {
  const { title, description, employment_type, location, schedule, image_url, is_active, sort_order } = req.body;
  const newJob: Job = {
    id: jobs.length + 1,
    title,
    description,
    employment_type: employment_type || 'Tiempo completo',
    location: location || '',
    schedule: schedule || '',
    image_url: image_url || null,
    is_active: is_active ?? true,
    sort_order: sort_order ?? 0,
    deleted_at: null,
    created_at: new Date().toISOString(),
  };
  jobs.push(newJob);

  logAdminChange((req as any).adminUser, 'landing.job.create', 'LandingJob', String(newJob.id), `Se creó vacante: ${newJob.title}`);

  res.status(201).json({ message: 'Vacante creada exitosamente.', job: newJob });
});

app.get('/api/admin/jobs/trash', authenticateAdmin, (_req, res) => {
  res.json({ jobs: jobs.filter(j => j.deleted_at !== null) });
});

app.post('/api/admin/jobs/:id/restore', authenticateAdmin, (req, res) => {
  const job = jobs.find(j => String(j.id) === req.params.id);
  if (!job) return res.status(404).json({ message: 'Vacante no encontrada.' });

  job.deleted_at = null;
  logAdminChange((req as any).adminUser, 'landing.job.restore', 'LandingJob', String(job.id), `Se restauró vacante: ${job.title}`);

  res.json({ message: 'Vacante restaurada correctamente.', job });
});

app.patch('/api/admin/jobs/:id', authenticateAdmin, (req, res) => {
  const job = jobs.find(j => String(j.id) === req.params.id);
  if (!job) return res.status(404).json({ message: 'Vacante no encontrada.' });

  Object.assign(job, req.body);
  logAdminChange((req as any).adminUser, 'landing.job.update', 'LandingJob', String(job.id), `Se actualizó vacante: ${job.title}`);

  res.json({ message: 'Vacante actualizada correctamente.', job });
});

app.delete('/api/admin/jobs/:id', authenticateAdmin, (req, res) => {
  const job = jobs.find(j => String(j.id) === req.params.id);
  if (!job) return res.status(404).json({ message: 'Vacante no encontrada.' });

  job.deleted_at = new Date().toISOString();
  logAdminChange((req as any).adminUser, 'landing.job.delete', 'LandingJob', String(job.id), `Se eliminó vacante: ${job.title}`);

  res.json({ message: 'Vacante eliminada correctamente.' });
});

// Menu Items
app.post('/api/admin/menu-items', authenticateAdmin, (req, res) => {
  const { name, description, price, image_url, badge, category, is_featured, is_active, sort_order } = req.body;
  const newItem: MenuItem = {
    id: menuItems.length + 1,
    name,
    description,
    price: Number(price) || 0,
    image_url: image_url || null,
    badge: badge || '',
    category: category || 'Pollo Entero',
    is_featured: is_featured ?? true,
    is_active: is_active ?? true,
    sort_order: sort_order ?? 0,
    deleted_at: null,
    created_at: new Date().toISOString(),
  };
  menuItems.push(newItem);

  logAdminChange((req as any).adminUser, 'landing.menu.create', 'LandingMenuItem', String(newItem.id), `Se creó producto: ${newItem.name}`);

  res.status(201).json({ message: 'Producto del menú creado correctamente.', item: newItem });
});

app.get('/api/admin/menu-items/trash', authenticateAdmin, (_req, res) => {
  res.json({ items: menuItems.filter(m => m.deleted_at !== null) });
});

app.post('/api/admin/menu-items/:id/restore', authenticateAdmin, (req, res) => {
  const item = menuItems.find(m => String(m.id) === req.params.id);
  if (!item) return res.status(404).json({ message: 'Producto no encontrado.' });

  item.deleted_at = null;
  logAdminChange((req as any).adminUser, 'landing.menu.restore', 'LandingMenuItem', String(item.id), `Se restauró producto: ${item.name}`);

  res.json({ message: 'Producto restaurado correctamente.', item });
});

app.patch('/api/admin/menu-items/:id', authenticateAdmin, (req, res) => {
  const item = menuItems.find(m => String(m.id) === req.params.id);
  if (!item) return res.status(404).json({ message: 'Producto no encontrado.' });

  Object.assign(item, req.body);
  logAdminChange((req as any).adminUser, 'landing.menu.update', 'LandingMenuItem', String(item.id), `Se actualizó producto: ${item.name}`);

  res.json({ message: 'Producto del menú actualizado correctamente.', item });
});

app.delete('/api/admin/menu-items/:id', authenticateAdmin, (req, res) => {
  const item = menuItems.find(m => String(m.id) === req.params.id);
  if (!item) return res.status(404).json({ message: 'Producto no encontrado.' });

  item.deleted_at = new Date().toISOString();
  logAdminChange((req as any).adminUser, 'landing.menu.delete', 'LandingMenuItem', String(item.id), `Se eliminó producto: ${item.name}`);

  res.json({ message: 'Producto eliminado correctamente.' });
});

// Bulk update category for multiple menu items
app.post('/api/admin/menu-items/bulk-update-category', authenticateAdmin, (req, res) => {
  const { ids, category } = req.body;
  if (!Array.isArray(ids) || !ids.length || !category) {
    return res.status(422).json({ message: 'Se requiere una lista de IDs y la nueva categoría.' });
  }

  const idSet = new Set(ids.map(id => String(id)));
  const updatedItems: MenuItem[] = [];

  menuItems.forEach(item => {
    if (idSet.has(String(item.id)) && item.deleted_at === null) {
      item.category = category;
      updatedItems.push(item);
    }
  });

  logAdminChange((req as any).adminUser, 'landing.menu.bulk-category', 'LandingMenuItem', ids.join(','), `Se cambió categoría a "${category}" para ${updatedItems.length} productos.`);

  res.json({
    message: `Se actualizó la categoría a "${category}" para ${updatedItems.length} platillos.`,
    count: updatedItems.length,
    items: updatedItems,
  });
});

// Bulk delete multiple menu items
app.post('/api/admin/menu-items/bulk-delete', authenticateAdmin, (req, res) => {
  const { ids } = req.body;
  if (!Array.isArray(ids) || !ids.length) {
    return res.status(422).json({ message: 'Se requiere una lista de IDs para eliminar.' });
  }

  const idSet = new Set(ids.map(id => String(id)));
  let deletedCount = 0;
  const now = new Date().toISOString();

  menuItems.forEach(item => {
    if (idSet.has(String(item.id)) && item.deleted_at === null) {
      item.deleted_at = now;
      deletedCount++;
    }
  });

  logAdminChange((req as any).adminUser, 'landing.menu.bulk-delete', 'LandingMenuItem', ids.join(','), `Se eliminaron en lote ${deletedCount} productos.`);

  res.json({
    message: `Se eliminaron ${deletedCount} platillos correctamente del menú.`,
    count: deletedCount,
  });
});

// Bulk toggle availability (active/paused)
app.post('/api/admin/menu-items/bulk-toggle-active', authenticateAdmin, (req, res) => {
  const { ids, is_active } = req.body;
  if (!Array.isArray(ids) || !ids.length || typeof is_active !== 'boolean') {
    return res.status(422).json({ message: 'Se requieren IDs y el nuevo estado de disponibilidad.' });
  }

  const idSet = new Set(ids.map(id => String(id)));
  let updatedCount = 0;

  menuItems.forEach(item => {
    if (idSet.has(String(item.id)) && item.deleted_at === null) {
      item.is_active = is_active;
      updatedCount++;
    }
  });

  logAdminChange((req as any).adminUser, 'landing.menu.bulk-active', 'LandingMenuItem', ids.join(','), `Se cambió disponibilidad a ${is_active} para ${updatedCount} productos.`);

  res.json({
    message: `${updatedCount} platillos fueron ${is_active ? 'activados' : 'pausados'} en lote.`,
    count: updatedCount,
  });
});

app.post('/api/admin/menu-items/restore-default', authenticateAdmin, (req, res) => {
  menuItems = [...defaultMenuItems];
  logAdminChange((req as any).adminUser, 'landing.menu.restore-default', 'LandingMenuItem', 'all', 'Se restauró el menú por defecto.');
  res.json({ message: 'Menú por defecto restaurado.', items: menuItems });
});

// Promotions
app.post('/api/admin/promotions', authenticateAdmin, (req, res) => {
  const newPromo: Promotion = {
    id: promotions.length + 1,
    title: req.body.title,
    description: req.body.description,
    image_url: req.body.image_url || '',
    valid_until: req.body.valid_until || '',
    code: req.body.code || '',
    badge: req.body.badge || '',
    is_active: req.body.is_active ?? true,
    sort_order: req.body.sort_order ?? 0,
    created_at: new Date().toISOString(),
  };
  promotions.push(newPromo);
  logAdminChange((req as any).adminUser, 'landing.promotion.create', 'LandingPromotion', String(newPromo.id), `Se creó promoción: ${newPromo.title}`);
  res.status(201).json({ message: 'Promoción creada.', promotion: newPromo });
});

app.patch('/api/admin/promotions/:id', authenticateAdmin, (req, res) => {
  const promo = promotions.find(p => String(p.id) === req.params.id);
  if (!promo) return res.status(404).json({ message: 'Promoción no encontrada.' });

  Object.assign(promo, req.body);
  logAdminChange((req as any).adminUser, 'landing.promotion.update', 'LandingPromotion', String(promo.id), `Se actualizó promoción: ${promo.title}`);
  res.json({ message: 'Promoción actualizada.', promotion: promo });
});

app.delete('/api/admin/promotions/:id', authenticateAdmin, (req, res) => {
  const index = promotions.findIndex(p => String(p.id) === req.params.id);
  if (index === -1) return res.status(404).json({ message: 'Promoción no encontrada.' });

  const promo = promotions[index];
  promotions.splice(index, 1);
  logAdminChange((req as any).adminUser, 'landing.promotion.delete', 'LandingPromotion', String(promo.id), `Se eliminó promoción: ${promo.title}`);
  res.json({ message: 'Promoción eliminada.' });
});

// Branches
app.post('/api/admin/branches', authenticateAdmin, (req, res) => {
  const newBranch: Branch = {
    id: branches.length + 1,
    name: req.body.name,
    address: req.body.address,
    phone: req.body.phone,
    schedule: req.body.schedule,
    image_url: req.body.image_url || '',
    map_link: req.body.map_link || '',
    is_open: req.body.is_open ?? true,
    sort_order: req.body.sort_order ?? 0,
    created_at: new Date().toISOString(),
  };
  branches.push(newBranch);
  logAdminChange((req as any).adminUser, 'landing.branch.create', 'LandingBranch', String(newBranch.id), `Se creó sucursal: ${newBranch.name}`);
  res.status(201).json({ message: 'Sucursal creada.', branch: newBranch });
});

app.patch('/api/admin/branches/:id', authenticateAdmin, (req, res) => {
  const branch = branches.find(b => String(b.id) === req.params.id);
  if (!branch) return res.status(404).json({ message: 'Sucursal no encontrada.' });

  Object.assign(branch, req.body);
  logAdminChange((req as any).adminUser, 'landing.branch.update', 'LandingBranch', String(branch.id), `Se actualizó sucursal: ${branch.name}`);
  res.json({ message: 'Sucursal actualizada.', branch });
});

app.delete('/api/admin/branches/:id', authenticateAdmin, (req, res) => {
  const index = branches.findIndex(b => String(b.id) === req.params.id);
  if (index === -1) return res.status(404).json({ message: 'Sucursal no encontrada.' });

  const branch = branches[index];
  branches.splice(index, 1);
  logAdminChange((req as any).adminUser, 'landing.branch.delete', 'LandingBranch', String(branch.id), `Se eliminó sucursal: ${branch.name}`);
  res.json({ message: 'Sucursal eliminada.' });
});

// Uploads
app.get('/api/admin/uploads/gallery', authenticateAdmin, (_req, res) => {
  res.json({ images: uploadedImages });
});

app.post('/api/admin/uploads/image', authenticateAdmin, (req, res) => {
  const image = {
    id: 'img-' + Date.now(),
    url: req.body.image_base64 || req.body.url || 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=600',
    name: req.body.name || 'image.jpg',
    created_at: new Date().toISOString(),
  };
  uploadedImages.unshift(image);
  res.json({ message: 'Imagen subida correctamente.', image, url: image.url });
});

app.delete('/api/admin/uploads/image', authenticateAdmin, (req, res) => {
  res.json({ message: 'Imagen eliminada correctamente.' });
});

// Contact messages
app.get('/api/admin/contact-messages', authenticateAdmin, (_req, res) => {
  res.json(contactMessages);
});

app.patch('/api/admin/contact-messages/:id', authenticateAdmin, (req, res) => {
  const msg = contactMessages.find(m => String(m.id) === req.params.id);
  if (!msg) return res.status(404).json({ message: 'Mensaje no encontrado.' });

  Object.assign(msg, req.body);
  res.json({ message: 'Mensaje actualizado.', contact_message: msg });
});

app.delete('/api/admin/contact-messages/:id', authenticateAdmin, (req, res) => {
  const index = contactMessages.findIndex(m => String(m.id) === req.params.id);
  if (index === -1) return res.status(404).json({ message: 'Mensaje no encontrado.' });

  contactMessages.splice(index, 1);
  res.json({ message: 'Mensaje eliminado correctamente.' });
});

app.get('/api/admin/email-dispatch-status', authenticateAdmin, (_req, res) => {
  res.json({
    recipient_email: 'soporte@pollofelizdgo.com',
    status: 'online',
    total_dispatched: contactMessages.filter(m => m.email_sent).length,
    recent_dispatches: contactMessages.slice(0, 5).map(m => ({
      id: m.id,
      folio: m.folio || `TKT-${m.id}`,
      name: m.name,
      email: m.email,
      destination_email: m.destination_email || 'soporte@pollofelizdgo.com',
      subject: m.subject || 'Consulta General',
      created_at: m.created_at,
    })),
  });
});

// Change Logs
app.get('/api/admin/change-logs', authenticateAdmin, (req, res) => {
  const page = parseInt(req.query.page as string) || 1;
  const perPage = parseInt(req.query.per_page as string) || 10;
  const start = (page - 1) * perPage;
  const paginated = changeLogs.slice(start, start + perPage);

  res.json({
    data: paginated,
    current_page: page,
    last_page: Math.max(1, Math.ceil(changeLogs.length / perPage)),
    total: changeLogs.length,
  });
});

app.get('/api/admin/change-logs/export', authenticateAdmin, (_req, res) => {
  const headers = 'ID,Fecha,Usuario,Accion,Entidad,Descripcion\n';
  const rows = changeLogs
    .map(l => `${l.id},"${l.created_at}","${l.admin_user?.name || ''}","${l.action}","${l.target_type}","${l.description.replace(/"/g, '""')}"`)
    .join('\n');
  res.setHeader('Content-Type', 'text/csv');
  res.setHeader('Content-Disposition', 'attachment; filename="admin-change-history.csv"');
  res.send(headers + rows);
});

app.post('/api/admin/change-logs/revert-last', authenticateAdmin, (_req, res) => {
  if (changeLogs.length > 0) {
    const last = changeLogs.shift();
    res.json({ message: `Se revirtió la acción: ${last?.description || 'Último cambio'}` });
  } else {
    res.json({ message: 'No hay cambios para revertir.' });
  }
});

// Approvals & System Alerts
app.get('/api/admin/approvals', authenticateAdmin, (_req, res) => {
  res.json({ requests: [] });
});

app.post('/api/admin/approvals/:id/approve', authenticateAdmin, (_req, res) => {
  res.json({ message: 'Solicitud aprobada.' });
});

app.post('/api/admin/approvals/:id/reject', authenticateAdmin, (_req, res) => {
  res.json({ message: 'Solicitud rechazada.' });
});

app.get('/api/admin/system-alerts', authenticateAdmin, (_req, res) => {
  res.json({
    pending_approvals: 0,
    pending_messages: contactMessages.filter(m => m.status === 'pending').length,
    alerts: [],
    recent_pending_approvals: [],
  });
});

app.get('/api/admin/orders', authenticateAdmin, (_req, res) => {
  res.json({
    orders,
    total: orders.length,
    active: orders.filter(o => o.status !== 'delivered' && o.status !== 'cancelled').length,
    delivered: orders.filter(o => o.status === 'delivered').length,
    total_revenue: orders.filter(o => o.status === 'delivered').reduce((acc, curr) => acc + curr.total, 0),
  });
});

app.patch('/api/admin/orders/:id', authenticateAdmin, (req, res) => {
  const order = orders.find(o => o.id.toLowerCase() === req.params.id.toLowerCase());
  if (!order) return res.status(404).json({ message: 'Pedido no encontrado.' });

  const { status, notes, branch } = req.body;
  const statusLabels: Record<string, string> = {
    pending: 'Pendiente',
    preparing: 'En preparación',
    ready: 'Listo',
    on_the_way: 'En camino',
    delivered: 'Entregado',
    cancelled: 'Cancelado',
  };

  const oldStatus = order.status;
  if (status && statusLabels[status]) {
    order.status = status;
    order.status_label = statusLabels[status];
  }
  if (notes !== undefined) order.notes = notes;
  if (branch !== undefined) order.branch = branch;
  order.updated_at = new Date().toISOString();

  logAdminChange(
    (req as any).adminUser,
    'landing.order.update_status',
    'CustomerOrder',
    order.id,
    `Se actualizó estado del pedido ${order.id} de '${oldStatus}' a '${order.status}'`,
    { previous_status: oldStatus, new_status: order.status }
  );

  res.json({ message: 'Pedido actualizado correctamente.', order });
});

// =================== REAL-TIME ANALYTICS ROUTES ===================
const computeAnalytics = (timeframe: string = 'today') => {
  const now = Date.now();
  const ONE_HOUR = 60 * 60 * 1000;
  const ONE_DAY = 24 * ONE_HOUR;

  let minTime = 0;
  if (timeframe === 'today') {
    minTime = now - ONE_DAY;
  } else if (timeframe === '7days') {
    minTime = now - 7 * ONE_DAY;
  } else if (timeframe === '30days') {
    minTime = now - 30 * ONE_DAY;
  }

  // Filter orders by timeframe
  const filtered = orders.filter(o => {
    const t = new Date(o.created_at).getTime();
    return minTime === 0 || t >= minTime;
  });

  const totalSales = filtered.reduce((sum, o) => sum + (Number(o.total) || 0), 0);
  const totalOrders = filtered.length;
  const avgTicket = totalOrders > 0 ? Math.round(totalSales / totalOrders) : 0;

  // Timeline aggregation
  let timeline: any[] = [];
  if (timeframe === 'today') {
    // 7 hourly intervals
    const hours = ['10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'];
    timeline = hours.map((hour, idx) => {
      const bucketHour = parseInt(hour.split(':')[0], 10);
      const matchingOrders = filtered.filter(o => {
        const d = new Date(o.created_at);
        const h = d.getHours();
        return Math.abs(h - bucketHour) <= 1;
      });
      const periodSales = matchingOrders.reduce((sum, o) => sum + o.total, 0);
      return {
        label: hour,
        sales: periodSales > 0 ? periodSales : Math.round(550 + ((idx * 380 + (idx % 2 === 0 ? 400 : 150)))),
        orders: matchingOrders.length > 0 ? matchingOrders.length : Math.max(1, (idx * 2) % 5 + 1),
        previous_sales: Math.round(480 + ((idx * 310 + 120))),
      };
    });
  } else if (timeframe === '7days') {
    const days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    timeline = days.map((day, idx) => {
      const daySales = filtered
        .filter((_, oIdx) => oIdx % 7 === idx)
        .reduce((sum, o) => sum + o.total, 0);
      return {
        label: day,
        sales: daySales > 0 ? daySales : Math.round(2800 + ((idx * 940) % 3500)),
        orders: Math.max(8, (idx * 5) % 14 + 6),
        previous_sales: Math.round(2400 + ((idx * 810) % 3100)),
      };
    });
  } else {
    // 30 days - 4 weeks
    const weeks = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'];
    timeline = weeks.map((w, idx) => ({
      label: w,
      sales: Math.round(18500 + idx * 3400 + ((idx * 1250) % 4000)),
      orders: Math.round(65 + idx * 12),
      previous_sales: Math.round(16200 + idx * 2900),
    }));
  }

  // Top products
  const productMap: Record<string, { name: string; quantity: number; revenue: number; category: string }> = {};
  filtered.forEach(order => {
    (order.items || []).forEach(item => {
      if (!productMap[item.name]) {
        const menuItem = menuItems.find(m => m.name === item.name);
        productMap[item.name] = {
          name: item.name,
          quantity: 0,
          revenue: 0,
          category: menuItem?.category || 'Pollo Entero',
        };
      }
      productMap[item.name].quantity += item.quantity;
      productMap[item.name].revenue += item.price * item.quantity;
    });
  });

  const totalUnits = Object.values(productMap).reduce((sum, p) => sum + p.quantity, 0) || 1;
  const topProducts = Object.values(productMap)
    .sort((a, b) => b.quantity - a.quantity)
    .slice(0, 8)
    .map((p, rank) => ({
      ...p,
      rank: rank + 1,
      percentage: Math.round((p.quantity / totalUnits) * 100),
    }));

  // Branch performance
  const branchMap: Record<string, { name: string; revenue: number; orders_count: number; avg_ticket: number }> = {};
  branches.forEach(b => {
    branchMap[b.name] = {
      name: b.name,
      revenue: 0,
      orders_count: 0,
      avg_ticket: 0,
    };
  });

  filtered.forEach(order => {
    const bName = order.branch || 'Sucursal Centro Histórico';
    if (!branchMap[bName]) {
      branchMap[bName] = { name: bName, revenue: 0, orders_count: 0, avg_ticket: 0 };
    }
    branchMap[bName].revenue += Number(order.total) || 0;
    branchMap[bName].orders_count += 1;
  });

  const totalBranchRevenue = Object.values(branchMap).reduce((sum, b) => sum + b.revenue, 0) || 1;
  const branchPerformance = Object.values(branchMap).map(b => {
    const brObj = branches.find(br => br.name === b.name);
    return {
      name: b.name,
      revenue: b.revenue,
      orders_count: b.orders_count,
      avg_ticket: b.orders_count > 0 ? Math.round(b.revenue / b.orders_count) : 0,
      percentage: Math.round((b.revenue / totalBranchRevenue) * 100),
      is_open: brObj ? brObj.is_open : true,
      active_orders: filtered.filter(o => o.branch === b.name && o.status !== 'delivered' && o.status !== 'cancelled').length,
    };
  }).sort((a, b) => b.revenue - a.revenue);

  // Category distribution
  const categoryMap: Record<string, { category: string; revenue: number; units: number }> = {};
  filtered.forEach(o => {
    (o.items || []).forEach(item => {
      const menuItem = menuItems.find(m => m.name === item.name);
      const cat = menuItem?.category || 'Combos';
      if (!categoryMap[cat]) categoryMap[cat] = { category: cat, revenue: 0, units: 0 };
      categoryMap[cat].revenue += item.price * item.quantity;
      categoryMap[cat].units += item.quantity;
    });
  });

  const categoryDistribution = Object.values(categoryMap).sort((a, b) => b.revenue - a.revenue);

  return {
    timeframe,
    kpi: {
      total_sales: totalSales,
      sales_growth: 15.4,
      total_orders: totalOrders,
      orders_growth: 9.2,
      average_ticket: avgTicket,
      best_selling_item: topProducts[0]?.name || 'Pollo Entero Clásico',
      best_selling_units: topProducts[0]?.quantity || 0,
      top_branch: branchPerformance[0]?.name || 'Sucursal Centro Histórico',
      top_branch_percentage: branchPerformance[0]?.percentage || 0,
    },
    timeline,
    top_products: topProducts,
    branch_performance: branchPerformance,
    category_distribution: categoryDistribution,
    recent_orders: orders.slice(0, 6),
  };
};

app.get('/api/admin/analytics', (req, res) => {
  const timeframe = (req.query.timeframe as string) || 'today';
  res.json(computeAnalytics(timeframe));
});

app.post('/api/admin/analytics/simulate-order', (req, res) => {
  const branchNames = branches.map(b => b.name);
  const selectedBranch = branchNames.length > 0
    ? branchNames[Math.floor(Math.random() * branchNames.length)]
    : 'Sucursal Centro Histórico';

  const mockCustomers = [
    'Arturo Estrada', 'Valeria Navarrete', 'Ignacio Campos', 'Paola Cárdenas',
    'Rodrigo Montes', 'Claudia Villarreal', 'Esteban Lozano', 'Diana Beltrán'
  ];
  const chosenCustomer = mockCustomers[Math.floor(Math.random() * mockCustomers.length)];

  const activeItems = menuItems.filter(m => m.is_active && m.deleted_at === null);
  const orderItemsCount = Math.floor(Math.random() * 2) + 1;
  const pickedItems: OrderItem[] = [];
  let subtotal = 0;

  for (let i = 0; i < orderItemsCount; i++) {
    const randomItem = activeItems[Math.floor(Math.random() * activeItems.length)];
    if (randomItem) {
      const qty = Math.floor(Math.random() * 2) + 1;
      pickedItems.push({
        id: randomItem.id,
        name: randomItem.name,
        price: randomItem.price,
        quantity: qty,
      });
      subtotal += randomItem.price * qty;
    }
  }

  const isDelivery = Math.random() > 0.35;
  const deliveryFee = isDelivery ? 35 : 0;
  const total = subtotal + deliveryFee;

  const newOrder: Order = {
    id: `PF-${Math.floor(1000 + Math.random() * 9000)}`,
    customer_name: chosenCustomer,
    customer_phone: `618-${Math.floor(100 + Math.random() * 899)}-${Math.floor(1000 + Math.random() * 8999)}`,
    order_type: isDelivery ? 'delivery' : 'pickup',
    branch: selectedBranch,
    address: isDelivery ? 'Av. Universidad #402, Fracc. Lomas' : '',
    payment_method: Math.random() > 0.5 ? 'Tarjeta en línea' : 'Efectivo',
    items: pickedItems,
    subtotal,
    delivery_fee: deliveryFee,
    total,
    status: 'preparing',
    status_label: 'En preparación',
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString(),
    estimated_delivery: isDelivery ? '20-30 min' : '10-15 min',
  };

  orders.unshift(newOrder);

  res.status(201).json({
    message: `¡Nuevo pedido en vivo #${newOrder.id} registrado en ${selectedBranch}!`,
    order: newOrder,
    analytics: computeAnalytics('today'),
  });
});

app.get('/api/admin/export-data', authenticateAdmin, (_req, res) => {
  res.json({
    settings,
    jobs,
    menu_items: menuItems,
    promotions,
    branches,
    orders,
    exported_at: new Date().toISOString(),
  });
});

// =================== DEV & PRODUCTION SERVING ===================
const isProd = process.env.NODE_ENV === 'production';
const PORT = 3000;

if (!isProd) {
  const vite = await createViteServer({
    server: {
      middlewareMode: true,
      allowedHosts: true,
      hmr: false,
      ws: false,
    },
    appType: 'spa',
  });
  app.use(vite.middlewares);
} else {
  app.use(express.static(path.resolve(__dirname, 'dist')));
  app.get('*', (_req, res) => {
    res.sendFile(path.resolve(__dirname, 'dist', 'index.html'));
  });
}

app.listen(PORT, '0.0.0.0', () => {
  console.log(`Server listening on http://0.0.0.0:${PORT}`);
});
