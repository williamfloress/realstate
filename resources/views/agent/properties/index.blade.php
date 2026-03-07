@extends('layouts.agent')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center flex-wrap" style="gap: 0.75rem;">
                <h4 class="mb-0">Propiedades</h4>
                <div class="btn-group" role="group">
                    <a href="{{ route('agent.properties.index', ['filter' => 'mias']) }}" class="btn btn-sm {{ ($filter ?? 'mias') === 'mias' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Mías ({{ $countMias ?? 0 }})
                    </a>
                    <a href="{{ route('agent.properties.index', ['filter' => 'todas']) }}" class="btn btn-sm {{ ($filter ?? 'mias') === 'todas' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Todas ({{ $countTodas ?? 0 }})
                    </a>
                </div>
            </div>
            <a href="{{ route('agent.properties.create') }}" class="btn btn-primary">Nueva propiedad</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Oferta</th>
                            <th>Precio</th>
                            <th>Ciudad</th>
                            <th>Fecha publicación</th>
                            <th>Estado</th>
                            <th style="width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($properties as $prop)
                            <tr>
                                <td>{{ Str::limit($prop->title, 30) }}@if($prop->agent_id === auth()->id()) <span class="badge badge-info">Mía</span>@endif</td>
                                <td>{{ $prop->homeType?->name ?? '-' }}</td>
                                <td>{{ $prop->offer_type === 'sale' ? 'Venta' : ($prop->offer_type === 'rent' ? 'Renta' : 'Arrendamiento') }}</td>
                                <td>${{ number_format($prop->price, 0) }}</td>
                                <td>{{ $prop->city ?? '-' }}</td>
                                <td>{{ $prop->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $statusBadge = match($prop->status) {
                                            'active' => 'success',
                                            'draft' => 'secondary',
                                            'paused' => 'warning',
                                            'sold' => 'danger',
                                            'rented' => 'info',
                                            'reserved' => 'warning',
                                            default => 'dark',
                                        };
                                        $statusLabel = match($prop->status) {
                                            'active' => 'Activa',
                                            'draft' => 'Borrador',
                                            'paused' => 'Pausada',
                                            'sold' => 'Vendida',
                                            'rented' => 'Rentada',
                                            'reserved' => 'Reservado',
                                            default => $prop->status,
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusBadge }}">{{ $statusLabel }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actions-{{ $prop->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actions-{{ $prop->id }}">
                                            <a class="dropdown-item" href="{{ route('single.property', $prop->id) }}" target="_blank">Ver en sitio</a>
                                            @if ($prop->agent_id === auth()->id())
                                            <a class="dropdown-item" href="{{ route('agent.properties.edit', $prop) }}">Editar</a>
                                            @if ($prop->status === 'active')
                                                <form method="POST" action="{{ route('agent.properties.updateStatus', $prop) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="paused">
                                                    <button type="submit" class="dropdown-item">Pausar</button>
                                                </form>
                                            @endif
                                            @if (in_array($prop->status, ['draft', 'paused']))
                                                <form method="POST" action="{{ route('agent.properties.updateStatus', $prop) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="dropdown-item">Activar</button>
                                                </form>
                                            @endif
                                            @if ($prop->offer_type === 'sale' && $prop->status !== 'sold')
                                                <form method="POST" action="{{ route('agent.properties.updateStatus', $prop) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="sold">
                                                    <button type="submit" class="dropdown-item">Marcar como vendida</button>
                                                </form>
                                            @endif
                                            @if (in_array($prop->offer_type, ['rent', 'lease']) && $prop->status !== 'rented')
                                                <form method="POST" action="{{ route('agent.properties.updateStatus', $prop) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rented">
                                                    <button type="submit" class="dropdown-item">Marcar como rentada</button>
                                                </form>
                                            @endif
                                            @if ($prop->status === 'active')
                                                <form method="POST" action="{{ route('agent.properties.updateStatus', $prop) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="reserved">
                                                    <button type="submit" class="dropdown-item">Marcar como reservado</button>
                                                </form>
                                            @endif
                                            @if (in_array($prop->status, ['sold', 'rented', 'reserved']))
                                                <form method="POST" action="{{ route('agent.properties.updateStatus', $prop) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="dropdown-item">Republicar</button>
                                                </form>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <form method="POST" action="{{ route('agent.properties.destroy', $prop) }}" onsubmit="return confirm('¿Está seguro de eliminar esta propiedad?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Borrar</button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    @if (($filter ?? 'mias') === 'mias')
                                        No tienes propiedades. <a href="{{ route('agent.properties.create') }}">Crear una</a>
                                    @else
                                        No hay propiedades en el portal.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($properties->hasPages())
            <div class="card-footer">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
