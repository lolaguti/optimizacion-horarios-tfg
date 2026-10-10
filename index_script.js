document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('select_estudiantes');
  
    //Control error array
    if (!Array.isArray(Object.values(estudiantes))) {
        console.error('El array de estudiantes no está disponible o no es válido.');
        return;
    }

    // Rellena select
    Object.values(estudiantes).forEach(est => {
        const option = document.createElement('option');
        option.value = est.id_est;

        const nombre = est.nombre?.trim();
        const apellido1 = est.apellido1?.trim();
        const apellido2 = est.apellido2?.trim();
        const id_grado = est.id_grado?.trim();

        if (nombre) {
            option.textContent = `${nombre} ${apellido1 || ''} ${apellido2 || ''}`.trim();
        } else {
            option.textContent = `Estudiante #${est.id_est}`;
        }

        select.appendChild(option);
    });

    //saludo
    const saludo = document.getElementById('saludo_estudiante');

    select.addEventListener('change', () => {
    const idSeleccionado = select.value;

    if (idSeleccionado && estudiantes[idSeleccionado]) {
        const est = estudiantes[idSeleccionado];

        if (!est.nombre || est.nombre.trim() === '') {
            saludo.textContent = `Hola, Estudiante #${est.id_est}`;
        } else {
            saludo.textContent = `Hola, ${est.nombre}`;
        }
    }
    });

    //envio datos
    const linkHorario = document.getElementById('link_horario');

    select.addEventListener('change', () => {
        const idSeleccionado = select.value;

        if (!idSeleccionado || !estudiantes[idSeleccionado]) {
            saludo.textContent = '';
            linkHorario.href = `horario.php?id=${idSeleccionado}`;
            return;
        }

        const est = estudiantes[idSeleccionado];

        saludo.textContent = est.nombre?.trim()
            ? `Hola, ${est.nombre}`
            : `Hola, Estudiante #${est.id_est}`;

        sessionStorage.setItem('estudianteSeleccionado', idSeleccionado);

        linkHorario.href = linkHorario.href = `horario.php?id=${idSeleccionado}`;;
    });

});
