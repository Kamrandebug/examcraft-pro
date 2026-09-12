<div class="btn-group action-btn-group" role="group">
    @if(isset($viewRoute))
    <a href="{{ $viewRoute }}" class="btn btn-action" data-toggle="tooltip" title="View">
        <i class="fas fa-eye"></i>
    </a>
    @endif
    @if(isset($editRoute))
    <a href="{{ $editRoute }}" class="btn btn-action" data-toggle="tooltip" title="Edit">
        <i class="fas fa-pen"></i>
    </a>
    @endif
    @if(isset($printRoute))
    <a href="{{ $printRoute }}" class="btn btn-action" data-toggle="tooltip" title="Print" target="_blank">
        <i class="fas fa-print"></i>
    </a>
    @endif
    @if(isset($downloadRoute))
    <a href="{{ $downloadRoute }}" class="btn btn-action" data-toggle="tooltip" title="Download">
        <i class="fas fa-download"></i>
    </a>
    @endif
    @if(isset($deleteForm))
    <button type="button" class="btn btn-action btn-action-danger delete-btn"
            data-form="{{ $deleteForm }}"
            data-id="{{ $deleteId ?? '' }}"
            data-url="{{ $deleteUrl ?? '' }}"
            {{ $deleteAttributes ?? '' }}
            data-toggle="tooltip" title="Delete">
        <i class="fas fa-trash"></i>
    </button>
    @endif
</div>
