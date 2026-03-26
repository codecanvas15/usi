<div class="box">
    @if ($authorization)
        <div class="box-header">
            <h3 class="box-title">Otorisasi</h3>
            <br>
            @if ($authorization->revert_status == 'submitted' || $authorization->void_status == 'submitted')
                @if ($authorization->revert_status == 'submitted')
                    <span class="badge badge-warning text-capitalize mt-3">revert diajukan</span>
                    <form action="{{ toLocalLink($authorization->update_status_link) }}" method="post" id="revert-form">
                        @csrf
                        <input type="hidden" name="status" value="revert">
                        <input type="hidden" name="message">
                        <input type="hidden" name="note">
                        <input type="hidden" name="authorization_detail_id">
                    </form>
                @endif
                @if ($authorization->void_status == 'submitted')
                    <span class="badge badge-dark text-capitalize mt-3">void diajukan</span>
                    <form action="{{ toLocalLink($authorization->update_status_link) }}" method="post" id="void-form">
                        @csrf
                        <input type="hidden" name="status" value="void">
                        <input type="hidden" name="message">
                        <input type="hidden" name="note">
                        <input type="hidden" name="authorization_detail_id">
                    </form>
                @endif
                <p class="mb-0">"{{ $authorization->revert_or_void_necessary }}"</p>
            @endif
        </div>
        <div class="media-list media-list-divided media-list-hover">
            @foreach ($authorization_details as $key => $authorization_detail)
                <div class="media align-items-center">
                    <div class="media-body">
                        <p class="mb-2">
                            <a href="#"><strong>{{ $authorization_detail->user->name }} - {{ $authorization_detail->user->employee->position->nama ?? 'Tidak Ada Posisi' }} - Level {{ $authorization_detail->level }} </strong></a>
                            <br>
                            @if ($authorization->revert_status != 'submitted' && $authorization->void_status != 'submitted')
                                <div class="row">
                                    <div class="col-xl-6 col-md-12">
                                        <span class="align-self-start mb-1 badge badge-pill bg-{{ AUTHORIZATION_STATUS[$authorization_detail->status]['color'] }} text-capitalize">{{ AUTHORIZATION_STATUS[$authorization_detail->status]['text'] }}</span>
                                    </div>
                                    <div class="col-xl-6 col-md-12 text-end">
                                        @if ($authorization_detail->status_at)
                                            <small class="text-end">{{ Carbon\Carbon::parse($authorization_detail->status_at)->translatedFormat('d M Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                @if ($authorization_detail->note)
                                    <div class="my-1">
                                        <small class="sidetitle">{{ $authorization_detail->note }}</small>
                                    </div>
                                @endif
                            @else
                                @if ($authorization_detail->revert_status)
                                    <span class="mb-1 badge badge-pill bg-{{ REVERT_VOID_REQ_STATUS[$authorization_detail->revert_status]['color'] }} text-capitalize">Revert {{ REVERT_VOID_REQ_STATUS[$authorization_detail->revert_status]['text'] }}</span>
                                @endif
                                @if ($authorization_detail->void_status)
                                    <span class="mb-1 badge badge-pill bg-{{ REVERT_VOID_REQ_STATUS[$authorization_detail->void_status]['color'] }} text-capitalize">Void {{ REVERT_VOID_REQ_STATUS[$authorization_detail->void_status]['text'] }}</span>
                                @endif
                            @endif
                        </p>
                        @if ($authorization_detail->user_id == auth()->user()->id)
                            @if ($authorization->revert_status != 'submitted' && $authorization->void_status != 'submitted')
                                @if ($cant_approve_reason ?? null)
                                    <div class="alert alert-danger">
                                        <i class="fa fa-times"></i> {{ $cant_approve_reason }}
                                    </div>
                                @endif
                                @if ($can_approve ?? true)
                                    @if ($authorization_detail->status == 'pending')
                                        <x-button color="success" icon="check" fontawesome label="approve" size="sm" dataToggle="modal" dataTarget="#approve-modal" />
                                        <x-modal title="approve" id="approve-modal" headerColor="success">
                                            <x-slot name="modal_body">
                                                <form action="{{ toLocalLink($authorization_detail->authorization->update_status_link) }}" method="post" id="approve-form">
                                                    @csrf
                                                    <input type="hidden" name="authorization_detail_id" value="{{ $authorization_detail->id }}">
                                                    <input type="hidden" name="status" value="approve">
                                                    <div class="mt-10">
                                                        <div class="form-group">
                                                            <x-input type="text" id="message" label="message" name="message" required />
                                                        </div>
                                                    </div>
                                                    <div class="mt-10 border-top pt-10">
                                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                        <x-button type="submit" color="primary" label="submit" size="sm" icon="reject" fontawesome />
                                                    </div>
                                                </form>
                                            </x-slot>
                                        </x-modal>

                                        <x-button color="danger" icon="x" fontawesome label="reject" size="sm" dataToggle="modal" dataTarget="#reject-modal" />
                                        <x-modal title="reject" id="reject-modal" headerColor="danger">
                                            <x-slot name="modal_body">
                                                <form action="{{ toLocalLink($authorization_detail->authorization->update_status_link) }}" method="post" id="reject-form">
                                                    @csrf
                                                    <input type="hidden" name="authorization_detail_id" value="{{ $authorization_detail->id }}">
                                                    <input type="hidden" name="status" value="reject">
                                                    <div class="mt-10">
                                                        <div class="form-group">
                                                            <x-input type="text" id="message" label="message" name="message" required />
                                                        </div>
                                                    </div>
                                                    <div class="mt-10 border-top pt-10">
                                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                        <x-button type="submit" color="primary" label="submit" size="sm" icon="reject" fontawesome />
                                                    </div>
                                                </form>
                                            </x-slot>
                                        </x-modal>
                                    @endif
                                @endif
                            @else
                                @if ($authorization_detail->revert_status == 'submitted')
                                    <x-button color="success" icon="check" fontawesome label="setujui" size="sm" dataToggle="modal" dataTarget="#approve_revert-modal" />
                                    <x-modal title="approve_revert" id="approve_revert-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action="{{ route('admin.authorization-response-revert-void', ['id' => $authorization_detail->id]) }}" method="post" id="approve_revert-form" class="revert-void-form">
                                                @csrf
                                                <input type="hidden" name="status_submitted" value="revert">
                                                <input type="hidden" name="status" value="approve">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="submit" size="sm" icon="reject" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>

                                    <x-button color="danger" icon="x" fontawesome label="tolak" size="sm" dataToggle="modal" dataTarget="#reject_revert-modal" />
                                    <x-modal title="reject_revert" id="reject_revert-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action="{{ route('admin.authorization-response-revert-void', ['id' => $authorization_detail->id]) }}" method="post" id="reject_revert-form" class="revert-void-form">
                                                @csrf
                                                <input type="hidden" name="status_submitted" value="revert">
                                                <input type="hidden" name="status" value="reject">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="submit" size="sm" icon="reject" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endif
                                @if ($authorization_detail->void_status == 'submitted')
                                    <x-button color="success" icon="check" fontawesome label="setujui" size="sm" dataToggle="modal" dataTarget="#approve_void-modal" />
                                    <x-modal title="approve_void" id="approve_void-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action="{{ route('admin.authorization-response-revert-void', ['id' => $authorization_detail->id]) }}" method="post" id="approve_void-form" class="revert-void-form">
                                                @csrf
                                                <input type="hidden" name="status_submitted" value="void">
                                                <input type="hidden" name="status" value="approve">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="submit" size="sm" icon="reject" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>

                                    <x-button color="danger" icon="x" fontawesome label="tolak" size="sm" dataToggle="modal" dataTarget="#reject_void-modal" />
                                    <x-modal title="reject_void" id="reject_void-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action="{{ route('admin.authorization-response-revert-void', ['id' => $authorization_detail->id]) }}" method="post" id="reject_void-form" class="revert-void-form">
                                                @csrf
                                                <input type="hidden" name="status_submitted" value="void">
                                                <input type="hidden" name="status" value="reject">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="submit" size="sm" icon="reject" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endif
                            @endif
                        @endif
                    </div>
                    <div class="media-right gap-items">

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
