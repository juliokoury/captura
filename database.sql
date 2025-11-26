CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `como_quer_ser_chamado` varchar(255) DEFAULT NULL,
  `telefone` varchar(50) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `local_dor` varchar(255) DEFAULT NULL,
  `tempo_dor` varchar(255) DEFAULT NULL,
  `urgencia` enum('baixa','media','alta') DEFAULT 'baixa',
  `tags_ai` text,
  `resumo_ai` text,
  `status_kanban` enum('baixa','media','alta') DEFAULT 'baixa',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
-- You should change this hash in production
INSERT INTO `admin_users` (`username`, `password`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
