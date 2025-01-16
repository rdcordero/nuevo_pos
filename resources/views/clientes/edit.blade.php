@extends('layouts.app')

@section('title', 'Editar Cliente')

@push('css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 Bootstrap 5 Theme-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Custom Select2 Styles -->
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single {
        padding-top: 4px;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 12px;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #86b7fe;
        border-radius: 0.375rem;
    }
    .select2-container--bootstrap-5 .select2-search__field:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Editar Cliente</h3>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('clientes.update', $cliente) }}" method="POST" id="clienteForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <h5>Información General</h5>
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $cliente->nombre) }}" 
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nombre_comercial" class="form-label">Nombre Comercial</label>
                            <input type="text" 
                                   class="form-control @error('nombre_comercial') is-invalid @enderror" 
                                   id="nombre_comercial" 
                                   name="nombre_comercial" 
                                   value="{{ old('nombre_comercial', $cliente->nombre_comercial) }}">
                            @error('nombre_comercial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tipo_cliente" class="form-label">Tipo de Cliente *</label>
                            <select class="form-select @error('tipo_cliente') is-invalid @enderror" 
                                    id="tipo_cliente" 
                                    name="tipo_cliente" 
                                    required>
                                <option value="">Seleccione el tipo</option>
                                <option value="contribuyente" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'contribuyente' ? 'selected' : '' }}>
                                    Contribuyente
                                </option>
                                <option value="no_contribuyente" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'no_contribuyente' ? 'selected' : '' }}>
                                    No Contribuyente
                                </option>
                            </select>
                            @error('tipo_cliente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="documentos-contribuyente" style="display: none;">
                            <div class="mb-3">
                                <label for="nrc" class="form-label">NRC *</label>
                                <input type="text" 
                                       class="form-control @error('nrc') is-invalid @enderror" 
                                       id="nrc" 
                                       name="nrc" 
                                       value="{{ old('nrc', $cliente->nrc) }}"
                                       placeholder="######-#">
                                @error('nrc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="actividad_economica_codigo" class="form-label">Actividad Económica *</label>
                                <select class="form-select select2 @error('actividad_economica_codigo') is-invalid @enderror" 
                                        id="actividad_economica_codigo" 
                                        name="actividad_economica_codigo"
                                        data-placeholder="Buscar actividad económica">
                                    <option value="">Seleccione la actividad</option>
                                    @foreach($actividadesEconomicas as $actividad)
                                        <option value="{{ $actividad->codigo }}" 
                                                {{ old('actividad_economica_codigo', $cliente->actividad_economica_codigo) == $actividad->codigo ? 'selected' : '' }}>
                                            {{ $actividad->codigo }} - {{ $actividad->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('actividad_economica_codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nit" class="form-label">NIT</label>
                            <input type="text" 
                                   class="form-control @error('nit') is-invalid @enderror" 
                                   id="nit" 
                                   name="nit" 
                                   value="{{ old('nit', $cliente->nit) }}"
                                   placeholder="####-######-###-#">
                            @error('nit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="documentos-no-contribuyente" style="display: none;">
                            <div class="mb-3">
                                <label for="dui" class="form-label">DUI</label>
                                <input type="text" 
                                       class="form-control @error('dui') is-invalid @enderror" 
                                       id="dui" 
                                       name="dui" 
                                       value="{{ old('dui', $cliente->dui) }}"
                                       placeholder="########-#">
                                @error('dui')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Ubicación y Contacto</h5>

                        <div class="mb-3">
                            <label for="pais_id" class="form-label">País *</label>
                            <select class="form-select select2 @error('pais_id') is-invalid @enderror" 
                                    id="pais_id" 
                                    name="pais_id" 
                                    required
                                    data-placeholder="Seleccione el país">
                                <option value="">Seleccione el país</option>
                                @foreach($paises as $pais)
                                    <option value="{{ $pais->codigo }}" 
                                            {{ old('pais_id', $cliente->pais->codigo) == $pais->codigo ? 'selected' : '' }}>
                                        {{ $pais->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pais_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección *</label>
                            <input type="text" 
                                   class="form-control @error('direccion') is-invalid @enderror" 
                                   id="direccion" 
                                   name="direccion" 
                                   value="{{ old('direccion', $cliente->direccion) }}" 
                                   required>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="departamento" class="form-label">Departamento</label>
                                    <input type="text" 
                                           class="form-control @error('departamento') is-invalid @enderror" 
                                           id="departamento" 
                                           name="departamento" 
                                           value="{{ old('departamento', $cliente->departamento) }}">
                                    @error('departamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="municipio" class="form-label">Municipio</label>
                                    <input type="text" 
                                           class="form-control @error('municipio') is-invalid @enderror" 
                                           id="municipio" 
                                           name="municipio" 
                                           value="{{ old('municipio', $cliente->municipio) }}">
                                    @error('municipio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="distrito" class="form-label">Distrito</label>
                                    <input type="text" 
                                           class="form-control @error('distrito') is-invalid @enderror" 
                                           id="distrito" 
                                           name="distrito" 
                                           value="{{ old('distrito', $cliente->distrito) }}">
                                    @error('distrito')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" 
                                   class="form-control @error('complemento') is-invalid @enderror" 
                                   id="complemento" 
                                   name="complemento" 
                                   value="{{ old('complemento', $cliente->complemento) }}">
                            @error('complemento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control @error('telefono') is-invalid @enderror" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono', $cliente->telefono) }}">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="celular" class="form-label">Celular</label>
                                    <input type="tel" 
                                           class="form-control @error('celular') is-invalid @enderror" 
                                           id="celular" 
                                           name="celular" 
                                           value="{{ old('celular', $cliente->celular) }}">
                                    @error('celular')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $cliente->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="web" class="form-label">Sitio Web</label>
                                    <input type="url" 
                                           class="form-control @error('web') is-invalid @enderror" 
                                           id="web" 
                                           name="web" 
                                           value="{{ old('web', $cliente->web) }}">
                                    @error('web')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Información Comercial</h5>

                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría *</label>
                            <select class="form-select @error('categoria') is-invalid @enderror" 
                                    id="categoria" 
                                    name="categoria" 
                                    required>
                                <option value="normal" {{ old('categoria', $cliente->categoria) == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="frecuente" {{ old('categoria', $cliente->categoria) == 'frecuente' ? 'selected' : '' }}>Frecuente</option>
                                <option value="vip" {{ old('categoria', $cliente->categoria) == 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                            @error('categoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="limite_credito" class="form-label">Límite de Crédito *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('limite_credito') is-invalid @enderror" 
                                               id="limite_credito" 
                                               name="limite_credito" 
                                               value="{{ old('limite_credito', $cliente->limite_credito) }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                        @error('limite_credito')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dias_credito" class="form-label">Días de Crédito *</label>
                                    <input type="number" 
                                           class="form-control @error('dias_credito') is-invalid @enderror" 
                                           id="dias_credito" 
                                           name="dias_credito" 
                                           value="{{ old('dias_credito', $cliente->dias_credito) }}" 
                                           min="0" 
                                           required>
                                    @error('dias_credito')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="vendedor" class="form-label">Vendedor Asignado</label>
                            <input type="text" 
                                   class="form-control @error('vendedor') is-invalid @enderror" 
                                   id="vendedor" 
                                   name="vendedor" 
                                   value="{{ old('vendedor', $cliente->vendedor) }}">
                            @error('vendedor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Configuración Adicional</h5>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="3">{{ old('observaciones', $cliente->observaciones) }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="exento" 
                                       name="exento" 
                                       value="1" 
                                       {{ old('exento', $cliente->exento) ? 'checked' : '' }}>
                                <label class="form-check-label" for="exento">Cliente Exento de IVA</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="gran_contribuyente" 
                                       name="gran_contribuyente" 
                                       value="1" 
                                       {{ old('gran_contribuyente', $cliente->gran_contribuyente) ? 'checked' : '' }}>
                                <label class="form-check-label" for="gran_contribuyente">Gran Contribuyente</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="activo" 
                                       name="activo" 
                                       value="1" 
                                       {{ old('activo', $cliente->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Cliente Activo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar Select2 para país
    $('#pais_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    // Inicializar Select2 para actividad económica
    $('#actividad_economica_codigo').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#documentos-contribuyente'),
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    const tipoClienteSelect = document.getElementById('tipo_cliente');
    const documentosContribuyente = document.getElementById('documentos-contribuyente');
    const documentosNoContribuyente = document.getElementById('documentos-no-contribuyente');
    const nrcInput = document.getElementById('nrc');
    const actividadSelect = document.getElementById('actividad_economica_codigo');
    const paisSelect = document.getElementById('pais_id');

    // Función para mostrar/ocultar campos según tipo de cliente
    function toggleClienteFields() {
        const isContribuyente = tipoClienteSelect.value === 'contribuyente';
        documentosContribuyente.style.display = isContribuyente ? 'block' : 'none';
        documentosNoContribuyente.style.display = !isContribuyente ? 'block' : 'none';

        // Actualizar required en campos
        nrcInput.required = isContribuyente;
        actividadSelect.required = isContribuyente;

        // Refrescar Select2 cuando se muestra
        if (isContribuyente) {
            $('#actividad_economica_codigo').select2('destroy').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#documentos-contribuyente'),
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
        }
    }

    // Función para cargar actividades económicas según país
    async function cargarActividadesEconomicas() {
        const paisCodigo = paisSelect.value;
        if (!paisCodigo) return;

        try {
            const response = await fetch(`/api/paises/${paisCodigo}/actividades-economicas`);
            if (!response.ok) throw new Error('Error al cargar actividades económicas');
            
            const actividades = await response.json();
            
            // Guardar la selección actual
            const selectedActividad = $('#actividad_economica_codigo').val();
            
            // Limpiar y actualizar el select
            $('#actividad_economica_codigo').empty().append('<option value="">Seleccione la actividad</option>');
            
            actividades.forEach(actividad => {
                $('#actividad_economica_codigo').append(new Option(
                    `${actividad.codigo} - ${actividad.descripcion}`, 
                    actividad.codigo,
                    false,
                    actividad.codigo === selectedActividad
                ));
            });
            
            // Trigger change para actualizar Select2
            $('#actividad_economica_codigo').trigger('change');
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar las actividades económicas');
        }
    }

    // Formatear documentos
    function formatearDocumento(input, patron) {
        let valor = input.value.replace(/[^0-9]/g, '');
        
        if (patron === 'nit' && valor.length === 14) {
            valor = valor.replace(/(\d{4})(\d{6})(\d{3})(\d{1})/, '$1-$2-$3-$4');
        } else if (patron === 'nrc' && valor.length === 7) {
            valor = valor.replace(/(\d{6})(\d{1})/, '$1-$2');
        } else if (patron === 'dui' && valor.length === 9) {
            valor = valor.replace(/(\d{8})(\d{1})/, '$1-$2');
        }
        
        input.value = valor;
    }

    // Event Listeners
    tipoClienteSelect.addEventListener('change', toggleClienteFields);
    $('#pais_id').on('change', cargarActividadesEconomicas);

    document.getElementById('nit').addEventListener('input', function() {
        formatearDocumento(this, 'nit');
    });

    document.getElementById('nrc').addEventListener('input', function() {
        formatearDocumento(this, 'nrc');
    });

    document.getElementById('dui').addEventListener('input', function() {
        formatearDocumento(this, 'dui');
    });

    // Inicializar estado de campos
    toggleClienteFields();
    if (paisSelect.value) {
        cargarActividadesEconomicas();
    }
});
</script>
@endpush

