--
-- Database: `zvn`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl_privileges`
--

CREATE TABLE IF NOT EXISTS `acl_privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(10) unsigned NOT NULL DEFAULT '0',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `privilege` varchar(20) NOT NULL DEFAULT '0',
  `assert` varchar(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
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
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `password_salt` varchar(50) NOT NULL,
  `real_name` varchar(150) NOT NULL,
  `email` varchar(250) NOT NULL,
  `register_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_visit_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `active` varchar(100) NOT NULL DEFAULT '0',
  `params` varchar(5120) CHARACTER SET ucs2 NOT NULL DEFAULT '{}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `password_salt`, `real_name`, `email`, `register_date`, `last_visit_date`, `block`, `active`, `params`) VALUES
(1, 'admin', 'ddf1fcdc062258c60e18f6a3859b164b', 'smkxkb9hgk1wtu1eg4tprvh0k1m264b2h36pteb90zafed7eui', 'Administrator', 'it@bni.vn', '1999-11-29 02:30:00', '2014-04-21 14:29:08', 1, '1', '{"template":null,"language":null,"editor":null,"timezone":"Asia\\/Bangkok"}');


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

--
-- Table structure for table `extensions`
--

CREATE TABLE IF NOT EXISTS `extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(70) CHARACTER SET utf8 NOT NULL,
  `name` varchar(70) CHARACTER SET utf8 NOT NULL,
  `type` varchar(70) CHARACTER SET utf8 NOT NULL,
  `location` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'site',
  `protected` tinyint(4) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'unpublish',
  `params` varchar(5120) CHARACTER SET utf8 NOT NULL DEFAULT '{}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `extensions`
--

INSERT INTO `extensions` (`id`, `title`, `name`, `type`, `location`, `protected`, `status`, `params`) VALUES
(1, 'Default Module', 'index', 'module', 'admin', 1, 'publish', '{}'),
(2, 'Users Module', 'users', 'module', 'admin', 1, 'publish', '{"allow_user_register":"1","new_user_register_group":"2","new_user_account_activation":"1","notification_mail_to_administrators":"0"}'),
(3, 'Menus Module', 'menus', 'module', 'admin', 1, 'publish', '{}'),
(4, 'Admin Module', 'admin', 'module', 'admin', 1, 'publish', '{}'),
(5, 'Extensions Module', 'extensions', 'module', 'admin', 1, 'publish', '{}'),
(6, 'Content Module', 'contents', 'module', 'admin', 1, 'publish', '{}'),
(7, 'Widgets', 'widgets', 'module', 'admin', 1, 'publish', '{}'),
(8, 'Plugins', 'plugins', 'module', 'admin', 1, 'publish', '{}'),
(9, 'Languages', 'languages', 'module', 'admin', 1, 'publish', '{}'),
(10, 'Templates', 'templates', 'module', 'admin', 1, 'publish', '{}'),
(11, 'Menu', 'menu', 'widget', 'site', 1, 'publish', '{}'),
(12, 'Breadcrumb', 'breadcrumb', 'widget', 'site', 1, 'publish', '{}'),
(13, 'Template Default', 'default', 'template', 'site', 1, 'publish', '{"author":"Author: J Nguyen <br/>Email: vunh2301@gmail.com ","date":"2014-04-08 06:20:00","version":"1.0"}'),
(14, 'Template Bootstrap', 'bootstrap', 'template', 'site', 0, 'publish', '{"author":"Author: J Nguyen <br/>Email: vunh2301@gmail.com ","date":"2014-04-08 06:20:00","version":"1.0"}'),
(15, 'Template Admin', 'admin', 'template', 'admin', 1, 'publish', '{"author":"Author: J Nguyen <br/>Email: vunh2301@gmail.com ","date":"2014-04-08 06:20:00","version":"1.0"}'),
(16, 'Administrator Menu', 'menu', 'widget', 'admin', 1, 'publish', '{}'),
(17, 'Administrator Breadcrumb', 'breadcrumb', 'widget', 'admin', 1, 'publish', '{}');