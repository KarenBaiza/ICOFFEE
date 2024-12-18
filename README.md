# **ICoffee - Aplicación Web para Cafetería Escolar**

## **Descripción del Proyecto**
**ICoffee** es una aplicación web desarrollada para facilitar la **digitalización de pedidos en la cafetería escolar** del Instituto Tecnológico de Culiacán. La plataforma permite a los estudiantes y personal realizar pedidos de manera eficiente, ya sea para recogerlos en el establecimiento o solicitar entrega a edificios dentro del campus.

---

## **Funcionalidades Principales**
- **Gestor de Pedidos:** Permite a los usuarios realizar pedidos y hacer seguimiento del estado de los mismos.
- **Carrito de Compras:** Posibilidad de agregar y eliminar productos antes de finalizar un pedido.
- **Autenticación de Usuarios:** Inicio de sesión para usuarios registrados.
- **Panel de Administrador:** Gestiona productos, tiempos de entrega y actualización de inventarios.
- **Notificaciones de Estado:** Los usuarios son notificados cuando su pedido está listo para recoger o entregar.

---

## **Tecnologías Utilizadas**
- **Frontend**:
  - **HTML5**: Estructura del proyecto.
  - **CSS3** y **Bootstrap**: Estilizado responsivo y atractivo.
  - **JavaScript**: Funcionalidad interactiva del cliente.

- **Backend**:
  - **PHP**: Lógica de servidor y procesamiento de datos.
  - **MySQL**: Base de datos para almacenar información de productos, usuarios y pedidos.

- **Otros**:
  - **XAMPP**: Servidor local para desarrollo y pruebas.
  - **phpMyAdmin**: Administración de la base de datos.

---

## **Instalación y Configuración**
Sigue los pasos a continuación para ejecutar el proyecto localmente:

1. **Clonar el Repositorio**:
   ```bash
   git clone https://github.com/JosueMa98/ICoffee.git
   ```

2. **Configurar el Entorno Local**:
   - Instala **XAMPP** o cualquier otro servidor local.
   - Asegúrate de iniciar Apache y MySQL.

3. **Importar la Base de Datos**:
   - Abre `phpMyAdmin` en tu servidor local.
   - Importa el archivo `database/icoffee.sql` para crear las tablas necesarias.

4. **Configurar la Conexión a la Base de Datos**:
   - Abre el archivo `conexion.php` y configura tus credenciales:
     ```php
     $host = 'localhost';
     $user = 'root';
     $password = ''; // Contraseña vacía por defecto
     $database = 'icoffee';
     ```

5. **Iniciar la Aplicación**:
   - Guarda los archivos en la carpeta `htdocs` (si usas XAMPP).
   - Accede a `http://localhost/ICoffee` desde tu navegador.

---

## **Estructura del Proyecto**
```bash
ICoffee/
|-- database/
|   |-- icoffee.sql       # Archivo de la base de datos
|-- img/                  # Imágenes de productos
|-- css/
|   |-- styles.css        # Archivo de estilos
|-- js/
|   |-- menu.js           # Lógica de interactividad
|-- php/
|   |-- conexion.php      # Configuración de la base de datos
|-- index.php             # Página principal
|-- carrito.php           # Gestor del carrito de compras
|-- login.php             # Inicio de sesión
|-- registro.php          # Registro de nuevos usuarios
|-- admin/                # Panel de administración
```

---

## **Créditos**
- **Desarrollador**: Maldonado Arana Victor Josue y Baiza Orona Karen Bibiana
- **Institución**: Instituto Tecnológico de Culiacán
- **Contacto**: [Correo electrónico](L20170599@culiacan.tecnm.mx)

---

## **Licencia**
Este proyecto está licenciado bajo la [Licencia Apache 2.0](LICENSE).

---

## **Imágenes**
![image](https://github.com/user-attachments/assets/e2a820f0-250c-4063-886a-3c19e52f1eef)
![image](https://github.com/user-attachments/assets/a231078e-7e32-4086-ba6a-a84cc05d1306)
![image](https://github.com/user-attachments/assets/0b278efa-65c2-4735-8151-916406efd4d0)
![image](https://github.com/user-attachments/assets/4e76f637-9c45-4935-94f7-eceb20040905)







---

Si tienes dudas o sugerencias, no dudes en contactarme. ¡Gracias por visitar el proyecto ICoffee! ☕️
