-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Ott 14, 2026 alle 15:57
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
  `Appello_id` int(11) NOT NULL,
  `ins_corso_id` int(100) NOT NULL,
  `sessione_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `corso`
--

CREATE TABLE `corso` (
  `corso_id` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `tipo_corso` enum('Triennale','Magistrale','Magistrale a ciclo unico') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `docente_insegnamento`
--

CREATE TABLE `docente_insegnamento` (
  `Doc_ins_id` int(100) NOT NULL,
  `ins_corso_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `ruolo` enum('Primario','Secondario') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `insegnamento`
--

CREATE TABLE `insegnamento` (
  `insegnamento_id` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `insegnamento_corso`
--

CREATE TABLE `insegnamento_corso` (
  `ins_corso_id` int(100) NOT NULL,
  `corso_id` varchar(100) NOT NULL,
  `insegnamento_id` varchar(100) NOT NULL,
  `durata` int(50) NOT NULL,
  `cfu` int(10) NOT NULL,
  `anno_di_corso` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `appello`
--
ALTER TABLE `appello`
  ADD PRIMARY KEY (`Appello_id`),
  ADD KEY `ins_corso_id` (`ins_corso_id`),
  ADD KEY `sessione_id` (`sessione_id`);

--
-- Indici per le tabelle `corso`
--
ALTER TABLE `corso`
  ADD PRIMARY KEY (`corso_id`);

--
-- Indici per le tabelle `docente_insegnamento`
--
ALTER TABLE `docente_insegnamento`
  ADD PRIMARY KEY (`Doc_ins_id`),
  ADD KEY `ins_corso_id` (`ins_corso_id`),
  ADD KEY `docente_id` (`utente_id`);

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
-- Indici per le tabelle `sessione_esami`
--
ALTER TABLE `sessione_esami`
  ADD PRIMARY KEY (`sessione_id`);

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
  MODIFY `Appello_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `docente_insegnamento`
--
ALTER TABLE `docente_insegnamento`
  MODIFY `Doc_ins_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `insegnamento_corso`
--
ALTER TABLE `insegnamento_corso`
  MODIFY `ins_corso_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `sessione_esami`
--
ALTER TABLE `sessione_esami`
  MODIFY `sessione_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `utente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
