<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'statePath' => null,
    'tools' => [],
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
    'statePath' => null,
    'tools' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php if(in_array('media', $tools)): ?>
<div
    class="flex gap-1 items-center"
    x-show="editor().isActive('image', updatedAt)"
    style="display: none;"
>
    <?php if (isset($component)) { $__componentOriginal06672bf012be21bb8964812da299660e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal06672bf012be21bb8964812da299660e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.edit-media','data' => ['statePath' => $statePath,'icon' => 'edit','active' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.edit-media'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath),'icon' => 'edit','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal06672bf012be21bb8964812da299660e)): ?>
<?php $attributes = $__attributesOriginal06672bf012be21bb8964812da299660e; ?>
<?php unset($__attributesOriginal06672bf012be21bb8964812da299660e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal06672bf012be21bb8964812da299660e)): ?>
<?php $component = $__componentOriginal06672bf012be21bb8964812da299660e; ?>
<?php unset($__componentOriginal06672bf012be21bb8964812da299660e); ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/menus/image-bubble-menu.blade.php ENDPATH**/ ?>