-- ==============================================================================
-- DATOS INICIALES Y SEMILLA DE LA BASE DE DATOS (SEED DATA)
-- Proyecto: Pollo Feliz Durango
-- ==============================================================================

-- 1. Inserción de Usuarios Administrativos con Roles
INSERT INTO users (id, name, email, password, is_admin, role, api_token, created_at)
VALUES
(1, 'Super Administrador', 'admin@pollofeliz.com', 'admin123456', TRUE, 'super_admin', 'default-admin-token-12345', NOW()),
(2, 'Lic. Roberto Garza', 'gerente@pollofeliz.com', 'gerente123', TRUE, 'gerente', 'token-gerente-12345', NOW()),
(3, 'Chef Manuel Parra', 'cocina@pollofeliz.com', 'cocina123', TRUE, 'cocina', 'token-cocina-12345', NOW()),
(4, 'Valeria Soto', 'soporte@pollofeliz.com', 'soporte123', TRUE, 'atencion', 'token-soporte-12345', NOW()),
(5, 'Andrea Morales', 'cajero@pollofeliz.com', 'cajero123', TRUE, 'cajero', 'token-cajero-12345', NOW())
ON CONFLICT (email) DO NOTHING;

-- 2. Sucursales de Pollo Feliz Durango
INSERT INTO branches (id, name, address, phone, schedule, image_url, map_link, is_open, sort_order)
VALUES
(1, 'Sucursal Centro Histórico', 'Av. 5 de Mayo #123, Colonia Centro, Durango', '555-123-4567', 'Lun - Dom: 10:00 AM - 10:00 PM', '', 'https://maps.google.com/?q=Durango', TRUE, 1),
(2, 'Sucursal Jardines', 'Av. Las Rosas #456, Fracc. Jardines, Durango', '555-987-6543', 'Lun - Dom: 11:00 AM - 11:00 PM', '', 'https://maps.google.com/?q=Durango', TRUE, 2),
(3, 'Sucursal Coyoacán', 'Calle Hidalgo #789, Durango', '555-456-7890', 'Lun - Vie: 12:00 PM - 9:00 PM', '', 'https://maps.google.com/?q=Durango', TRUE, 3)
ON CONFLICT (id) DO NOTHING;

-- 3. Promociones Principales
INSERT INTO promotions (id, title, description, valid_until, code, badge, is_active, sort_order)
VALUES
(1, '2x1 en Pollo Entero', 'Lleva 2 pollos enteros por el precio de 1. ¡Válido todos los martes!', '2026-12-31', 'POLLO2X1', 'Oferta Especial', TRUE, 1),
(2, 'Paquete Familiar Especial', 'Pollo entero + 4 complementos + refresco familiar a precio especial', '2026-12-31', 'FAMILIA30', 'Más Vendido', TRUE, 2),
(3, 'Combo Estudiante', 'Medio pollo + papas + refresco. ¡Presenta tu credencial!', '2026-12-31', 'ESTUDIANTE', 'Promoción Especial', TRUE, 3)
ON CONFLICT (id) DO NOTHING;
