USE `tecweb`;

-- usr_password = usr_name
INSERT INTO `user` (`id`, `usr_name`, `usr_mail`, `usr_first_name`, `usr_gender`, `usr_birth_date`, `usr_password`, `usr_is_admin`, `usr_is_vegan`, `usr_is_celiac`, `usr_is_lactose_intolerant`) VALUES
(0, 'admin', 'admin@example.com', 'Admin', 'altro', '1990-01-01', '$2y$10$qCWHBW5FJ.OdSJiHY6Ox7eewz/2n2VE02oatT7Fzbj/sA5FAHZhha', true, false, false, false),
(1, 'user', 'user@example.com', 'User', 'altro', '1995-05-10', '$2y$10$ZfSGzYfcbBj4hTAEat8LD.vTQ3AnvpzfsfOUerzL7wWDGFJDCxBqq', false, false, false, false);



