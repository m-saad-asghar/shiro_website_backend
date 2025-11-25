<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'action' => null,
    'active' => null,
    'label' => null,
    'icon' => null,
    'secondary' => false,
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
    'action' => null,
    'active' => null,
    'label' => null,
    'icon' => null,
    'secondary' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    if ($active && ! (str_starts_with($active, '{') || str_starts_with($active, '['))) {
        $active = "'{$active}'";
    }
?>

<button
    type="button"
    x-on:click="<?php echo e($action); ?>"
    <?php echo e($attributes); ?>

    <?php if($label): ?>
        x-tooltip="'<?php echo e($label); ?>'"
    <?php endif; ?>
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'tiptap-tool rounded block p-0.5 outline-none ring-1 ring-transparent hover:ring-primary-500 focus:ring-primary-500',
        'hover:bg-gray-500/20 focus:bg-gray-500/20' => ! $secondary,
        'hover:bg-gray-500/40 focus:bg-gray-500/40' => $secondary,
    ]); ?>"
    <?php if($active): ?>
        x-bind:class="{ '!bg-gray-500/30': editor().isActive(<?php echo e($active); ?>, updatedAt) }"
    <?php endif; ?>
>
    <?php echo e($slot); ?>

    <?php if($icon): ?>
        <span class="sr-only"><?php echo e($label); ?></span>
        <?php if (isset($component)) { $__componentOriginal7c634cdf6f736c358551febe7dde1a17 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c634cdf6f736c358551febe7dde1a17 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.icon','data' => ['icon' => ''.e($icon).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => ''.e($icon).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c634cdf6f736c358551febe7dde1a17)): ?>
<?php $attributes = $__attributesOriginal7c634cdf6f736c358551febe7dde1a17; ?>
<?php unset($__attributesOriginal7c634cdf6f736c358551febe7dde1a17); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c634cdf6f736c358551febe7dde1a17)): ?>
<?php $component = $__componentOriginal7c634cdf6f736c358551febe7dde1a17; ?>
<?php unset($__componentOriginal7c634cdf6f736c358551febe7dde1a17); ?>
<?php endif; ?>
    <?php endif; ?>
</button>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/button.blade.php ENDPATH**/ ?>