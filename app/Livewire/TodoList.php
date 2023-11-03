<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    public $name;
    public $search;
    public $editTodoName;
    public $editTodoId;

    public function create()
    {
        $validate = $this->validate([
            'name' => 'required|min:3|max:50'
        ]);

        Todo::create($validate);
        $this->reset('name');
        session()->flash('success', 'Created!');

        $this->resetPage();
    }

    public function delete($id)
    {
        Todo::find($id)->delete();
    }

    public function toggle($id)
    {
        $todo = Todo::find($id);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($id)
    {
        $this->editTodoId = $id;
        $this->editTodoName = Todo::find($id)->name;
    }
    public function cancelEdit()
    {
        $this->reset('editTodoId', 'editTodoName');
    }

    public function update()
    {
        $validate = $this->validate([
            'editTodoName' => 'required|min:3|max:50',
        ]);
    
        Todo::find($this->editTodoId)->update([
            'name' => $validate['editTodoName'],
        ]);
    
        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5)
        ]);
    }
}
