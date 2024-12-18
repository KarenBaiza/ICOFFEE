let tiempoRestante = null;

// Función para obtener el tiempo restante del servidor
function actualizarTiempoRestante() {
    fetch('calcular_tiempo_restante.php') // Asegúrate de que este archivo existe y funciona
        .then(response => response.json())
        .then(data => {
            const tiempoLink = document.getElementById('tiempo-restante');
            if (data.tiempo_restante !== null) {
                tiempoLink.textContent = `Tiempo restante: ${data.tiempo_restante} mins`;
            } else {
                tiempoLink.textContent = 'Pedido listo para recoger';
            }
        })
        .catch(error => console.error('Error al obtener el tiempo restante:', error));
}

// Actualizar cada 30 segundos
setInterval(actualizarTiempoRestante, 30000);
actualizarTiempoRestante();


// Función para reducir el tiempo en la interfaz
function actualizarUI() {
    const navTiempo = document.querySelector('.nav-link');
    if (tiempoRestante !== null && tiempoRestante > 0) {
        navTiempo.innerHTML = `Tiempo restante: ${tiempoRestante} mins`;
        tiempoRestante--; // Reduce 1 minuto en cada iteración
    } else {
        navTiempo.innerHTML = 'Pedido listo para recoger';
    }
}

// Actualiza desde el servidor cada 30 segundos
setInterval(actualizarTiempoRestante, 30000);

// Actualiza localmente cada segundo
setInterval(() => {
    if (tiempoRestante !== null) {
        actualizarUI();
    }
}, 60000); // Cada 1 minuto

// Inicializa la primera actualización
actualizarTiempoRestante();
