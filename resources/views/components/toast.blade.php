@if (session()->has('success'))
    @if (session('success'))
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success">
                <strong class="me-auto"><i data-feather="check-circle"></i></strong>
                <strong class="me-auto">Yeay !!!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('message') }}
            </div>
        </div>
        <script>
            $(document).ready(function() {
                setTimeout(() => {
                    $('.toast').toast('hide');
                }, 3000);
            });
        </script>
    @else
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="max-width: 50vw; min-width: 350px">
            <div class="toast-header bg-danger">
                <strong class="me-auto"><i data-feather="x-circle"></i></strong>
                <strong class="me-auto">Opps !!!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('message') }}
            </div>
        </div>
        <script>
            $(document).ready(function() {
                setTimeout(() => {
                    $('.toast').toast('hide');
                }, 10000);
            });
        </script>
    @endif

@endif

{{-- uncomment for testing --}}

{{-- <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger">
            <strong class="me-auto"><i data-feather="check-circle"></i></strong>
            <strong class="me-auto">Opps !!!</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            an error
        </div>
    </div>
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success">
            <strong class="me-auto"><i data-feather="x-circle"></i></strong>
            <strong class="me-auto">Yeay !!!</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Success
        </div>
    </div> --}}

{{-- uncomment for testing --}}
