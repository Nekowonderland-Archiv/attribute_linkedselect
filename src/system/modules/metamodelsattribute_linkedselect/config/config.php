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

//$GLOBALS['METAMODELS']['attributes']['linkedselect'] = array
//(
//	'class' => 'MetaModels\Attribute\LinkedSelect\LinkedSelect',
//	'image' => 'system/modules/metamodelsattribute_linkedselect/html/select.png'
//);

$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'MetaModels\Attribute\LinkedSelect\AttributeTypeFactory';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'MetaModels\DcGeneral\Events\MetaModels\LinkedSelect\BackendSubscriber';

$GLOBALS['TL_EVENTS'][\ContaoCommunityAlliance\Contao\EventDispatcher\Event\CreateEventDispatcherEvent::NAME][] =
	'MetaModels\DcGeneral\Events\Table\Attribute\LinkedSelect\PropertyAttribute::registerEvents';

$GLOBALS['METAMODELS']['filters']['select']['attr_filter'][] = 'linkedselect';
