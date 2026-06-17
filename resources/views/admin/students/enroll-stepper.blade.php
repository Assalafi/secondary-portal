<div class="d-flex justify-content-between align-items-start mb-4 p-3 bg-light rounded-3" style="--bs-breadcrumb-divider: '';">
    @php $steps = ['Student Informations', 'Academic Placement', 'Guardian Information', 'Confirmation']; @endphp
    @foreach($steps as $index => $stepName)
        @php $stepNum = $index + 1; @endphp
        <div class="text-center flex-fill">
            <div class="rounded-circle {{ $step == $stepNum ? 'bg-primary text-white' : 'bg-white border text-muted' }} d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <h5 class="m-0">{{ $stepNum }}</h5>
            </div>
            <p class="mb-0 mt-2 small {{ $step == $stepNum ? 'fw-bold' : '' }}">{{ $stepName }}</p>
        </div>
        @if (!$loop->last)
        <div class="flex-fill" style="position: relative; top: 18px;">
            <hr>
        </div>
        @endif
    @endforeach
</div>
