<script setup>
    // Function to handle modal close event
    $(document).ready(function() {
        $('#{{ $modalId }}').on('hidden.bs.modal', function () {
            // Reset the form fields when modal is closed
            $('#{{ $formId }}')[0].reset();
        });
    });
</script>

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">{{ $modalTitle }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="{{ $formId }}" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{ $slot }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="{{ $formId }}" class="btn btn-primary">{{ $submitButton }}</button>
            </div>
        </div>
    </div>
</div>