CREATE TABLE IF NOT EXISTS `{DATABASE}`.`{TABLE}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(255) NOT NULL,
  `niveau` smallint(6) NOT NULL,
  `points` int(11) NOT NULL,
  `combo` int(11) NOT NULL,
  `block` mediumint(9) NOT NULL,
  `single` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
