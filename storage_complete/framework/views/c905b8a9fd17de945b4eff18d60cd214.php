<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'blocks' => [],
    'statePath' => null
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'blocks' => [],
    'statePath' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php if (isset($component)) { $__componentOriginalf7690039e327e71c1c1930ed6f608619 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf7690039e327e71c1c1930ed6f608619 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.dropdown-button','data' => ['label' => ''.e(trans('filament-tiptap-editor::editor.blocks.insert')).'','icon' => 'blocks','active' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::dropdown-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => ''.e(trans('filament-tiptap-editor::editor.blocks.insert')).'','icon' => 'blocks','active' => true]); ?>
    <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if (isset($component)) { $__componentOriginal3827cc64736fe7c137cf396d8edaea22 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3827cc64736fe7c137cf396d8edaea22 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.dropdown-button-item','data' => ['action' => '$wire.mountFormComponentAction(\''.e($statePath).'\', \'insertBlock\', {
                type: \''.e($key).'\'
            })']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::dropdown-button-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => '$wire.mountFormComponentAction(\''.e($statePath).'\', \'insertBlock\', {
                type: \''.e($key).'\'
            })']); ?>
            <?php echo e($block->getLabel()); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3827cc64736fe7c137cf396d8edaea22)): ?>
<?php $attributes = $__attributesOriginal3827cc64736fe7c137cf396d8edaea22; ?>
<?php unset($__attributesOriginal3827cc64736fe7c137cf396d8edaea22); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3827cc64736fe7c137cf396d8edaea22)): ?>
<?php $component = $__componentOriginal3827cc64736fe7c137cf396d8edaea22; ?>
<?php unset($__componentOriginal3827cc64736fe7c137cf396d8edaea22); ?>
<?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf7690039e327e71c1c1930ed6f608619)): ?>
<?php $attributes = $__attributesOriginalf7690039e327e71c1c1930ed6f608619; ?>
<?php unset($__attributesOriginalf7690039e327e71c1c1930ed6f608619); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf7690039e327e71c1c1930ed6f608619)): ?>
<?php $component = $__componentOriginalf7690039e327e71c1c1930ed6f608619; ?>
<?php unset($__componentOriginalf7690039e327e71c1c1930ed6f608619); ?>
<?php endif; ?><?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/tools/blocks.blade.php ENDPATH**/ ?>