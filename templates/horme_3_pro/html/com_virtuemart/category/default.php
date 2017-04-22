<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 8811 2015-03-30 23:11:08Z Milbo $
 */

defined ('_JEXEC') or die('Restricted access');

if(vRequest::getInt('dynamic')){
	if (!empty($this->products)) {
		if($this->fallback){
			$p = $this->products;
			$this->products = array();
			$this->products[0] = $p;
			vmdebug('Refallback');
		}

		echo shopFunctionsF::renderVmSubLayout($this->productsLayout,array('products'=>$this->products,'currency'=>$this->currency,'products_per_row'=>$this->perRow,'showRating'=>$this->showRating));

	}

	return ;
}
?>
<div class="category-view">
<?php
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";
//vmJsApi::addJScript('vm.hover',$js);
?>
	<h1 class="page-header"><?php echo vmText::_($this->category->category_name); ?></h1>
	<?php
	if (empty($this->keyword) and !empty($this->category) and !empty($this->category->category_description)) {
	?>

	<div class="category_description">
		<?php echo $this->category->category_description; ?>
	</div>
	<hr>
	<?php
	}

	// Show child categories
	if ($this->showcategory and empty($this->keyword)) {
		if (!empty($this->category->haschildren)) {

			echo ShopFunctionsF::renderVmSubLayout('categories',array('categories'=>$this->category->children));
	    echo '<hr>';
		}
	}

	if($this->showproducts){
	?>
	<div class="browse-view">
	<?php

	if ($this->showsearch or !empty($this->keyword)) {
		//id taken in the view.html.php could be modified
		$category_id  = vRequest::getInt ('virtuemart_category_id', 0); ?>
  	<!--BEGIN Search Box -->
  	<div class="virtuemart_search">
  		<form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0', FALSE); ?>" method="get">
  			<?php if(!empty($this->searchCustomList)) { ?>
  			<div class="vm-search-custom-list">
  				<?php echo $this->searchCustomList ?>
  			</div>
  			<?php } ?>

  			<?php if(!empty($this->searchCustomValues)) { ?>
  			<div class="vm-search-custom-values">
  				<?php echo $this->searchCustomValues ?>
  			</div>
  			<?php } ?>
  			<div class="vm-search-custom-search-input input-group form-group">
  				<input name="keyword" class="inputbox" type="text" size="40" value="<?php echo $this->keyword ?>"/>
          <span class="input-group-btn">
  				  <button type="submit" class="button btn-primary"><?php echo vmText::_ ('COM_VIRTUEMART_SEARCH') ?></button>
          </span>
  				<?php //echo VmHtml::checkbox ('searchAllCats', (int)$this->searchAllCats, 1, 0, 'class="changeSendForm"'); ?>
  			</div>
        <div class="vm-search-descr text-warning">
          <p><?php echo vmText::_('COM_VM_SEARCH_DESC') ?></p>
        </div>
  			<!-- input type="hidden" name="showsearch" value="true"/ -->
  			<input type="hidden" name="view" value="category"/>
  			<input type="hidden" name="option" value="com_virtuemart"/>
  			<input type="hidden" name="virtuemart_category_id" value="<?php echo $category_id; ?>"/>
  			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
  		</form>
  	</div>
  	<!-- End Search Box -->
  	<?php  } ?>

	<?php
	  if ( VmConfig::get ('show_manufacturers',1) ) {
	    $col = "col-sm-4";
	  } else {
	    $col = "col-sm-6";
	  }
	?>
  <?php if (!empty($this->products)) : ?>
	<div class="orderby-displaynumber text-center">
		<div class="vm-order-list small row">
	    <div class="orderby-product <?php echo $col; ?>">
				<div class="well well-sm">
				<?php
		      $search  = array('+/-', '-/+');
		      $replace = array('', '');
		      $subject = $this->orderByList['orderby'];
		      echo str_replace($search, $replace, $subject);
		    ?>
				</div>
	    </div>
      <?php if (VmConfig::get ('show_manufacturers',1)) : ?>
	    <div class="orderby-manufacturer <?php echo $col; ?>">
				<div class="well well-sm">
	  		<?php echo $this->orderByList['manufacturer']; ?>
				</div>
	    </div>
      <?php endif; ?>
	  	<div class="display-number <?php echo $col; ?> form-inline">
			  <div class="well well-sm">
	        <span class="display-number-results"><?php echo $this->vmPagination->getResultsCounter ();?></span>
	        <?php echo $this->vmPagination->getLimitBox ($this->category->limit_list_step); ?>
				</div>
	    </div>
	  </div>
	  <hr>
		<?php if ( !empty($this->orderByList) ) { ?>
	  <div class="row">
	    <div class="vm-page-counter col-sm-3 small text-muted"><?php echo $this->vmPagination->getPagesCounter (); ?></div>
	  	<div class="vm-pagination vm-pagination-top col-sm-9">
	  		<?php echo $this->vmPagination->getPagesLinks (); ?>
	  	</div>
	  </div>
		<hr>
		<?php } ?>
	</div> <!-- end of orderby-displaynumber -->
  <?php endif; ?>

	<?php
	if (!empty($this->products)) {
	$products = array();
	$products[0] = $this->products;
	echo shopFunctionsF::renderVmSubLayout($this->productsLayout,array('products'=>$products,'currency'=>$this->currency,'products_per_row'=>$this->perRow,'showRating'=>$this->showRating));
	?>

  <?php if ( !empty($this->orderByList) ) { ?>
  <hr>
	<div class="vm-pagination vm-pagination-bottom text-center row">
		<div class="vm-page-counter col-sm-3 small text-muted"><?php echo $this->vmPagination->getPagesCounter (); ?></div>
		<div class="col-sm-9 text-right">
	  <?php echo $this->vmPagination->getPagesLinks (); ?>
		</div>
	</div>
  <hr>
  <?php } ?>

	<?php
	} elseif (!empty($this->keyword)) {
	?>
	  <div class="alert alert-info">
		<?php echo vmText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : ''); ?>
	  </div>
	<?php } ?>
	</div>

<?php } ?>
</div>
<?php
if(VmConfig::get ('jdynupdate', TRUE)){
	$j = "Virtuemart.container = jQuery('.category-view');
	Virtuemart.containerSelector = '.category-view';";

	//vmJsApi::addJScript('ajaxContent',$j);
}
?>
<!-- end browse-view -->
<script>
// Create the buttons sorting layout. See vm-ltr-site.css also
jQuery('div.orderlist').addClass('collapse');
jQuery('div.activeOrder').click(function(){
	jQuery(this).siblings('div.orderlist').collapse('toggle');
});

jQuery('.activeOrder a, .orderby-manufacturer div.activeOrder').addClass('btn btn-xs btn-primary');
jQuery('.activeOrder a').click(function(e){
	e.preventDefault();
});
jQuery('.orderby-product div.activeOrder').after('<div class="sorting"><a id="sorting" class="btn btn-info btn-xs"></a></div>');

var sorthref = jQuery('.activeOrder a').attr('href');
jQuery('#sorting').attr('href', sorthref);
jQuery('.orderlist a').addClass('btn btn-xs btn-default btn-block');
jQuery('.display-number select').addClass('input-sm');
</script>