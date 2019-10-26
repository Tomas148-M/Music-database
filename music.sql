-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Sob 26. říj 2019, 22:55
-- Verze serveru: 10.1.38-MariaDB
-- Verze PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `hudba`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `friends`
--

CREATE TABLE `friends` (
  `friend_id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `kapela_name`
--

CREATE TABLE `kapela_name` (
  `ID` int(200) NOT NULL,
  `K_name` varchar(200) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `kapela_name`
--

INSERT INTO `kapela_name` (`ID`, `K_name`) VALUES
(1, 'KABAT'),
(2, 'JELEN'),
(3, 'EWA-FARNA'),
(4, 'Mandrage'),
(5, 'Wohnout'),
(6, 'Kristina'),
(7, 'Michal Hrůza'),
(8, 'Lenny'),
(9, 'Škwor'),
(10, 'CHINASKI'),
(11, 'Divokej Bill'),
(12, 'Xindl X'),
(13, 'Adele'),
(14, 'Avicii'),
(15, 'Rybičky 48'),
(16, 'Harlej'),
(17, 'ARGEMA'),
(19, 'Walda gang'),
(20, 'Katy Perry');

-- --------------------------------------------------------

--
-- Struktura tabulky `media_name`
--

CREATE TABLE `media_name` (
  `ID` int(200) NOT NULL,
  `M_name` varchar(200) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `modul`
--

CREATE TABLE `modul` (
  `ID` int(10) NOT NULL,
  `Modul` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `Page` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `number` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `modul`
--

INSERT INTO `modul` (`ID`, `Modul`, `Page`, `number`) VALUES
(1, 'Update', 'song_detail', 0),
(2, 'Delete_song', 'song_detail', 0),
(3, 'Insert_to_playlist', 'song_detail', 0),
(4, 'Insert', 'song_vypis', 2),
(5, 'Add_song', 'song_detail', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `OrderNumber` int(11) NOT NULL,
  `PersonID` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `playlist`
--

CREATE TABLE `playlist` (
  `ID` int(15) NOT NULL,
  `ID_unikatni` int(20) NOT NULL,
  `ID_uzivatele` int(11) NOT NULL,
  `ID_playlist` int(11) NOT NULL,
  `ID_song` int(11) NOT NULL,
  `Date_insert` varchar(25) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `playlist`
--

INSERT INTO `playlist` (`ID`, `ID_unikatni`, `ID_uzivatele`, `ID_playlist`, `ID_song`, `Date_insert`) VALUES
(9, 65, 1, 4, 24, '2019/04/06'),
(12, 75, 1, 4, 29, '2019/04/06'),
(13, 35, 1, 4, 9, '2019/04/06'),
(14, 0, 1, 7, 22, '2019/10/26');

-- --------------------------------------------------------

--
-- Struktura tabulky `playlist_name`
--

CREATE TABLE `playlist_name` (
  `ID` int(100) NOT NULL,
  `Name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `ID_uzivatele` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `playlist_name`
--

INSERT INTO `playlist_name` (`ID`, `Name`, `ID_uzivatele`) VALUES
(3, 'Auto', 2),
(7, 'Pokus', 1),
(4, 'Farna', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `pok`
--

CREATE TABLE `pok` (
  `ID` int(2) NOT NULL,
  `ID_user` int(2) NOT NULL,
  `ID_role` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `pok`
--

INSERT INTO `pok` (`ID`, `ID_user`, `ID_role`) VALUES
(1, 2, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `pravo`
--

CREATE TABLE `pravo` (
  `ID` int(10) NOT NULL,
  `Role` int(10) NOT NULL,
  `Modul` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `pravo`
--

INSERT INTO `pravo` (`ID`, `Role`, `Modul`) VALUES
(1, 1, 3),
(2, 2, 1),
(3, 2, 2),
(4, 2, 3),
(5, 2, 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `role`
--

CREATE TABLE `role` (
  `ID` int(20) NOT NULL,
  `Role_name` varchar(100) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `role`
--

INSERT INTO `role` (`ID`, `Role_name`) VALUES
(1, 'ordinar'),
(2, 'extra'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Struktura tabulky `role_user`
--

CREATE TABLE `role_user` (
  `ID` int(20) NOT NULL,
  `ID_user` int(40) NOT NULL,
  `ID_role` int(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `role_user`
--

INSERT INTO `role_user` (`ID`, `ID_user`, `ID_role`) VALUES
(1, 1, 2),
(2, 2, 1),
(6, 7, 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `skladby`
--

CREATE TABLE `skladby` (
  `ID` int(5) NOT NULL,
  `Nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `Kapela` smallint(6) NOT NULL,
  `Delka` smallint(6) DEFAULT NULL,
  `ID_uzivatel` int(11) NOT NULL,
  `Odkaz` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `STAV` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `skladby`
--

INSERT INTO `skladby` (`ID`, `Nazev`, `Kapela`, `Delka`, `ID_uzivatel`, `Odkaz`, `STAV`) VALUES
(2, 'V pekle sudy valej', 1, 3, 1, 'https://www.youtube.com/watch?v=tEbllP3j32A&t=9s', 0),
(8, 'Peří,prach a broky', 1, 186, 1, 'https://www.youtube.com/watch?v=iWtrItlynr4', 0),
(9, 'Na ostří nože', 3, 3, 1, 'https://www.youtube.com/watch?v=ac8v0WsJpZs', 0),
(14, 'Vino', 10, 0, 1, 'https://www.youtube.com/watch?v=gOqv-VBrXeA', 0),
(15, 'Skyfall', 13, 0, 1, 'https://www.youtube.com/watch?v=DeumyOzKqgI', 0),
(16, 'Do roka a do dne', 11, 0, 1, 'https://www.youtube.com/watch?v=2fJPimmEa7c', 0),
(17, 'Vstavej', 11, 0, 1, 'https://www.youtube.com/watch?v=GGSmYpPWf3Y', 0),
(18, 'Spinavy zada', 17, 0, 1, 'https://www.youtube.com/watch?v=-CPFFsnRoMk', 0),
(19, 'Magdalena', 2, 0, 1, 'https://www.youtube.com/watch?v=c2j9BxOF8yo', 0),
(20, 'Každý ráno', 10, 0, 1, 'https://www.youtube.com/watch?v=jtrn_bcRm7Y', 0),
(21, 'Si pro me best', 6, 3, 1, 'https://www.youtube.com/watch?v=Q59Hbseycgk', 0),
(22, 'Rolling in the Deep', 13, 3, 1, 'https://www.youtube.com/watch?v=rYEDA3JcQqw', 0),
(23, 'Opičáci', 19, 0, 2, 'https://www.youtube.com/watch?v=Axm-0pEFHjU', 0),
(24, 'Bumerang', 3, 3, 1, 'https://www.youtube.com/watch?v=1Jte2lMv8ik', 0),
(26, 'Horehronie', 6, 3, 1, 'https://www.youtube.com/watch?v=kghCqyMLPFA&start_radio=1&list=RDkghCqyMLPFA', 0),
(27, 'Firework', 20, 3, 1, 'https://www.youtube.com/watch?v=QGJuMBdaqIw', 0),
(28, 'Svařák', 19, 2, 1, 'https://www.youtube.com/watch?v=CASsiD5Vik0', 0),
(29, 'Boky jako skříň', 3, 3, 1, 'https://www.youtube.com/watch?v=Ozuv4qyraRc', 0),
(30, 'Jelen', 2, 3, 1, 'https://www.youtube.com/watch?v=wO1Ld8tVrRc', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
  `ID` int(10) UNSIGNED NOT NULL,
  `Jmeno` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `Prijmeni` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `Email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `Login` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `Heslo` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `Stav` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`ID`, `Jmeno`, `Prijmeni`, `Email`, `Login`, `Heslo`, `Stav`) VALUES
(1, 'Tomáš', 'Novotný', 'admin@-.cz', 'Tomas', '148', 0);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`friend_id`);

--
-- Klíče pro tabulku `kapela_name`
--
ALTER TABLE `kapela_name`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `K_name` (`K_name`),
  ADD UNIQUE KEY `K_name_2` (`K_name`),
  ADD UNIQUE KEY `K_name_3` (`K_name`);

--
-- Klíče pro tabulku `media_name`
--
ALTER TABLE `media_name`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `modul`
--
ALTER TABLE `modul`
  ADD UNIQUE KEY `ID` (`ID`,`Modul`);

--
-- Klíče pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `FK_PersonOrder` (`PersonID`);

--
-- Klíče pro tabulku `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Uk_ID` (`ID_unikatni`);

--
-- Klíče pro tabulku `playlist_name`
--
ALTER TABLE `playlist_name`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `pok`
--
ALTER TABLE `pok`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `pravo`
--
ALTER TABLE `pravo`
  ADD UNIQUE KEY `ID` (`ID`,`Role`,`Modul`);

--
-- Klíče pro tabulku `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Role_name` (`Role_name`),
  ADD UNIQUE KEY `Role_name_2` (`Role_name`),
  ADD UNIQUE KEY `Role_name_3` (`Role_name`),
  ADD UNIQUE KEY `Role_name_4` (`Role_name`);

--
-- Klíče pro tabulku `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID_user` (`ID_user`,`ID_role`);

--
-- Klíče pro tabulku `skladby`
--
ALTER TABLE `skladby`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Nazev` (`Nazev`),
  ADD UNIQUE KEY `Nazev_2` (`Nazev`),
  ADD UNIQUE KEY `Nazev_3` (`Nazev`),
  ADD KEY `Kapela` (`Kapela`,`ID_uzivatel`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Login` (`Login`),
  ADD UNIQUE KEY `Login_2` (`Login`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `friends`
--
ALTER TABLE `friends`
  MODIFY `friend_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `kapela_name`
--
ALTER TABLE `kapela_name`
  MODIFY `ID` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pro tabulku `media_name`
--
ALTER TABLE `media_name`
  MODIFY `ID` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `modul`
--
ALTER TABLE `modul`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `playlist`
--
ALTER TABLE `playlist`
  MODIFY `ID` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pro tabulku `playlist_name`
--
ALTER TABLE `playlist_name`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pro tabulku `pok`
--
ALTER TABLE `pok`
  MODIFY `ID` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `pravo`
--
ALTER TABLE `pravo`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `role`
--
ALTER TABLE `role`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pro tabulku `role_user`
--
ALTER TABLE `role_user`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `skladby`
--
ALTER TABLE `skladby`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
