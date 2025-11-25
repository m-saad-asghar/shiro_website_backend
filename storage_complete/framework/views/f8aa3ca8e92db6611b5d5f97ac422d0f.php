<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'statePath' => null,
    'icon' => 'media',
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
    'icon' => 'media',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    if (str(config('filament-tiptap-editor.media_action'))->contains('\\')) {
        $action = "\$wire.dispatchFormEvent('tiptap::setMediaContent', '" . $statePath . "', arguments);";
    } else {
        $action = "this.\$dispatch('open-modal', {id: '" . config('filament-tiptap-editor.media_action') . "', statePath: '" . $statePath . "'}, arguments)";
    }
?>

<?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'openModal()','label' => ''.e(trans('filament-tiptap-editor::editor.media.insert_edit')).'','active' => 'image','icon' => $icon,'xData' => '{
        openModal() {
            let media = this.editor().getAttributes(\'image\');
            let arguments = {
                type: \'media\',
                src: media.src || \'\',
                alt: media.alt || \'\',
                title: media.title || \'\',
                width: media.width || \'\',
                height: media.height || \'\',
                lazy: media.lazy || false,
                media: media.media || \'\',
                srcset: media.srcset || \'\',
                sizes: media.sizes || \'\',
            };

            '.e($action).'

        }
    }']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'openModal()','label' => ''.e(trans('filament-tiptap-editor::editor.media.insert_edit')).'','active' => 'image','icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'x-data' => '{
        openModal() {
            let media = this.editor().getAttributes(\'image\');
            let arguments = {
                type: \'media\',
                src: media.src || \'\',
                alt: media.alt || \'\',
                title: media.title || \'\',
                width: media.width || \'\',
                height: media.height || \'\',
                lazy: media.lazy || false,
                media: media.media || \'\',
                srcset: media.srcset || \'\',
                sizes: media.sizes || \'\',
            };

            '.e($action).'

        }
    }']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31)): ?>
<?php $attributes = $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31; ?>
<?php unset($__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31)): ?>
<?php $component = $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31; ?>
<?php unset($__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31); ?>
<?php endif; ?><?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/tools/media.blade.php ENDPATH**/ ?>