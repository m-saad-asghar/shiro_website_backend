<?php
    $tools = $getTools();
    $bubbleMenuTools = $getBubbleMenuTools();
    $floatingMenuTools = $getFloatingMenuTools();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $blocks = $getBlocks();
    $mergeTags = $getMergeTags();
    $shouldSupportBlocks = $shouldSupportBlocks();
    $shouldShowMergeTagsInBlocksPanel = $shouldShowMergeTagsInBlocksPanel();
    $customDocument = $getCustomDocument();
    $nodePlaceholders = $getNodePlaceholders();
    $showOnlyCurrentPlaceholder = $getShowOnlyCurrentPlaceholder();
    // Mentions
    $mentionItems = $getMentionItems();
    $emptyMentionItemsMessage = $getEmptyMentionItemsMessage();
    $mentionItemsPlaceholder = $getMentionItemsPlaceholder();
    $mentionItemsLoading = $getMentionItemsLoading();
    $getMentionItemsUsingEnabled = $getMentionItemsUsingEnabled();
    $maxMentionItems = $getMaxMentionItems();
    $mentionTrigger = $getMentionTrigger();
    $mentionDebounce = $getMentionDebounce();
    $mentionSearchStrategy = $getMentionSearchStrategy();
    $tippyPlacement = $getTippyPlacement();
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $getFieldWrapperView()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => $field]); ?>
    <div class="flex gap-3">
        <div class="flex-1">
            <div
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'tiptap-editor rounded-md relative text-gray-950 bg-white shadow-sm ring-1 dark:bg-white/5 dark:text-white',
                    'ring-gray-950/10 dark:ring-white/20' => ! $errors->has($statePath),
                    'ring-danger-600 dark:ring-danger-600' => $errors->has($statePath),
                ]); ?>"
                x-data="{}"
                <?php if(! $shouldDisableStylesheet()): ?>
                    x-load-css="[<?php echo \Illuminate\Support\Js::from(\Filament\Support\Facades\FilamentAsset::getStyleHref('tiptap', 'awcodes/tiptap-editor'))->toHtml() ?>]"
                <?php endif; ?>
            >
                <div
                    wire:ignore
                    x-load
                    x-load-src="<?php echo e(\Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tiptap', 'awcodes/tiptap-editor')); ?>"
                    class="relative z-0 tiptap-wrapper rounded-md bg-white dark:bg-gray-900 focus-within:ring focus-within:ring-primary-500 focus-within:z-10"
                    x-bind:class="{ 'tiptap-fullscreen': fullScreenMode }"
                    x-data="tiptap({
                        state: $wire.<?php echo e($applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: true)); ?>,
                        statePath: '<?php echo e($statePath); ?>',
                        tools: <?php echo \Illuminate\Support\Js::from($tools)->toHtml() ?>,
                        disabled: <?php echo \Illuminate\Support\Js::from($isDisabled)->toHtml() ?>,
                        locale: '<?php echo e(app()->getLocale()); ?>',
                        bubbleMenuTools: <?php echo \Illuminate\Support\Js::from($bubbleMenuTools)->toHtml() ?>,
                        floatingMenuTools: <?php echo \Illuminate\Support\Js::from($floatingMenuTools)->toHtml() ?>,
                        placeholder: <?php echo \Illuminate\Support\Js::from($getPlaceholder())->toHtml() ?>,
                        mergeTags: <?php echo \Illuminate\Support\Js::from($mergeTags)->toHtml() ?>,
                        customDocument: <?php echo \Illuminate\Support\Js::from($customDocument)->toHtml() ?>,
                        nodePlaceholders: <?php echo \Illuminate\Support\Js::from($nodePlaceholders)->toHtml() ?>,
                        showOnlyCurrentPlaceholder: <?php echo \Illuminate\Support\Js::from($showOnlyCurrentPlaceholder)->toHtml() ?>,
                        debounce: <?php echo \Illuminate\Support\Js::from($getLiveDebounce())->toHtml() ?>,
                        mentionItems: <?php echo \Illuminate\Support\Js::from($mentionItems)->toHtml() ?>,
                        emptyMentionItemsMessage: <?php echo \Illuminate\Support\Js::from($emptyMentionItemsMessage)->toHtml() ?>,
                        mentionItemsPlaceholder: <?php echo \Illuminate\Support\Js::from($mentionItemsPlaceholder)->toHtml() ?>,
                        mentionItemsLoading: <?php echo \Illuminate\Support\Js::from($mentionItemsLoading)->toHtml() ?>,
                        maxMentionItems: <?php echo \Illuminate\Support\Js::from($maxMentionItems)->toHtml() ?>,
                        mentionTrigger: <?php echo \Illuminate\Support\Js::from($mentionTrigger)->toHtml() ?>,
                        livewireId: <?php echo \Illuminate\Support\Js::from($this->getId())->toHtml() ?>,
                        getMentionItemsUsingEnabled: <?php echo \Illuminate\Support\Js::from($getMentionItemsUsingEnabled)->toHtml() ?>,
                        getSearchResultsUsing: async (search) => {
                          return await $wire.getMentionsItems(<?php echo \Illuminate\Support\Js::from($statePath)->toHtml() ?>, search)
                        },
                        mentionDebounce: <?php echo \Illuminate\Support\Js::from($mentionDebounce)->toHtml() ?>,
                        tippyPlacement: <?php echo \Illuminate\Support\Js::from($tippyPlacement)->toHtml() ?>,
                        mentionSearchStrategy: <?php echo \Illuminate\Support\Js::from($mentionSearchStrategy)->toHtml() ?>,
                        linkProtocols: <?php echo \Illuminate\Support\Js::from(config('filament-tiptap-editor.link_protocols'))->toHtml() ?>,
                    })"
                    x-init="$nextTick(() => { init() })"
                    x-on:click.away="blur()"
                    x-on:keydown.escape="fullScreenMode = false"
                    x-on:insert-content.window="insertContent($event)"
                    x-on:unset-link.window="$event.detail.statePath === '<?php echo e($statePath); ?>' ? unsetLink() : null"
                    x-on:update-editor-content.window="updateEditorContent($event)"
                    x-on:refresh-tiptap-editors.window="refreshEditorContent()"
                    x-on:dragged-block.stop="$wire.mountFormComponentAction('<?php echo e($statePath); ?>', 'insertBlock', {
                        type: $event.detail.type,
                        coordinates: $event.detail.coordinates,
                    })"
                    x-on:dragged-merge-tag.stop="insertMergeTag($event)"
                    x-on:insert-block.window="insertBlock($event)"
                    x-on:update-block.window="updateBlock($event)"
                    x-on:open-block-settings.window="openBlockSettings($event)"
                    x-on:delete-block.window="deleteBlock($event)"
                    x-on:locale-change.window="updateLocale($event)"
                    x-trap.noscroll="fullScreenMode"
                >
                    <?php if(! $isDisabled && ! $isToolbarMenusDisabled() && $tools): ?>
                        <template x-if="editor()">
                            <div>
                                <button type="button" x-on:click="editor().chain().focus()" class="z-20 rounded sr-only focus:not-sr-only focus:absolute focus:py-1 focus:px-3 focus:bg-white focus:text-gray-900"><?php echo e(trans('filament-tiptap-editor::editor.skip_toolbar')); ?></button>

                                <div class="tiptap-toolbar text-gray-800 border-b border-gray-950/10 bg-gray-50 divide-x divide-gray-950/10 rounded-t-md z-[1] relative flex flex-col md:flex-row dark:text-gray-300 dark:border-white/20 dark:bg-gray-950 dark:divide-white/20">

                                    <div class="flex flex-wrap items-center flex-1 gap-1 p-1 tiptap-toolbar-left">
                                        <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'filament-tiptap-editor::tools.paragraph'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => $statePath]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                        <?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($tool === '|'): ?>
                                                <div class="border-l border-gray-950/10 dark:border-white/20 h-5"></div>
                                            <?php elseif($tool === '-'): ?>
                                                <div class="border-t border-gray-950/10 dark:border-white/20 w-full"></div>
                                            <?php elseif(is_array($tool)): ?>
                                                <?php if(array_key_exists('button', $tool) && !is_null($tool['button'])): ?>
                                                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => ''.e($tool['button']).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => $statePath]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                                <?php endif; ?>
                                            <?php elseif($tool === 'blocks'): ?>
                                                <?php if($blocks && $shouldSupportBlocks): ?>
                                                    <?php if (isset($component)) { $__componentOriginald133c8bb7df8d6a6cf9a1887651421a6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald133c8bb7df8d6a6cf9a1887651421a6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.blocks','data' => ['blocks' => $blocks,'statePath' => $statePath]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.blocks'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['blocks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($blocks),'state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald133c8bb7df8d6a6cf9a1887651421a6)): ?>
<?php $attributes = $__attributesOriginald133c8bb7df8d6a6cf9a1887651421a6; ?>
<?php unset($__attributesOriginald133c8bb7df8d6a6cf9a1887651421a6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald133c8bb7df8d6a6cf9a1887651421a6)): ?>
<?php $component = $__componentOriginald133c8bb7df8d6a6cf9a1887651421a6; ?>
<?php unset($__componentOriginald133c8bb7df8d6a6cf9a1887651421a6); ?>
<?php endif; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'filament-tiptap-editor::tools.'.e($tool).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => $statePath,'editor' => $field]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>

                                    <div class="flex flex-wrap items-start self-stretch gap-1 p-1 pl-2 tiptap-toolbar-right">
                                        <?php if (isset($component)) { $__componentOriginal8ea9e79e150d07435ba002078b32c2da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ea9e79e150d07435ba002078b32c2da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.undo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.undo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ea9e79e150d07435ba002078b32c2da)): ?>
<?php $attributes = $__attributesOriginal8ea9e79e150d07435ba002078b32c2da; ?>
<?php unset($__attributesOriginal8ea9e79e150d07435ba002078b32c2da); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ea9e79e150d07435ba002078b32c2da)): ?>
<?php $component = $__componentOriginal8ea9e79e150d07435ba002078b32c2da; ?>
<?php unset($__componentOriginal8ea9e79e150d07435ba002078b32c2da); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginaled063b850eecd273d66c3049c14802d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled063b850eecd273d66c3049c14802d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.redo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.redo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled063b850eecd273d66c3049c14802d5)): ?>
<?php $attributes = $__attributesOriginaled063b850eecd273d66c3049c14802d5; ?>
<?php unset($__attributesOriginaled063b850eecd273d66c3049c14802d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled063b850eecd273d66c3049c14802d5)): ?>
<?php $component = $__componentOriginaled063b850eecd273d66c3049c14802d5; ?>
<?php unset($__componentOriginaled063b850eecd273d66c3049c14802d5); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginald8e1f3ad290fed93d3ef65259e6afd7d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8e1f3ad290fed93d3ef65259e6afd7d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.erase','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.erase'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8e1f3ad290fed93d3ef65259e6afd7d)): ?>
<?php $attributes = $__attributesOriginald8e1f3ad290fed93d3ef65259e6afd7d; ?>
<?php unset($__attributesOriginald8e1f3ad290fed93d3ef65259e6afd7d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8e1f3ad290fed93d3ef65259e6afd7d)): ?>
<?php $component = $__componentOriginald8e1f3ad290fed93d3ef65259e6afd7d; ?>
<?php unset($__componentOriginald8e1f3ad290fed93d3ef65259e6afd7d); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginal4686d9213218ac4e9c167ee3b539ef1d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4686d9213218ac4e9c167ee3b539ef1d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.tools.fullscreen','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::tools.fullscreen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4686d9213218ac4e9c167ee3b539ef1d)): ?>
<?php $attributes = $__attributesOriginal4686d9213218ac4e9c167ee3b539ef1d; ?>
<?php unset($__attributesOriginal4686d9213218ac4e9c167ee3b539ef1d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4686d9213218ac4e9c167ee3b539ef1d)): ?>
<?php $component = $__componentOriginal4686d9213218ac4e9c167ee3b539ef1d; ?>
<?php unset($__componentOriginal4686d9213218ac4e9c167ee3b539ef1d); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </template>
                    <?php endif; ?>

                    <?php if(! $isDisabled && ! $isBubbleMenusDisabled()): ?>
                    <template x-if="editor()">
                        <div>
                            <div x-ref="bubbleMenu" class="tiptap-editor-bubble-menu-wrapper">
                                <?php if (isset($component)) { $__componentOriginal2c8a19827b0fee0086f27eb4cfaecbca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c8a19827b0fee0086f27eb4cfaecbca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.menus.default-bubble-menu','data' => ['statePath' => $statePath,'tools' => $bubbleMenuTools]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::menus.default-bubble-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath),'tools' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bubbleMenuTools)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c8a19827b0fee0086f27eb4cfaecbca)): ?>
<?php $attributes = $__attributesOriginal2c8a19827b0fee0086f27eb4cfaecbca; ?>
<?php unset($__attributesOriginal2c8a19827b0fee0086f27eb4cfaecbca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c8a19827b0fee0086f27eb4cfaecbca)): ?>
<?php $component = $__componentOriginal2c8a19827b0fee0086f27eb4cfaecbca; ?>
<?php unset($__componentOriginal2c8a19827b0fee0086f27eb4cfaecbca); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal6cc83b4cc496ca5be63d4b8f034ea063 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cc83b4cc496ca5be63d4b8f034ea063 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.menus.link-bubble-menu','data' => ['statePath' => $statePath,'tools' => $tools]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::menus.link-bubble-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath),'tools' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tools)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cc83b4cc496ca5be63d4b8f034ea063)): ?>
<?php $attributes = $__attributesOriginal6cc83b4cc496ca5be63d4b8f034ea063; ?>
<?php unset($__attributesOriginal6cc83b4cc496ca5be63d4b8f034ea063); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cc83b4cc496ca5be63d4b8f034ea063)): ?>
<?php $component = $__componentOriginal6cc83b4cc496ca5be63d4b8f034ea063; ?>
<?php unset($__componentOriginal6cc83b4cc496ca5be63d4b8f034ea063); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalb5ad53bd731abf9a0e8c3bf322cafc9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5ad53bd731abf9a0e8c3bf322cafc9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.menus.image-bubble-menu','data' => ['statePath' => $statePath,'tools' => $tools]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::menus.image-bubble-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath),'tools' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tools)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5ad53bd731abf9a0e8c3bf322cafc9c)): ?>
<?php $attributes = $__attributesOriginalb5ad53bd731abf9a0e8c3bf322cafc9c; ?>
<?php unset($__attributesOriginalb5ad53bd731abf9a0e8c3bf322cafc9c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5ad53bd731abf9a0e8c3bf322cafc9c)): ?>
<?php $component = $__componentOriginalb5ad53bd731abf9a0e8c3bf322cafc9c; ?>
<?php unset($__componentOriginalb5ad53bd731abf9a0e8c3bf322cafc9c); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal65c7cb29cc6f82df37002e6526356108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65c7cb29cc6f82df37002e6526356108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.menus.table-bubble-menu','data' => ['statePath' => $statePath,'tools' => $tools]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::menus.table-bubble-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath),'tools' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tools)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65c7cb29cc6f82df37002e6526356108)): ?>
<?php $attributes = $__attributesOriginal65c7cb29cc6f82df37002e6526356108; ?>
<?php unset($__attributesOriginal65c7cb29cc6f82df37002e6526356108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65c7cb29cc6f82df37002e6526356108)): ?>
<?php $component = $__componentOriginal65c7cb29cc6f82df37002e6526356108; ?>
<?php unset($__componentOriginal65c7cb29cc6f82df37002e6526356108); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </template>
                    <?php endif; ?>

                    <?php if(! $isFloatingMenusDisabled() && filled($floatingMenuTools)): ?>
                    <template x-if="editor()">
                        <div>
                            <div x-ref="floatingMenu" class="tiptap-editor-floating-menu-wrapper">
                                <?php if (isset($component)) { $__componentOriginalc388f143be5edb55a24cb12cd098b866 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc388f143be5edb55a24cb12cd098b866 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tiptap-editor::components.menus.default-floating-menu','data' => ['statePath' => $statePath,'tools' => $floatingMenuTools,'blocks' => $blocks,'shouldSupportBlocks' => $shouldSupportBlocks,'editor' => $field]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tiptap-editor::menus.default-floating-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state-path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statePath),'tools' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($floatingMenuTools),'blocks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($blocks),'should-support-blocks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shouldSupportBlocks),'editor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc388f143be5edb55a24cb12cd098b866)): ?>
<?php $attributes = $__attributesOriginalc388f143be5edb55a24cb12cd098b866; ?>
<?php unset($__attributesOriginalc388f143be5edb55a24cb12cd098b866); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc388f143be5edb55a24cb12cd098b866)): ?>
<?php $component = $__componentOriginalc388f143be5edb55a24cb12cd098b866; ?>
<?php unset($__componentOriginalc388f143be5edb55a24cb12cd098b866); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </template>
                    <?php endif; ?>

                    <div class="flex h-full">
                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'tiptap-prosemirror-wrapper mx-auto w-full max-h-[40rem] min-h-[56px] h-auto overflow-y-scroll overflow-x-hidden rounded-b-md',
                            match ($getMaxContentWidth()) {
                                'sm' => 'prosemirror-w-sm',
                                'md' => 'prosemirror-w-md',
                                'lg' => 'prosemirror-w-lg',
                                'xl' => 'prosemirror-w-xl',
                                '2xl' => 'prosemirror-w-2xl',
                                '3xl' => 'prosemirror-w-3xl',
                                '4xl' => 'prosemirror-w-4xl',
                                '6xl' => 'prosemirror-w-6xl',
                                '7xl' => 'prosemirror-w-7xl',
                                'full' => 'prosemirror-w-none',
                                default => 'prosemirror-w-5xl',
                            }
                        ]); ?>">
                            <div
                                x-ref="element"
                                <?php echo e($getExtraInputAttributeBag()->class([
                                    'tiptap-content min-h-full'
                                ])); ?>

                            ></div>
                        </div>

                        <?php if((! $isDisabled) && ($shouldSupportBlocks || ($shouldShowMergeTagsInBlocksPanel && filled($mergeTags)))): ?>
                            <div
                                x-data="{
                                    isCollapsed: <?php echo \Illuminate\Support\Js::from($shouldCollapseBlocksPanel())->toHtml() ?>,
                                }"
                                class="hidden shrink-0 space-y-2 max-w-sm md:flex flex-col h-full"
                                x-bind:class="{
                                    'bg-gray-50 dark:bg-gray-950/20': ! isCollapsed,
                                    'h-full': ! isCollapsed && fullScreenMode,
                                    'px-2': ! fullScreenMode,
                                    'px-3': fullScreenMode
                                }"
                            >
                                <div class="flex items-center mt-2">
                                    <p class="text-xs font-bold" x-show="! isCollapsed">
                                        <?php if($shouldSupportBlocks): ?>
                                            <?php echo e(trans('filament-tiptap-editor::editor.blocks.panel')); ?>

                                        <?php else: ?>
                                            <?php echo e(trans('filament-tiptap-editor::editor.merge_tags.panel')); ?>

                                        <?php endif; ?>
                                    </p>

                                    <button x-on:click="isCollapsed = false" x-show="isCollapsed" x-cloak type="button" class="ml-auto">
                                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-m-bars-3','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-m-bars-3','class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                                    </button>

                                    <button x-on:click="isCollapsed = true" x-show="! isCollapsed" type="button" class="ml-auto">
                                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-m-x-mark','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-m-x-mark','class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                                    </button>
                                </div>

                                <div x-show="! isCollapsed" class="overflow-y-auto space-y-1 h-full pb-2">
                                    <?php if($shouldShowMergeTagsInBlocksPanel): ?>
                                        <?php $__currentLoopData = $mergeTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mergeTag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div
                                                draggable="true"
                                                x-on:dragstart="$event?.dataTransfer?.setData('mergeTag', <?php echo \Illuminate\Support\Js::from($mergeTag)->toHtml() ?>)"
                                                class="cursor-move grid-col-1 flex items-center gap-2 rounded border text-xs px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-700"
                                            >
                                                &lcub;&lcub; <?php echo e($mergeTag); ?> &rcub;&rcub;
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>

                                    <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div
                                            draggable="true"
                                            x-on:dragstart="$event?.dataTransfer?.setData('block', <?php echo \Illuminate\Support\Js::from($block->getIdentifier())->toHtml() ?>)"
                                            class="cursor-move grid-col-1 flex items-center gap-2 rounded border text-xs px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-700"
                                        >
                                            <?php if($block->getIcon()): ?>
                                                <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => $block->getIcon(),'class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($block->getIcon()),'class' => 'h-5 w-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                                            <?php endif; ?>

                                            <?php echo e($block->getLabel()); ?>

                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/tiptap-editor.blade.php ENDPATH**/ ?>