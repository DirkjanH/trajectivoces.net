<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

?>
			<ul class="fwg-star-rating<?php if (!$this->params->get('public_voting') and !$this->user->id) { ?> fwg-star-rating-not-logged<?php } ?>">
				<li class="current-rating" style="width:<?php echo $this->row->_rating_count?(number_format(intval($this->row->_rating_sum) / intval( $this->row->_rating_count ),2)*20):'0'; ?>%;"></li>
<?php
			if (!$this->row->_is_voted and (!$this->row->user_id or ($this->row->user_id and $this->user->id != $this->row->user_id))) {
?>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_1" title="1 <?php echo JText :: _('FWG_STAR_OF'); ?> 5" class="fwgallery-stars one-star">1</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_2" title="2 <?php echo JText :: _('FWG_STARS_OF'); ?> 5" class="fwgallery-stars two-stars">2</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_3" title="3 <?php echo JText :: _('FWG_STARS_OF'); ?> 5" class="fwgallery-stars three-stars">3</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_4" title="4 <?php echo JText :: _('FWG_STARS_OF'); ?> 5" class="fwgallery-stars four-stars">4</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_5" title="5 <?php echo JText :: _('FWG_STARS_OF'); ?> 5" class="fwgallery-stars five-stars">5</a></li>
<?php
			}
?>
			</ul>
			<div class="fwg-vote-box">
				(<?php echo round($this->row->_rating_value, 2); ?>/<?php echo (int)$this->row->_rating_count; ?>) <?php echo JText :: _($this->row->_rating_count!=1?'FWG_VOTES':'FWG_VOTE'); ?>
			</div>
	        <div class="clr"></div>
