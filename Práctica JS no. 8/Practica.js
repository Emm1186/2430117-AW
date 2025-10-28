// Función para crear el formulario
function crearFormulario() {
    // Tomar el número de materias
    var materias = document.getElementById("numMaterias").value;
    
    // Crear el formulario
    var texto = "";
    
    // Hacer los campos para cada materia
    for(var i = 1; i <= materias; i++) {
        texto = texto + "<h3>Materia " + i + "</h3>";
        texto = texto + "Unidad 1: <input type='number' id='u1m" + i + "'><br>";
        texto = texto + "Unidad 2: <input type='number' id='u2m" + i + "'><br>";
        texto = texto + "Unidad 3: <input type='number' id='u3m" + i + "'><br>";
        texto = texto + "Unidad 4: <input type='number' id='u4m" + i + "'><br><br>";
    }
    
    // Agregar el botón
    texto = texto + "<button onclick='calcularPromedios()'>Ver Resultados</button>";
    
    // Poner todo en la página
    document.getElementById("formularioMaterias").innerHTML = texto;
}

// Función para calcular promedios
function calcularPromedios() {
    // Tomar el número de materias
    var materias = document.getElementById("numMaterias").value;
    
    // Preparar donde mostraremos resultados
    var texto = "<h3>Resultados:</h3>";
    
    // Revisar cada materia
    for(var i = 1; i <= materias; i++) {
        // Tomar calificaciones
        var cal1 = Number(document.getElementById("u1m" + i).value);
        var cal2 = Number(document.getElementById("u2m" + i).value);
        var cal3 = Number(document.getElementById("u3m" + i).value);
        var cal4 = Number(document.getElementById("u4m" + i).value);
        
        // Ver si pasó o no
        var promedio = 0;
        var resultado = "";
        
        // Si alguna calificación es menor a 70
        if(cal1 < 70) {
            promedio = 60;
            resultado = "No aprobado";
        } else if(cal2 < 70) {
            promedio = 60;
            resultado = "No aprobado";
        } else if(cal3 < 70) {
            promedio = 60;
            resultado = "No aprobado";
        } else if(cal4 < 70) {
            promedio = 60;
            resultado = "No aprobado";
        } else {
            // Calcular promedio normal
            promedio = (cal1 + cal2 + cal3 + cal4) / 4;
            if(promedio >= 70) {
                resultado = "Aprobado";
            } else {
                resultado = "No aprobado";
            }
        }
        
        // Agregar los resultados
        texto = texto + "<br>Materia " + i + ":<br>";
        texto = texto + "Promedio: " + promedio + "<br>";
        texto = texto + "Resultado: " + resultado + "<br>";
    }
    
    // Mostrar todos los resultados
    document.getElementById("resultados").innerHTML = texto;
}