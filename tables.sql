SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `channels` (
  `id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL DEFAULT '',
  `utopia_channelid` varchar(32) NOT NULL DEFAULT '',
  `description` varchar(64) NOT NULL DEFAULT '',
  `is_readonly` set('0','1') NOT NULL DEFAULT '0',
  `is_readonly_privacy` set('0','1') NOT NULL DEFAULT '0',
  `channel_type` varchar(16) NOT NULL DEFAULT 'public'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `channels` (`id`, `title`, `utopia_channelid`, `description`, `is_readonly`, `is_readonly_privacy`, `channel_type`) VALUES
(1, 'Utopia Chat Lobby', 'D53B4431FD604E2F0261792444797AA4', 'The official room for helping each other with Utopia', '0', '0', 'public');

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `channel_index` int(11) NOT NULL DEFAULT '0',
  `message_text` text NOT NULL,
  `user_nickname` varchar(64) NOT NULL DEFAULT '',
  `utopia_messageid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utopia_channelid` (`utopia_channelid`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utopia_messageid` (`utopia_messageid`),
  ADD KEY `channel_index` (`channel_index`);


ALTER TABLE `channels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
