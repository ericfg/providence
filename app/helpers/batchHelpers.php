<?php
/** ---------------------------------------------------------------------
 * app/helpers/batchHelpers.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012 Whirl-i-Gig
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
 * @package CollectiveAccess
 * @subpackage utils
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */

 /**
   *
   */
   

	# ---------------------------------------
	/**
	 * 
	 */
	function caBatchEditorRelationshipModeControl($t_item, $ps_id_prefix) {
		$vs_buf = "	<div class='editorBatchModeControl'>"._t("In batch")." ".
			caHTMLSelect($ps_id_prefix."_batch_mode", array(
			_t("do not use") => "_disabled_", 
			_t('add to each item') => '_add_', 
			_t('replace value') => '_replace_',
			_t('remove all values') => '_delete_'
		), array('id' => $ps_id_prefix.$t_item->tableNum().'_rel_batch_mode_select'))."</div>\n

	<script type=\"text/javascript\">
		jQuery(document).ready(function() {
			jQuery('#".$ps_id_prefix.$t_item->tableNum()."_rel_batch_mode_select').change(function() {
				if ((jQuery(this).val() == '_disabled_') || (jQuery(this).val() == '_delete_')) {
					jQuery('#".$ps_id_prefix.$t_item->tableNum()."_rel').slideUp(250);
				} else {
					jQuery('#".$ps_id_prefix.$t_item->tableNum()."_rel').slideDown(250);
				}
			});
			jQuery('#".$ps_id_prefix.$t_item->tableNum()."_rel').hide();
		});
	</script>\n";
	
		return $vs_buf;
	}
	# ---------------------------------------
	/**
	 * 
	 */
	function caBatchEditorSetsModeControl($pn_table_num, $ps_id_prefix) {
		$vs_buf = "	<div class='editorBatchModeControl'>"._t("In batch")." ".
			caHTMLSelect($ps_id_prefix."_batch_mode", array(
			_t("do not use") => "_disabled_", 
			_t('add to each item') => '_add_', 
			_t('replace value') => '_replace_',
			_t('remove all values') => '_delete_'
		), array('id' => $ps_id_prefix.$pn_table_num.'_sets_batch_mode_select'))."</div>\n

	<script type=\"text/javascript\">
		jQuery(document).ready(function() {
			jQuery('#".$ps_id_prefix.$pn_table_num."_sets_batch_mode_select').change(function() {
				if ((jQuery(this).val() == '_disabled_') || (jQuery(this).val() == '_delete_')) {
					jQuery('#".$ps_id_prefix.$pn_table_num."_sets').slideUp(250);
				} else {
					jQuery('#".$ps_id_prefix.$pn_table_num."_sets').slideDown(250);
				}
			});
			jQuery('#".$ps_id_prefix.$pn_table_num."_sets').hide();
		});
	</script>\n";
	
		return $vs_buf;
	}
	# ---------------------------------------
?>