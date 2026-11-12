/**
 * CONTRATO ESTÁNDAR DEL ADAPTADOR DE BASE DE DATOS
 * ================================================
 * Define las operaciones para abstraer el motor de persistencia.
 */

import {
  User,
  Category,
  MenuItem,
  Job,
  JobApplication,
  Promotion,
  Branch,
  Order,
  ContactMessage,
  Review,
  SecurityAuditLog,
  LandingSettings
} from './schema.js';

export interface IDatabaseAdapter {
  name: string;
  isReady: boolean;

  connect(): Promise<void>;
  disconnect(): Promise<void>;
  healthCheck(): Promise<{ status: 'healthy' | 'degraded' | 'down'; latency_ms: number; details?: any }>;

  getUsers(): Promise<User[]>;
  findUserById(id: number): Promise<User | null>;
  findUserByEmail(email: string): Promise<User | null>;
  findUserByToken(token: string): Promise<User | null>;
  createUser(userData: Omit<User, 'id' | 'created_at'>): Promise<User>;
  updateUser(id: number, data: Partial<User>): Promise<User | null>;
  deleteUser(id: number): Promise<boolean>;
  verifyCredentials(email: string, password: string): Promise<User | null>;

  recordAuditLog(log: Omit<SecurityAuditLog, 'id' | 'created_at'>): Promise<SecurityAuditLog>;
  getAuditLogs(limit?: number, offset?: number): Promise<SecurityAuditLog[]>;

  getMenuItems(onlyActive?: boolean): Promise<MenuItem[]>;
  findMenuItemById(id: number): Promise<MenuItem | null>;
  createMenuItem(item: Omit<MenuItem, 'id' | 'created_at'>): Promise<MenuItem>;
  updateMenuItem(id: number, data: Partial<MenuItem>): Promise<MenuItem | null>;
  deleteMenuItem(id: number): Promise<boolean>;

  getCategories?(onlyActive?: boolean): Promise<Category[]>;
  createCategory?(cat: Omit<Category, 'id' | 'created_at'>): Promise<Category>;
  updateCategory?(id: number, data: Partial<Category>): Promise<Category | null>;
  deleteCategory?(id: number): Promise<boolean>;

  getOrders(filters?: { status?: string; phone?: string; search?: string }): Promise<Order[]>;
  findOrderById(id: string): Promise<Order | null>;
  createOrder(order: Omit<Order, 'created_at' | 'updated_at'>): Promise<Order>;
  updateOrderStatus(id: string, status: Order['status'], statusLabel: string): Promise<Order | null>;

  getPromotions(onlyActive?: boolean): Promise<Promotion[]>;
  createPromotion(promo: Omit<Promotion, 'id' | 'created_at'>): Promise<Promotion>;
  updatePromotion(id: number, data: Partial<Promotion>): Promise<Promotion | null>;
  deletePromotion(id: number): Promise<boolean>;

  getBranches(): Promise<Branch[]>;
  updateBranch(id: number, data: Partial<Branch>): Promise<Branch | null>;

  getJobs(onlyActive?: boolean): Promise<Job[]>;
  createJob(job: Omit<Job, 'id' | 'created_at'>): Promise<Job>;
  updateJob(id: number, data: Partial<Job>): Promise<Job | null>;
  deleteJob(id: number): Promise<boolean>;

  getJobApplications?(vacancyId?: number): Promise<JobApplication[]>;
  createJobApplication?(app: Omit<JobApplication, 'id' | 'created_at'>): Promise<JobApplication>;
  updateJobApplicationStatus?(id: number, status: JobApplication['status']): Promise<JobApplication | null>;
  deleteJobApplication?(id: number): Promise<boolean>;

  getReviews(filters?: { branch?: string; rating?: number; status?: string; onlyActive?: boolean }): Promise<Review[]>;
  createReview(review: Omit<Review, 'id' | 'created_at'>): Promise<Review>;
  updateReview(id: number, data: Partial<Review>): Promise<Review | null>;
  likeReview(id: number): Promise<number>;

  getContactMessages(status?: string): Promise<ContactMessage[]>;
  createContactMessage(message: Omit<ContactMessage, 'id' | 'created_at'>): Promise<ContactMessage>;
  updateContactMessageStatus(id: number, status: ContactMessage['status']): Promise<ContactMessage | null>;

  getSettings(): Promise<LandingSettings>;
  updateSettings(data: Partial<LandingSettings>): Promise<LandingSettings>;
}
