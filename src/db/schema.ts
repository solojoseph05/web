/**
 * DEFINICIÓN DE ESQUEMA Y TIPOS DE DATOS DE LA BASE DE DATOS
 * ==========================================================
 * Esquema unificado para Pollo Feliz Durango.
 * Compatible con PostgreSQL, MySQL, SQLite, Supabase y Firestore.
 */

export interface User {
  id: number;
  name: string;
  email: string;
  password: string;
  password_salt?: string;
  is_admin: boolean;
  role: 'super_admin' | 'gerente' | 'cocina' | 'atencion' | 'cajero';
  api_token: string | null;
  two_factor_enabled: boolean;
  two_factor_secret: string | null;
  last_login_at?: string | null;
  last_login_ip?: string | null;
  failed_login_attempts?: number;
  locked_until?: string | null;
  created_at: string;
  updated_at?: string;
}

export interface Session {
  id: string;
  user_id: number;
  token: string;
  ip_address: string;
  user_agent: string;
  payload?: Record<string, any>;
  last_activity: number;
  expires_at: number;
  created_at: string;
}

export interface SecurityAuditLog {
  id: number;
  user_id?: number | null;
  user_email?: string | null;
  action: string;
  category: 'auth' | 'security' | 'order' | 'content' | 'user' | 'system';
  severity: 'low' | 'medium' | 'high' | 'critical';
  target_type: string;
  target_id: string;
  description: string;
  ip_address?: string;
  user_agent?: string;
  changes?: Record<string, any>;
  created_at: string;
}

export interface MenuItem {
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
  updated_at?: string;
}

export interface Job {
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
  updated_at?: string;
}

export interface Promotion {
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
  updated_at?: string;
}

export interface Branch {
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
  updated_at?: string;
}

export interface OrderItem {
  id: number;
  name: string;
  price: number;
  quantity: number;
  notes?: string;
}

export interface Order {
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

export interface ContactMessage {
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
  status: 'pending' | 'in_progress' | 'resolved' | 'archived';
  created_at: string;
}

export interface Review {
  id: number;
  customer_name: string;
  rating: number;
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
  updated_at?: string;
}

export interface LandingSettings {
  hero_title: string;
  hero_subtitle: string;
  hero_description: string;
  hero_badge: string;
  hero_image_url: string;
  hero_cta_primary: string;
  hero_cta_secondary: string;
  stat_1_value: string;
  stat_1_label: string;
  stat_2_value: string;
  stat_2_label: string;
  stat_3_value: string;
  stat_3_label: string;
  menu_section_title: string;
  menu_section_subtitle: string;
  promotions_section_title: string;
  promotions_section_subtitle: string;
  branches_section_title: string;
  branches_section_subtitle: string;
  company_name: string;
  company_phone: string;
  company_email: string;
  company_whatsapp: string;
  company_address: string;
  schedule_weekday: string;
  social_facebook: string;
  social_instagram: string;
  [key: string]: any;
}
