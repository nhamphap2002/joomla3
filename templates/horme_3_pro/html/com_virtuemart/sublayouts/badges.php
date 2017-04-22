<?php defined('_JEXEC') or die('Restricted access');
$product = $viewData['product'];
// Horme 3
$app = JFactory::getApplication('site');
$template = $app->getTemplate(true);
// Badge new
$days = VmConfig::get('latest_products_days');
$cdate = strtotime ($product->created_on) ;
$ndate = strtotime ('now') - ($days * 86400); ?>
  <?php if ($template->params->get('badges')) : ?>
  <div class="badges text-left clearfix">

    <?php if ($cdate > $ndate): // Show Badge New ?>
      <span class="label label-success"><?php echo JText::_('TPL_VM_NEW') ?></span>
    <?php endif; ?>

    <?php if ($product->product_special): // Show Badge Featured ?>
      <span class="label label-primary"><?php echo JText::_('TPL_VM_FEATURED') ?></span>
    <?php endif; ?>

    <?php if ($product->prices['discountAmount'] != -0 && !$template->params->get('percentage')): // Show Badge on Sale ?>
      <span class="label label-danger"><?php echo JText::_('TPL_VM_ONSALE') ?></span>
    <?php endif; ?>

  </div>
  <?php endif; ?>