@if ($authorization ?? null)
    @if ($can_revert_request ?? null)
        @if (auth()->user()->id == $authorization->user_id && $authorization->revert_status != 'submitted' && $authorization->void_status != 'submitted')
            <x-button color="warning" icon="backward" fontawesome label="revert" size="sm" dataToggle="modal" dataTarget="#revert-modal" />
            <x-modal title="revert request" id="revert-modal" headerColor="warning">
                <x-slot name="modal_body">
                    <form action="{{ route('admin.authorization-request-revert-void', ['id' => $authorization->id]) }}" method="post" id="revert-form">
                        @csrf
                        <input type="hidden" name="status" value="revert">
                        <div class="mt-10">
                            <div class="form-group">
                                <x-input type="text" id="note" label="alasan" name="note" required />
                            </div>
                        </div>
                        <div class="mt-10 border-top pt-10">
                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                            <x-button type="submit" color="primary" label="send request" size="sm" icon="save" fontawesome />
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endif
        @if ($authorization->revert_status == 'submitted')
            <x-button type="button" color="warning" fontawesome label="revert sedang diajukan" size="sm" />
        @endif
    @endif
    @if ($can_void_request ?? null)
        @if (auth()->user()->id == $authorization->user_id && $authorization->void_status != 'submitted' && $authorization->revert_status != 'submitted')
            <x-button color="dark" icon="ban" fontawesome label="void" size="sm" dataToggle="modal" dataTarget="#void-modal" />
            <x-modal title="void request" id="void-modal" headerColor="dark">
                <x-slot name="modal_body">
                    <form action="{{ route('admin.authorization-request-revert-void', ['id' => $authorization->id]) }}" method="post" id="void-form">
                        @csrf
                        <input type="hidden" name="status" value="void">
                        <div class="mt-10">
                            <div class="form-group">
                                <x-input type="text" id="note" label="alasan" name="note" required />
                            </div>
                        </div>
                        <div class="mt-10 border-top pt-10">
                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                            <x-button type="submit" color="primary" label="send request" size="sm" icon="save" fontawesome />
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endif
        @if ($authorization->void_status == 'submitted')
            <x-button type="button" color="dark" fontawesome label="void sedang diajukan" size="sm" />
        @endif
    @endif
@endif
