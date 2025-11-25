<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'active' => null,
    'label' => null,
    'icon' => null,
    'indicator' => null,
    'list' => true,
    'scrollable' => false,
    'customIcon' => null,
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
    'active' => null,
    'label' => null,
    'icon' => null,
    'indicator' => null,
    'list' => true,
    'scrollable' => false,
    'customIcon' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<div
    x-data="{
        indicator: () => <?php echo e($indicator ?? 'null'); ?>

    }"
    class="relative"
    x-on:close-panel="$refs.panel.close()"
>
    <?php if($indicator): ?>
        <div
            x-text="<?php echo e($indicator); ?>"
            class="text-[0.625rem] absolute top-0 right-0 font-mono text-gray-800 dark:text-gray-300 pointer-events-none"
            x-bind:class="{ 'hidden': ! indicator() }"
        ></div>
    <?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => '$refs.panel.toggle','active' => $active,'label' => $label,'icon' => $icon]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => '$refs.panel.toggle','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($active),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon)]); ?>
        <?php if(! $icon): ?>
            <?php echo $customIcon; ?>

        <?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31)): ?>
<?php $attributes = $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31; ?>
<?php unset($__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31)): ?>
<?php $component = $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31; ?>
<?php unset($__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31); ?>
<?php endif; ?>

    <div
        x-ref="panel"
        x-float.placement.bottom-middle.flip.offset.arrow="{
            arrow: {
              element: $refs.arrow
            }
        }"
        x-cloak
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'tiptap-panel absolute z-30 bg-gray-100 dark:bg-gray-800 rounded-md shadow-md top-full',
            'overflow-y-scroll max-h-48' => ! $active,
        ]); ?>"
    >
        <div x-ref="arrow" class="absolute z-1 bg-inherit w-2 h-2 transform rotate-45"></div>
        <?php if($list): ?>
            <ul class="relative z-2 text-sm divide-y rounded-md overflow-hidden divide-gray-300 dark:divide-gray-700 min-w-[144px] text-gray-800 dark:text-white">
                <?php echo e($slot); ?>

            </ul>
        <?php else: ?>
            <div class="relative z-2 flex gap-1 items-center p-1">
                <?php echo e($slot); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/dropdown-button.blade.php ENDPATH**/ ?>