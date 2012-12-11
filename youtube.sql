DROP TABLE IF EXISTS `videos`;

CREATE TABLE `videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `videourl` text NOT NULL,
  `expires` int(11) NOT NULL,
  `owner` text NOT NULL,
  `status` int(11) NOT NULL,
  `videofile` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
