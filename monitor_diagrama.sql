

-- Volcando estructura para tabla satindex_monitor.attendances
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timesheet_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `shift` enum('day','night') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in_date` datetime DEFAULT NULL,
  `break_date` datetime DEFAULT NULL,
  `end_break_date` datetime DEFAULT NULL,
  `check_out_date` datetime DEFAULT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_timesheet_id_foreign` (`timesheet_id`),
  KEY `attendances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_timesheet_id_foreign` FOREIGN KEY (`timesheet_id`) REFERENCES `timesheets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para función satindex_monitor.calculate_quote_total
DELIMITER //
CREATE FUNCTION `calculate_quote_total`(quoteId INT) RETURNS decimal(10,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
                DECLARE total DECIMAL(10,2);
                SELECT IFNULL(SUM(ROUND(quantity * unit_price, 2)), 0) INTO total
                FROM quote_details
                WHERE quote_id = quoteId;
                RETURN total;
            END//
DELIMITER ;

-- Volcando estructura para tabla satindex_monitor.clients
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'RUC, DNI, FOREIGN_CARD, PASSPORT',
  `document_number` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Document number',
  `person_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Natural Person, Legal Entity',
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Client business name',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Description of the client',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Contact phone number',
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Contact email address',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.client_project
CREATE TABLE IF NOT EXISTS `client_project` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_project_client_id_foreign` (`client_id`),
  KEY `client_project_project_id_foreign` (`project_id`),
  CONSTRAINT `client_project_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_project_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.compliance
CREATE TABLE IF NOT EXISTS `compliance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `assets` json DEFAULT NULL,
  `maintenance_observations` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `fullname_cliente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_signature` text COLLATE utf8mb4_unicode_ci,
  `employee_signature` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `compliance_project_id_foreign` (`project_id`),
  CONSTRAINT `compliance_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.contact_data
CREATE TABLE IF NOT EXISTS `contact_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_client_id` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_data_sub_client_id_foreign` (`sub_client_id`),
  CONSTRAINT `contact_data_sub_client_id_foreign` FOREIGN KEY (`sub_client_id`) REFERENCES `sub_clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.conversations
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_group` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.conversation_user
CREATE TABLE IF NOT EXISTS `conversation_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversation_user_conversation_id_user_id_unique` (`conversation_id`,`user_id`),
  KEY `conversation_user_user_id_foreign` (`user_id`),
  CONSTRAINT `conversation_user_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.dispatch_guides
CREATE TABLE IF NOT EXISTS `dispatch_guides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_warehouse_id` bigint unsigned NOT NULL,
  `guide_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_origin_id` bigint unsigned DEFAULT NULL,
  `location_destination_id` bigint unsigned DEFAULT NULL,
  `transfer_date` timestamp NULL DEFAULT NULL,
  `store_entry_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispatch_guides_quote_warehouse_id_foreign` (`quote_warehouse_id`),
  KEY `dispatch_guides_location_origin_id_foreign` (`location_origin_id`),
  KEY `dispatch_guides_location_destination_id_foreign` (`location_destination_id`),
  CONSTRAINT `dispatch_guides_location_destination_id_foreign` FOREIGN KEY (`location_destination_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispatch_guides_location_origin_id_foreign` FOREIGN KEY (`location_origin_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispatch_guides_quote_warehouse_id_foreign` FOREIGN KEY (`quote_warehouse_id`) REFERENCES `quote_warehouse` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.dispatch_transactions
CREATE TABLE IF NOT EXISTS `dispatch_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_warehouse_id` bigint unsigned NOT NULL,
  `project_requirement_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `is_external_purchase` tinyint(1) NOT NULL DEFAULT '0',
  `additional_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tool_unit_id` bigint unsigned DEFAULT NULL,
  `dispatch_guide_id` bigint unsigned DEFAULT NULL,
  `price_unit` decimal(10,2) DEFAULT NULL COMMENT 'Costo real unitario de compra',
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre del comercio/proveedor de la compra',
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'N° Boleta/Factura si aplica',
  PRIMARY KEY (`id`),
  KEY `dispatch_transactions_quote_warehouse_id_foreign` (`quote_warehouse_id`),
  KEY `dispatch_transactions_project_requirement_id_foreign` (`project_requirement_id`),
  KEY `dispatch_transactions_employee_id_foreign` (`employee_id`),
  KEY `dispatch_transactions_tool_unit_id_foreign` (`tool_unit_id`),
  KEY `dispatch_transactions_dispatch_guide_id_foreign` (`dispatch_guide_id`),
  CONSTRAINT `dispatch_transactions_dispatch_guide_id_foreign` FOREIGN KEY (`dispatch_guide_id`) REFERENCES `dispatch_guides` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispatch_transactions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispatch_transactions_project_requirement_id_foreign` FOREIGN KEY (`project_requirement_id`) REFERENCES `project_requirements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispatch_transactions_quote_warehouse_id_foreign` FOREIGN KEY (`quote_warehouse_id`) REFERENCES `quote_warehouse` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispatch_transactions_tool_unit_id_foreign` FOREIGN KEY (`tool_unit_id`) REFERENCES `tool_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.employees
CREATE TABLE IF NOT EXISTS `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_type` enum('DNI','PASAPORTE','CARNET DE EXTRANJERIA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DNI',
  `document_number` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_contract` date DEFAULT NULL,
  `date_birth` date DEFAULT NULL,
  `sex` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_id` bigint unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employees_position_id_foreign` (`position_id`),
  CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.employee_project
CREATE TABLE IF NOT EXISTS `employee_project` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_project_project_id_foreign` (`project_id`),
  KEY `employee_project_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_project_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_project_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.exports
CREATE TABLE IF NOT EXISTS `exports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exporter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exports_user_id_foreign` (`user_id`),
  CONSTRAINT `exports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.failed_import_rows
CREATE TABLE IF NOT EXISTS `failed_import_rows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data` json NOT NULL,
  `import_id` bigint unsigned NOT NULL,
  `validation_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_import_rows_import_id_foreign` (`import_id`),
  CONSTRAINT `failed_import_rows_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.imports
CREATE TABLE IF NOT EXISTS `imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imports_user_id_foreign` (`user_id`),
  CONSTRAINT `imports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.locations
CREATE TABLE IF NOT EXISTS `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_user_id_foreign` (`user_id`),
  KEY `messages_conversation_id_created_at_index` (`conversation_id`,`created_at`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.photos
CREATE TABLE IF NOT EXISTS `photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_report_id` bigint unsigned NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `before_work_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `before_work_descripcion` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `photos_work_report_id_foreign` (`work_report_id`),
  CONSTRAINT `photos_work_report_id_foreign` FOREIGN KEY (`work_report_id`) REFERENCES `work_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.positions
CREATE TABLE IF NOT EXISTS `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.pricelists
CREATE TABLE IF NOT EXISTS `pricelists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sat_line` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sat_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `price_type_id` bigint unsigned DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pricelists_unit_id_foreign` (`unit_id`),
  KEY `pricelists_price_type_id_foreign` (`price_type_id`),
  KEY `pricelists_sat_line_index` (`sat_line`),
  FULLTEXT KEY `pricelists_sat_description_fulltext` (`sat_description`),
  CONSTRAINT `pricelists_price_type_id_foreign` FOREIGN KEY (`price_type_id`) REFERENCES `price_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pricelists_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1470 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.price_types
CREATE TABLE IF NOT EXISTS `price_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.projects
CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Project name',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `requested_at` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `quote_id` bigint unsigned DEFAULT NULL,
  `sub_client_id` bigint unsigned DEFAULT NULL,
  `service_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `work_order_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'OT / Orden de Trabajo',
  `service_start_date` date DEFAULT NULL,
  `service_end_date` date DEFAULT NULL,
  `service_days` int DEFAULT NULL COMMENT 'Días calculados',
  `task_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'OPEX / CAPEX',
  `has_report` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fracttal_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_order` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `migo_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `quote_sent_at` timestamp NULL DEFAULT NULL,
  `quote_approved_at` timestamp NULL DEFAULT NULL,
  `wo_review_at` timestamp NULL DEFAULT NULL,
  `wo_completed_at` timestamp NULL DEFAULT NULL,
  `days_to_completion` int DEFAULT NULL,
  `final_comments` text COLLATE utf8mb4_unicode_ci,
  `employee_id` bigint unsigned DEFAULT NULL,
  `supervisor_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_quote_id_foreign` (`quote_id`),
  KEY `projects_sub_client_id_foreign` (`sub_client_id`),
  KEY `projects_employee_id_foreign` (`employee_id`),
  KEY `projects_supervisor_id_foreign` (`supervisor_id`),
  CONSTRAINT `projects_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_sub_client_id_foreign` FOREIGN KEY (`sub_client_id`) REFERENCES `sub_clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.project_consumptions
CREATE TABLE IF NOT EXISTS `project_consumptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `quote_warehouse_detail_id` bigint unsigned NOT NULL,
  `work_report_id` bigint unsigned DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `consumed_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_consumptions_project_id_foreign` (`project_id`),
  KEY `project_consumptions_quote_warehouse_detail_id_foreign` (`quote_warehouse_detail_id`),
  KEY `project_consumptions_work_report_id_foreign` (`work_report_id`),
  CONSTRAINT `project_consumptions_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_consumptions_quote_warehouse_detail_id_foreign` FOREIGN KEY (`quote_warehouse_detail_id`) REFERENCES `quote_warehouse_details` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_consumptions_work_report_id_foreign` FOREIGN KEY (`work_report_id`) REFERENCES `work_reports` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.project_requirements
CREATE TABLE IF NOT EXISTS `project_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `requirementable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Material',
  `requirementable_id` bigint unsigned NOT NULL,
  `quantity` decimal(12,2) unsigned NOT NULL,
  `price_unit` decimal(12,2) unsigned NOT NULL,
  `comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_requirements_project_id_foreign` (`project_id`),
  KEY `reqable_index` (`requirementable_type`,`requirementable_id`),
  KEY `project_requirements_unit_id_foreign` (`unit_id`),
  CONSTRAINT `project_requirements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_requirements_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.project_tools
CREATE TABLE IF NOT EXISTS `project_tools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `tool_unit_id` bigint unsigned NOT NULL,
  `assigned_at` date DEFAULT NULL,
  `returned_at` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_tools_project_id_foreign` (`project_id`),
  KEY `project_tools_tool_unit_id_foreign` (`tool_unit_id`),
  CONSTRAINT `project_tools_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_tools_tool_unit_id_foreign` FOREIGN KEY (`tool_unit_id`) REFERENCES `tool_units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.push_subscriptions
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscribable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscribable_id` bigint unsigned NOT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `public_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_encoding` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`),
  KEY `push_subscriptions_subscribable_morph_idx` (`subscribable_type`,`subscribable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.quotes
CREATE TABLE IF NOT EXISTS `quotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned DEFAULT NULL,
  `request_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `sub_client_id` bigint unsigned DEFAULT NULL,
  `quote_category_id` bigint unsigned DEFAULT NULL,
  `energy_sci_manager` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ceco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pendiente','Enviado','Aprobado','Anulado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote_date` date DEFAULT NULL,
  `execution_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotes_employee_id_foreign` (`employee_id`),
  KEY `quotes_sub_client_id_foreign` (`sub_client_id`),
  KEY `quotes_quote_category_id_foreign` (`quote_category_id`),
  KEY `quotes_project_id_foreign` (`project_id`),
  CONSTRAINT `quotes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotes_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotes_quote_category_id_foreign` FOREIGN KEY (`quote_category_id`) REFERENCES `quote_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotes_sub_client_id_foreign` FOREIGN KEY (`sub_client_id`) REFERENCES `sub_clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.quote_categories
CREATE TABLE IF NOT EXISTS `quote_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.quote_details
CREATE TABLE IF NOT EXISTS `quote_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` bigint unsigned NOT NULL,
  `quote_group_id` bigint unsigned DEFAULT NULL,
  `line` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pricelist_id` bigint unsigned DEFAULT NULL,
  `item_type` enum('SERVICIO','VIATICOS','SUMINISTRO','MANO DE OBRA','CONSUMIBLE','TRANSPORTE','OTROS') COLLATE utf8mb4_unicode_ci DEFAULT 'SERVICIO',
  `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `quote_details_quote_id_line_index` (`quote_id`,`pricelist_id`),
  KEY `quote_details_pricelist_id_foreign` (`pricelist_id`),
  KEY `quote_details_quote_group_id_foreign` (`quote_group_id`),
  CONSTRAINT `quote_details_pricelist_id_foreign` FOREIGN KEY (`pricelist_id`) REFERENCES `pricelists` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quote_details_quote_group_id_foreign` FOREIGN KEY (`quote_group_id`) REFERENCES `quote_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quote_details_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1078 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.quote_groups
CREATE TABLE IF NOT EXISTS `quote_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_groups_quote_id_foreign` (`quote_id`),
  CONSTRAINT `quote_groups_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.quote_warehouse
CREATE TABLE IF NOT EXISTS `quote_warehouse` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attended_at` timestamp NULL DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_warehouse_quote_id_foreign` (`quote_id`),
  KEY `quote_warehouse_user_id_foreign` (`employee_id`),
  KEY `quote_warehouse_status_index` (`status`),
  CONSTRAINT `quote_warehouse_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quote_warehouse_user_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.quote_warehouse_details
CREATE TABLE IF NOT EXISTS `quote_warehouse_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_warehouse_id` bigint unsigned NOT NULL,
  `project_requirement_id` bigint unsigned DEFAULT NULL,
  `attended_quantity` decimal(10,2) NOT NULL,
  `is_external_purchase` tinyint(1) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `additional_cost` decimal(12,2) DEFAULT '0.00',
  `cost_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tool_unit_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_warehouse_details_quote_warehouse_id_foreign` (`quote_warehouse_id`),
  KEY `quote_warehouse_details_project_requirement_id_foreign` (`project_requirement_id`),
  KEY `quote_warehouse_details_tool_unit_id_foreign` (`tool_unit_id`),
  CONSTRAINT `quote_warehouse_details_project_requirement_id_foreign` FOREIGN KEY (`project_requirement_id`) REFERENCES `project_requirements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quote_warehouse_details_quote_warehouse_id_foreign` FOREIGN KEY (`quote_warehouse_id`) REFERENCES `quote_warehouse` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quote_warehouse_details_tool_unit_id_foreign` FOREIGN KEY (`tool_unit_id`) REFERENCES `tool_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.requests
CREATE TABLE IF NOT EXISTS `requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_client_id` bigint unsigned DEFAULT NULL,
  `cotizador_id` bigint unsigned DEFAULT NULL,
  `supervisor_id` bigint unsigned DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','attended','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requests_request_number_unique` (`request_number`),
  KEY `requests_sub_client_id_foreign` (`sub_client_id`),
  KEY `requests_cotizador_id_foreign` (`cotizador_id`),
  KEY `requests_supervisor_id_foreign` (`supervisor_id`),
  CONSTRAINT `requests_cotizador_id_foreign` FOREIGN KEY (`cotizador_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `requests_sub_client_id_foreign` FOREIGN KEY (`sub_client_id`) REFERENCES `sub_clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `requests_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.request_visit
CREATE TABLE IF NOT EXISTS `request_visit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint unsigned NOT NULL,
  `visit_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_visit_request_id_foreign` (`request_id`),
  KEY `request_visit_visit_id_foreign` (`visit_id`),
  CONSTRAINT `request_visit_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `request_visit_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.requirements
CREATE TABLE IF NOT EXISTS `requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requirement_type_id` bigint unsigned NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requirements_unit_id_foreign` (`unit_id`),
  KEY `requirements_requirement_type_id_product_description_index` (`requirement_type_id`,`product_description`),
  KEY `requirements_product_description_index` (`product_description`),
  CONSTRAINT `requirements_requirement_type_id_foreign` FOREIGN KEY (`requirement_type_id`) REFERENCES `requirement_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `requirements_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.requirement_types
CREATE TABLE IF NOT EXISTS `requirement_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_reusable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.sub_clients
CREATE TABLE IF NOT EXISTS `sub_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL COMMENT 'Reference to client',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sub-client name',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ceco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_clients_client_id_index` (`client_id`),
  CONSTRAINT `sub_clients_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=281 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.timesheets
CREATE TABLE IF NOT EXISTS `timesheets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `shift` enum('day','night') COLLATE utf8mb4_unicode_ci NOT NULL,
  `check_in_date` datetime NOT NULL,
  `break_date` datetime DEFAULT NULL,
  `end_break_date` datetime DEFAULT NULL,
  `check_out_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timesheets_employee_id_foreign` (`employee_id`),
  KEY `timesheets_project_id_foreign` (`project_id`),
  CONSTRAINT `timesheets_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `timesheets_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.tools
CREATE TABLE IF NOT EXISTS `tools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Herramienta',
  `tool_brand_id` bigint unsigned DEFAULT NULL,
  `tool_category_id` bigint unsigned DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tools_tool_brand_id_foreign` (`tool_brand_id`),
  KEY `idx_tools_name` (`name`),
  KEY `idx_tools_category` (`tool_category_id`),
  CONSTRAINT `tools_tool_brand_id_foreign` FOREIGN KEY (`tool_brand_id`) REFERENCES `tool_brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tools_tool_category_id_foreign` FOREIGN KEY (`tool_category_id`) REFERENCES `tool_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.tool_brands
CREATE TABLE IF NOT EXISTS `tool_brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.tool_categories
CREATE TABLE IF NOT EXISTS `tool_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.tool_units
CREATE TABLE IF NOT EXISTS `tool_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tool_id` bigint unsigned NOT NULL,
  `internal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certification_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certification_expiry` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tool_units_internal_code` (`internal_code`),
  KEY `idx_tool_units_serial` (`serial_number`),
  KEY `idx_tool_units_status` (`status`),
  KEY `idx_tool_units_tool_id` (`tool_id`),
  KEY `idx_tool_units_cert_expiry` (`certification_expiry`),
  CONSTRAINT `tool_units_tool_id_foreign` FOREIGN KEY (`tool_id`) REFERENCES `tools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.units
CREATE TABLE IF NOT EXISTS `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'User active status',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  CONSTRAINT `users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.visits
CREATE TABLE IF NOT EXISTS `visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned DEFAULT NULL,
  `inspector_id` bigint unsigned DEFAULT NULL,
  `quoted_by_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suggestions` text COLLATE utf8mb4_unicode_ci,
  `tools` text COLLATE utf8mb4_unicode_ci,
  `materials` text COLLATE utf8mb4_unicode_ci,
  `entry_time` time DEFAULT NULL,
  `exit_time` time DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visits_employee_id_foreign` (`inspector_id`),
  KEY `visits_project_id_foreign` (`project_id`),
  KEY `visits_quoted_by_id_foreign` (`quoted_by_id`),
  CONSTRAINT `visits_employee_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visits_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visits_quoted_by_id_foreign` FOREIGN KEY (`quoted_by_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.visit_photos
CREATE TABLE IF NOT EXISTS `visit_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visit_report_id` bigint unsigned NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_photos_visit_report_id_foreign` (`visit_report_id`),
  CONSTRAINT `visit_photos_visit_report_id_foreign` FOREIGN KEY (`visit_report_id`) REFERENCES `visit_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.visit_reports
CREATE TABLE IF NOT EXISTS `visit_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `sub_client_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suggestions` text COLLATE utf8mb4_unicode_ci,
  `conclusions` text COLLATE utf8mb4_unicode_ci,
  `work_to_do` text COLLATE utf8mb4_unicode_ci,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_reports_employee_id_foreign` (`employee_id`),
  KEY `visit_reports_project_id_foreign` (`project_id`),
  KEY `visit_reports_sub_client_id_foreign` (`sub_client_id`),
  CONSTRAINT `visit_reports_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visit_reports_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visit_reports_sub_client_id_foreign` FOREIGN KEY (`sub_client_id`) REFERENCES `sub_clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.warehouses
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` text COLLATE utf8mb4_unicode_ci,
  `manager_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_manager_id_foreign` (`manager_id`),
  CONSTRAINT `warehouses_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.work_orders
CREATE TABLE IF NOT EXISTS `work_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint unsigned DEFAULT NULL,
  `work_order_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task` enum('OPEX','CAPEX') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `fracttal_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `review_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `purchase_order` int DEFAULT NULL,
  `migo` int DEFAULT NULL,
  `work_order_status` enum('Quoted','Approved','In Progress','Completed','Invoiced') COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_order_comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_orders_request_id_foreign` (`request_id`),
  CONSTRAINT `work_orders_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla satindex_monitor.work_reports
CREATE TABLE IF NOT EXISTS `work_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `compliance_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_date` date DEFAULT NULL,
  `work_to_do` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `suggestions` longtext COLLATE utf8mb4_unicode_ci,
  `personnel` json DEFAULT NULL,
  `materials` json DEFAULT NULL,
  `conclusions` text COLLATE utf8mb4_unicode_ci,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_reports_employee_id_foreign` (`employee_id`),
  KEY `work_reports_project_id_foreign` (`project_id`),
  KEY `work_reports_compliance_id_foreign` (`compliance_id`),
  CONSTRAINT `work_reports_compliance_id_foreign` FOREIGN KEY (`compliance_id`) REFERENCES `compliance` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_reports_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `work_reports_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
