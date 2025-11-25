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

<?php if(in_array('link', $tools)): ?>
<div
    class="flex gap-1 items-center"
    x-show="editor().isActive('link', updatedAt)"
    style="display: none;"
>
    <span x-text="editor().getAttributes('link', updatedAt).href" class="max-w-xs truncate overflow-hidden whitespace-nowrap"></span>
    <?php if (isset($component)) { $__componentOriginal33e10ebb3c8dffce197baf94e2f989ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e10ebb3c8dffce197baf94e2f989ed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.link','data' => ['statePath' => $statePath,'icon' => 'edit','active' => false,'label' => ''.e(trans('filament-tiptap-editor::editor.link.edit')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath),'icon' => 'edit','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'label' => ''.e(trans('filament-tiptap-editor::editor.link.edit')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33e10ebb3c8dffce197baf94e2f989ed)): ?>
<?php $attributes = $__attributesOriginal33e10ebb3c8dffce197baf94e2f989ed; ?>
<?php unset($__attributesOriginal33e10ebb3c8dffce197baf94e2f989ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33e10ebb3c8dffce197baf94e2f989ed)): ?>
<?php $component = $__componentOriginal33e10ebb3c8dffce197baf94e2f989ed; ?>
<?php unset($__componentOriginal33e10ebb3c8dffce197baf94e2f989ed); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalde4dd57dbfe94b8420aa2bcba8a902ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalde4dd57dbfe94b8420aa2bcba8a902ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.remove-link','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.remove-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalde4dd57dbfe94b8420aa2bcba8a902ef)): ?>
<?php $attributes = $__attributesOriginalde4dd57dbfe94b8420aa2bcba8a902ef; ?>
<?php unset($__attributesOriginalde4dd57dbfe94b8420aa2bcba8a902ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalde4dd57dbfe94b8420aa2bcba8a902ef)): ?>
<?php $component = $__componentOriginalde4dd57dbfe94b8420aa2bcba8a902ef; ?>
<?php unset($__componentOriginalde4dd57dbfe94b8420aa2bcba8a902ef); ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/menus/link-bubble-menu.blade.php ENDPATH**/ ?>