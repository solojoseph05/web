-- ==============================================================================
-- MIGRACIÓN INICIAL DE BASE DE DATOS (ESQUEMA COMPLETO SQL)
-- Proyecto: Pollo Feliz Durango
-- Motores compatibles: PostgreSQL 14+, MySQL 8+, SQLite 3
-- ==============================================================================

-- 1. Tabla de Usuarios y Roles de Acceso (RBAC)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    password_salt VARCHAR(64) DEFAULT NULL,
    is_admin BOOLEAN DEFAULT TRUE,
    role VARCHAR(50) DEFAULT 'super_admin' CHECK (role IN ('super_admin', 'gerente', 'cocina', 'atencion', 'cajero')),
    api_token VARCHAR(255) DEFAULT NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255) DEFAULT NULL,
    last_login_at TIMESTAMP DEFAULT NULL,
    last_login_ip VARCHAR(45) DEFAULT NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_api_token ON users(api_token);

-- 2. Registro de Auditoría de Seguridad (Security Audit Logs)
CREATE TABLE IF NOT EXISTS security_audit_logs (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    user_email VARCHAR(255),
    action VARCHAR(100) NOT NULL,
    category VARCHAR(50) DEFAULT 'auth',
    severity VARCHAR(20) DEFAULT 'medium',
    target_type VARCHAR(100) NOT NULL,
    target_id VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    changes JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_audit_created ON security_audit_logs(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_audit_action ON security_audit_logs(action);

-- 3. Catálogo del Menú
CREATE TABLE IF NOT EXISTS menu_items (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url TEXT,
    badge VARCHAR(50) DEFAULT '',
    category VARCHAR(100) NOT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    deleted_at TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_menu_category ON menu_items(category);
CREATE INDEX IF NOT EXISTS idx_menu_active ON menu_items(is_active);

-- 4. Pedidos y Despacho
CREATE TABLE IF NOT EXISTS orders (
    id VARCHAR(50) PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    order_type VARCHAR(20) DEFAULT 'delivery',
    branch VARCHAR(255) NOT NULL,
    address TEXT,
    payment_method VARCHAR(100) DEFAULT 'Efectivo',
    notes TEXT,
    subtotal DECIMAL(10, 2) DEFAULT 0.00,
    delivery_fee DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    status_label VARCHAR(100) DEFAULT 'Pendiente',
    estimated_delivery VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_orders_phone ON orders(customer_phone);

-- 5. Ítems de Pedidos
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id VARCHAR(50) REFERENCES orders(id) ON DELETE CASCADE,
    menu_item_id INT REFERENCES menu_items(id) ON DELETE SET NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    notes TEXT
);

-- 6. Promociones
CREATE TABLE IF NOT EXISTS promotions (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url TEXT,
    valid_until DATE,
    code VARCHAR(50),
    badge VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Sucursales
CREATE TABLE IF NOT EXISTS branches (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(50) NOT NULL,
    schedule VARCHAR(255) NOT NULL,
    image_url TEXT,
    map_link TEXT,
    is_open BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. Mensajes de Contacto y Cotizaciones
CREATE TABLE IF NOT EXISTS contact_messages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(255),
    branch VARCHAR(255),
    destination_email VARCHAR(255) DEFAULT 'soporte@pollofelizdgo.com',
    folio VARCHAR(50),
    email_sent BOOLEAN DEFAULT TRUE,
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. Reseñas y Calificaciones
CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NOT NULL,
    branch VARCHAR(255) NOT NULL,
    favorite_dish VARCHAR(255),
    tags TEXT[],
    is_verified BOOLEAN DEFAULT TRUE,
    status VARCHAR(50) DEFAULT 'approved',
    is_featured BOOLEAN DEFAULT FALSE,
    likes_count INT DEFAULT 0,
    admin_reply_author VARCHAR(255),
    admin_reply_message TEXT,
    admin_reply_created_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Configuración del Landing Page (Key-Value)
CREATE TABLE IF NOT EXISTS landing_settings (
    key VARCHAR(100) PRIMARY KEY,
    value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
