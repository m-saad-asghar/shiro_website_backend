<?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'fullScreenMode = !fullScreenMode; if (fullScreenMode) editor().chain().focus()','xTooltip' => 'fullScreenMode ? \''.e(trans('filament-tiptap-editor::editor.fullscreen.exit')).'\' : \''.e(trans('filament-tiptap-editor::editor.fullscreen.enter')).'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'fullScreenMode = !fullScreenMode; if (fullScreenMode) editor().chain().focus()','x-tooltip' => 'fullScreenMode ? \''.e(trans('filament-tiptap-editor::editor.fullscreen.exit')).'\' : \''.e(trans('filament-tiptap-editor::editor.fullscreen.enter')).'\'']); ?>
    <div x-show="!fullScreenMode">
        <?php if (isset($component)) { $__componentOriginal7c634cdf6f736c358551febe7dde1a17 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c634cdf6f736c358551febe7dde1a17 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.icon','data' => ['icon' => 'fullscreen-enter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fullscreen-enter']); ?>
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
    </div>
    <div x-show="fullScreenMode" style="display: none;">
        <?php if (isset($component)) { $__componentOriginal7c634cdf6f736c358551febe7dde1a17 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c634cdf6f736c358551febe7dde1a17 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.icon','data' => ['icon' => 'fullscreen-exit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fullscreen-exit']); ?>
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
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31)): ?>
<?php $attributes = $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31; ?>
<?php unset($__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31)): ?>
<?php $component = $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31; ?>
<?php unset($__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31); ?>
<?php endif; ?><?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/tools/fullscreen.blade.php ENDPATH**/ ?>