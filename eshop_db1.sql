-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 18. bře 2025, 08:06
-- Verze serveru: 10.4.32-MariaDB
-- Verze PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `eshop_db1`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL,
  `nazev` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `kategorie`
--

INSERT INTO `kategorie` (`id`, `nazev`) VALUES
(1, 'Elektronika'),
(2, 'Oblečení'),
(3, 'Obuv'),
(4, 'Knihy'),
(5, 'Sportovní vybavení'),
(6, 'Dům a zahrada');

-- --------------------------------------------------------

--
-- Struktura tabulky `objednavky`
--

CREATE TABLE `objednavky` (
  `id` int(11) NOT NULL,
  `jmeno_prijmeni` varchar(255) DEFAULT NULL,
  `ulice` varchar(255) DEFAULT NULL,
  `cislo_popisne` varchar(50) DEFAULT NULL,
  `mesto` varchar(255) DEFAULT NULL,
  `psc` varchar(10) DEFAULT NULL,
  `doprava` varchar(50) DEFAULT NULL,
  `celkova_cena` decimal(10,2) DEFAULT NULL,
  `celkova_cena_dph` decimal(10,2) DEFAULT NULL,
  `uzivatel_id` int(11) DEFAULT NULL,
  `datum_objednavky` timestamp NOT NULL DEFAULT current_timestamp(),
  `stav` enum('čeká na vyřízení','odesláno','doručeno','zrušeno') DEFAULT 'čeká na vyřízení'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `objednavky`
--

INSERT INTO `objednavky` (`id`, `jmeno_prijmeni`, `ulice`, `cislo_popisne`, `mesto`, `psc`, `doprava`, `celkova_cena`, `celkova_cena_dph`, `uzivatel_id`, `datum_objednavky`, `stav`) VALUES
(1, 'Jonáš Sobotka', 'ajdaodaj', '40011', 'Praha', '40011', 'osobni', NULL, NULL, NULL, '2025-03-17 21:48:59', 'čeká na vyřízení'),
(2, 'Jonáš Sobotka', 'ajdaodaj', '40011', 'Praha', '40011', 'osobni', NULL, NULL, NULL, '2025-03-17 21:50:22', 'čeká na vyřízení'),
(3, 'asdad', 'dads', '1', 'dadua', '42777', 'osobni', NULL, NULL, NULL, '2025-03-17 22:09:41', 'čeká na vyřízení');

-- --------------------------------------------------------

--
-- Struktura tabulky `objednavky_polozky`
--

CREATE TABLE `objednavky_polozky` (
  `id` int(11) NOT NULL,
  `objednavka_id` int(11) NOT NULL,
  `produkt_id` int(11) NOT NULL,
  `mnozstvi` int(11) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `cena_kus` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `objednavky_polozky`
--

INSERT INTO `objednavky_polozky` (`id`, `objednavka_id`, `produkt_id`, `mnozstvi`, `cena`, `cena_kus`) VALUES
(1, 2, 23, 2, 0.00, 2399.00),
(2, 3, 11, 1, 0.00, 18999.00),
(3, 3, 14, 2, 0.00, 1299.00),
(4, 3, 13, 1, 0.00, 599.00);

-- --------------------------------------------------------

--
-- Struktura tabulky `produkty`
--

CREATE TABLE `produkty` (
  `id` int(11) NOT NULL,
  `nazev` varchar(255) NOT NULL,
  `popis` text DEFAULT NULL,
  `cena` decimal(10,2) NOT NULL,
  `sklad` int(11) NOT NULL DEFAULT 0,
  `kategorie_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `produkty`
--

INSERT INTO `produkty` (`id`, `nazev`, `popis`, `cena`, `sklad`, `kategorie_id`) VALUES
(7, 'Notebook Lenovo', 'Výkonný pracovní notebook', 24999.99, 10, 1),
(8, 'Smartphone Samsung', 'Špičkový smartphone s OLED displejem', 15999.99, 15, 1),
(9, 'TV LG OLED', '4K OLED televizor', 34999.00, 5, 1),
(10, 'Sluchátka Sony', 'Bezdrátová sluchátka s potlačením hluku', 5999.00, 30, 1),
(11, 'Počítač HP', 'Výkonný desktop pro práci i zábavu', 18999.00, 8, 1),
(12, 'Externí disk WD', 'Externí SSD disk 1TB', 3499.00, 25, 1),
(13, 'Tričko Adidas', 'Pohodlné sportovní tričko', 599.00, 50, 2),
(14, 'Mikina Nike', 'Stylová mikina s kapucí', 1299.00, 40, 2),
(15, 'Džíny Levi\'s', 'Klasické džíny pro každodenní nošení', 1799.00, 60, 2),
(16, 'Boty Converse', 'Pohodlné plátěné boty', 1299.00, 35, 2),
(17, 'Bundy Columbia', 'Větruodolná bunda do deště', 2499.00, 20, 2),
(18, 'Kalhoty Jack & Jones', 'Moderní pánské kalhoty', 1499.00, 45, 2),
(19, 'Běžecké boty Nike', 'Kvalitní běžecká obuv', 2199.00, 30, 3),
(20, 'Kotníkové boty Timberland', 'Stylové kotníkové boty pro každé počasí', 3499.00, 15, 3),
(21, 'Sandály Birkenstock', 'Pohodlné sandály pro letní dny', 1499.00, 50, 3),
(22, 'Tenisky Adidas', 'Sportovní tenisky pro volný čas', 1799.00, 25, 3),
(23, 'Boty Under Armour', 'Kvalitní sportovní obuv', 2399.00, 40, 3),
(24, 'Pánské boty Nike', 'Pohodlné pánské boty pro každý den', 1999.00, 10, 3),
(25, 'Kniha: Programování v Pythonu', 'Učebnice Pythonu pro začátečníky', 799.00, 20, 4),
(26, 'Kniha: JavaScript pro každého', 'Kompletní průvodce JavaScriptem', 599.00, 25, 4),
(27, 'Kniha: Web design', 'Jak vytvořit úžasné weby', 399.00, 15, 4),
(28, 'Kniha: C# pro začátečníky', 'Učebnice jazyka C#', 499.00, 30, 4),
(29, 'Kniha: Algoritmy a datové struktury', 'Podrobný průvodce algoritmy', 749.00, 10, 4),
(30, 'Kniha: Základy databází', 'Vše, co potřebujete vědět o databázích', 699.00, 12, 4),
(31, 'Činka 10kg', 'Činka vhodná pro domácí cvičení', 1200.00, 15, 5),
(32, 'Běžecký pás', 'Elektrický běžecký pás pro domácí cvičení', 7999.00, 5, 5),
(33, 'Kettlebell 12kg', 'Kettlebell pro silový trénink', 1499.00, 25, 5),
(34, 'Skákací guma', 'Tréninková skákací guma pro fitness', 299.00, 50, 5),
(35, 'Jóga podložka', 'Podložka pro cvičení jógy', 599.00, 60, 5),
(36, 'Sportovní hodinky Garmin', 'Chytré hodinky pro sportovce', 3999.00, 20, 5),
(37, 'LED žárovka Philips', 'Úsporná LED žárovka 10W', 299.00, 100, 6),
(38, 'Sečka na trávu', 'Elektrická sekačka na trávu', 2999.00, 15, 6),
(39, 'Zahradní nábytek', 'Kompletní set zahradního nábytku', 7999.00, 5, 6),
(40, 'Zahradní gril', 'Kvalitní gril pro vaření na zahradě', 3499.00, 25, 6),
(41, 'Květináče 3ks', 'Sada květináčů pro zahradu', 199.00, 50, 6),
(42, 'Postel pro psa', 'Pohodlná postel pro vašeho mazlíčka', 999.00, 30, 6);

-- --------------------------------------------------------

--
-- Struktura tabulky `uzivatele`
--

CREATE TABLE `uzivatele` (
  `id` int(11) NOT NULL,
  `jmeno` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `heslo` varchar(255) NOT NULL,
  `adresa` text DEFAULT NULL,
  `datum_registrace` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `objednavky`
--
ALTER TABLE `objednavky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uzivatel_id` (`uzivatel_id`);

--
-- Indexy pro tabulku `objednavky_polozky`
--
ALTER TABLE `objednavky_polozky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `objednavka_id` (`objednavka_id`),
  ADD KEY `produkt_id` (`produkt_id`);

--
-- Indexy pro tabulku `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategorie_id` (`kategorie_id`);

--
-- Indexy pro tabulku `uzivatele`
--
ALTER TABLE `uzivatele`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `objednavky`
--
ALTER TABLE `objednavky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `objednavky_polozky`
--
ALTER TABLE `objednavky_polozky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pro tabulku `uzivatele`
--
ALTER TABLE `uzivatele`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `objednavky`
--
ALTER TABLE `objednavky`
  ADD CONSTRAINT `objednavky_ibfk_1` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatele` (`id`) ON DELETE SET NULL;

--
-- Omezení pro tabulku `objednavky_polozky`
--
ALTER TABLE `objednavky_polozky`
  ADD CONSTRAINT `objednavky_polozky_ibfk_1` FOREIGN KEY (`objednavka_id`) REFERENCES `objednavky` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `objednavky_polozky_ibfk_2` FOREIGN KEY (`produkt_id`) REFERENCES `produkty` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `produkty`
--
ALTER TABLE `produkty`
  ADD CONSTRAINT `produkty_ibfk_1` FOREIGN KEY (`kategorie_id`) REFERENCES `kategorie` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
