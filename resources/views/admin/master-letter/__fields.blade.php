@push('style')
    <style>
        .ck-editor__editable[role="textbox"] {
            /* Editing area */
            min-height: 200px;
        }
    </style>
@endpush
<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="document_name" name="document_name" label="nama dokumen" value="{{ $model->document_name ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-12">
            <textarea name="template" id="content-generated" cols="30" rows="10">{{ $model->template ?? '' }}</textarea>
        </div>
    </div>
    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>
</form>

@push('script')
    <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
        let editor = null;
        setTimeout(() => {
            ClassicEditor
                .create(document.querySelector('#content-generated'), {
                    tabSpaces: 4,
                    toolbar: {
                        shouldNotGroupWhenFull: true
                    }
                })
                .then(newEditor => {
                    editor = newEditor;
                    const view = editor.editing.view;
                    const viewDocument = view.document;
                    viewDocument.on('keydown', (evt, data) => {

                        if ((data.keyCode == 9) && viewDocument.isFocused) {

                            // with white space setting to pre
                            editor.execute('input', {
                                text: "^\t^"
                            });

                            evt.stop(); // Prevent executing the default handler.
                            data.preventDefault();
                            view.scrollToTheSelection();
                        }

                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }, 2000);
    </script>
@endpush
