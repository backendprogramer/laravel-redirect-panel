<?php

namespace Backendprogramer\RedirectPanel\Livewire;

use Backendprogramer\RedirectPanel\Models\RedirectPanel;
use Backendprogramer\RedirectPanel\Traits\WriteToHtaccessFile;
use Livewire\WithPagination;
use Livewire\Component;

class RedirectPanelManagement extends Component
{
    use WithPagination;
    use WriteToHtaccessFile;

    /**
     * The entered text in the search box
     *
     * @var string|null searchText
     */
    public ?string $searchText = null;

    /**
     * Selected redirect ID for editing and deletion
     *
     * @var int selectedId
     */
    public int $selectedId;

    /**
     * The "fromPath" field entered in the create and edit form
     *
     * @var string fromPath
     */
    public string $fromPath;

    /**
     * The "toPath" field entered in the create and edit form
     *
     * @var string toPath
     */
    public string $toPath;

    /**
     * The "type" field entered in the create and edit form
     *
     * @var string type
     */
    public string $type;

    /**
     * The IDs of the selected redirects for deletion
     *
     * @var array selectedItems
     */
    public array $selectedItems = [];

    /**
     * The IDs of the selected redirects for deletion
     *
     * @var array redirectTypes
     */
    public array $redirectTypes = [301,302,303,307];

    /**
     * Is the option to select all redirects enabled or not?
     *
     * @var bool selectedAll
     */
    public bool $selectedAll;

    /**
     * Rules validation
     *
     * @return string[]
     */
    protected function rules()
    {
        return [
            'fromPath' => 'required',
            'toPath' => 'required',
            'type' => 'required|in:301,302,303,307',
        ];
    }

    /**
     * Messages validation
     *
     * @return string[]
     */
    protected function messages()
    {
        return [
            'fromPath.required' => trans('redirect-panel::messages.fromPath_required'),
            'toPath.required' => trans('redirect-panel::messages.toPath_required'),
            'type.required' => trans('redirect-panel::messages.type_required'),
            'type.in' => trans('redirect-panel::messages.type_in'),
        ];
    }

    /**
     * Render function of livewire
     *
     * @return mixed
     */
    public function render()
    {
        $redirects = RedirectPanel::when($this->searchText, function ($query) {
            $query->where('from_path', 'like', '%' . $this->searchText . '%')
                ->orWhere('to_path', 'like', '%' . $this->searchText . '%')
                ->orWhere('type', 'like', '%' . $this->searchText . '%');
        })->orderBy('id', 'desc')->paginate(config('redirect-panel.per-page', 10));
        return view('redirect-panel::livewire/redirectPanel', compact('redirects'))->layout('redirect-panel::components.layouts.app');
    }

    /**
     * Finding redirects based on the entered text
     *
     * @return mixed
     */
    public function search()
    {
        return $this->render();
    }

    public function add()
    {
        $this->resetEditFields();
        $this->dispatch('show-add-modal');
    }
    /**
     * Adding a new redirect
     *
     * @return void
     */
    public function save()
    {
        $this->validate();
        $redirect = [
            'from_path' => $this->fromPath,
            'to_path' => $this->toPath,
            'type' => $this->type,
        ];
        RedirectPanel::create($redirect);
        $this->writeNewLineToHtaccess($redirect);
        $this->resetEditFields();
        $this->dispatch('hide-add-modal');
    }

    /**
     * Showing information of the selected redirect for editing
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id)
    {
        $redirectPanel = RedirectPanel::find($id);
        $this->fromPath = $redirectPanel->from_path;
        $this->toPath = $redirectPanel->to_path;
        $this->type = $redirectPanel->type;
        $this->selectedId = $id;

        $this->dispatch('show-edit-modal');
    }

    /**
     * Updating the redirect using the delete and add new redirect method
     *
     * @return void
     */
    public function update()
    {
        $this->validate();

        $this->delete($this->selectedId);
        $this->save();

        $this->resetEditFields();
        $this->dispatch('hide-edit-modal');
    }

    /**
     * Selecting the ID of the chosen redirect for deleting
     *
     * @param int $id
     * @return void
     */
    public function setSelectedId(int $id)
    {
        $this->selectedId = $id;
        $this->dispatch('show-delete-modal');
    }

    /**
     * Deleting the selected redirect
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        RedirectPanel::find($id)->delete();
        $this->dispatch('hide-delete-modal');
    }

    /**
     * Showing the bulk delete modal
     *
     * @return void
     */
    public function showDeletedItemsModal()
    {
        if(!empty($this->selectedItems) && count($this->selectedItems)) {
            $this->dispatch('show-delete-items-modal');
        }
    }

    /**
     * Deleting the selected redirects
     *
     * @return void
     */
    public function deleteItems()
    {
        if(!empty($this->selectedItems) && count($this->selectedItems)) {
            foreach ($this->selectedItems as $selectedItem) {
                RedirectPanel::find($selectedItem)->delete();
            }
            $this->dispatch('hide-delete-items-modal');
        }
    }

    /**
     * Selecting all rows on the current page
     *
     * @param int $page
     * @return void
     */
    public function checkedAll(int $page = 1)
    {
        $paginate = config('redirect-panel.per-page', 10);
        if($this->selectedAll) {
            $this->selectedItems = RedirectPanel::skip(($page - 1) * $paginate)->take($paginate)->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    /**
     * Resetting the edit form data after an update.
     *
     * @return void
     */
    private function resetEditFields()
    {
        $this->selectedId = 0;
        $this->fromPath = '';
        $this->toPath = '';
        $this->type = '';
    }

}
