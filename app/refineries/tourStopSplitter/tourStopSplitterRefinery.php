<?php
/* ----------------------------------------------------------------------
 * tourStopSplitterRefinery.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 	require_once(__CA_LIB_DIR__.'/ca/Import/BaseRefinery.php');
 	require_once(__CA_LIB_DIR__.'/ca/Utils/DataMigrationUtils.php');
 
	class tourStopSplitterRefinery extends BaseRefinery {
		# -------------------------------------------------------
		
		# -------------------------------------------------------
		public function __construct() {
			$this->ops_name = 'tourStopSplitter';
			$this->ops_title = _t('Tour stop splitter');
			$this->ops_description = _t('Splits tour stops');
			
			parent::__construct();
		}
		# -------------------------------------------------------
		/**
		 * Override checkStatus() to return true
		 */
		public function checkStatus() {
			return array(
				'description' => $this->getDescription(),
				'errors' => array(),
				'warnings' => array(),
				'available' => true,
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public function refine(&$pa_destination_data, $pa_group, $pa_item, $pa_source_data, $pa_options=null) {
			$va_group_dest = explode(".", $pa_group['destination']);
			$vs_terminal = array_pop($va_group_dest);
			$pm_value = $pa_source_data[$pa_item['source']];
			
			if ($vs_delimiter = $pa_item['settings']['tourStopSplitter_delimiter']) {
				$va_tour_stops = explode($vs_delimiter, $pm_value);
			} else {
				$va_tour_stops = array($pm_value);
			}
			
			$va_vals = array();
			$vn_c = 0;
			foreach($va_tour_stops as $vn_i => $vs_tour_stop) {
				if (!$vs_tour_stop = trim($vs_tour_stop)) { continue; }
				
				
				if(in_array($vs_terminal, array('name_singular', 'name_plural'))) {
					return $vs_tour_stop;
				}
			
				if (in_array($vs_terminal, array('preferred_labels', 'nonpreferred_labels'))) {
					return array('name_singular' => $vs_tour_stop, 'name_plural' => $vs_tour_stop);	
				}
			
				// Set label
				$va_val = array('preferred_labels' => array('name_singular' => $vs_tour_stop, 'name_plural' => $vs_tour_stop));
			
				// Set relationship type
				if (
					($vs_rel_type_opt = $pa_item['settings']['tourStopSplitter_relationshipType'])
				) {
					if (!($va_val['_relationship_type'] = BaseRefinery::parsePlaceholder($vs_rel_type_opt, $pa_source_data, $pa_item, $vs_delimiter, $vn_c))) {
						if ($vs_rel_type_opt = $pa_item['settings']['tourStopSplitter_relationshipTypeDefault']) {
							$va_val['_relationship_type'] = BaseRefinery::parsePlaceholder($vs_rel_type_opt, $pa_source_data, $pa_item, $vs_delimiter, $vn_c);
						}
					}
				}
			
				// Set tour_stop_type
				if (
					($vs_type_opt = $pa_item['settings']['tourStopSplitter_tourStopType'])
				) {
					if (!($va_val['_type'] = BaseRefinery::parsePlaceholder($vs_type_opt, $pa_source_data, $pa_item, $vs_delimiter, $vn_c))) {
						if($vs_type_opt = $pa_item['settings']['tourStopSplitter_tourStopTypeDefault']) {
							$va_val['_type'] = BaseRefinery::parsePlaceholder($vs_type_opt, $pa_source_data, $pa_item, $vs_delimiter, $vn_c);
						}
					}
				}
				
				// Set tour 
				$vn_tour_id = null;
				if ($vs_tour = $pa_item['settings']['tourStopSplitter_tour']) {
					$vn_tour_id = caGetTourID($vs_tour);
				}
				if (!$vn_tour_id) {
					// No tour = bail!
					// TODO: log this
					return array();
				} 
				
				$va_val['tour_id'] = $vn_tour_id;
				
				$t_item = new ca_tour_stops();
				$t_item->load(array('parent_id' => null, 'tour_id' => $vn_tour_id));	// get root
				$va_val['_parent_id'] = $t_item->getPrimaryKey();
				
				// Set attributes
				if (is_array($pa_item['settings']['tourStopSplitter_attributes'])) {
					$va_attr_vals = array();
					foreach($pa_item['settings']['tourStopSplitter_attributes'] as $vs_element_code => $va_attrs) {
						if(is_array($va_attrs)) {
							foreach($va_attrs as $vs_k => $vs_v) {
								$va_attr_vals[$vs_element_code][$vs_k] = BaseRefinery::parsePlaceholder($vs_v, $pa_source_data, $pa_item);
							}
						}
					}
					$va_val = array_merge($va_val, $va_attr_vals);
				}
				
				$va_vals[] = $va_val;
				$vn_c++;
			}
			
			return $va_vals;
		}
		# -------------------------------------------------------	
		/**
		 * tourStopSplitter returns multiple values
		 *
		 * @return bool Always true
		 */
		public function returnsMultipleValues() {
			return true;
		}
		# -------------------------------------------------------
	}
	
	 BaseRefinery::$s_refinery_settings['tourStopSplitter'] = array(		
			'tourStopSplitter_delimiter' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_SELECT,
				'width' => 10, 'height' => 1,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Delimiter'),
				'description' => _t('Sets the value of the delimiter to break on, separating data source values.')
			),
			'tourStopSplitter_relationshipType' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_SELECT,
				'width' => 10, 'height' => 1,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Relationship type'),
				'description' => _t('Accepts a constant type code for the relationship type or a reference to the location in the data source where the type can be found.')
			),
			'tourStopSplitter_tourStopType' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_SELECT,
				'width' => 10, 'height' => 1,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Tour stop type'),
				'description' => _t('Accepts a constant list item idno from the list tour_stop_types or a reference to the location in the data source where the type can be found.')
			),
			'tourStopSplitter_attributes' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_SELECT,
				'width' => 10, 'height' => 1,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Attributes'),
				'description' => _t('Sets or maps metadata for the tour stop record by referencing the metadataElement code and the location in the data source where the data values can be found.')
			),
			'tourStopSplitter_tour' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_SELECT,
				'width' => 10, 'height' => 1,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Tour'),
				'description' => _t('Identifies the tour to add the stop to.')
			),
			'tourStopSplitter_relationshipTypeDefault' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_FIELD,
				'width' => 10, 'height' => 1,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Relationship type default'),
				'description' => _t('Sets the default relationship type that will be used if none are defined or if the data source values do not match any values in the CollectiveAccess system')
			),
			'tourStopSplitter_tourStopTypeDefault' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_FIELD,
				'width' => 10, 'height' => 1,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Tour stop type default'),
				'description' => _t('Sets the default tour stop type that will be used if none are defined or if the data source values do not match any values in the CollectiveAccess list tour_stop_types')
			)
		);
?>