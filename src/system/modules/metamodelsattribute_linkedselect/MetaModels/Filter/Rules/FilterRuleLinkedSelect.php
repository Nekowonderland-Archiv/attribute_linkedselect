<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package     MetaModels
 * @subpackage  AttributeSelect
 * @author      Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Rules;

use MetaModels\Filter\FilterRule;
use MetaModels\Attribute\Select\Select;
use MetaModels\Attribute\Select\LinkedSelect;

/**
 * This is the MetaModelFilterRule class for handling select fields.
 *
 * @package    MetaModels
 * @subpackage AttributeSelect
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class FilterRuleLinkedSelect extends FilterRule
{
	/**
	 * The attribute this rule applies to.
	 *
	 * @var IAttribute
	 */
	protected $objAttribute = null;

	/**
	 * {@inheritDoc}
	 */
	public function __construct(LinkedSelect $objAttribute, $strValue)
	{
		parent::__construct();

		$this->objAttribute = $objAttribute;
		$this->value        = $strValue;
	}

	/**
	 * Convert a list of aliases to id list.
	 *
	 * @return int[]
	 */
	public function sanitizeValue()
	{
		$strTableNameId  = $this->objAttribute->get('mm_table');
		$strColNameId    = 'id';

		$arrValues = is_array($this->value) ? $this->value : explode(',', $this->value);

		$objDB = \Database::getInstance();

		$objSelectIds = $objDB
			->prepare(
				sprintf('SELECT %1$s FROM %2$s WHERE %1$s IN (%3$s)',
					$strColNameId,
					$strTableNameId,
					implode(',', array_fill(0, count($arrValues), '?'))
				)
			)
			->execute($arrValues);

		$arrValues = $objSelectIds->fetchEach($strColNameId);

		return $arrValues;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMatchingIds()
	{
		$arrValues = $this->sanitizeValue();
		if (!$arrValues)
		{
			return array();
		}

		$objDB      = \Database::getInstance();
		$objMatches = $objDB->execute(sprintf(
			'SELECT id FROM %s WHERE %s IN (%s)',
			$this->objAttribute->getMetaModel()->getTableName(),
			$this->objAttribute->getColName(),
			implode(',', $arrValues)
		));

		return $objMatches->fetchEach('id');
	}
}
