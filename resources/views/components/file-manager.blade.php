@if(trim($slot) != '')
    <div class="card card-flush">
        <div class="card-body text-center pt-0">
            {{ $slot }}
        </div>
    </div>
@else
    @domiForgetFooterContent('media_modal')
@endif
