<?php if (isset($component)) { $__componentOriginalf7690039e327e71c1c1930ed6f608619 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf7690039e327e71c1c1930ed6f608619 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.dropdown-button','data' => ['label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).'','active' => 'heading','icon' => 'heading','indicator' => 'editor().isActive(\'heading\', updatedAt) && editor().isFocused ? editor().getAttributes(\'heading\').level : null','list' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::dropdown-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).'','active' => 'heading','icon' => 'heading','indicator' => 'editor().isActive(\'heading\', updatedAt) && editor().isFocused ? editor().getAttributes(\'heading\').level : null','list' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'editor().chain().focus().toggleHeading({level: 1}).run()','icon' => 'heading-one','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'editor().chain().focus().toggleHeading({level: 1}).run()','icon' => 'heading-one','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 1']); ?>
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

    <?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'editor().chain().focus().toggleHeading({level: 2}).run()','icon' => 'heading-two','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'editor().chain().focus().toggleHeading({level: 2}).run()','icon' => 'heading-two','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 2']); ?>
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

    <?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'editor().chain().focus().toggleHeading({level: 3}).run()','icon' => 'heading-three','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'editor().chain().focus().toggleHeading({level: 3}).run()','icon' => 'heading-three','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 3']); ?>
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

    <?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'editor().chain().focus().toggleHeading({level: 4}).run()','icon' => 'heading-four','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'editor().chain().focus().toggleHeading({level: 4}).run()','icon' => 'heading-four','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 4']); ?>
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

    <?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'editor().chain().focus().toggleHeading({level: 5}).run()','icon' => 'heading-five','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'editor().chain().focus().toggleHeading({level: 5}).run()','icon' => 'heading-five','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 5']); ?>
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

    <?php if (isset($component)) { $__componentOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74117f8bb7cf8ec0e06f3d6b01d14a31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.button','data' => ['action' => 'editor().chain().focus().toggleHeading({level: 6}).run()','icon' => 'heading-six','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => 'editor().chain().focus().toggleHeading({level: 6}).run()','icon' => 'heading-six','secondary' => true,'label' => ''.e(trans('filament-tiptap-editor::editor.heading.label')).' 6']); ?>
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
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/tools/heading.blade.php ENDPATH**/ ?>