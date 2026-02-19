<?php

namespace App\Livewire;

use App\Models\SKU;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class SkusTable extends Component
{
    use WithPagination;

    public int $perPage = 15;

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public string $formCodigo = '';
    public string $formFamilia = '';
    public float $formMetaDiaria = 0;
    public float $formProdHora = 0;

    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->formCodigo = '';
        $this->formFamilia = '';
        $this->formMetaDiaria = 0;
        $this->formProdHora = 0;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $sku = SKU::findOrFail($id);
        $this->editingId = $id;
        $this->formCodigo = $sku->codigo;
        $this->formFamilia = $sku->familia;
        $this->formMetaDiaria = $sku->meta_diaria;
        $this->formProdHora = $sku->prod_hora;
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
        $this->formCodigo = '';
        $this->formFamilia = '';
        $this->formMetaDiaria = 0;
        $this->formProdHora = 0;
    }

    public function save(): void
    {
        if (! Auth::user()?->isSupervisor()) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden crear o editar SKUs.'],
            ]);
        }

        $this->validate([
            'formCodigo' => 'required|string|max:255',
            'formFamilia' => 'required|string|max:255',
            'formMetaDiaria' => 'required|numeric|min:0',
            'formProdHora' => 'required|numeric|min:0',
        ], [
            'formCodigo.required' => 'El código SKU es obligatorio.',
            'formFamilia.required' => 'La familia es obligatoria.',
            'formMetaDiaria.required' => 'La meta diaria es obligatoria.',
            'formMetaDiaria.min' => 'La meta diaria no puede ser negativa.',
            'formProdHora.required' => 'La productividad por hora es obligatoria.',
            'formProdHora.min' => 'La productividad por hora no puede ser negativa.',
        ]);

        $data = [
            'codigo' => $this->formCodigo,
            'familia' => $this->formFamilia,
            'meta_diaria' => $this->formMetaDiaria,
            'prod_hora' => $this->formProdHora,
        ];

        if ($this->editingId) {
            $sku = SKU::findOrFail($this->editingId);
            
            // Validar que el código sea único si cambia
            if ($sku->codigo !== $this->formCodigo) {
                $this->validate([
                    'formCodigo' => 'unique:skus,codigo',
                ], [
                    'formCodigo.unique' => 'El código SKU ya está en uso por otro producto.',
                ]);
            }
            
            $sku->update($data);
            session()->flash('message', 'SKU actualizado correctamente.');
        } else {
            // Validar unicidad de código al crear
            $this->validate([
                'formCodigo' => 'unique:skus,codigo',
            ], [
                'formCodigo.unique' => 'El código SKU ya está en uso por otro producto.',
            ]);
            
            SKU::create($data);
            session()->flash('message', 'SKU creado correctamente.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        if (! Auth::user()?->isSupervisor()) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden activar/desactivar SKUs.'],
            ]);
        }

        $sku = SKU::findOrFail($id);
        $sku->update(['activo' => ! $sku->activo]);
        session()->flash('message', $sku->activo ? 'SKU activado.' : 'SKU desactivado.');
        $this->resetPage();
    }

    public function openDeleteModal(int $id): void
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
        if (! Auth::user()?->isSupervisor()) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden eliminar SKUs.'],
            ]);
        }

        if ($this->deletingId) {
            $sku = SKU::findOrFail($this->deletingId);
            $codigo = $sku->codigo;
            $sku->delete();
            session()->flash('message', "SKU {$codigo} eliminado permanentemente.");
            $this->closeDeleteModal();
            $this->resetPage();
        }
    }

    public function render()
    {
        $skus = SKU::orderBy('codigo')
            ->paginate($this->perPage);

        return view('livewire.skus-table', [
            'skus' => $skus,
        ]);
    }
}
