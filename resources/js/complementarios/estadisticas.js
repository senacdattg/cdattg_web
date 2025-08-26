// Este script filtra los municipios según el departamento seleccionado

document.addEventListener('DOMContentLoaded', function () {
    const departamentoSelect = document.getElementById('departamento');
    const municipioSelect = document.getElementById('municipio');

    // Guarda todos los municipios con su departamento_id
    const allMunicipios = Array.from(municipioSelect.options).map(option => ({
        value: option.value,
        text: option.text,
        departamentoId: option.getAttribute('data-departamento')
    }));

    departamentoSelect.addEventListener('change', function () {
        const selectedDepartamento = this.value;
        municipioSelect.innerHTML = '<option value="">Seleccione...</option>';

        allMunicipios.forEach(mun => {
            if (!selectedDepartamento || mun.departamentoId === selectedDepartamento) {
                if (mun.value !== "") {
                    const opt = document.createElement('option');
                    opt.value = mun.value;
                    opt.text = mun.text;
                    opt.setAttribute('data-departamento', mun.departamentoId);
                    municipioSelect.appendChild(opt);
                }
            }
        });
    });
});