<?php

/**
 * @var rex_yform_value_be_media $this
 * @psalm-scope-this rex_yform_value_be_media
 */

$counter = $counter ?? 1;

$buttonId = $counter;
$name = $this->getFieldName();
$value = htmlspecialchars($this->getValue());
$types = $types ?? $this->getElement('types');

$widget_params = [];
$widget_params['category'] = 0;
if ('' != $this->getElement('category')) {
    $widget_params['category'] = (int) ($this->getElement('category'));
}
$widget_params['preview'] = $this->getElement('preview');
if ('' != $types) {
    $widget_params['types'] = trim($types);
}

if (1 == $this->getElement('multiple')) {
    $widget = rex_var_medialist::getWidget($buttonId, $name, $value, $widget_params);
} else {
    $widget = rex_var_media::getWidget($buttonId, $name, $value, $widget_params);
}

$class_group = trim('form-group ' . $this->getHTMLClass() . ' ' . $this->getWarningClass());

$notice = [];
if ('' != $this->getElement('notice')) {
    $notice[] = rex_i18n::translate($this->getElement('notice'), false);
}
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $notice[] = '<span class="text-warning">' . rex_i18n::translate($this->params['warning_messages'][$this->getId()], false) . '</span>'; //    var_dump();
}
if (count($notice) > 0) {
    $notice = '<p class="help-block small">' . implode('<br />', $notice) . '</p>';
} else {
    $notice = '';
}

?>
<div data-be-media-wrapper="<?php echo $this->getFieldName(); ?>" class="<?php echo $class_group ?>" id="<?php echo $this->getHTMLId() ?>">
    <label class="control-label" for="<?php echo $this->getFieldId() ?>"><?php echo $this->getLabel() ?></label>
    <?php echo $widget; ?>
    <?php echo $notice ?>
</div>
