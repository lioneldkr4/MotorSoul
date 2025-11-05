// validaciones.js adaptado para CRUD de usuarios

document.addEventListener("DOMContentLoaded", () => {
  const formRegistro = document.getElementById("formRegistro");
  const tablaUsuarios = document.getElementById("tabla-usuarios");

  // -------- CARGAR USUARIOS EN TABLA --------
  if (tablaUsuarios) {
    const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];
    const tbody = tablaUsuarios.querySelector("tbody");
    tbody.innerHTML = "";

    usuarios.forEach((usuario, index) => {
      const fila = document.createElement("tr");
      fila.innerHTML = `
        <td>${index + 1}</td>
        <td>${usuario.nombre} ${usuario.apellidos}</td>
        <td>${usuario.edad}</td>
        <td>${usuario.correo}</td>
        <td class="acciones"></td>
      `;

      const btnEditar = document.createElement("button");
      btnEditar.className = "btn btn-sm btn-warning me-2";
      btnEditar.innerText = "Editar";
      btnEditar.onclick = () => {
        sessionStorage.setItem("correoUsuario", usuario.correo);
        location.href = "registrousuario.html";
      };

      const btnEliminar = document.createElement("button");
      btnEliminar.className = "btn btn-sm btn-danger";
      btnEliminar.innerText = "Eliminar";
      btnEliminar.onclick = () => {
        mostrarModal(`¿Eliminar al usuario ${usuario.nombre} ${usuario.apellidos}?`, "danger", () => {
          usuarios.splice(index, 1);
          localStorage.setItem("usuarios", JSON.stringify(usuarios));
          location.reload();
        });
      };

      fila.querySelector(".acciones").append(btnEditar, btnEliminar);
      tbody.appendChild(fila);
    });
  }

  // -------- REGISTRO DE USUARIOS --------
  if (formRegistro) {
    const correoClave = sessionStorage.getItem("correoUsuario");
    let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

    if (correoClave) {
      const usuario = usuarios.find(u => u.correo === correoClave);
      if (usuario) {
        document.getElementById("nombre").value = usuario.nombre;
        document.getElementById("apellidos").value = usuario.apellidos;
        document.getElementById("edad").value = usuario.edad;
        document.getElementById("correo").value = usuario.correo;
        document.getElementById("contrasenia").value = usuario.contrasenia;
        document.getElementById("confirmar").value = usuario.contrasenia;
        document.getElementById("terminos_condiciones").checked = true;
      }
    }

    formRegistro.addEventListener("submit", function (event) {
      event.preventDefault();

      const nombre = document.getElementById("nombre").value.trim();
      const apellidos = document.getElementById("apellidos").value.trim();
      const edad = parseInt(document.getElementById("edad").value);
      const correo = document.getElementById("correo").value.trim();
      const contrasenia = document.getElementById("contrasenia").value;
      const confirmar = document.getElementById("confirmar").value;
      const terminos = document.getElementById("terminos_condiciones").checked;

      const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;

      if (!nombre || nombre.length < 3 || nombre.length > 50)
        return mostrarModal("El nombre debe tener entre 3 y 50 caracteres.", "danger");
      if (!apellidos || apellidos.length < 3 || apellidos.length > 50)
        return mostrarModal("Apellido(s) debe(n) tener entre 3 y 50 caracteres.", "danger");
      if (!edad || isNaN(edad) || edad < 18)
        return mostrarModal("La edad mínima es 18", "danger");
      if (!emailRegex.test(correo))
        return mostrarModal("El correo no es válido", "danger");
      if (!contrasenia || contrasenia.length < 6)
        return mostrarModal("La contraseña requiere al menos 6 caracteres", "danger");
      if (contrasenia !== confirmar)
        return mostrarModal("Las contraseñas no coinciden", "danger");
      if (!terminos)
        return mostrarModal("Debes aceptar los términos y condiciones", "danger");

      const nuevoUsuario = { nombre, apellidos, edad, correo, contrasenia };

      if (correoClave) {
        const index = usuarios.findIndex(u => u.correo === correoClave);
        if (index !== -1) usuarios[index] = nuevoUsuario;
        sessionStorage.removeItem("correoUsuario");
      } else {
        const duplicado = usuarios.some(u => u.correo === correo);
        if (duplicado) return mostrarModal("Ya existe un usuario con este correo", "warning");
        usuarios.push(nuevoUsuario);
      }

      localStorage.setItem("usuarios", JSON.stringify(usuarios));
      formRegistro.reset(); // limpia el formulario
      mostrarModal("Usuario registrado exitosamente.", "success");
    });
  }
});



// Validación del formulario de inicio de sesión
const formLogin = document.getElementById("formLogin");

if (formLogin) {
  formLogin.addEventListener("submit", function (event) {
    event.preventDefault();

    const correoLogin = document.getElementById("correo").value.trim();
    const contraseniaLogin = document.getElementById("contrasenia").value;

    const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    const passwordRegex = /^.{6,}$/;

    if (!emailRegex.test(correoLogin)) {
      mostrarModal("Ingresa un correo electrónico válido.", "danger");
      return false;
    }

    if (!passwordRegex.test(contraseniaLogin)) {
      mostrarModal("La contraseña debe tener al menos 6 caracteres.", "danger");
      return false;
    }

    this.submit();
  });
}

// Validación del formulario de inicio de sesión de administrador
const formLoginAdmon = document.getElementById("formLoginAdmon");

if (formLoginAdmon) {
  formLoginAdmon.addEventListener("submit", function (event) {
    event.preventDefault();

    const usuarioLoginAdmon = document.getElementById("usuario").value;
    const contraseniaLoginAdmon = document.getElementById("contrasenia").value;

    const passwordRegex = /^.{6,}$/;

    if (!usuarioLoginAdmon) {
      mostrarModal("Ingresar usuario", "danger");
      return false;
    }

    if (!passwordRegex.test(contraseniaLoginAdmon)) {
      mostrarModal("La contraseña debe tener al menos 6 caracteres.", "danger");
      return false;
    }

    this.submit();
  });
}


// validaciones.js limpio, funcional y conectado al CRUD de motocicletas

document.addEventListener("DOMContentLoaded", () => {
  const formRegistroMoto = document.getElementById("formRegistroMoto");
  const tablaMotos = document.getElementById("tabla-motos");

  // --------- RENDERIZAR TABLA DE MOTOS ---------
  if (tablaMotos) {
    const motos = JSON.parse(localStorage.getItem("motos")) || [];

    const transmisionTexto = {
      "1": "Manual",
      "2": "Semiautomática",
      "3": "Automática"
    };

    const tipoTexto = {
      "1": "Deportiva",
      "2": "Trabajo",
      "3": "Doble Propósito",
      "4": "Motoneta",
      "5": "Cuatrimoto"
    };

    const tbody = tablaMotos.querySelector("tbody");
    tbody.innerHTML = "";

    motos.forEach((moto, index) => {
      const fila = document.createElement("tr");
      fila.innerHTML = `
        <td>${moto.marca}</td>
        <td>${moto.modelo}</td>
        <td>${moto.anio}</td>
        <td>$${Number(moto.precio).toLocaleString("en-US", { minimumFractionDigits: 2 })}</td>
        <td>${moto.hp}</td>
        <td>${transmisionTexto[moto.transmision] || "-"}</td>
        <td>${tipoTexto[moto.tipo] || "-"}</td>
        <td>${moto.color}</td>
        <td>${moto.existencia}</td>
        <td class="acciones"></td>
      `;

      const btnEditar = document.createElement("button");
      btnEditar.className = "btn btn-sm btn-warning me-2";
      btnEditar.innerText = "Editar";
      btnEditar.onclick = () => {
        sessionStorage.setItem("marcaModelo", moto.marca + "|" + moto.modelo);
        location.href = "registromoto.html";
      };

      const btnEliminar = document.createElement("button");
      btnEliminar.className = "btn btn-sm btn-danger";
      btnEliminar.innerText = "Eliminar";
      btnEliminar.onclick = () => {
        mostrarModal(`¿Eliminar la motocicleta ${moto.marca} ${moto.modelo}?`, "danger", () => {
          motos.splice(index, 1);
          localStorage.setItem("motos", JSON.stringify(motos));
          location.reload();
        });
      };

      fila.querySelector(".acciones").append(btnEditar, btnEliminar);
      tbody.appendChild(fila);
    });
  }

  // --------- FORMULARIO DE REGISTRO Y EDICIÓN ---------
  if (formRegistroMoto) {
    const clave = sessionStorage.getItem("marcaModelo");
    let motos = JSON.parse(localStorage.getItem("motos")) || [];

    if (clave) {
      const [marcaBuscada, modeloBuscado] = clave.split("|");
      const moto = motos.find(m => m.marca === marcaBuscada && m.modelo === modeloBuscado);
      if (moto) {
        document.getElementById("marca").value = moto.marca;
        document.getElementById("modelo").value = moto.modelo;
        document.getElementById("anio").value = moto.anio;
        document.getElementById("precio").value = moto.precio;
        document.getElementById("caballos").value = moto.hp;
        document.getElementById("transmision").value = moto.transmision;
        document.getElementById("tipo").value = moto.tipo;
        document.getElementById("color").value = moto.color;
        document.getElementById("existencia").value = moto.existencia;
        document.getElementById("preview").src = moto.imagen || "";
      }
    }

    document.getElementById("imagen").addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById("preview").src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });

    formRegistroMoto.addEventListener("submit", function (event) {
      event.preventDefault();

      const marca = document.getElementById("marca").value.trim();
      const modelo = document.getElementById("modelo").value.trim();
      const anio = parseInt(document.getElementById("anio").value);
      const precio = parseFloat(document.getElementById("precio").value);
      const hp = parseInt(document.getElementById("caballos").value);
      const transmision = document.getElementById("transmision").value;
      const tipo = document.getElementById("tipo").value;
      const color = document.getElementById("color").value.trim();
      const existencia = parseInt(document.getElementById("existencia").value);
      const imagen = document.getElementById("preview").src;

      if (!marca || !modelo || !anio || !precio || !hp || !transmision || !tipo || !color || !existencia)
        return mostrarModal("Todos los campos son obligatorios", "danger");

      const nuevaMoto = { marca, modelo, anio, precio, hp, transmision, tipo, color, existencia, imagen };

      if (clave) {
        const [marcaBuscada, modeloBuscado] = clave.split("|");
        const index = motos.findIndex(m => m.marca === marcaBuscada && m.modelo === modeloBuscado);
        if (index !== -1) motos[index] = nuevaMoto;
        sessionStorage.removeItem("marcaModelo");
      } else {
        const duplicado = motos.some(m => m.marca === marca && m.modelo === modelo);
        if (duplicado) return mostrarModal("Ya existe una motocicleta con esa marca y modelo", "warning");
        motos.push(nuevaMoto);
      }

      localStorage.setItem("motos", JSON.stringify(motos));
      formRegistroMoto.reset(); // limpia el formulario
      mostrarModal("Motocicleta registrada exitosamente.", "success");    });
  }
});


function mostrarModal(mensaje, color = "primary", callback = null) {
  const modal = new bootstrap.Modal('#mdlMensaje', { backdrop: 'static', keyboard: false });
  document.querySelector("#mdlMensaje .modal-body p").innerText = mensaje;
  document.querySelector("#mdlMensaje .modal-header").className = "modal-header bg-" + color;
  
  const btn = document.querySelector("#mdlMensaje .btn-primary");
  const nuevoBoton = btn.cloneNode(true);
  btn.parentNode.replaceChild(nuevoBoton, btn);

  nuevoBoton.style.display = "inline-block";
  nuevoBoton.onclick = null;

  if (callback) {
    nuevoBoton.addEventListener("click", callback);
  }

  modal.show();
}

