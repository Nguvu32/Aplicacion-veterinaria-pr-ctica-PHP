<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 'user') {
    header("Location: ../html/login.html"); 
    exit();
}

$tiempoActual = time();
$limiteInactivo = 60*5;
$ultimoClick = 0;
if (isset($_COOKIE['userUltimoClick'])) {
    $ultimoClick = (int)$_COOKIE['userUltimoClick'];
    if (($tiempoActual - $ultimoClick) > $limiteInactivo) {
        $_SESSION = array();
        session_destroy();
        setcookie('userUltimoClick', '', time() - 1);
        header('Location: ../html/login.html?mensaje=Sesión cerrada por inactividad de seguridad.');
        exit();
    }else{
        
    }
}
setcookie('userUltimoClick', $tiempoActual);
$recordatorio = 0; 
$imagenPerfil = !empty($_SESSION['imagen_perfil']) ? $_SESSION['imagen_perfil'] : '../imagenes/avatar_defecto.png';
$validacionEmail = (bool)($_SESSION['validacion_email']);
if(isset($_COOKIE['userUltimoClick']) || !$validacionEmail){
    if (($tiempoActual - $ultimoClick > $recordatorio && !$validacionEmail)) {
        setcookie('recordatorio', 'true', $tiempoActual + 3600); 
    }
}




?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wildvet | Veterinaria Nutricionista</title>
    <link href="https://fonts.googleapis.com/css2?family=Livvic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>

        #validation-banner {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ffd400; 
            color: var(--color-principal-morado);
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: none; 
            max-width: 300px;
            text-align: center;
        }
        #validation-banner p {
            margin: 0 0 10px 0;
            font-weight: bold;
            line-height: 1.3;
        }
        #validation-banner a {
            background-color: var(--color-principal-morado);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div id="validation-banner">
        <p>¡Atención! Tu correo no está validado. ⚠️</p>
        <p>Valida tu email para poder hacer pedidos online.</p>
        <a href="perfilUsuario.php">Validar Mi Cuenta Ahora</a>
    </div>

    <header id="main-header">
        <div id="logo-container">
            <h2>Wildvet</h2> 
        </div>
        <nav>
            <a href="usuario.php#asesorias">Asesorías</a>
            <a href="usuario.php#planes-salud">Planes de Salud</a>
            <a href="usuario.php#formaciones">Formaciones</a>
            <a href="usuario.php#contacto">Contacto</a>
            <a href="perfilUsuario.php">
                Perfil
            </a>
            <a href="cerrarSesion.php" class="cta-button" style="padding: 8px 15px; margin-left: 20px; background-color: #ffd400; color: var(--color-principal-morado); font-size: 1em;">Cerrar Sesión</a>
        </nav>
    </header>

    <section id="hero" class="section-container">
        <h1>Veterinaria Nutricionista</h1>
        <p>Expertos en Wildvet ofrecen alimentación natural individualizada y nutriterapia para la salud y el bienestar de perros y gatos.</p>
        <a href="#asesorias" class="cta-button morado">Ver Todos los Servicios</a>
    </section>

    <section id="asesorias" class="section-container">
        <h2>Asesorías Nutricionales</h2>
        <p>Con este servicio podrás darle a tu perro o gato una alimentación individualizada que se ajuste a sus necesidades específicas. Adaptamos la dieta a tus hábitos para que puedas seguirla de forma independiente con seguridad.</p>
        
        <div class="services-grid">
            
            <div class="card">
                <img src="../imagenes/mauiCaja.jpg" alt="Consulta para Animales Sanos">
                <h3>Consulta Nutricional: Animales Sanos</h3>
                <p>Perfecta para iniciar la transición a la alimentación natural.</p>
                <ul>
                    <li>Videollamada de 45 min</li>
                    <li>Dieta personalizada</li>
                    <li>No incluye seguimiento</li>
                </ul>
                <div class="price">
                    <span style="font-size: 2em; color: var(--color-secundario-verde); font-weight: bold;">50€</span>
                </div>
                <a href="#contacto" class="cta-button morado">Solicitar Cita</a>
            </div>

            <div class="card">
                <img src="../imagenes/mauiComiendo.jpg" alt="Consulta para Alergias o Dermatitis">
                <h3>Alergias / Dermatitis</h3>
                <p>Dieta terapéutica para gestionar problemas comunes de la piel.</p>
                <ul>
                    <li>Videollamada 1-1.5 horas</li>
                    <li>Dieta personalizada</li>
                    <li>Seguimiento: 2 meses</li>
                </ul>
                <div class="price">
                    <span style="font-size: 2em; color: var(--color-secundario-verde); font-weight: bold;">120€</span>
                </div>
                <a href="#contacto" class="cta-button morado">Solicitar Cita</a>
            </div>

            <div class="card">
                <img src="../imagenes/isaVet.jpg" alt="Consulta para Otras Enfermedades">
                <h3>Otras Enfermedades</h3>
                <p>Alimentación individualizada para el manejo de diversas patologías.</p>
                <ul>
                    <li>Videollamada de 45-60 min</li>
                    <li>Dieta personalizada</li>
                    <li>Seguimiento: 2 meses</li>
                </ul>
                <div class="price">
                    <span style="font-size: 2em; color: var(--color-secundario-verde); font-weight: bold;">100€</span>
                </div>
                <a href="#contacto" class="cta-button morado">Solicitar Cita</a>
            </div>
            
        </div>
        
        <div style="margin-top: 30px; padding: 20px; border: 1px dashed #d2b1ea;">
            <p style="font-size: 0.9em; font-style: italic; color: var(--color-principal-morado);">
                **Revisiones Adicionales:** 30€. Para ampliar los seguimientos, se pueden contratar revisiones aparte. En caso de requerir una nueva dieta se cobrará como una consulta para animales sanos.
            </p>
        </div>
    </section>

    <section id="planes-salud" class="section-container">
        <h2 style="color: var(--color-principal-morado);">Planes de Salud</h2>
        <p>Servicio específico para un problema concreto. Buscaremos resultados satisfactorios con ayuda de la nutriterapia y el compromiso del tutor.</p>
        
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
            
            <div class="card">
                <img src="../imagenes/kyraLoca.jpg" alt="Plan Adiós Diarreas">
                <h3>Adiós Diarreas</h3>
                <p>Plan diseñado para resolver problemas digestivos recurrentes.</p>
                <ul>
                    <li>Videollamada 1h</li>
                    <li>Dieta de descarte</li>
                    <li>Seguimiento: 2 meses</li>
                </ul>
                <div class="price">
                    <span style="font-size: 2em; color: var(--color-secundario-verde); font-weight: bold;">100€</span>
                </div>
                <a href="#contacto" class="cta-button">Solicitar Cita</a>
            </div>

            <div class="card">
                <img src="../imagenes/kenaLoca.jpg" alt="Plan Pérdida de Peso">
                <h3>Pérdida de Peso</h3>
                <p>Dieta específica para lograr una figura saludable.</p>
                <ul>
                    <li>Dieta destinada a la pérdida de peso</li>
                    <li>Seguimiento: 2 meses</li>
                </ul>
                <div class="price">
                    <span style="font-size: 2em; color: var(--color-secundario-verde); font-weight: bold;">80€</span>
                </div>
                <a href="#contacto" class="cta-button">Solicitar Cita</a>
            </div>
            
        </div>
    </section>

    <section id="formaciones" class="section-container">
        <h2>Formaciones Online</h2>
        <p>Aquí encontrarás distintas formaciones muy útiles para iniciarte en el mundo de la alimentación natural para perros y gatos. Recibirás un certificado de asistencia.</p>
        
        <div class="services-grid">
            
            <div class="card">
                <img src="../imagenes/mauiComiendo.jpg" alt="Masterclass Dieta BARF Perros">
                <h3>Masterclass: Iniciación a dieta BARF (Perro Sano)</h3>
                <p>Todo lo que necesitas saber para elaborar menús BARF para tu perro sano.</p>
                <div class="price">
                    <span style="font-size: 1.8em; color: var(--color-principal-morado); font-weight: bold;">30€</span>
                </div>
                <a href="#" class="cta-button morado">Comprar Curso</a>
            </div>

            <div class="card">
                <img src="../imagenes/isaVet.jpg" alt="Masterclass Dieta BARF Gatos">
                <h3>Masterclass: Iniciación a dieta BARF (Gato Sano)</h3>
                <p>Todo lo que necesitas saber para elaborar menús BARF para tu gato sano.</p>
                <div class="price">
                    <span style="font-size: 1.8em; color: var(--color-principal-morado); font-weight: bold;">30€</span>
                </div>
                <a href="#" class="cta-button morado">Comprar Curso</a>
            </div>

            <div class="card">
                <img src="../imagenes/isaVet.jpg" alt="Masterclass Dieta Cocinada Perros">
                <h3>Masterclass: Iniciación a dieta Cocinada (Perro Sano)</h3>
                <p>Todo lo que necesitas saber para elaborar una dieta cocinada para tu perro sano.</p>
                <div class="price">
                    <span style="font-size: 1.8em; color: var(--color-principal-morado); font-weight: bold;">30€</span>
                </div>
                <a href="#" class="cta-button morado">Comprar Curso</a>
            </div>
            
             <div class="card">
                <img src="../imagenes/isaVet.jpg" alt="Masterclass Dieta Cocinada Gatos">
                <h3>Masterclass: Iniciación a dieta Cocinada (Gato Sano)</h3>
                <p>Todo lo que necesitas saber para elaborar una dieta cocinada para tu gato sano.</p>
                <div class="price">
                    <span style="font-size: 1.8em; color: var(--color-principal-morado); font-weight: bold;">30€</span>
                </div>
                <a href="#" class="cta-button morado">Comprar Curso</a>
            </div>
            
        </div>
    </section>
    <section id="ventaOnline" class="section-container">
    <h2>Productos de venta Online recomendados por mi:</h2>
    <p>Aquí te dejo diferentes productos para que puedas elegir lo que más necesites y mejor te venga, te dejo los precios y puedes añadirlos al carrito para comprarlos.</p>
    
    <div class="services-grid">
        
        <div class="card">
            <img src="../imagenes/packVikingas.png" alt="Pack Esencias Vikingas - Premios Naturales">
            <span class="category-tag" style="font-size: 0.9em; color: gray; margin-top: 10px; display: block;">PACKS</span>
            <h3>Pack Esencias Vikingas</h3>
            <p>Galletas naturales y premios masticables variados para perros.</p>
            <div class="price">
                <span style="font-size: 1.8em; color: var(--color-principal-morado); font-weight: bold;">23,90€</span>
            </div>
            <a href="#" class="cta-button morado">Comprar Pack</a>
        </div>

        <div class="card">
            <img src="../imagenes/caldero.png" alt="El Caldero de Orelletes - Galletas Artesanales">
            <span class="category-tag" style="font-size: 0.9em; color: gray; margin-top: 10px; display: block;">PACKS</span>
            <h3>El Caldero de Orelletes</h3>
            <p>Surtido de galletas artesanales con formas variadas y divertidas para perros.</p>
            <div class="price">
                <span style="font-size: 1.8em; color: var(--color-principal-morado); font-weight: bold;">17,85€</span>
            </div>
            <a href="#" class="cta-button morado">Comprar Pack</a>
        </div>
        
    </div>
</section>

    <section id="empresas">
        <div class="section-container">
            <h2>Formulación para Empresas de Alimentación Animal Natural</h2>
            <p>Ofrecemos servicios de formulación experta y consultoría nutricional para que tu marca de alimentos naturales cumpla con los más altos estándares de calidad y balance.</p>
            <a href="#contacto" class="cta-button" style="background-color: #ffd400; color: var(--color-principal-morado);">Contactar Consultoría</a>
        </div>
    </section>
    
    <section id="contacto" class="section-container">
        <h2>Contacto</h2>
        <p>Estamos listos para ayudarte a mejorar la vida de tus mascotas o a impulsar tu negocio.</p>
        
        <div class="form-container">
            <form action="formularioMensaje.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="motivo">Motivo de Contacto:</label>
                    <select id="motivo" name="motivo" required>
                        <option value="" disabled selected>Selecciona un servicio</option>
                        <option value="asesoria">Asesoría y planes de salud</option>
                        <option value="formacion">Formaciones</option>
                        <option value="empresa">Consultoría para Empresas (Formulación)</option>
                        <option value="otro">Otro Motivo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
                </div>

                <button type="submit" class="cta-button morado">Enviar Mensaje</button>
            </form>
        </div>
    </section>

    <footer>
        <p>© 2025 Wildvet. Todos los derechos reservados.</p>
        <div class="social-links">
            <a href="https://www.linkedin.com/in/isabel-calzado-s%C3%a1nchez-isawildvet/" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
            <a href="https://www.instagram.com/isa_wildvet/" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://wa.me/644436552" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
    </footer>

    <script>
        // Efecto en el Encabezado al hacer Scroll (Original)
        const header = document.getElementById('main-header');
        const moradoPrincipal = '#8e3ccb'; 
        const verdeSecundario = '#3ca485';

        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.style.backgroundColor = verdeSecundario; 
            } else {
                header.style.backgroundColor = moradoPrincipal;
            }
        });

        // --- LÓGICA NUEVA DE TEMPORIZADOR Y COOKIE PARA BANNER DE VALIDACIÓN ---
        
        // El PHP ya determinó si debe mostrarse el prompt
        const SHOW_PROMPT = <?php if($_COOKIE["recordatorio"] == false || $validacionEmail == false){
            echo "true";
        } else{
            echo "false";
        }?>;
        const banner = document.getElementById('validation-banner');
        function checkValidationTimer() {
            if (SHOW_PROMPT) {
                banner.style.display = 'block';
            }
        }

        window.addEventListener('load', checkValidationTimer);
        // --- FIN LÓGICA DE TEMPORIZADOR Y COOKIE ---
    </script>
</body>
</html>