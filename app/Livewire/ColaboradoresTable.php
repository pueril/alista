<?php

namespace App\Livewire;

use App\Models\Colaborador;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ColaboradoresTable extends Component
{
    use WithPagination;

    public int $perPage = 15;

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?string $editingId = null;
    public ?string $deletingId = null;
    public string $formIniciales = '';
    public string $formNombre = '';
    public string $formPerfil = 'AYUDANTE';

    public function getIsSupervisorProperty(): bool
    {
        $user = Auth::user();

        return $user && $user->isSupervisor();
    }

    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->formIniciales = '';
        $this->formNombre = '';
        $this->formPerfil = 'AYUDANTE';
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $colaborador = Colaborador::findOrFail($id);
        $this->editingId = $id;
        $this->formIniciales = $colaborador->iniciales;
        $this->formNombre = $colaborador->nombre;
        $this->formPerfil = $colaborador->perfil;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->formIniciales = '';
        $this->formNombre = '';
        $this->formPerfil = 'AYUDANTE';
    }

    public function save(): void
    {
        if (! $this->isSupervisor) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden crear o editar colaboradores.'],
            ]);
        }

        $this->validate([
            'formIniciales' => 'required|string|max:4',
            'formNombre' => 'required|string|max:255',
            'formPerfil' => 'required|in:LIDER,AYUDANTE',
        ], [
            'formIniciales.required' => 'Las iniciales son obligatorias.',
            'formIniciales.max' => 'Las iniciales no pueden tener más de 4 caracteres.',
            'formNombre.required' => 'El nombre completo es obligatorio.',
            'formPerfil.required' => 'Debe seleccionar un perfil.',
        ]);

        $data = [
            'iniciales' => strtoupper($this->formIniciales),
            'nombre' => $this->formNombre,
            'perfil' => $this->formPerfil,
        ];

        if ($this->editingId) {
            $colaborador = Colaborador::findOrFail($this->editingId);
            
            // Validar que las iniciales sean únicas si cambian
            if ($colaborador->iniciales !== strtoupper($this->formIniciales)) {
                $this->validate([
                    'formIniciales' => 'unique:colaboradores,iniciales',
                ], [
                    'formIniciales.unique' => 'Las iniciales ya están en uso por otro colaborador.',
                ]);
            }
            
            $colaborador->update($data);
            session()->flash('message', 'Colaborador actualizado correctamente.');
        } else {
            // Validar unicidad de iniciales al crear
            $this->validate([
                'formIniciales' => 'unique:colaboradores,iniciales',
            ], [
                'formIniciales.unique' => 'Las iniciales ya están en uso por otro colaborador.',
            ]);
            
            Colaborador::create($data);
            session()->flash('message', 'Colaborador creado correctamente.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function toggleActive(string $id): void
    {
        if (! $this->isSupervisor) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden activar/desactivar colaboradores.'],
            ]);
        }

        $colaborador = Colaborador::findOrFail($id);
        $colaborador->update(['activo' => ! $colaborador->activo]);
        session()->flash('message', $colaborador->activo ? 'Colaborador activado.' : 'Colaborador desactivado.');
        $this->resetPage();
    }

    public function openDeleteModal(string $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function confirmDelete(): void
    {
        if (! $this->isSupervisor) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden eliminar colaboradores.'],
            ]);
        }

        if ($this->deletingId) {
            $colaborador = Colaborador::findOrFail($this->deletingId);
            $nombre = $colaborador->nombre;
            $colaborador->delete();
            session()->flash('message', "Colaborador {$nombre} eliminado permanentemente.");
            $this->closeDeleteModal();
            $this->resetPage();
        }
    }

    public function render()
    {
        $colaboradores = Colaborador::orderBy('nombre')
            ->paginate($this->perPage);

        return view('livewire.colaboradores-table', [
            'colaboradores' => $colaboradores,
        ]);
    }
}
