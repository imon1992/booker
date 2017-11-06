-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 06 2017 г., 16:17
-- Версия сервера: 5.5.53
-- Версия PHP: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
(1, 1, 1, 'some desc', '2017-11-08', '2017-10-31 10:00:00', 0),
(2, 1, 1, 'some desc', '2017-11-15', '2017-10-31 10:00:00', 1),
(3, 1, 1, 'some desc', '2017-11-22', '2017-10-31 10:00:00', 1),
(4, 1, 1, 'some desc', '2017-11-08', '2017-10-31 10:00:00', 0),
(5, 1, 1, 'some desc', '2017-11-15', '2017-10-31 10:00:00', 4);

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
(1, 1, '14:30:00', '15:30:00'),
(2, 2, '14:30:00', '15:30:00'),
(3, 3, '14:30:00', '15:30:00'),
(4, 4, '12:30:00', '13:30:00'),
(5, 5, '12:30:00', '13:30:00');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `eventsTime`
--
ALTER TABLE `eventsTime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
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
  ADD CONSTRAINT `eventId` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
