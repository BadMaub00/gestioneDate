-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Ott 14, 2026 alle 23:06
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coordinamento_date_esame`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `appello`
--

CREATE TABLE `appello` (
  `appello_id` int(11) NOT NULL,
  `ins_corso_id` int(11) NOT NULL,
  `sessione_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `appello`
--

INSERT INTO `appello` (`appello_id`, `ins_corso_id`, `sessione_id`, `data`, `ora_inizio`, `ora_fine`) VALUES
(1, 1, 1, '2026-01-20', '09:00:00', '11:00:00'),
(2, 1, 1, '2026-02-10', '09:00:00', '11:00:00'),
(3, 2, 1, '2026-01-22', '14:00:00', '17:00:00'),
(4, 3, 1, '2026-02-05', '10:00:00', '12:00:00'),
(5, 7, 1, '2026-01-25', '09:00:00', '12:00:00'),
(6, 7, 1, '2026-02-15', '09:00:00', '12:00:00'),
(7, 7, 2, '2026-06-20', '14:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `corso`
--

CREATE TABLE `corso` (
  `corso_id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `tipo_corso` enum('Triennale','Magistrale','Magistrale a ciclo unico') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `corso`
--

INSERT INTO `corso` (`corso_id`, `nome`, `tipo_corso`) VALUES
(1, 'Ingegneria Informatica', 'Triennale'),
(2, 'Informatica', 'Triennale'),
(3, 'Ingegneria Elettronica', 'Triennale'),
(4, 'Artificial Intelligence Engineering', 'Magistrale'),
(5, 'Ingegneria dell Automazione', 'Triennale');

-- --------------------------------------------------------

--
-- Struttura della tabella `docente_insegnamento`
--

CREATE TABLE `docente_insegnamento` (
  `doc_ins_id` int(11) NOT NULL,
  `ins_corso_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `ruolo` enum('Primario','Secondario') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `docente_insegnamento`
--

INSERT INTO `docente_insegnamento` (`doc_ins_id`, `ins_corso_id`, `utente_id`, `ruolo`) VALUES
(1, 1, 6, 'Primario'),
(2, 2, 6, 'Primario'),
(3, 3, 6, 'Secondario'),
(4, 4, 7, 'Primario'),
(5, 5, 7, 'Primario'),
(6, 6, 7, 'Secondario'),
(8, 7, 6, 'Primario');

-- --------------------------------------------------------

--
-- Struttura della tabella `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `insegnamento`
--

CREATE TABLE `insegnamento` (
  `insegnamento_id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `insegnamento`
--

INSERT INTO `insegnamento` (`insegnamento_id`, `nome`) VALUES
(1, 'Analisi Matematica I'),
(2, 'Programmazione e Strutture Dati'),
(3, 'Architettura dei Calcolatori'),
(4, 'Machine Learning e Data Mining'),
(5, 'Cybersecurity e Sicurezza delle Reti'),
(6, 'Robottica e Sistemi Intelligenti'),
(8, 'Programmazione Web');

-- --------------------------------------------------------

--
-- Struttura della tabella `insegnamento_corso`
--

CREATE TABLE `insegnamento_corso` (
  `ins_corso_id` int(11) NOT NULL,
  `corso_id` int(11) NOT NULL,
  `insegnamento_id` int(11) NOT NULL,
  `durata` int(50) NOT NULL,
  `cfu` int(10) NOT NULL,
  `anno_di_corso` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `insegnamento_corso`
--

INSERT INTO `insegnamento_corso` (`ins_corso_id`, `corso_id`, `insegnamento_id`, `durata`, `cfu`, `anno_di_corso`) VALUES
(1, 1, 1, 60, 9, 1),
(2, 2, 2, 60, 12, 1),
(3, 3, 3, 60, 6, 2),
(4, 4, 4, 60, 9, 1),
(5, 4, 5, 60, 6, 2),
(6, 5, 6, 60, 9, 2),
(7, 1, 8, 60, 6, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sessione_esami`
--

CREATE TABLE `sessione_esami` (
  `sessione_id` int(11) NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL,
  `nome` varchar(150) NOT NULL,
  `anno` int(5) NOT NULL,
  `numero_appelli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `sessione_esami`
--

INSERT INTO `sessione_esami` (`sessione_id`, `data_inizio`, `data_fine`, `nome`, `anno`, `numero_appelli`) VALUES
(1, '2026-01-12', '2026-02-27', 'Sessione Invernale', 2026, 2),
(2, '2026-06-15', '2026-07-24', 'Sessione Estiva', 2026, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BHhMztD5OX1s0DhiPzaFjsEtrYqOplj4I0t7Yo43', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiM3k0REVNV25jOG5UTjM5NjdTOUN0bmlkQ0QxTjY4eE1RSmZNZW1HZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2NzoiaHR0cDovL2xvY2FsaG9zdC9nZXN0aW9uZURhdGUvZ2VzdGlvbmVEYXRlL3B1YmxpYy9kb2NlbnRlL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY3OiJodHRwOi8vbG9jYWxob3N0L2dlc3Rpb25lRGF0ZS9nZXN0aW9uZURhdGUvcHVibGljL2RvY2VudGUvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjE3OiJkb2NlbnRlLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjY7fQ==', 1792011964);

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `utente_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `data_nascita` date NOT NULL,
  `ruolo` enum('Docente','Amministratore') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`utente_id`, `username`, `password`, `nome`, `cognome`, `data_nascita`, `ruolo`) VALUES
(5, 'mrossi', '$2y$12$uvr8qflKSaq/FGrvDLOWb.7ft/2bT39yTm6iC3b25iriRpzZumCqi', 'Mario', 'Rossi', '1990-05-15', 'Amministratore'),
(6, 'lverdi', '$2y$12$EH.XFqQ4XJrCQvaBaZRjOuwApRM39J7WdkYP1GfAagNUK3s.2yEvu', 'Luigi', 'Verdi', '1985-04-20', 'Docente'),
(7, 'gmineiro', '$2y$12$UzKu2ajt5wImELwBZeWj4.bPCgUOa3f5vTmjBY9j4yLLaOy7zQr5u', 'Giuseppe', 'Verdi', '1980-05-20', 'Docente');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `appello`
--
ALTER TABLE `appello`
  ADD PRIMARY KEY (`appello_id`),
  ADD KEY `ins_corso_id` (`ins_corso_id`),
  ADD KEY `sessione_id` (`sessione_id`);

--
-- Indici per le tabelle `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indici per le tabelle `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indici per le tabelle `corso`
--
ALTER TABLE `corso`
  ADD PRIMARY KEY (`corso_id`);

--
-- Indici per le tabelle `docente_insegnamento`
--
ALTER TABLE `docente_insegnamento`
  ADD PRIMARY KEY (`doc_ins_id`),
  ADD KEY `ins_corso_id` (`ins_corso_id`),
  ADD KEY `docente_id` (`utente_id`);

--
-- Indici per le tabelle `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indici per le tabelle `insegnamento`
--
ALTER TABLE `insegnamento`
  ADD PRIMARY KEY (`insegnamento_id`);

--
-- Indici per le tabelle `insegnamento_corso`
--
ALTER TABLE `insegnamento_corso`
  ADD PRIMARY KEY (`ins_corso_id`),
  ADD UNIQUE KEY `corso_id` (`corso_id`,`insegnamento_id`),
  ADD KEY `insegnamento_id` (`insegnamento_id`),
  ADD KEY `ins_corso_id` (`ins_corso_id`);

--
-- Indici per le tabelle `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indici per le tabelle `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indici per le tabelle `sessione_esami`
--
ALTER TABLE `sessione_esami`
  ADD PRIMARY KEY (`sessione_id`);

--
-- Indici per le tabelle `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`utente_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `appello`
--
ALTER TABLE `appello`
  MODIFY `appello_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `corso`
--
ALTER TABLE `corso`
  MODIFY `corso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `docente_insegnamento`
--
ALTER TABLE `docente_insegnamento`
  MODIFY `doc_ins_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `insegnamento`
--
ALTER TABLE `insegnamento`
  MODIFY `insegnamento_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `insegnamento_corso`
--
ALTER TABLE `insegnamento_corso`
  MODIFY `ins_corso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `sessione_esami`
--
ALTER TABLE `sessione_esami`
  MODIFY `sessione_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `utente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `appello`
--
ALTER TABLE `appello`
  ADD CONSTRAINT `appello_ibfk_1` FOREIGN KEY (`ins_corso_id`) REFERENCES `insegnamento_corso` (`ins_corso_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appello_ibfk_2` FOREIGN KEY (`sessione_id`) REFERENCES `sessione_esami` (`sessione_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `docente_insegnamento`
--
ALTER TABLE `docente_insegnamento`
  ADD CONSTRAINT `docente_insegnamento_ibfk_1` FOREIGN KEY (`ins_corso_id`) REFERENCES `insegnamento_corso` (`ins_corso_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `docente_insegnamento_ibfk_2` FOREIGN KEY (`utente_id`) REFERENCES `utente` (`utente_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `insegnamento_corso`
--
ALTER TABLE `insegnamento_corso`
  ADD CONSTRAINT `insegnamento_corso_ibfk_1` FOREIGN KEY (`corso_id`) REFERENCES `corso` (`corso_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `insegnamento_corso_ibfk_2` FOREIGN KEY (`insegnamento_id`) REFERENCES `insegnamento` (`insegnamento_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
