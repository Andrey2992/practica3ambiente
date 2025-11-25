// script.js - Conversor de Monedas Frontend con Fetch API
const monedaUno = document.getElementById('moneda-uno');
const monedaDos = document.getElementById('moneda-dos');
const cantidadUno = document.getElementById('cantidad-uno');
const cantidadDos = document.getElementById('cantidad-dos');
const cambioEl = document.getElementById('cambio');
const tazaEl = document.getElementById('taza');
const errorMessageEl = document.getElementById('error-message');
const errorTextEl = document.getElementById('error-text');

// Función para obtener tasa y calcular usando Fetch API
function calculate() {
  const moneda_origin = monedaEl_one.value;
  const moneda_destiny = monedaEl_two.value;
  const cantidad = cantidadEl_one.value;

  // Mostrar estado de carga
  cambioEl.innerText = 'Cargando...';
  errorMessageEl.style.display = 'none';

  // Crear FormData para enviar datos POST
  const formData = new FormData();
  formData.append('moneda_origen', moneda_origin);
  formData.append('moneda_destino', moneda_destiny);
  formData.append('cantidad', cantidad);

  // Realizar solicitud POST al backend
  fetch('backend.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Error en la respuesta del servidor: ' + response.status);
    }
    return response.json();
  })
  .then(data => {
    if (data.exito) {
      // Mostrar la tasa de cambio
      cambioEl.innerText = `1 ${data.moneda_origen} = ${data.tasa} ${data.moneda_destino}`;
      cambioEl.classList.add('texto-exito');
      
      // Mostrar el monto convertido
      cantidadEl_two.value = data.monto_convertido;
      
      // Ocultar mensaje de error si existe
      errorMessageEl.style.display = 'none';
    } else {
      // Mostrar error
      mostrarError(data.mensaje);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    mostrarError('Error al obtener las tasas de cambio. Intenta nuevamente.');
  });
}

// Función para mostrar mensajes de error
function mostrarError(mensaje) {
  cambioEl.innerText = 'Error';
  cambioEl.classList.add('texto-error');
  errorTextEl.innerText = mensaje;
  errorMessageEl.style.display = 'block';
}

// Evento para cerrar el mensaje de error
document.querySelector('#error-message .delete').addEventListener('click', function() {
  errorMessageEl.style.display = 'none';
});

// Eventos
monedaEl_one.addEventListener('change', calculate);
cantidadEl_one.addEventListener('input', calculate);
monedaEl_two.addEventListener('change', calculate);

tazaEl.addEventListener('click', () => {
  const temp = monedaEl_one.value;
  monedaEl_one.value = monedaEl_two.value;
  monedaEl_two.value = temp;
  calculate();
});

// Calcular al cargar la página
calculate();