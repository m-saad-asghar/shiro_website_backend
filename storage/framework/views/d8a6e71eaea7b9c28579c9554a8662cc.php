<?php
    $colors = ['gray_light', 'gray', 'gray_dark', 'primary', 'secondary', 'tertiary', 'accent'];
?>

<?php if (isset($component)) { $__componentOriginalf7690039e327e71c1c1930ed6f608619 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf7690039e327e71c1c1930ed6f608619 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.dropdown-button','data' => ['label' => ''.e(trans('filament-tiptap-editor::editor.hurdle.label')).'','active' => 'hurdle','icon' => 'hurdle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::dropdown-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => ''.e(trans('filament-tiptap-editor::editor.hurdle.label')).'','active' => 'hurdle','icon' => 'hurdle']); ?>
    <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if (isset($component)) { $__componentOriginal3827cc64736fe7c137cf396d8edaea22 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3827cc64736fe7c137cf396d8edaea22 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.dropdown-button-item','data' => ['action' => 'editor().chain().focus().setHurdle({ color: \''.e($color).'\' }).run()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::dropdown-button-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'editor().chain().focus().setHurdle({ color: \''.e($color).'\' }).run()']); ?>
            <?php echo e(trans('filament-tiptap-editor::editor.hurdle.colors.' . $color)); ?>

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
<?php endif; ?>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/tools/hurdle.blade.php ENDPATH**/ ?>