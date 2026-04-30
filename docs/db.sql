CREATE TABLE IF NOT EXISTS `users` (
	`id` int AUTO_INCREMENT NOT NULL,
	`name` varchar(255) NOT NULL,
	`password` int NOT NULL,
	`email` varchar(255) NOT NULL UNIQUE,
	`role_id` int NOT NULL,
	`created_at` datetime NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `roles` (
	`id` int AUTO_INCREMENT NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `permissions` (
	`id` int AUTO_INCREMENT NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `role_permissions` (
	`role_id` int NOT NULL,
	`permission_id` int NOT NULL,
	PRIMARY KEY (`role_id`, `permission_id`)
);

CREATE TABLE IF NOT EXISTS `settings` (
	`user_id` int NOT NULL,
	`theme` varchar(255) NOT NULL DEFAULT 'white',
	PRIMARY KEY (`user_id`)
);

CREATE TABLE IF NOT EXISTS `products` (
	`id` int AUTO_INCREMENT NOT NULL,
	`name` varchar(255) NOT NULL,
	`price_cents` int NOT NULL,
	`discount_percent` int,
	`stock` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `orders` (
	`id` int AUTO_INCREMENT NOT NULL,
	`user_id` int NOT NULL,
	`status` int NOT NULL,
	`created_at` datetime NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `order_items` (
	`id` int AUTO_INCREMENT NOT NULL,
	`order_id` int NOT NULL,
	`product_id` int NOT NULL,
	`quantity` int NOT NULL,
	`unit_price_snapshot` int NOT NULL,
	`discount_percent_snapshot` int,
	PRIMARY KEY (`id`)
);

ALTER TABLE `users` ADD CONSTRAINT `users_fk4` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`);


ALTER TABLE `role_permissions` ADD CONSTRAINT `role_permissions_fk0` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`);

ALTER TABLE `role_permissions` ADD CONSTRAINT `role_permissions_fk1` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`);
ALTER TABLE `settings` ADD CONSTRAINT `settings_fk0` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `orders` ADD CONSTRAINT `orders_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);
ALTER TABLE `order_items` ADD CONSTRAINT `order_items_fk1` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`);

ALTER TABLE `order_items` ADD CONSTRAINT `order_items_fk2` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`);
CREATE INDEX `idx_users_password` ON `users` (password);
ALTER TABLE `products` ADD CONSTRAINT `constraint_stock` CHECK stock >= 0;
ALTER TABLE `products` ADD CONSTRAINT `constraint_discount` CHECK discount_percent BETWEEN 0 AND 100;
ALTER TABLE `products` ADD CONSTRAINT `constraint_price_cents` CHECK price_cents > 0;
CREATE INDEX `idx_orders_user_id` ON `orders` (user_id);
ALTER TABLE `orders` ADD CONSTRAINT `constraint_orders_1` PRIMARY KEY status IN (0, 1, 2);
CREATE INDEX `idx_order_items_order_id` ON `order_items` (order_id);
CREATE INDEX `idx_order_items_product_id` ON `order_items` (product_id);
ALTER TABLE `order_items` ADD CONSTRAINT `constraint_quantity` CHECK quantity > 0;
ALTER TABLE `order_items` ADD CONSTRAINT `constraint_unit_price_snapshot` CHECK unit_price_snapshot > 0;