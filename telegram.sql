-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Окт 28 2020 г., 23:07
-- Версия сервера: 10.3.16-MariaDB
-- Версия PHP: 7.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `id12952695_bd`
--

-- --------------------------------------------------------

--
-- Структура таблицы `telegram`
--

CREATE TABLE `telegram` (
  `id` int(11) NOT NULL,
  `user_id` int(30) NOT NULL,
  `code` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `input` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `step` int(1) NOT NULL DEFAULT 0,
  `lang` int(2) NOT NULL DEFAULT 27
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `telegram`
--

INSERT INTO `telegram` (`id`, `user_id`, `code`, `language`, `input`, `step`, `lang`) VALUES
(1, 283631065, '#include <iostream>\nusing namespace std;\nint main(){\nint a,b;\ncin>>a>>b;\ncout<<a+b;\n}', NULL, '15 5', 3, 27);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `telegram`
--
ALTER TABLE `telegram`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `telegram`
--
ALTER TABLE `telegram`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
