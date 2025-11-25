<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'headings' => [],
    'depth' => 0,
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
    'headings' => [],
    'depth' => 0,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<ul class="filament-tiptap-contents-list" data-list-depth="<?php echo e($depth); ?>">
    <?php $__currentLoopData = $headings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $heading): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="filament-tiptap-contents-item">
            <a class="filament-tiptap-contents-url" href="#<?php echo e($heading['id']); ?>"><?php echo e($heading['text']); ?></a>
            <?php if(array_key_exists('subs', $heading)): ?>
                <?php if (isset($component)) { $__componentOriginalef4206c069e877305118eefacbc85edd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef4206c069e877305118eefacbc85edd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.table-of-contents','data' => ['headings' => $heading['subs'],'depth' => $heading['depth']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::table-of-contents'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headings' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($heading['subs']),'depth' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($heading['depth'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalef4206c069e877305118eefacbc85edd)): ?>
<?php $attributes = $__attributesOriginalef4206c069e877305118eefacbc85edd; ?>
<?php unset($__attributesOriginalef4206c069e877305118eefacbc85edd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalef4206c069e877305118eefacbc85edd)): ?>
<?php $component = $__componentOriginalef4206c069e877305118eefacbc85edd; ?>
<?php unset($__componentOriginalef4206c069e877305118eefacbc85edd); ?>
<?php endif; ?>
            <?php endif; ?>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/table-of-contents.blade.php ENDPATH**/ ?>