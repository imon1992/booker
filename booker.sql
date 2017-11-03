-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 03 2017 г., 16:16
-- Версия сервера: 5.5.53
-- Версия PHP: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `booker`
--

-- --------------------------------------------------------

--
-- Структура таблицы `boardrooms`
--

CREATE TABLE `boardrooms` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `boardrooms`
--

INSERT INTO `boardrooms` (`id`, `name`) VALUES
(1, 'Badroom1');

-- --------------------------------------------------------

--
-- Структура таблицы `bookerUsers`
--

CREATE TABLE `bookerUsers` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hash` varchar(200) NOT NULL,
  `role` int(11) NOT NULL,
  `isActive` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `bookerUsers`
--

INSERT INTO `bookerUsers` (`id`, `name`, `email`, `login`, `password`, `hash`, `role`, `isActive`) VALUES
(1, 'Abdrew Kolotii', 'imon@mksat.net', 'imon', '1234', '', 1, 'active'),
(2, 'Vasya Pupkin', 'som2mail,ru', 'asde', '1234', '', 2, 'active');

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `boardroom_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `timeOfCreate` datetime NOT NULL,
  `recursive` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `events`
--

INSERT INTO `events` (`id`, `user_id`, `boardroom_id`, `description`, `date`, `timeOfCreate`, `recursive`) VALUES
(1, 1, 1, 'desc', '2017-10-31', '2017-10-31 10:31:00', 0),
(2, 1, 1, 'desc', '2017-10-29', '2017-10-29 10:09:25', 0),
(3, 1, 1, 'desc', '2017-10-30', '2017-10-29 10:32:00', 0),
(4, 1, 1, 'some desc', '2017-11-03', '2017-11-03 10:00:00', 0),
(5, 1, 1, 'some desc', '2017-12-01', '2017-10-31 10:00:00', 1),
(6, 1, 1, 'some desc', '2017-11-07', '2017-10-31 10:00:00', 1),
(7, 1, 1, 'some desc', '2017-11-14', '2017-10-31 10:00:00', 1),
(8, 1, 1, 'some desc', '2017-11-21', '2017-10-31 10:00:00', 1),
(9, 1, 1, 'some desc', '2017-11-28', '2017-10-31 10:00:00', 1),
(10, 1, 1, 'some desc', '2017-11-07', '2017-10-31 10:00:00', 1),
(11, 1, 1, 'some desc', '2017-11-14', '2017-10-31 10:00:00', 1),
(12, 1, 1, 'some desc', '2017-11-21', '2017-10-31 10:00:00', 1),
(13, 1, 1, 'some desc', '2017-11-28', '2017-10-31 10:00:00', 1),
(14, 1, 1, 'some desc', '2017-11-07', '2017-10-31 10:00:00', 1),
(15, 1, 1, 'some desc', '2017-11-14', '2017-10-31 10:00:00', 1),
(16, 1, 1, 'some desc', '2017-11-21', '2017-10-31 10:00:00', 1),
(17, 1, 1, 'some desc', '2017-11-28', '2017-10-31 10:00:00', 1),
(18, 1, 1, 'some desc', '2017-11-03', '2017-10-31 10:00:00', 1),
(19, 1, 1, 'some desc', '2017-11-10', '2017-10-31 10:00:00', 1),
(20, 1, 1, 'some desc', '2017-11-17', '2017-10-31 10:00:00', 1),
(21, 1, 1, 'some desc', '2017-11-24', '2017-10-31 10:00:00', 1),
(22, 1, 1, 'some desc', '2017-12-01', '2017-10-31 10:00:00', 1),
(23, 1, 1, 'some desc', '2017-11-04', '2017-10-31 10:00:00', 1),
(24, 1, 1, 'some desc', '2017-11-11', '2017-10-31 10:00:00', 1),
(25, 1, 1, 'some desc', '2017-11-18', '2017-10-31 10:00:00', 1),
(26, 1, 1, 'some desc', '2017-11-25', '2017-10-31 10:00:00', 1),
(27, 1, 1, 'some desc', '2017-12-02', '2017-10-31 10:00:00', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `eventsTime`
--

CREATE TABLE `eventsTime` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `eventsTime`
--

INSERT INTO `eventsTime` (`id`, `event_id`, `startTime`, `endTime`) VALUES
(2, 1, '09:00:00', '10:00:00'),
(3, 1, '12:00:00', '16:00:00'),
(4, 2, '08:00:00', '10:00:00'),
(5, 2, '12:00:00', '16:00:00'),
(6, 3, '12:00:00', '16:00:00'),
(7, 14, '08:30:00', '09:30:00'),
(8, 15, '08:30:00', '09:30:00'),
(9, 16, '08:30:00', '09:30:00'),
(10, 17, '08:30:00', '09:30:00'),
(11, 18, '08:30:00', '09:30:00'),
(12, 19, '08:30:00', '09:30:00'),
(13, 20, '08:30:00', '09:30:00'),
(14, 21, '08:30:00', '09:30:00'),
(15, 22, '08:30:00', '09:30:00'),
(16, 23, '08:30:00', '09:30:00'),
(17, 24, '08:30:00', '09:30:00'),
(18, 25, '08:30:00', '09:30:00'),
(19, 26, '08:30:00', '09:30:00'),
(20, 27, '08:30:00', '09:30:00');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'user');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `boardrooms`
--
ALTER TABLE `boardrooms`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bookerUsers`
--
ALTER TABLE `bookerUsers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role` (`role`);

--
-- Индексы таблицы `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user_id`),
  ADD KEY `boardroom` (`boardroom_id`);

--
-- Индексы таблицы `eventsTime`
--
ALTER TABLE `eventsTime`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eventId` (`event_id`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `boardrooms`
--
ALTER TABLE `boardrooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `bookerUsers`
--
ALTER TABLE `bookerUsers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT для таблицы `eventsTime`
--
ALTER TABLE `eventsTime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookerUsers`
--
ALTER TABLE `bookerUsers`
  ADD CONSTRAINT `role` FOREIGN KEY (`role`) REFERENCES `roles` (`id`);

--
-- Ограничения внешнего ключа таблицы `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `boardroom` FOREIGN KEY (`boardroom_id`) REFERENCES `boardrooms` (`id`),
  ADD CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `bookerUsers` (`id`);

--
-- Ограничения внешнего ключа таблицы `eventsTime`
--
ALTER TABLE `eventsTime`
  ADD CONSTRAINT `eventId` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
