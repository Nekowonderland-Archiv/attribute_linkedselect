-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the Contao    *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_metamodel_attribute`
-- 

CREATE TABLE `tl_metamodel_attribute` (
  `mm_table` varchar(255) NOT NULL default '',
  `mm_displayedValue` varchar(255) NOT NULL default '',
  `mm_sorting` varchar(255) NOT NULL default '',
  `mm_filter` int(11) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_metamodel_dcasetting`
-- 

CREATE TABLE `tl_metamodel_dcasetting` (
  `select_as_radio` varchar(1) NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;