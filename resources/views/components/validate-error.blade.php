@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif

{{-- uncomment for testing --}}
{{-- <div class="alert alert-danger alert-dismissible fade show" role="alert">
    Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia aut pariatur consequatur est id, blanditiis facilis vitae quas repudiandae deleniti, asperiores delectus adipisci sapiente esse eos quae! Esse, fugiat ab?
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div> --}}
{{-- uncomment for testing --}}
