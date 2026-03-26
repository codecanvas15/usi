@if ($document_print)
    @if ($document_print->document_print_approvals->where('status', 'pending')->count() > 0)
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Request Print</h3>
                <h4>Diajukan oleh :{{ $document_print->user->name }}</h4>
                <p>{{ $document_print->reason }}</p>
            </div>
            <div class="media-list media-list-divided media-list-hover">
                @foreach ($document_print->document_print_approvals as $key => $document_print_approval)
                    <div class="media align-items-center">
                        <div class="media-body">
                            <p class="mb-2">
                                <a href="#"><strong>{{ $document_print_approval->user->name }} - {{ $document_print_approval->user->employee->position->nama ?? 'Tidak Ada Posisi' }} - Level {{ $document_print_approval->level }} </strong></a>
                                <br>
                            <div class="row">
                                <div class="col-xl-6 col-md-12">
                                    <span class="align-self-start mb-1 badge badge-pill bg-{{ AUTHORIZATION_STATUS[$document_print_approval->status]['color'] }} text-capitalize">{{ AUTHORIZATION_STATUS[$document_print_approval->status]['text'] }}</span>
                                </div>
                                <div class="col-xl-6 col-md-12 text-end">
                                    @if ($document_print_approval->status_at)
                                        <small class="text-end">{{ Carbon\Carbon::parse($document_print_approval->status_at)->translatedFormat('d M Y H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                            @if ($document_print_approval->note)
                                <div class="my-1">
                                    <small class="sidetitle">{{ $document_print_approval->note }}</small>
                                </div>
                            @endif
                            </p>
                            @if ($document_print_approval->user_id == auth()->user()->id)
                                @if ($document_print_approval->status == 'pending')
                                    <x-button color="success" icon="check" fontawesome label="approve" size="sm" dataToggle="modal" dataTarget="#print-approve-modal" />
                                    <x-modal title="approve" id="print-approve-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action="{{ route('admin.authorize-request-print', ['id' => $document_print_approval->id]) }}" method="post" id="print-approve-form">
                                                @csrf
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

                                    <x-button color="danger" icon="x" fontawesome label="reject" size="sm" dataToggle="modal" dataTarget="#print-reject-modal" />
                                    <x-modal title="reject" id="print-reject-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action="{{ route('admin.authorize-request-print', ['id' => $document_print_approval->id]) }}" method="post" id="print-reject-form">
                                                @csrf
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
                        </div>
                        <div class="media-right gap-items">

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif
