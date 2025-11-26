
const monedaUno = document.getElementById('moneda-uno');//dolar
const monedaDos = document.getElementById('moneda-dos');//colones
const cantidadUno = document.getElementById('cantidad-uno');
const cantidadDos = document.getElementById('cantidad-dos');
const cambioEl = document.getElementById('cambio');
const tazaEl = document.getElementById('taza');
const errorMessageEl = document.getElementById('error-message');
const errorTextEl = document.getElementById('error-text');

// bueno la funcion calcular es para hacer la conversion 
function calculate() {
  const moneda_origin = monedaUno.value;
  const moneda_destiny = monedaDos.value;
  const cantidad = cantidadUno.value;

  //muestre en el estado que esta
  cambioEl.innerText = 'Cargando...';
  errorMessageEl.style.display = 'none';

  // se crea el "form data" para enviar datos al backend
  const formData = new FormData();
  formData.append('moneda_origen', moneda_origin);
  formData.append('moneda_destino', moneda_destiny);
  formData.append('cantidad', cantidad);

  // este realiza la peticion fetch
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
      //muestrar la tasa de cambio
      cambioEl.innerText = `1 ${data.moneda_origen} = ${data.tasa} ${data.moneda_destino}`;
      cambioEl.classList.add('texto-exito');
      
      //muestra el monto en el que se convirtio
      cantidadDos.value = data.monto_convertido;
      
      //oculta el error en caso que exista uno
      errorMessageEl.style.display = 'none';
    } else {
      // muestra error si algo sale mal
      mostrarError(data.mensaje);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    mostrarError('Error al obtener las tasas de cambio. Intenta nuevamente.');
  });
}
//esta funcion muestra error 
function mostrarError(mensaje) {
  cambioEl.innerText = 'Error';
  cambioEl.classList.add('texto-error');
  errorTextEl.innerText = mensaje;
  errorMessageEl.style.display = 'block';
}

//evento para cerrar error
document.querySelector('#error-message .delete').addEventListener('click', function() {
  errorMessageEl.style.display = 'none';
});

//eventos
monedaUno.addEventListener('change', calculate);
cantidadUno.addEventListener('input', calculate);
monedaDos.addEventListener('change', calculate);

tazaEl.addEventListener('click', () => {
  const temp = monedaUno.value;
  monedaUno.value = monedaDos.value;
  monedaDos.value = temp;
  calculate();
});
calculate();