-- ============================================================================
-- KhajaPOS - Fix script for: "Base table or view not found: menu_items"
-- ----------------------------------------------------------------------------
-- If you CAN run PHP commands, the RECOMMENDED way is:
--
--     php artisan migrate:fresh --seed
--
-- (this rebuilds every table and seeds only the single admin account).
--
-- Use THIS SQL file only when you cannot run `php artisan migrate` (e.g. you
-- can only use phpMyAdmin). Run the whole file once against your database.
-- If a statement errors because it was already applied (e.g. a table/column
-- already exists), you can ignore that specific error and continue.
-- ============================================================================

USE restaurant_pos;

-- ----------------------------------------------------------------------------
-- 1) Create the new `menu_items` table (dishes/drinks - NO stock tracking)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'Piece',
  `cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_items_sku_unique` (`sku`),
  KEY `menu_items_category_id_foreign` (`category_id`),
  KEY `menu_items_name_index` (`name`),
  KEY `menu_items_is_available_index` (`is_available`),
  CONSTRAINT `menu_items_category_id_foreign`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2) `sale_items`: drop the old inventory link, add the new menu-item link.
--    (Menu items are sold with quantity only - no stock is deducted.)
--    Ignore the "Can't DROP FOREIGN KEY" error if your DB was not seeded yet.
-- ----------------------------------------------------------------------------
ALTER TABLE `sale_items` DROP FOREIGN KEY `sale_items_inventory_item_id_foreign`;
ALTER TABLE `sale_items` DROP COLUMN `inventory_item_id`;
ALTER TABLE `sale_items` ADD COLUMN `menu_item_id` bigint unsigned NULL AFTER `sale_id`;
ALTER TABLE `sale_items` ADD CONSTRAINT `sale_items_menu_item_id_foreign`
    FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL;

-- ----------------------------------------------------------------------------
-- 3) Remove the old demo accounts and create the single administrator.
--    Credentials: admin@khajapos.com / KhajaPOS@123
-- ----------------------------------------------------------------------------
SET @adminRoleId := (SELECT `id` FROM `roles` WHERE `slug` = 'admin' LIMIT 1);

DELETE FROM `users`
 WHERE `email` IN ('admin@restaurant.com', 'cashier@restaurant.com', 'stock@restaurant.com');

INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role_id`, `is_active`, `created_at`, `updated_at`)
VALUES (
    'Administrator',
    'admin@khajapos.com',
    '+977-9841234567',
    '$2b$10$jq6cllicO7gDgtR.i1DILOm5a.CUaZXAOaQw0LQOX0Ru5Em92sEP6',
    @adminRoleId,
    1,
    NOW(),
    NOW()
);

-- ----------------------------------------------------------------------------
-- 4) Add your menu items to the new table (the old dishes live in
--    `inventory_items` under Food/Drinks/Snacks/Desserts categories).
--    This copies them across automatically:
--    purchase_price -> cost, selling_price -> price, status='active' -> available.
--    Run it again later to import any dishes you add through the old table.
-- ----------------------------------------------------------------------------
INSERT INTO `menu_items` (`name`, `sku`, `category_id`, `unit`, `cost`, `price`, `is_available`, `created_at`, `updated_at`)
SELECT `name`, `sku`, `category_id`, `unit`, `purchase_price`, `selling_price`, IF(`status` = 'active', 1, 0), NOW(), NOW()
FROM `inventory_items`
WHERE `category_id` IN (SELECT `id` FROM `categories` WHERE `slug` IN ('food', 'drinks', 'snacks', 'desserts'))
  AND `name` NOT IN (SELECT `name` FROM `menu_items`);

-- ----------------------------------------------------------------------------
-- 5) OPTIONAL: connect existing sales to their menu items so invoice detail
--    links stay intact (matches by dish NAME copied into menu_items above).
-- ----------------------------------------------------------------------------
UPDATE `sale_items` si
JOIN `menu_items` mi ON mi.`name` = si.`item_name`
SET si.`menu_item_id` = mi.`id`
WHERE si.`menu_item_id` IS NULL;

-- ----------------------------------------------------------------------------
-- 6) OPTIONAL - ONLY if you plan to run `php artisan migrate` later on the
--    same database: record the two new migrations as already applied so
--    artisan does not attempt to re-create them (ignore if you don't use
--    artisan at all).
-- ----------------------------------------------------------------------------
-- INSERT INTO `migrations` (`migration`, `batch`)
-- SELECT '2026_09_11_000012_create_menu_items_table', (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`)
-- UNION ALL
-- SELECT '2026_09_11_000013_replace_inventory_item_with_menu_item_in_sale_items_table', (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);