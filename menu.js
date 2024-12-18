function agregarAlCarrito(id, precio, cantidad, nota) {
    console.log("Datos enviados al servidor:", {
        producto_id: id,
        cantidad: cantidad,
        precio_unitario: precio,
        nota: nota || null // Muestra lo que se enviará como nota
    });

    fetch('agregar_carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            producto_id: id,
            cantidad: cantidad,
            precio_unitario: precio,
            nota: nota || null // Asegúrate de enviar "null" si la nota está vacía
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta del servidor:", data);
        if (data.success) {
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}






function cargarCarrito() {
    fetch('obtener_carrito.php')
        .then(response => response.json())
        .then(data => {
            const listaCarrito = document.getElementById('lista-carrito');
            listaCarrito.innerHTML = '';
            let total = 0;

            data.forEach(item => {
                const li = document.createElement('li');
                const totalPorItem = item.precio_unitario * item.cantidad;

                // Crear contenido con la nota si está disponible
                const nota = item.nota ? ` (Nota: ${item.nota})` : ' (Sin nota)';
                li.textContent = `${item.nombre} x${item.cantidad} - $${totalPorItem} MXN${nota}`;

                // Botón para eliminar
                const btnEliminar = document.createElement('button');
                btnEliminar.textContent = 'Eliminar';
                btnEliminar.onclick = function () {
                    eliminarDelCarrito(item.id);
                };
                li.appendChild(btnEliminar);

                listaCarrito.appendChild(li);

                total += totalPorItem;
            });

            document.getElementById('total').textContent = `Total: $${total} MXN`;
        })
        .catch(error => console.error('Error:', error));
}


function filterProducts() {
    const searchBox = document.getElementById('searchBox').value.toLowerCase();
    const products = document.querySelectorAll('.product-card');

    products.forEach(product => {
        const productName = product.getAttribute('data-name');
        if (productName.includes(searchBox)) {
            product.style.display = 'block'; // Muestra la tarjeta si coincide
        } else {
            product.style.display = 'none'; // Oculta la tarjeta si no coincide
        }
    });
}
