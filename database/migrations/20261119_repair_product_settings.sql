-- Execute once in the database belonging to dotbright2 before uploading the PHP changes.
-- Separate settings table; no changes to products, families, prices or existing requests.
CREATE TABLE IF NOT EXISTS repair_product_settings (
    product_id INT UNSIGNED NOT NULL,
    time_code VARCHAR(16) NOT NULL DEFAULT '',
    time_custom VARCHAR(120) NOT NULL DEFAULT '',
    part_type VARCHAR(24) NOT NULL DEFAULT '',
    part_label VARCHAR(120) NOT NULL DEFAULT '',
    part_detail VARCHAR(500) NOT NULL DEFAULT '',
    part_icon VARCHAR(16) NOT NULL DEFAULT 'shield',
    warranty_months SMALLINT UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
