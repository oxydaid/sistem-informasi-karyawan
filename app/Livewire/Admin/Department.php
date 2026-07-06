<?php

namespace App\Livewire\Admin;

use App\Models\Department as DepartmentModel;
use App\Models\Position;
use Illuminate\Database\QueryException;
use Livewire\Component;

class Department extends Component
{
    public $newDeptName = '';

    // Form tambah jabatan
    public $selectedDeptId = null;

    public $newPosName = '';

    public $newPosSalary = '';

    // Edit & Delete State for Department
    public $editingDeptId = null;

    public $editingDeptName = '';

    public $confirmingDeptDeleteId = null;

    // Edit & Delete State for Position
    public $editingPosId = null;

    public $editingPosName = '';

    public $editingPosSalary = '';

    public $confirmingPosDeleteId = null;

    protected $rules = [
        'newDeptName' => 'required|string|min:3|unique:departments,name',
    ];

    public function addDepartment()
    {
        $this->validate();

        DepartmentModel::create([
            'name' => $this->newDeptName,
        ]);

        $this->reset('newDeptName');
        session()->flash('success', 'Divisi berhasil ditambahkan!');
    }

    public function selectDepartment($id)
    {
        $this->selectedDeptId = $id;
        $this->reset(['newPosName', 'newPosSalary']);
    }

    public function addPosition()
    {
        $this->validate([
            'selectedDeptId' => 'required|exists:departments,id',
            'newPosName' => 'required|string|min:3',
            'newPosSalary' => 'required|numeric|min:0',
        ]);

        Position::create([
            'department_id' => $this->selectedDeptId,
            'name' => $this->newPosName,
            'base_salary' => $this->newPosSalary,
        ]);

        $this->reset(['newPosName', 'newPosSalary']);
        session()->flash('success', 'Jabatan berhasil ditambahkan!');
    }

    // Department CRUD (Edit & Delete)
    public function editDepartment($id)
    {
        $dept = DepartmentModel::findOrFail($id);
        $this->editingDeptId = $dept->id;
        $this->editingDeptName = $dept->name;
    }

    public function updateDepartment()
    {
        $this->validate([
            'editingDeptName' => 'required|string|min:3|unique:departments,name,'.$this->editingDeptId,
        ], [], [
            'editingDeptName' => 'Nama Divisi',
        ]);

        $dept = DepartmentModel::findOrFail($this->editingDeptId);
        $dept->update(['name' => $this->editingDeptName]);

        $this->reset(['editingDeptId', 'editingDeptName']);
        session()->flash('success', 'Divisi berhasil diperbarui!');
    }

    public function cancelEditDepartment()
    {
        $this->reset(['editingDeptId', 'editingDeptName']);
    }

    public function confirmDeptDelete($id)
    {
        $this->confirmingDeptDeleteId = $id;
    }

    public function deleteDepartment()
    {
        if ($this->confirmingDeptDeleteId) {
            try {
                $dept = DepartmentModel::findOrFail($this->confirmingDeptDeleteId);
                $dept->delete();

                if ($this->selectedDeptId == $this->confirmingDeptDeleteId) {
                    $this->reset(['selectedDeptId', 'newPosName', 'newPosSalary']);
                }

                session()->flash('success', 'Divisi berhasil dihapus!');
            } catch (QueryException $e) {
                session()->flash('error', 'Tidak dapat menghapus divisi karena masih ada karyawan yang menempati jabatan di divisi ini.');
            }
        }
        $this->reset('confirmingDeptDeleteId');
    }

    // Position CRUD (Edit & Delete)
    public function editPosition($id)
    {
        $pos = Position::findOrFail($id);
        $this->editingPosId = $pos->id;
        $this->editingPosName = $pos->name;
        $this->editingPosSalary = $pos->base_salary;
    }

    public function updatePosition()
    {
        $this->validate([
            'editingPosName' => 'required|string|min:3',
            'editingPosSalary' => 'required|numeric|min:0',
        ], [], [
            'editingPosName' => 'Nama Jabatan',
            'editingPosSalary' => 'Gaji Pokok',
        ]);

        $pos = Position::findOrFail($this->editingPosId);
        $pos->update([
            'name' => $this->editingPosName,
            'base_salary' => $this->editingPosSalary,
        ]);

        $this->reset(['editingPosId', 'editingPosName', 'editingPosSalary']);
        session()->flash('success', 'Jabatan berhasil diperbarui!');
    }

    public function cancelEditPosition()
    {
        $this->reset(['editingPosId', 'editingPosName', 'editingPosSalary']);
    }

    public function confirmPosDelete($id)
    {
        $this->confirmingPosDeleteId = $id;
    }

    public function deletePosition()
    {
        if ($this->confirmingPosDeleteId) {
            try {
                $pos = Position::findOrFail($this->confirmingPosDeleteId);
                $pos->delete();
                session()->flash('success', 'Jabatan berhasil dihapus!');
            } catch (QueryException $e) {
                session()->flash('error', 'Tidak dapat menghapus jabatan karena masih ada karyawan yang menempati jabatan ini.');
            }
        }
        $this->reset('confirmingPosDeleteId');
    }

    public function render()
    {
        $departments = DepartmentModel::with('positions')->get();
        $selectedDepartment = $this->selectedDeptId ? DepartmentModel::with('positions')->find($this->selectedDeptId) : null;

        return view('livewire.admin.department', [
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartment,
        ])->layout('layouts.app');
    }
}
