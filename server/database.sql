-- ============================================================
-- Asmira OPS Pilot - MariaDB Veritabanı Yapısı
-- Versiyon: 1.0
-- Uyumlu: MariaDB 10.3+ / MySQL 5.7+
-- Karakter Seti: utf8mb4 (Türkçe karakter desteği)
-- ============================================================
-- KURULUM:
-- 1. MariaDB'de veritabanı oluşturun:
--    CREATE DATABASE asmira_ops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Bu dosyayı içe aktarın:
--    mysql -u kullanici -p asmira_ops < database.sql
-- 3. Veya phpMyAdmin/Adminer ile SQL olarak çalıştırın
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ============================================================
-- 1. KULLANICILAR (users)
-- Sistem kullanıcıları ve yetkileri
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`         VARCHAR(50)  NOT NULL,
    `username`   VARCHAR(100) NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `name`       VARCHAR(255) NOT NULL,
    `role`       VARCHAR(20)  NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sistem kullanıcıları';

-- ============================================================
-- 2. ÇEKİCİLER (trucks)
-- Asmira ve tedarikçi firma çekicileri
-- ============================================================
CREATE TABLE IF NOT EXISTS `trucks` (
    `id`         VARCHAR(50)  NOT NULL,
    `plate`      VARCHAR(50)  NOT NULL,
    `category`   VARCHAR(50)  NOT NULL DEFAULT 'asmira',
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_trucks_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Çekici araçlar';

-- ============================================================
-- 3. DORSELER (trailers)
-- Asmira ve tedarikçi firma dorseleri
-- ============================================================
CREATE TABLE IF NOT EXISTS `trailers` (
    `id`         VARCHAR(50)  NOT NULL,
    `plate`      VARCHAR(50)  NOT NULL,
    `category`   VARCHAR(50)  NOT NULL DEFAULT 'asmira',
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_trailers_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Dorse araçlar';

-- ============================================================
-- 4. ARAÇ SETLERİ (vehicle_sets)
-- Çekici + dorse eşleşmeleri
-- ============================================================
CREATE TABLE IF NOT EXISTS `vehicle_sets` (
    `id`         VARCHAR(50) NOT NULL,
    `truck_id`   VARCHAR(50) NOT NULL,
    `trailer_id` VARCHAR(50) NOT NULL,
    `category`   VARCHAR(50) NOT NULL DEFAULT 'asmira',
    PRIMARY KEY (`id`),
    KEY `idx_vsets_truck` (`truck_id`),
    KEY `idx_vsets_trailer` (`trailer_id`),
    CONSTRAINT `fk_vsets_truck`   FOREIGN KEY (`truck_id`)   REFERENCES `trucks`(`id`)   ON DELETE CASCADE,
    CONSTRAINT `fk_vsets_trailer` FOREIGN KEY (`trailer_id`) REFERENCES `trailers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Çekici-dorse eşleşmeleri';

-- ============================================================
-- 5. ARAÇ EVRAKLARI (vehicle_documents)
-- Çekici ve dorse evrakları (ruhsat, sigorta, muayene vb.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `vehicle_documents` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `owner_id`    VARCHAR(50)  NOT NULL,
    `owner_type`  VARCHAR(20)  NOT NULL COMMENT 'truck veya trailer',
    `doc_type`    VARCHAR(100) NOT NULL,
    `label`       VARCHAR(255) NOT NULL,
    `file_name`   VARCHAR(255) DEFAULT NULL,
    `file_path`   VARCHAR(500) DEFAULT NULL,
    `expiry_date` DATE         DEFAULT NULL,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_vdocs_owner` (`owner_id`, `owner_type`),
    KEY `idx_vdocs_expiry` (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Araç evrakları (çekici + dorse)';

-- ============================================================
-- 6. ŞOFÖRLER (drivers)
-- Firma şoför bilgileri
-- ============================================================
CREATE TABLE IF NOT EXISTS `drivers` (
    `id`         VARCHAR(50)  NOT NULL,
    `name`       VARCHAR(255) NOT NULL,
    `tc_no`      VARCHAR(20)  NOT NULL,
    `phone`      VARCHAR(30)  NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_drivers_tcno` (`tc_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Şoför bilgileri';

-- ============================================================
-- 7. ŞOFÖR EVRAKLARI (driver_documents)
-- Şoför kişisel evrakları (ehliyet, SRC, sağlık vb.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `driver_documents` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `driver_id`   VARCHAR(50)  NOT NULL,
    `doc_type`    VARCHAR(100) NOT NULL,
    `label`       VARCHAR(255) NOT NULL,
    `file_name`   VARCHAR(255) DEFAULT NULL,
    `file_path`   VARCHAR(500) DEFAULT NULL,
    `expiry_date` DATE         DEFAULT NULL,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ddocs_driver` (`driver_id`),
    KEY `idx_ddocs_expiry` (`expiry_date`),
    CONSTRAINT `fk_ddocs_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Şoför evrakları';

-- ============================================================
-- 8. OPERASYONLAR (operations)
-- Yakıt ikmal operasyonları
-- ============================================================
CREATE TABLE IF NOT EXISTS `operations` (
    `id`            VARCHAR(50)    NOT NULL,
    `vessel_name`   VARCHAR(255)   NOT NULL,
    `vessel_type`   VARCHAR(20)    NOT NULL DEFAULT 'ship',
    `imo_number`    VARCHAR(50)    DEFAULT NULL,
    `quantity`      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `unit`          VARCHAR(10)    NOT NULL DEFAULT 'MT',
    `loading_place` VARCHAR(255)   DEFAULT NULL,
    `port`          VARCHAR(255)   NOT NULL,
    `date`          DATE           NOT NULL,
    `status`        VARCHAR(20)    NOT NULL DEFAULT 'planned',
    `driver_name`   VARCHAR(255)   DEFAULT '',
    `driver_phone`  VARCHAR(50)    DEFAULT '',
    `agent_note`    TEXT           DEFAULT NULL,
    `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ops_date` (`date`),
    KEY `idx_ops_status` (`status`),
    KEY `idx_ops_port` (`port`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Yakıt ikmal operasyonları';

-- ============================================================
-- 9. DİLEKÇE KATEGORİLERİ (petition_categories)
-- Dilekçe/belge kategori tanımları
-- ============================================================
CREATE TABLE IF NOT EXISTS `petition_categories` (
    `id`          VARCHAR(50)  NOT NULL,
    `title`       VARCHAR(255) NOT NULL,
    `description` TEXT         DEFAULT NULL,
    `icon`        VARCHAR(50)  DEFAULT 'FileText',
    `slug`        VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_petcat_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Dilekçe kategorileri';

-- ============================================================
-- 10. DİLEKÇE ŞABLONLARI (petition_templates)
-- Dilekçe metin şablonları
-- ============================================================
CREATE TABLE IF NOT EXISTS `petition_templates` (
    `id`           VARCHAR(50)  NOT NULL,
    `short_name`   VARCHAR(100) NOT NULL,
    `name`         VARCHAR(255) NOT NULL,
    `default_text` TEXT         DEFAULT NULL,
    `category`     VARCHAR(100) NOT NULL,
    `is_default`   TINYINT(1)   DEFAULT 0,
    `created_at`   BIGINT       DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_tpl_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Dilekçe şablonları';

-- ============================================================
-- 11. TEDARİKÇİ FİRMALARI (supplier_data)
-- Tedarikçi firma verileri (JSON formatında)
-- ============================================================
CREATE TABLE IF NOT EXISTS `supplier_data` (
    `id`         INT       NOT NULL DEFAULT 1,
    `data`       LONGTEXT  NOT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tedarikçi firma verileri (JSON)';

-- ============================================================
-- VARSAYILAN VERİLER
-- ============================================================

-- Varsayılan admin kullanıcısı (şifre: 123)
-- NOT: İlk girişte şifre otomatik bcrypt hash'e yükseltilir
INSERT IGNORE INTO `users` (`id`, `username`, `password`, `name`, `role`)
VALUES ('user_1', 'asmira', '123', 'Asmira Operasyon', 'admin');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- KURULUM TAMAMLANDI
-- Giriş: kullanıcı adı: asmira / şifre: 123
-- ============================================================
