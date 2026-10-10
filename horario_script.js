document.addEventListener('DOMContentLoaded', () => {

  const contenedorSelect = document.getElementById('select_cuatrimestre');
  const contenedorHorario = document.getElementById('horario');

  const select = document.createElement('select');
  select.id = 'cuatrimestre_select';

  const option1 = document.createElement('option');
  option1.value = '1';
  option1.textContent = 'Primer cuatrimestre';
  select.appendChild(option1);

  const option2 = document.createElement('option');
  option2.value = '2';
  option2.textContent = 'Segundo cuatrimestre';
  select.appendChild(option2);

  const option3 = document.createElement('option');
  option3.value = '3';
  option3.textContent = 'Horario completo de la titulación';
  select.appendChild(option3);

  contenedorSelect.appendChild(select);

  crearCuadricula(horario_est, 1);

  select.addEventListener('change', () => {
    const cuatrimestre = parseInt(select.value);
    
    if (cuatrimestre == 3) {
      crearCuadricula(asig_grado, 1, true);
      crearCuadricula(asig_grado, 2, true);
      crearCuadricula(asig_grado, 3, true);
      crearCuadricula(asig_grado, 4, true);
      crearCuadricula(asig_grado, 5, true);
      crearCuadricula(asig_grado, 6, true);
      crearCuadricula(asig_grado, 7, true);
      crearCuadricula(asig_grado, 8, true);
      
      if (horario_est[0].id_grado==1){
        crearCuadricula(asig_grado, 9, true);
        crearCuadricula(asig_grado, 10, true);
      }
    } else {
      crearCuadricula(horario_est, cuatrimestre, false);
    }
      
  });
});

function crearCuadricula(horario_est, cuatrimestreSeleccionado, flag) {

  const tabla = document.createElement('table');
  tabla.id = 'tabla_horario';

  const diasSemana = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
  const horas = ['8:15 - 9:05', '9:15 - 10:05', '10:15 - 11:05', '11:15 - 12:05', '12:15 - 13:05', '13:15 - 14:05', '14:15 - 15:05'];
  const filas = horas.length;
  const columnas = diasSemana.length;

  //cabecera
  const filaCabecera = document.createElement('tr');
  for (let j = 0; j < columnas; j++) {
    const th = document.createElement('th');
    th.textContent = diasSemana[j];
    filaCabecera.appendChild(th);
  }
  tabla.appendChild(filaCabecera);

  //tabla
  const matrizCeldas = [];

  for (let i = 0; i < filas; i++) {
    const fila = document.createElement('tr');
    const filaCeldas = [];

    for (let j = 0; j < columnas; j++) {
      const celda = document.createElement('td');

      if (j === 0) {
       
        celda.textContent = horas[i];
        celda.classList.add('hora-columna');
      }

      fila.appendChild(celda);
      filaCeldas.push(celda);
    }

    tabla.appendChild(fila);
    matrizCeldas.push(filaCeldas);
  }

  const contenedorHorario = document.getElementById('horario');
  if(!flag) {
    contenedorHorario.innerHTML = '';
  } else {
    if(cuatrimestreSeleccionado == 1) {
      contenedorHorario.innerHTML = '';
    }
    const div = document.getElementById('horario');
    const titulo = document.createElement('h2');
      
    titulo.textContent =" Semestre " + cuatrimestreSeleccionado + " (asignaturas obligatorias)" ;
    div.appendChild(titulo);
  }

  contenedorHorario.appendChild(tabla);

  // Rellenar asignaturas según cuatrimestre seleccionado
  if (Array.isArray(horario_est)) {
    horario_est.forEach(asig => {

      if(flag) {

          if (asig.cuatri_asig === cuatrimestreSeleccionado) {
          const fila = asig.hora - 1; 
          const col = asig.dia;

          if (
            fila >= 0 && fila < filas &&
            col >= 1 && col < columnas
          ) {
            const celda = matrizCeldas[fila][col];

            const asignaturaHTML = `<div class="asignatura">${asig.nombre_asig}</div>`;

            if (celda.innerHTML.trim() !== '') {
              celda.innerHTML += `<div class="separador">--------</div>${asignaturaHTML}`;
            } else {
              celda.innerHTML = asignaturaHTML;
            }
          }
        }
        
      } else {
        if (asig.cuatri_asig%2 === cuatrimestreSeleccionado%2) {
          const fila = asig.hora - 1; 
          const col = asig.dia;

          if (
            fila >= 0 && fila < filas &&
            col >= 1 && col < columnas
          ) {
            const celda = matrizCeldas[fila][col];

            const asignaturaHTML = `<div class="asignatura">${asig.nombre_asig}</div>`;

            if (celda.innerHTML.trim() !== '') {
              celda.innerHTML += `<div class="separador">--------</div>${asignaturaHTML}`;
            } else {
              celda.innerHTML = asignaturaHTML;
            }
          }
        }
        }

      
    });
  }
}
