USE `tecweb`;

INSERT INTO `user` (`usr_name`, `usr_mail`, `usr_first_name`, `usr_gender`, `usr_birth_date`, `usr_password`, `usr_is_admin`) VALUES
('admin', 'admin@example.com', 'Admin', 'altro', '1990-01-01', '$2y$10$qCWHBW5FJ.OdSJiHY6Ox7eewz/2n2VE02oatT7Fzbj/sA5FAHZhha', true),
('user', 'user@example.com', 'User', 'altro', '1995-05-10', '$2y$10$ZfSGzYfcbBj4hTAEat8LD.vTQ3AnvpzfsfOUerzL7wWDGFJDCxBqq', false);

INSERT INTO restriction (rst_type, rst_disorder_name)
VALUES 
  ('Vegano', NULL),
  ('Senza glutine', 'Celiaco'),
  ('Senza lattosio', 'Intollerante al lattosio');

INSERT INTO user_restriction (user, restriction)
VALUES
    (1, 2),
    (2, 1);

INSERT INTO recipe (id, rcp_title, rcp_image, rcp_ready_minutes, rcp_servings, rcp_instructions, rcp_price_servings, rcp_difficult) VALUES
(1, 'Pasta al Pomodoro', 'pasta', 30, 2, 'Cuocere la pasta, preparare il sugo, unire e servire.', 2.50, 'Facile'),
(2, 'Zuppa di Lenticchie', 'pasta', 45, 4, 'Soffriggere, aggiungere le lenticchie e cuocere finché sono morbide.', 3.20, 'Medio'),
(3, 'Insalata di Quinoa', 'pasta', 20, 2, 'Cuocere la quinoa, aggiungere verdure fresche e condire.', 4.00, 'Facile'),
(4, 'Riso Integrale con Verdure', 'pasta', 40, 4, 'Cuocere il riso, saltare le verdure e mescolare.', 3.50, 'Facile'),  
(5, 'Lasagna Vegetariana', 'pasta', 60, 6, 'Preparare la lasagna alternando strati di pasta e verdure, cuocere in forno.', 5.00, 'Difficile'),
(6, 'Polpette di Lenticchie', 'pasta', 50, 4, 'Formare delle polpette con le lenticchie, cuocerle in forno.', 3.80, 'Medio');  

INSERT INTO ingredient (igr_name, igr_image) VALUES
('Petto di pollo', 'placeholder'),
('Salmone', 'placeholder'),
('Broccoli', 'placeholder'),
('Pomodoro', 'placeholder'),
('Riso basmati', 'placeholder'),
('Mozzarella', 'placeholder'),
('Zucchine', 'placeholder'),
('Avena', 'placeholder'),
('Mandorle', 'placeholder'),
('Uova', 'placeholder');

INSERT INTO nutrient (id, ntr_name, ntr_unit)
VALUES
    ('cal' , 'Calorie', 'kcal'),
    ('prt', 'Proteine', 'g'),
    ('fat', 'Grassi', 'g'),
    ('carbo', 'Carboidrati', 'g');

INSERT INTO recipe_restriction (recipe, restriction)
VALUES 
    (1, 2),
    (3, 1),
    (4, 2),  
    (5, 2),  
    (6, 1);  

INSERT INTO dish_type (id, dt_type) VALUES 
    (1, 'Primo'),
    (2, 'Zuppa'), 
    (3, 'Secondo');

INSERT INTO dish_type_recipe (recipe, dish_type) VALUES 
    (1, 1),
    (2, 2),
    (3, 1),
    (4, 1),
    (5, 1),
    (6, 3),
    (6, 1);

INSERT INTO ingredient_nutrient (ingredient, nutrient, amount)
VALUES
    (1, 'cal', 165),   -- Petto di pollo, Calorie
    (1, 'prt', 31),    -- Petto di pollo, Proteine
    (1, 'fat', 3.6),   -- Petto di pollo, Grassi
    (1, 'carbo', 0),   -- Petto di pollo, Carboidrati

    (2, 'cal', 208),   -- Salmone, Calorie
    (2, 'prt', 20),    -- Salmone, Proteine
    (2, 'fat', 13),    -- Salmone, Grassi
    (2, 'carbo', 0),   -- Salmone, Carboidrati

    (3, 'cal', 34),    -- Broccoli, Calorie
    (3, 'prt', 2.8),   -- Broccoli, Proteine
    (3, 'fat', 0.4),   -- Broccoli, Grassi
    (3, 'carbo', 6.6), -- Broccoli, Carboidrati

    (4, 'cal', 18),    -- Pomodoro, Calorie
    (4, 'prt', 0.9),   -- Pomodoro, Proteine
    (4, 'fat', 0.2),   -- Pomodoro, Grassi
    (4, 'carbo', 3.9), -- Pomodoro, Carboidrati

    (5, 'cal', 121),   -- Riso basmati, Calorie
    (5, 'prt', 2.4),   -- Riso basmati, Proteine
    (5, 'fat', 0.3),   -- Riso basmati, Grassi
    (5, 'carbo', 25.2),-- Riso basmati, Carboidrati

    (6, 'cal', 280),   -- Mozzarella, Calorie
    (6, 'prt', 18),    -- Mozzarella, Proteine
    (6, 'fat', 17),    -- Mozzarella, Grassi
    (6, 'carbo', 3),   -- Mozzarella, Carboidrati

    (7, 'cal', 17),    -- Zucchine, Calorie
    (7, 'prt', 1.2),   -- Zucchine, Proteine
    (7, 'fat', 0.3),   -- Zucchine, Grassi
    (7, 'carbo', 3.1), -- Zucchine, Carboidrati

    (8, 'cal', 389),   -- Avena, Calorie
    (8, 'prt', 13.5),  -- Avena, Proteine
    (8, 'fat', 7),     -- Avena, Grassi
    (8, 'carbo', 66),  -- Avena, Carboidrati

    (9, 'cal', 579),   -- Mandorle, Calorie
    (9, 'prt', 21),    -- Mandorle, Proteine
    (9, 'fat', 49),    -- Mandorle, Grassi
    (9, 'carbo', 22),  -- Mandorle, Carboidrati

    (10, 'cal', 155),  -- Uova, Calorie
    (10, 'prt', 12.6), -- Uova, Proteine
    (10, 'fat', 11),   -- Uova, Grassi
    (10, 'carbo', 1.1);-- Uova, Carboidrati

