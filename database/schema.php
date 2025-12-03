-- =====================================================
-- SCRIPT SQL: Convertidor de Monedas
-- Basado en tu práctica anterior
-- =====================================================

-- Eliminar BD si existe
DROP DATABASE IF EXISTS convertidor_monedas;

-- Crear BD
CREATE DATABASE convertidor_monedas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE convertidor_monedas;

-- =====================================================
-- TABLA: Tasas de Cambio
-- Reemplaza el array que tenías en backend.php
-- =====================================================
CREATE TABLE tasas_cambio (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único',
    moneda_origen VARCHAR(3) NOT NULL COMMENT 'Código ISO (USD, EUR, etc)',
    moneda_destino VARCHAR(3) NOT NULL COMMENT 'Código ISO destino',
    tasa DECIMAL(10, 6) NOT NULL COMMENT 'Tasa de conversión',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para búsquedas rápidas
    UNIQUE KEY uk_pares (moneda_origen, moneda_destino),
    INDEX idx_origen (moneda_origen),
    INDEX idx_destino (moneda_destino)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR TASAS DE CAMBIO
-- Estos son los mismos valores que tenías en el array de backend.php
-- =====================================================

-- USD como base (1.0)
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('USD', 'USD', 1.0),
('USD', 'EUR', 0.92),
('USD', 'CRC', 520.5),
('USD', 'MXN', 17.05),
('USD', 'BRL', 4.97),
('USD', 'CAD', 1.32),
('USD', 'COP', 3850.0),
('USD', 'ARS', 850.0);

-- EUR como origen
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('EUR', 'USD', 1.087),
('EUR', 'EUR', 1.0),
('EUR', 'CRC', 565.76),
('EUR', 'MXN', 18.53),
('EUR', 'BRL', 5.40),
('EUR', 'CAD', 1.43),
('EUR', 'COP', 4184.0),
('EUR', 'ARS', 924.0);

-- CRC como origen
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('CRC', 'USD', 0.00192),
('CRC', 'EUR', 0.00177),
('CRC', 'CRC', 1.0),
('CRC', 'MXN', 0.0328),
('CRC', 'BRL', 0.00955),
('CRC', 'CAD', 0.00254),
('CRC', 'COP', 7.40),
('CRC', 'ARS', 1.63);

-- MXN como origen
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('MXN', 'USD', 0.0586),
('MXN', 'EUR', 0.0539),
('MXN', 'CRC', 30.51),
('MXN', 'MXN', 1.0),
('MXN', 'BRL', 0.291),
('MXN', 'CAD', 0.0774),
('MXN', 'COP', 225.8),
('MXN', 'ARS', 49.85);

-- BRL como origen
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('BRL', 'USD', 0.201),
('BRL', 'EUR', 0.185),
('BRL', 'CRC', 104.7),
('BRL', 'MXN', 3.43),
('BRL', 'BRL', 1.0),
('BRL', 'CAD', 0.265),
('BRL', 'COP', 775.8),
('BRL', 'ARS', 171.2);

-- CAD como origen
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('CAD', 'USD', 0.758),
('CAD', 'EUR', 0.698),
('CAD', 'CRC', 395.0),
('CAD', 'MXN', 12.91),
('CAD', 'BRL', 3.77),
('CAD', 'CAD', 1.0),
('CAD', 'COP', 2924.0),
('CAD', 'ARS', 645.0);

-- COP como origen
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('COP', 'USD', 0.000260),
('COP', 'EUR', 0.000239),
('COP', 'CRC', 0.135),
('COP', 'MXN', 0.00443),
('COP', 'BRL', 0.00129),
('COP', 'CAD', 0.000342),
('COP', 'COP', 1.0),
('COP', 'ARS', 0.221);

-- ARS como origen
INSERT INTO tasas_cambio (moneda_origen, moneda_destino, tasa) VALUES
('ARS', 'USD', 0.00118),
('ARS', 'EUR', 0.00108),
('ARS', 'CRC', 0.613),
('ARS', 'MXN', 0.0201),
('ARS', 'BRL', 0.0058),
('ARS', 'CAD', 0.00155),
('ARS', 'COP', 4.52),
('ARS', 'ARS', 1.0);

-- =====================================================
-- TABLA: Conversiones (Historial - Nuevo en Práctica #4)
-- Registra cada conversión realizada
-- =====================================================
CREATE TABLE conversiones (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único',
    moneda_origen VARCHAR(3) NOT NULL,
    moneda_destino VARCHAR(3) NOT NULL,
    cantidad_origen DECIMAL(15, 2) NOT NULL,
    cantidad_destino DECIMAL(15, 2) NOT NULL,
    tasa_aplicada DECIMAL(10, 6) NOT NULL,
    fecha_conversion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices para búsquedas
    INDEX idx_fecha (fecha_conversion),
    INDEX idx_monedas (moneda_origen, moneda_destino)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICACIÓN: Ver datos creados
-- =====================================================
SELECT 'Base de datos creada exitosamente' as Mensaje;
SELECT COUNT(*) as 'Pares de Monedas en la BD' FROM tasas_cambio;