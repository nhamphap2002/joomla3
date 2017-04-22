<?php defined('_JEXEC') or die('Restricted access');
// Horme 3.0.0
$app = JFactory::getApplication('site');
$template = $app->getTemplate(true);
// Badge new
$days = VmConfig::get('latest_products_days');
$cdate = strtotime ($product->created_on) ;
$ndate = strtotime ('now') - ($days * 86400); ?>

  <div class="badges text-left clearfix">
  <?php if (!$template->params->get('badges')) { ?>
    <?php if ($cdate > $ndate): // Show Badge New ?>
      <span class="label label-success"><?php echo JText::_('TPL_VM_NEW') ?></span>
    <?php endif; ?>

    <?php if ($product->product_special): // Show Badge Featured ?>
      <span class="label label-primary"><?php echo JText::_('TPL_VM_FEATURED') ?></span>
    <?php endif; ?>

    <?php if ($product->prices['discountAmount'] != -0): // Show Badge on Sale ?>
      <span class="label label-danger"><?php echo JText::_('TPL_VM_ONSALE') ?></span>
    <?php endif; ?>
  <?php } ?>
  </div>
