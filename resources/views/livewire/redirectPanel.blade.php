<div>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1>@lang('redirect-panel::messages.title')</h1>
            <div class="input-group w-auto ltr">
                <input wire:model="searchText" type="text" class="form-control" name="search" id="searchInput" placeholder="{{ trans('redirect-panel::messages.search') }}...">
                <div class="input-group-append">
                    <button wire:click="search" class="btn btn-outline-secondary" type="button" id="searchButton"><i class="fa fa-search mx-1"></i>@lang('redirect-panel::messages.search')</button>
                </div>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" wire:model="selectedAll" wire:click="checkedAll({{ $_GET['page'] ?? 1 }})" /></th>
                <th>#</th>
                <th>@lang('redirect-panel::messages.path_from')</th>
                <th>@lang('redirect-panel::messages.path_to')</th>
                <th>@lang('redirect-panel::messages.redirect_type')</th>
                <th>@lang('redirect-panel::messages.creation_date')</th>
                <th>@lang('redirect-panel::messages.actions')</th>
            </tr>
            </thead>
            <tbody>
            @php
            $index = (isset($_GET['page'])) ? (($_GET['page'] - 1) * 10) : 0;
            @endphp
            @foreach($redirects as $redirect)
            <tr wire:key="$redirect->id">
                <td><input type="checkbox" name="selectedId[]" wire:model.defer="selectedItems" value="{{ $redirect->id }}" id="check_{{ $redirect->id }}" /></td>
                <td>{{ ++$index }}</td>
                <td>{{ $redirect->from_path }}</td>
                <td>{{ $redirect->to_path }}</td>
                <td>{{ $redirect->type }}</td>
                <td>{{ $redirect->created_at }}</td>
                <td class="d-flex justify-content-center align-items-center">
                    <button type="button" class="btn btn-primary btn-sm ml-1 mr-1" data-toggle="modal" data-target="#editModal" wire:click="edit({{ $redirect->id }})"><i class="fa fa-edit mx-1"></i>@lang('redirect-panel::messages.edit')</button>
                    <button type="button" class="btn btn-danger btn-sm ml-1 mr-1" data-toggle="modal" data-target="#deleteModal" wire:click="setSelectedId({{ $redirect->id }})"><i class="fa fa-trash mx-1"></i>@lang('redirect-panel::messages.delete')</button>
                </td>
            </tr>
            @endforeach
            @if(!sizeof($redirects))
            <tr>
                <td colspan="7">@lang('redirect-panel::messages.there_are_no_records_to_display')</td>
            </tr>
            @endif
            <!-- Add more rows here -->
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-start mb-2">
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                {{ $redirects->links('pagination::bootstrap-4') }}
            </nav>

            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-success btn-sm mx-2" data-toggle="modal"  wire:click="add"><i class="fa fa-plus mx-1"></i>@lang('redirect-panel::messages.add_new_redirect')</button>
                <button type="button" class="btn btn-danger btn-sm mx-2" data-toggle="modal" wire:click="showDeletedItemsModal" id="deleteSelectedButton" @if(!count($selectedItems)) disabled @endif><i class="fa fa-trash mx-1"></i>@lang('redirect-panel::messages.delete_selected_rows')</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header ltr">
                    <h5 class="modal-title" id="deleteModalLabel">@lang('redirect-panel::messages.confirm_delete')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('redirect-panel::messages.are_you_sure_you_want_to_delete_this_record')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mx-1"></i>@lang('redirect-panel::messages.cancel')</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn" wire:click="delete({{ $selectedId }})"><i class="fa fa-trash mx-1"></i>@lang('redirect-panel::messages.delete')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Selected Rows Confirmation Modal -->
    <div class="modal fade" id="delete-items-modal" tabindex="-1" role="dialog" aria-labelledby="deleteSelectedRowsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header ltr">
                    <h5 class="modal-title" id="deleteModalLabel">@lang('redirect-panel::messages.confirm_delete')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('redirect-panel::messages.are_you_sure_you_want_to_delete_selected_rows')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mx-1"></i>@lang('redirect-panel::messages.cancel')</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn" wire:click="deleteItems()"><i class="fa fa-trash mx-1"></i>@lang('redirect-panel::messages.delete')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade @if (count($errors) && isset($selectedId)) show @endif"
    @if (count($errors) && isset($selectedId)) style="display: block!important;" @endif
    id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header ltr">
                    <h5 class="modal-title" id="editModalLabel">@lang('redirect-panel::messages.edit_record')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editPathFrom">@lang('redirect-panel::messages.path_from')</label>
                            <input type="text" class="form-control" id="editPathFrom" name="pathFrom" wire:model="fromPath">
                            @error('fromPath')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="editPathTo">@lang('redirect-panel::messages.path_to')</label>
                            <input type="text" class="form-control" id="editPathTo" name="pathTo" wire:model="toPath">
                            @error('toPath')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="editRedirectType">@lang('redirect-panel::messages.redirect_type')</label>
                            <select class="form-control" id="editRedirectType" name="redirectType" wire:model="type">
                                @foreach($redirectTypes as $redirectType)
                                    <option wire:key="{{ $redirectType }}" value="{{ $redirectType }}" @if($redirectType == $type) selected @endif>{{ $redirectType }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="alert alert-danger d-flex align-items-start">
                            <i class="fa fa-info-circle text-danger mt-1"></i>
                            <label class="text-danger mx-1">
                                @lang('redirect-panel::messages.structure_path_from')
                                <ul class="mt-2">
                                    <li>
                                        `*/path/params`
                                    </li>
                                    <li>
                                        `/path/*/params`
                                    </li>
                                    <li>
                                        `/path/*`
                                    </li>
                                </ul>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mx-1"></i>@lang('redirect-panel::messages.cancel')</button>
                        <button type="submit" class="btn btn-primary" id="saveChangesBtn"><i class="fa fa-floppy-o mx-1"></i>@lang('redirect-panel::messages.save_changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade @if (count($errors) && !isset($selectedId)) show @endif"
         @if (count($errors) && !isset($selectedId)) style="display: block!important;" @endif
         id="add-modal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header ltr">
                    <h5 class="modal-title" id="addModalLabel">@lang('redirect-panel::messages.add_record')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="addPathFrom">@lang('redirect-panel::messages.path_from')</label>
                            <input type="text" class="form-control" id="addPathFrom" name="pathFrom" wire:model="fromPath">
                            @error('fromPath')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="addPathTo">@lang('redirect-panel::messages.path_to')</label>
                            <input type="text" class="form-control" id="addPathTo" name="pathTo" wire:model="toPath">
                            @error('toPath')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="addRedirectType">@lang('redirect-panel::messages.redirect_type')</label>
                            <select class="form-control" id="addRedirectType" name="redirectType" wire:model="type">
                                <option value="" selected>@lang('redirect-panel::messages.select_type')</option>
                                @foreach($redirectTypes as $key => $redirectType)
                                    <option wire:key="{{ $key }}" value="{{ $redirectType }}" >{{ $redirectType }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="alert alert-danger d-flex align-items-start">
                            <i class="fa fa-info-circle text-danger mt-1"></i>
                            <label class="text-danger mx-1">
                                @lang('redirect-panel::messages.structure_path_from')
                                <ul class="mt-2">
                                    <li>
                                        `*/path/params`
                                    </li>
                                    <li>
                                        `/path/*/params`
                                    </li>
                                    <li>
                                        `/path/*`
                                    </li>
                                </ul>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mx-1"></i>@lang('redirect-panel::messages.cancel')</button>
                        <button type="button" class="btn btn-primary" id="addRecordBtn" wire:click="save"><i class="fa fa-floppy-o mx-1"></i>@lang('redirect-panel::messages.add_record')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div  wire:loading.inline-flex class="overlay">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>

@section('styles')
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Adjust the background color and opacity as needed */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999; /* Ensure the overlay is on top of other content */
        }

        :root {
            --btn-primary-fg-color: {{ config('redirect-panel.colors.fg-color.btn-primary', '#fff') }};
            --btn-primary-bg-color: {{ config('redirect-panel.colors.bg-color.btn-primary', '#007bff') }};
            --btn-primary-border-color: {{ config('redirect-panel.colors.border-color.btn-primary', '#007bff') }};

            --btn-danger-fg-color: {{ config('redirect-panel.colors.fg-color.btn-danger', '#fff') }};
            --btn-danger-bg-color: {{ config('redirect-panel.colors.bg-color.btn-danger', '#dc3545') }};
            --btn-danger-border-color: {{ config('redirect-panel.colors.border-color.btn-danger', '#dc3545') }};

            --btn-success-fg-color: {{ config('redirect-panel.colors.fg-color.btn-success', '#fff') }};
            --btn-success-bg-color: {{ config('redirect-panel.colors.bg-color.btn-success', '#28a745') }};
            --btn-success-border-color: {{ config('redirect-panel.colors.border-color.btn-success', '#28a745') }};

            --btn-secondary-fg-color: {{ config('redirect-panel.colors.fg-color.btn-secondary', '#fff') }};
            --btn-secondary-bg-color: {{ config('redirect-panel.colors.bg-color.btn-secondary', '#6c757d;') }};
            --btn-secondary-border-color: {{ config('redirect-panel.colors.border-color.btn-secondary', '#6c757d;') }};

            --text-danger-fg-color: {{ config('redirect-panel.colors.fg-color.text-danger', '#dc3545') }};
        }

        .ltr {
            direction: ltr;
        }

        .btn-primary {
            color: var(--btn-primary-fg-color) !important;
            background-color: var(--btn-primary-bg-color) !important;
            border-color: var(--btn-primary-border-color) !important;

        }

        .btn-danger {
            color: var(--btn-danger-fg-color) !important;
            background-color: var(--btn-danger-bg-color) !important;
            border-color: var(--btn-danger-border-color) !important;
        }

        .btn-success {
            color: var(--btn-success-fg-color) !important;
            background-color: var(--btn-success-bg-color) !important;
            border-color: var(--btn-success-border-color) !important;
        }

        .btn-secondary {
            color: var(--btn-secondary-fg-color) !important;
            background-color: var(--btn-secondary-bg-color) !important;
            border-color: var(--btn-secondary-border-color) !important;
        }

        .text-danger {
            color: var(--text-danger-fg-color) !important;
        }
    </style>
@endsection
@section('scripts')
    <script>
        window.addEventListener('show-edit-modal', event => {
            $('#edit-modal').modal('show');
        });

        window.addEventListener('hide-edit-modal', event => {
            $('#edit-modal').modal('hide');
        });

        window.addEventListener('show-delete-modal', event => {
            $('#delete-modal').modal('show');
        });

        window.addEventListener('hide-delete-modal', event => {
            $('#delete-modal').modal('hide');
        });

        window.addEventListener('show-add-modal', event => {
            $('#add-modal').modal('show');
        });

        window.addEventListener('hide-add-modal', event => {
            $('#add-modal').modal('hide');
        });

        window.addEventListener('show-delete-items-modal', event => {
            $('#delete-items-modal').modal('show');
        });

        window.addEventListener('hide-delete-items-modal', event => {
            $('#delete-items-modal').modal('hide');
        });

        $(document).ready(function() {
            // Attach an event handler to the checkboxes with the name "selectedId[]"
            $('input[name="selectedId[]"]').on('change', function() {
                // Count the number of checked checkboxes
                const selectedCheckboxes = $('input[name="selectedId[]"]:checked');
                if (selectedCheckboxes.length > 0) {
                    $('#deleteSelectedButton').prop('disabled', false);
                } else {
                    $('#deleteSelectedButton').prop('disabled', true);
                }
            });
        });
    </script>
@endsection
