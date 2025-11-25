<?php if (isset($component)) { $__componentOriginalf7690039e327e71c1c1930ed6f608619 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf7690039e327e71c1c1930ed6f608619 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.dropdown-button','data' => ['label' => ''.e(trans('filament-tiptap-editor::editor.color.label')).'','active' => 'color','icon' => 'color','list' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::dropdown-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => ''.e(trans('filament-tiptap-editor::editor.color.label')).'','active' => 'color','icon' => 'color','list' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <div
        x-data="{
            state: editor().getAttributes('textStyle').color || '#000000',

            init: function () {
                if (!(this.state === null || this.state === '')) {
                    this.setState(this.state)
                }

                this.$watch('state', (value) => {
                    if (! value.startsWith('#')) {
                        this.state = `#${value}`
                    }
                })
            },

            setState: function (value) {
                this.state = value
            }
        }"
        x-on:keydown.esc="isOpen() && $event.stopPropagation()"
        class="relative flex-1 p-1"
    >
        <tiptap-hex-color-picker x-bind:color="state" x-on:color-changed="setState($event.detail.value)"></tiptap-hex-color-picker>

        <label
            class="fi-input-wrp mt-2 flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-text-input overflow-hidden"
        >
            <input
                x-model="state"
                class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3"
            />
            <span class="sr-only"><?php echo e(trans('filament-tiptap-editor::editor.color.input_label')); ?></span>
        </label>

        <?php if(filled(config('filament-tiptap-editor.preset_colors'))): ?>
        <div class="mt-2 flex flex-wrap justify-start gap-2">
            <?php $__currentLoopData = config('filament-tiptap-editor.preset_colors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span
                    wire:key="<?php echo e($name); ?>"
                    x-tooltip.raw="<?php echo e($name); ?>"
                    class="rounded-full w-5 h-5 cursor-pointer"
                    style="background-color:<?php echo e($value); ?>;"
                    x-on:click="setState('<?php echo e($value); ?>');"
                >
            </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <div class="w-full flex gap-2 mt-2">
            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['xOn:click' => 'editor().chain().focus().setColor(state).run(); $dispatch(\'close-panel\')','size' => 'sm','class' => 'flex-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => 'editor().chain().focus().setColor(state).run(); $dispatch(\'close-panel\')','size' => 'sm','class' => 'flex-1']); ?>
                <?php echo e(trans('filament-tiptap-editor::editor.color.choose')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['xOn:click' => 'editor().chain().focus().unsetColor().run(); $dispatch(\'close-panel\')','size' => 'sm','class' => 'flex-1','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => 'editor().chain().focus().unsetColor().run(); $dispatch(\'close-panel\')','size' => 'sm','class' => 'flex-1','color' => 'danger']); ?>
                <?php echo e(trans('filament-tiptap-editor::editor.color.remove')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
        </div>
    </div>
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
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/tools/color.blade.php ENDPATH**/ ?>