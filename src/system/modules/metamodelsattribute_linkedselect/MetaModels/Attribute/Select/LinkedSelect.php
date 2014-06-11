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

namespace MetaModels\Attribute\Select;

use MetaModels\Attribute\AbstractHybrid as MetaModelAttributeHybrid;
use MetaModels\Filter\Setting\Factory as FilterSettingFactory;
use MetaModels\Render\Template as MetaModelTemplate;
use MetaModels\Factory as MetaModelFactory;

/**
 * This is the MetaModelAttribute class for handling tag attributes.
 *
 * @package    MetaModels
 * @subpackage AttributeTags
 */
class LinkedSelect extends MetaModelAttributeHybrid
{

	/**
	 * when rendered via a template, this returns the values to be stored in the template.
	 */
	protected function prepareTemplate(MetaModelTemplate $objTemplate, $arrRowData, $objSettings = null)
	{
		parent::prepareTemplate($objTemplate, $arrRowData, $objSettings);
		$objTemplate->displayedValue = $this->get('mm_displayedValue');
	}

	/**
	 * Determine the column to be used for alias.
	 * This is either the configured alias column or the id, if
	 * an alias column is absent.
	 *
	 * @return string the name of the column.
	 */
	public function getAliasCol()
	{
		$strColNameAlias = $this->get('tag_alias');
		if (!$strColNameAlias)
		{
			$strColNameAlias = $this->get('tag_id');
		}
		return $strColNameAlias;
	}

	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttribute
	/////////////////////////////////////////////////////////////////

	/**
	 * {@inheritdoc}
	 */
	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'mm_table',
			'mm_displayedValue',
			'mm_sorting',
			'mm_filter',
			'mm_filterparams',
			'select_as_radio',
			'includeBlankOption',
			'mandatory',
			'chosen',
			'filterable',
			'searchable',
			'sortable',
			'flag'
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFieldDefinition($arrOverrides = array())
	{
		// TODO: add tree support here.
		$arrFieldDef = parent::getFieldDefinition($arrOverrides);

		// If select as radio is true, change the input type.
		if ($arrOverrides['select_as_radio'] == true)
		{
			$arrFieldDef['inputType'] = 'radio';
		}
		else
		{
			$arrFieldDef['inputType'] = 'select';
		}

		$arrFieldDef['options'] = $this->getFilterOptions(null, false);
		return $arrFieldDef;
	}

	/**
	 * {@inheritdoc}
	 */
	public function valueToWidget($varValue)
	{
		$arrReturn = array();

		if (!is_array($varValue) || empty($varValue))
		{
			return $arrReturn;
		}

		foreach ($varValue as $mixItem)
		{
			if (is_array($mixItem) && isset($mixItem['id']))
			{
				$arrReturn[] = $mixItem['id'];
			}
			elseif (!is_array($mixItem))
			{
				$arrReturn[] = $mixItem;
			}
		}

		return $arrReturn;
	}

	/**
	 * {@inheritdoc}
	 */
	public function widgetToValue($varValue, $intId)
	{
		return $varValue;
	}

	/**
	 * {@inheritdoc}
	 *
	 * Fetch filter options from foreign table.
	 *
	 */
	public function getFilterOptions($arrIds, $usedOnly, &$arrCount = null)
	{
		$strMMName         = $this->get('mm_table');
		$strDisplayedValue = $this->get('mm_displayedValue');
		$strSortingValue   = $this->get('mm_sorting') ? $this->get('mm_sorting') : 'id';
		$intFilterId       = $this->get('mm_filter');
		$arrFilterParams   = (array) $this->get('mm_filterparams');

		$arrReturn = array();

		if ($strMMName && $strDisplayedValue)
		{
			// Change language.
			if (TL_MODE == 'BE')
			{
				$strCurrentLanguage     = $GLOBALS['TL_LANGUAGE'];
				$GLOBALS['TL_LANGUAGE'] = $this->getMetaModel()->getActiveLanguage();
			}

			$objMetaModel = MetaModelFactory::byTableName($strMMName);
			$objFilter    = $objMetaModel->getEmptyFilter();

			// Set Filter and co.
			$objFilterSettings = FilterSettingFactory::byId($intFilterId);
			if ($objFilterSettings)
			{
				$arrValues         = $_GET;
				$arrPresets        = $arrFilterParams;
				$arrPresetNames    = $objFilterSettings->getParameters();
				$arrFEFilterParams = array_keys($objFilterSettings->getParameterFilterNames());

				$arrProcessed = array();

				// We have to use all the preset values we want first.
				foreach ($arrPresets as $strPresetName => $arrPreset)
				{
					if (in_array($strPresetName, $arrPresetNames))
					{
						$arrProcessed[$strPresetName] = $arrPreset['value'];
					}
				}

				// now we have to use all FE filter params, that are either:
				// * not contained within the presets
				// * or are overridable.
				foreach ($arrFEFilterParams as $strParameter)
				{
					// unknown parameter? - next please
					if (!array_key_exists($strParameter, $arrValues))
					{
						continue;
					}

					// not a preset or allowed to override? - use value
					if ((!array_key_exists($strParameter, $arrPresets)) || $arrPresets[$strParameter]['use_get'])
					{
						$arrProcessed[$strParameter] = $arrValues[$strParameter];
					}
				}

				$objFilterSettings->addRules($objFilter, $arrProcessed);
			}

			$objItems = $objMetaModel->findByFilter($objFilter, $strSortingValue);

			// Reset language.
			if (TL_MODE == 'BE')
			{
				$GLOBALS['TL_LANGUAGE'] = $strCurrentLanguage;
			}

			foreach ($objItems as $objItem)
			{
				$arrItem = $objItem->parseValue();

				$strValue = $arrItem['text'][$strDisplayedValue];
				$strAlias = $objItem->get('id');

				$arrReturn[$strAlias] = $strValue;
			}
		}

		return $arrReturn;
	}

	/**
	 * {@inheritdoc}
	 */
	public function searchFor($strPattern)
	{
//		$objFilterRule = new MetaModelFilterRuleTags($this, $strPattern);
//		return $objFilterRule->getMatchingIds();

		return array();
	}

	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttributeSimple
	/////////////////////////////////////////////////////////////////

	public function getSQLDataType()
	{
		return 'int(11) NOT NULL default \'0\'';
	}

	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttributeComplex
	/////////////////////////////////////////////////////////////////

	public function getDataFor($arrIds)
	{
		$strMMName         = $this->get('mm_table');
		$strDisplayedValue = $this->get('mm_displayedValue');
		$arrReturn         = array();

		// Get data from MM.
		$objMetaModel = MetaModelFactory::byTableName($strMMName);

		if ($strMMName && $objMetaModel && $strDisplayedValue)
		{
			$strColName = $this->getColName();
			$strQuery   = sprintf('SELECT %2$s, id FROM %1$s WHERE id IN (%3$s)',
				$this->getMetaModel()->getTableName(), // 1
				$strColName, // 2
				implode(',', array_map('intval', $arrIds)) // 3
			);

			$objResult = \Database::getInstance()->prepare($strQuery)->execute();

			while ($objResult->next())
			{
				$objItem = $objMetaModel->findById($objResult->$strColName);

				if ($objItem)
				{
					$mixID     = $objResult->id;
					$arrValues = $objItem->parseValue();

					$arrReturn[$mixID][] = array_merge(array(
						'id'      => $arrValues['raw']['id'],
						'pid'     => $arrValues['raw']['pid'],
						'sorting' => $arrValues['raw']['sorting'],
						'tstamp'  => $arrValues['raw']['tstamp'],
					), $arrValues['text']);
				}
			}
		}

		return $arrReturn;
	}

	public function setDataFor($arrValues)
	{
		$strMMName         = $this->get('mm_table');
		$strDisplayedValue = $this->get('mm_displayedValue');

		if ($strMMName && $strDisplayedValue)
		{
			$strQuery = sprintf('UPDATE %1$s SET %2$s=? WHERE %1$s.id=?',
				$this->getMetaModel()->getTableName(), // 1
				$this->getColName() // 2
			);

			$objDB = \Database::getInstance();
			foreach ($arrValues as $intItemId => $arrValue)
			{
				if (is_array($arrValue) && array_key_exists('id', $arrValue))
				{
					$objDB->prepare($strQuery)->execute($arrValue['id'], $intItemId);
				}
				else if (is_array($arrValue))
				{
					$arrValues = array_values($arrValue);
					$objDB->prepare($strQuery)->execute($arrValues[0], $intItemId);
				}
				else
				{
					$objDB->prepare($strQuery)->execute($arrValue, $intItemId);
				}
			}
		}
	}

	public function unsetDataFor($arrIds)
	{
		$strMMName         = $this->get('mm_table');
		$strDisplayedValue = $this->get('mm_displayedValue');

		if ($strMMName && $strDisplayedValue)
		{
			$strQuery = sprintf('UPDATE %1$s SET %2$s=0 WHERE %1$s.id IN (%3$s)', $this->getMetaModel()->getTableName(), $this->getColName(), implode(',', $arrIds)
			);
			\Database::getInstance()->execute($strQuery);
		}
	}

}
