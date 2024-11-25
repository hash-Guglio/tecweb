USE `tecweb`;

-- usr_password = usr_name
INSERT INTO `user` (`id`, `usr_name`, `usr_mail`, `usr_first_name`, `usr_gender`, `usr_birth_date`, `usr_password`, `usr_is_admin`, `usr_is_vegan`, `usr_is_celiac`, `usr_is_lactose_intolerant`) VALUES
(0, 'admin', 'admin@example.com', 'Admin', 'altro', '1990-01-01', '$2y$10$qCWHBW5FJ.OdSJiHY6Ox7eewz/2n2VE02oatT7Fzbj/sA5FAHZhha', true, false, false, false),
(1, 'user', 'user@example.com', 'User', 'altro', '1995-05-10', '$2y$10$ZfSGzYfcbBj4hTAEat8LD.vTQ3AnvpzfsfOUerzL7wWDGFJDCxBqq', false, false, false, false);


INSERT INTO recipe (id, rcp_title, rcp_image, rcp_ready_minutes, rcp_servings, rcp_instructions, rcp_price_servings, rcp_difficult, rcp_is_dairy_free, rcp_is_gluten_free, rcp_is_vegan) VALUES 
(1, 'Pasta al Pomodoro', 'pasta.jpg', 30, 2, 'Cuocere la pasta, preparare il sugo, unire e servire.', 2.50, 'Facile', TRUE, FALSE, FALSE),
(2, 'Zuppa di Lenticchie', 'zuppa.jpg', 45, 4, 'Soffriggere, aggiungere le lenticchie e cuocere finché sono morbide.', 3.20, 'Medio', TRUE, TRUE, TRUE);

INSERT INTO dish_type (id, dt_type) VALUES 
(1, 'Primo piatto'),
(2, 'Zuppa'), 
(3, 'Secondo');

INSERT INTO dish_type_recipe (recipe, dish_type) VALUES 
(1, 1),
(2, 2), 
(2, 3);

