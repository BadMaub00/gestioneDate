-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Ott 18, 2026 alle 21:55
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
  `ora_fine` time NOT NULL,
  `aula_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `appello`
--

INSERT INTO `appello` (`appello_id`, `ins_corso_id`, `sessione_id`, `data`, `ora_inizio`, `ora_fine`, `aula_id`) VALUES
(1, 1, 1, '2026-01-20', '09:00:00', '11:00:00', 1),
(2, 1, 1, '2026-02-10', '09:00:00', '11:00:00', 1),
(3, 2, 1, '2026-01-22', '14:00:00', '17:00:00', 7),
(4, 3, 1, '2026-02-05', '10:00:00', '12:00:00', 3),
(5, 7, 1, '2026-01-23', '09:00:00', '12:00:00', 1),
(6, 7, 1, '2026-02-10', '09:00:00', '12:00:00', 2),
(7, 7, 2, '2026-06-20', '14:00:00', '17:00:00', 1),
(8, 4, 1, '2026-01-28', '09:00:00', '12:00:00', 7),
(9, 4, 1, '2026-02-18', '09:00:00', '12:00:00', 7),
(10, 4, 2, '2026-06-25', '09:00:00', '12:00:00', 7),
(11, 5, 1, '2026-01-30', '14:00:00', '17:00:00', 3),
(12, 5, 1, '2026-02-20', '14:00:00', '17:00:00', 3),
(13, 5, 2, '2026-07-02', '14:00:00', '17:00:00', 3),
(14, 6, 1, '2026-02-02', '10:00:00', '13:00:00', 5),
(15, 6, 1, '2026-02-23', '10:00:00', '13:00:00', 5),
(16, 6, 2, '2026-06-29', '10:00:00', '13:00:00', 5),
(17, 8, 1, '2026-01-26', '14:00:00', '17:00:00', 18),
(18, 8, 1, '2026-02-16', '14:00:00', '17:00:00', 18),
(19, 8, 2, '2026-06-22', '14:00:00', '17:00:00', 18),
(20, 9, 2, '2026-07-06', '09:00:00', '12:00:00', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `aula`
--

CREATE TABLE `aula` (
  `aula_id` int(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `edificio_id` int(100) NOT NULL,
  `capienza` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `aula`
--

INSERT INTO `aula` (`aula_id`, `nome`, `edificio_id`, `capienza`) VALUES
(1, 'MTB2', 2, 120),
(2, 'MTB3', 2, 80),
(3, 'MTB4', 2, 150),
(4, 'Aula Magna Ingegneria', 2, 250),
(5, 'Lab N1', 3, 50),
(6, 'Lab N2', 3, 45),
(7, 'Lab Informatica 1', 3, 60),
(8, 'Aula A', 4, 200),
(9, 'Aula B', 4, 180),
(10, 'Aula C', 4, 120),
(11, 'Aula Magna Medicina', 4, 300),
(12, 'Aula B1', 5, 140),
(13, 'Aula B2', 5, 110),
(14, 'Aula B3', 5, 90),
(15, 'Aula Calini 1', 6, 130),
(16, 'Aula Calini 2', 6, 85),
(17, 'Aula Magna Giurisprudenza', 6, 220),
(18, 'Valotti 1', 7, 75),
(19, 'Valotti 2', 7, 75),
(20, 'Valotti 3', 7, 100),
(21, 'Santa Chiara A1', 8, 60),
(22, 'Santa Chiara A2', 8, 50),
(23, 'Brixia 1', 9, 90),
(24, 'Brixia 2', 9, 90),
(25, 'Lab Chimica', 10, 35),
(26, 'Lab Fisica', 10, 40),
(27, 'Aula Spec 1', 11, 50),
(28, 'Aula Spec 2', 11, 40);

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
(5, 'Ingegneria dell Automazione', 'Triennale'),
(7, 'Ingegneria Elettronica', 'Magistrale');

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
(8, 7, 6, 'Primario'),
(9, 8, 7, 'Primario'),
(10, 9, 6, 'Primario'),
(11, 3, 7, 'Primario'),
(12, 10, 7, 'Primario');

-- --------------------------------------------------------

--
-- Struttura della tabella `edificio`
--

CREATE TABLE `edificio` (
  `edificio_id` int(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `via` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `edificio`
--

INSERT INTO `edificio` (`edificio_id`, `nome`, `via`) VALUES
(2, 'Ingegneria - Polo Didattico', 'Via Branze 38'),
(3, 'Ingegneria - Laboratori NORD', 'Via Branze 45'),
(4, 'Medicina e Chirurgia', 'Viale Europa 11'),
(5, 'Economia e Management', 'Via San Faustino 74'),
(6, 'Giurisprudenza - Palazzo Calini', 'Via San Faustino 41'),
(7, 'Polo Didattico Valotti', 'Via Valotti 3'),
(8, 'Polo Studenti Santa Chiara', 'Contrada Santa Chiara 50'),
(9, 'Complesso Brixia', 'Via San Rocchino 68'),
(10, 'Laboratori di Ricerca Nord', 'Via Duca degli Abruzzi 15'),
(11, 'Dipartimento di Specialità Mediche', 'Piazzale Spedali Civili 1');

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
(6, 'Robotica e Sistemi Intelligenti'),
(8, 'Programmazione Web'),
(10, 'Deep Learning e Reti Neurali'),
(12, 'Fisica I'),
(13, 'Sistemi Operativi'),
(14, 'Basi di Dati'),
(15, 'Elaborazione delle Immagini'),
(16, 'Calcolo Numerico'),
(17, 'Reti di Calcolatori'),
(18, 'Elettrotecnica'),
(19, 'Sistemi Embedded e IoT'),
(20, 'Elaborazione del Linguaggio Naturale'),
(21, 'Ingegneria del Software');

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
(7, 1, 8, 60, 6, 2),
(8, 7, 8, 60, 6, 1),
(9, 4, 10, 60, 6, 2),
(10, 4, 6, 60, 6, 1),
(11, 1, 12, 60, 9, 1),
(12, 2, 12, 60, 6, 1),
(13, 1, 13, 60, 9, 2),
(14, 2, 14, 60, 9, 2),
(15, 4, 15, 60, 6, 1),
(16, 3, 16, 60, 6, 2),
(17, 1, 17, 60, 6, 3),
(18, 5, 18, 60, 9, 1),
(19, 7, 19, 60, 6, 1),
(20, 4, 20, 60, 6, 2),
(21, 2, 21, 60, 9, 3);

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
('j6I2exJWuY4oTI2cW1lYRAGDqiWYiaycMJSlxxin', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNkRwekpQd3RQM3NsNkpUU2tYWHRjOEV6WVZoUFJLQWpnZGlkVWQyZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Njc6Imh0dHA6Ly9sb2NhbGhvc3QvZ2VzdGlvbmVEYXRlL2dlc3Rpb25lRGF0ZS9wdWJsaWMvZG9jZW50ZS9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTc6ImRvY2VudGUuZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Njt9', 1792261317);

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
(7, 'gmineiro', '$2y$12$UzKu2ajt5wImELwBZeWj4.bPCgUOa3f5vTmjBY9j4yLLaOy7zQr5u', 'Giuseppe', 'Mineiro', '1980-05-20', 'Docente'),
(8, 'delPiero10', '$2y$12$.PPxW6dr5xHh0SiXWouxCujC4DEU2muClHi2xrUJksca7Kl1FamTC', 'Alessandro', 'Del Piero', '1974-11-09', 'Amministratore');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `appello`
--
ALTER TABLE `appello`
  ADD PRIMARY KEY (`appello_id`),
  ADD KEY `ins_corso_id` (`ins_corso_id`),
  ADD KEY `sessione_id` (`sessione_id`),
  ADD KEY `fk_appello_aula` (`aula_id`);

--
-- Indici per le tabelle `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`aula_id`),
  ADD KEY `edificio_id` (`edificio_id`);

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
-- Indici per le tabelle `edificio`
--
ALTER TABLE `edificio`
  ADD PRIMARY KEY (`edificio_id`);

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
  MODIFY `appello_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `aula`
--
ALTER TABLE `aula`
  MODIFY `aula_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT per la tabella `corso`
--
ALTER TABLE `corso`
  MODIFY `corso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `docente_insegnamento`
--
ALTER TABLE `docente_insegnamento`
  MODIFY `doc_ins_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `edificio`
--
ALTER TABLE `edificio`
  MODIFY `edificio_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `insegnamento`
--
ALTER TABLE `insegnamento`
  MODIFY `insegnamento_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT per la tabella `insegnamento_corso`
--
ALTER TABLE `insegnamento_corso`
  MODIFY `ins_corso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `utente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `appello`
--
ALTER TABLE `appello`
  ADD CONSTRAINT `appello_ibfk_1` FOREIGN KEY (`ins_corso_id`) REFERENCES `insegnamento_corso` (`ins_corso_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appello_ibfk_2` FOREIGN KEY (`sessione_id`) REFERENCES `sessione_esami` (`sessione_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appello_aula` FOREIGN KEY (`aula_id`) REFERENCES `aula` (`aula_id`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `aula`
--
ALTER TABLE `aula`
  ADD CONSTRAINT `aula_ibfk_1` FOREIGN KEY (`edificio_id`) REFERENCES `edificio` (`edificio_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
