<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeTags
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

$GLOBALS['METAMODELS']['attributes']['linkedselect'] = array
(
	'class' => 'MetaModelAttributeLinkedSelect',
	'image' => 'system/modules/metamodelsattribute_linkedselect/html/select.png'
);

$GLOBALS['METAMODELS']['filters']['select']['attr_filter'][] = 'linkedselect';