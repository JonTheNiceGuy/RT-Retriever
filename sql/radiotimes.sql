-- Thanks to this post, the field columns in the channel data nows makes much more sense!
-- http://www.birtles.org.uk/phpbb3/viewtopic.php?f=5&t=245


CREATE TABLE IF NOT EXISTS `channels` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `channel_map` (
  `channel_id` int(11) NOT NULL,
  `channel_number` int(11) NOT NULL,
  `have_access` tinyint(1) NOT NULL,
  `is_favourite` tinyint(1) NOT NULL,
  PRIMARY KEY  (`channel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `listings` (
  `listing_id` int(11) NOT NULL auto_increment,
  `channel_id` int(11) NOT NULL,
  `show_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `episode_name` varchar(255) NOT NULL,
  `release_year` int(4) NOT NULL,
  `director` varchar(255) NOT NULL,
  `cast` text NOT NULL,
  `is_premier` varchar(5) NOT NULL,
  `is_film` varchar(5) NOT NULL,
  `is_repeat` varchar(5) NOT NULL,
  `is_subtitled` varchar(5) NOT NULL,
  `is_in_widescreen` varchar(5) NOT NULL,
  `is_new_series` varchar(5) NOT NULL,
  `is_BSL_signed` varchar(5) NOT NULL,
  `is_black_and_white` varchar(5) NOT NULL,
  `rating_stars` varchar(2) NOT NULL,
  `rating_age` varchar(2) NOT NULL,
  `genre` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `is_radio_times_choice` varchar(5) NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  `duration` varchar(255) NOT NULL,
  PRIMARY KEY  (`listing_id`),
  KEY `show_name` (`show_name`),
  FULLTEXT KEY `cast` (`cast`),
  FULLTEXT KEY `summary` (`summary`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `people` (
  `id` int(11) NOT NULL auto_increment,
  `show_id` int(11) NOT NULL,
  `person` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
