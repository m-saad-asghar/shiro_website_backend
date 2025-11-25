<div>
    <script src="https://unpkg.com/shiki"></script>
    <script>
        const raw = document.getElementById('source_code_editor').innerHTML

        console.log(raw)

        shiki
            .getHighlighter({
                theme: 'nord',
                langs: ['html'],
            })
            .then(highlighter => {
                document.getElementById('source_code_editor').innerHTML = highlighter.codeToHtml(raw, {lang: 'js'})
            })
    </script>
</div><?php /**PATH /home/wimo68zi/api.shiroproperties.com/vendor/awcodes/filament-tiptap-editor/resources/views/components/source-modal-footer.blade.php ENDPATH**/ ?>