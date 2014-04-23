--
-- Database: `zvn`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl_privileges`
--

CREATE TABLE IF NOT EXISTS `acl_privileges` (
  `resource_id` int(10) unsigned NOT NULL DEFAULT '0',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `privilege` varchar(20) NOT NULL DEFAULT '0',
  `assert` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `acl_resources`
--

CREATE TABLE IF NOT EXISTS `acl_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_resources`
--

INSERT INTO `acl_resources` (`id`, `name`, `title`, `parent_id`, `level`, `lft`, `rgt`) VALUES
(1, 'root', 'Root', 0, 0, 0, 1);

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(512) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(11) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) NOT NULL,
  `revision` tinyint(4) NOT NULL DEFAULT '1',
  `hits` int(11) NOT NULL DEFAULT '0',
  `metadesc` varchar(1024) NOT NULL,
  `metakey` varchar(1024) NOT NULL,
  `metadata` varchar(2048) NOT NULL DEFAULT '{}',
  `status` varchar(20) NOT NULL DEFAULT 'publish',
  `access` varchar(250) NOT NULL DEFAULT '*',
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `level`, `title`, `alias`, `description`, `image`, `created_date`, `created_user_id`, `modified_date`, `modified_user_id`, `revision`, `hits`, `metadesc`, `metakey`, `metadata`, `status`, `access`, `lft`, `rgt`) VALUES
(1, 0, 0, '- Root - ', 'root', 'root', '', '2014-04-08 09:20:00', 1, '2014-04-08 09:20:00', 1, 0, 0, '', '', '', 'publish', '*', 0, 1);

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `alias` varchar(250) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `image` varchar(512) NOT NULL,
  `text` mediumtext NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(11) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) NOT NULL,
  `revision` tinyint(4) NOT NULL DEFAULT '1',
  `publish_date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL DEFAULT '0',
  `metadesc` varchar(1024) NOT NULL,
  `metakey` varchar(1024) NOT NULL,
  `metadata` varchar(2048) NOT NULL DEFAULT '{}',
  `status` varchar(20) NOT NULL DEFAULT 'publish',
  `featured` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` varchar(5120) NOT NULL DEFAULT '{}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

