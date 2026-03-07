@extends(auth('admin')->check() ? 'layouts.admin' : 'layouts.agent')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-0">Análisis de Mercado Comparativo (AMC)</h4>
            <p class="text-muted mb-0">Obtén una valoración basada en propiedades comparables del sector.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Datos del inmueble en estudio</h5>
                    <form id="amc-form">
                        @csrf
                        <div class="form-group">
                            <label for="sector_id">Sector *</label>
                            <select name="sector_id" id="sector_id" class="form-control" required>
                                <option value="">-- Seleccionar sector --</option>
                                @foreach ($sectores as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="area_m2">Área m² *</label>
                                <input type="number" name="area_m2" id="area_m2" class="form-control" step="0.01" min="0.01" required placeholder="120.50">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="habitaciones">Hab.</label>
                                <input type="number" name="habitaciones" id="habitaciones" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="banos">Baños</label>
                                <input type="number" name="banos" id="banos" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="parqueos">Parq.</label>
                                <input type="number" name="parqueos" id="parqueos" class="form-control" min="0" value="0" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="anio_construccion">Año construcción</label>
                            <input type="number" name="anio_construccion" id="anio_construccion" class="form-control" min="1900" max="2100" placeholder="Opcional">
                        </div>
                        <h6 class="mt-3 mb-2">Acabados (opcional - si completa los tres, se aplica ajuste)</h6>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="finish_piso_id">Piso</label>
                                <select name="finish_piso_id" id="finish_piso_id" class="form-control">
                                    <option value="">-- Ninguno --</option>
                                    @foreach ($acabadosPiso as $a)
                                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="finish_cocina_id">Cocina</label>
                                <select name="finish_cocina_id" id="finish_cocina_id" class="form-control">
                                    <option value="">-- Ninguno --</option>
                                    @foreach ($acabadosCocina as $a)
                                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="finish_bano_id">Baño</label>
                                <select name="finish_bano_id" id="finish_bano_id" class="form-control">
                                    <option value="">-- Ninguno --</option>
                                    @foreach ($acabadosBano as $a)
                                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-run">Calcular AMC</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div id="amc-result" class="card shadow-sm" style="display: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Resultado del AMC</h5>
                        <button type="button" id="btn-export-pdf" class="btn btn-outline-danger btn-sm" style="display: none;">
                            Exportar PDF
                        </button>
                    </div>
                    <div id="amc-result-content"></div>
                    <div id="amc-comparables-wrap" class="mt-3" style="display: none;">
                        <h6 class="mb-2">Propiedades comparables encontradas</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="amc-comparables-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Sector</th>
                                        <th>Precio</th>
                                        <th>Área m²</th>
                                        <th>$/m²</th>
                                        <th>Hab.</th>
                                        <th>Baños</th>
                                        <th>Parq.</th>
                                        <th>Piso</th>
                                        <th>Cocina</th>
                                        <th>Baño</th>
                                    </tr>
                                </thead>
                                <tbody id="amc-comparables-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="amc-error" class="alert alert-danger" style="display: none;"></div>
            <div id="amc-loading" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Calculando...</p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('amc-form');
    var lastResult = null;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = document.getElementById('btn-run');
        var result = document.getElementById('amc-result');
        var error = document.getElementById('amc-error');
        var loading = document.getElementById('amc-loading');
        var content = document.getElementById('amc-result-content');
        var comparablesWrap = document.getElementById('amc-comparables-wrap');
        var comparablesBody = document.getElementById('amc-comparables-body');
        var btnPdf = document.getElementById('btn-export-pdf');

        btn.disabled = true;
        result.style.display = 'none';
        error.style.display = 'none';
        loading.style.display = 'block';

        var formData = new FormData(this);
        var data = {};
        formData.forEach(function(v, k) { if (v) data[k] = v; });
        data._token = formData.get('_token');

        fetch('{{ route("amc.run") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token,
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(r => r.json())
        .then(function(res) {
            loading.style.display = 'none';
            btn.disabled = false;
            if (res.errors) {
                error.innerHTML = Object.values(res.errors).flat().join('<br>');
                error.style.display = 'block';
                return;
            }
            if (res.message) {
                error.innerHTML = res.message;
                error.style.display = 'block';
                return;
            }
            lastResult = res;

            var html = '<table class="table table-sm"><tbody>';
            html += '<tr><th>Valor base</th><td>$' + Number(res.valorBase).toLocaleString('es', {minimumFractionDigits: 2}) + '</td></tr>';
            html += '<tr><th>Valor con acabados</th><td>$' + Number(res.valorConAcabados).toLocaleString('es', {minimumFractionDigits: 2}) + '</td></tr>';
            html += '<tr><th>Promedio $/m²</th><td>$' + Number(res.promedioValorM2).toLocaleString('es', {minimumFractionDigits: 2}) + '</td></tr>';
            html += '<tr><th>Comparables encontrados</th><td>' + res.cantidadComparables + '</td></tr>';
            html += '</tbody></table>';
            content.innerHTML = html;

            if (res.comparables && res.comparables.length > 0) {
                var tbody = '';
                res.comparables.forEach(function(c, i) {
                    tbody += '<tr>';
                    tbody += '<td>' + (i + 1) + '</td>';
                    tbody += '<td>' + (c.sector ? c.sector.nombre : '-') + '</td>';
                    tbody += '<td>$' + Number(c.precio).toLocaleString('es', {minimumFractionDigits: 2}) + '</td>';
                    tbody += '<td>' + c.areaConstruccionM2 + '</td>';
                    tbody += '<td>$' + Number(c.valorM2).toLocaleString('es', {minimumFractionDigits: 2}) + '</td>';
                    tbody += '<td>' + c.habitaciones + '</td>';
                    tbody += '<td>' + c.banos + '</td>';
                    tbody += '<td>' + c.parqueos + '</td>';
                    tbody += '<td>' + (c.acabadoPiso ? c.acabadoPiso.nombre : '-') + '</td>';
                    tbody += '<td>' + (c.acabadoCocina ? c.acabadoCocina.nombre : '-') + '</td>';
                    tbody += '<td>' + (c.acabadoBano ? c.acabadoBano.nombre : '-') + '</td>';
                    tbody += '</tr>';
                });
                comparablesBody.innerHTML = tbody;
                comparablesWrap.style.display = 'block';
                btnPdf.style.display = 'inline-block';
            } else {
                comparablesWrap.style.display = 'none';
                btnPdf.style.display = 'none';
            }

            result.style.display = 'block';
        })
        .catch(function(err) {
            loading.style.display = 'none';
            btn.disabled = false;
            error.innerHTML = 'Error de conexión. Intente de nuevo.';
            error.style.display = 'block';
        });
    });

    document.getElementById('btn-export-pdf').addEventListener('click', function() {
        var form = document.getElementById('amc-form');
        var formData = new FormData(form);
        var data = new URLSearchParams();
        formData.forEach(function(v, k) { if (v) data.append(k, v); });
        data.append('_token', document.querySelector('input[name="_token"]').value);

        var url = '{{ route("amc.export-pdf") }}';
        var formEl = document.createElement('form');
        formEl.method = 'POST';
        formEl.action = url;
        formEl.target = '_blank';
        formEl.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('input[name="_token"]').value + '">';
        formData.forEach(function(v, k) {
            if (v) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = k;
                inp.value = v;
                formEl.appendChild(inp);
            }
        });
        document.body.appendChild(formEl);
        formEl.submit();
        document.body.removeChild(formEl);
    });
})();
</script>
@endsection
