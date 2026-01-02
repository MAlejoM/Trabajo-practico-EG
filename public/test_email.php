<?php

/**
 * Test de Configuración de Email
 * Este archivo prueba que PHPMailer esté correctamente configurado
 * 
 * USO: 
 * 1. Abre este archivo en el navegador: http://localhost/public/test_email.php
 * 2. Se enviará un email de prueba a la dirección configurada abajo
 * 3. ELIMINAR ESTE ARCHIVO después de verificar que funciona
 */

// Cambia esto por TU email para recibir el test
$email_prueba = 'tu@email.com';  // ← CAMBIAR AQUÍ

require_once __DIR__ . '/../src/logic/mail.logic.php';

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Email - Veterinaria San Antón</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding: 50px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }

    .test-card {
      max-width: 600px;
      margin: 0 auto;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card test-card shadow-lg">
      <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><i class="fas fa-envelope"></i> Test de Configuración de Email</h3>
      </div>
      <div class="card-body">
        <?php
        // Verificar si PHPMailer está disponible
        if (!PHPMAILER_AVAILABLE) {
          echo '<div class="alert alert-danger">';
          echo '<h5>❌ PHPMailer NO está disponible</h5>';
          echo '<p>Las clases de PHPMailer no se pudieron cargar.</p>';
          echo '<p><strong>Verifica:</strong></p>';
          echo '<ul>';
          echo '<li>Que exista el directorio <code>vendor/PHPMailer/src/</code></li>';
          echo '<li>Que los archivos PHPMailer.php, Exception.php y SMTP.php estén ahí</li>';
          echo '</ul>';
          echo '</div>';
        } else {
          echo '<div class="alert alert-success">';
          echo '<h5>✅ PHPMailer está cargado correctamente</h5>';
          echo '</div>';

          // Mostrar configuración (sin mostrar contraseña)
          echo '<div class="alert alert-info">';
          echo '<h6>📝 Configuración actual:</h6>';
          echo '<ul class="mb-0">';
          echo '<li><strong>SMTP Host:</strong> ' . MAILHOST . '</li>';
          echo '<li><strong>Username:</strong> ' . USERNAME . '</li>';
          echo '<li><strong>Enviar desde:</strong> ' . SEND_FROM . ' (' . SEND_FROM_NAME . ')</li>';
          echo '<li><strong>Email de prueba:</strong> ' . htmlspecialchars($email_prueba) . '</li>';
          echo '</ul>';
          echo '</div>';

          // Solo enviar si el email fue cambiado
          if ($email_prueba === 'tu@email.com') {
            echo '<div class="alert alert-warning">';
            echo '<h5>⚠️ Acción requerida</h5>';
            echo '<p>Por favor edita este archivo y cambia <code>$email_prueba</code> por tu email real.</p>';
            echo '<p>Archivo: <code>public/test_email.php</code> línea 13</p>';
            echo '</div>';
          } else {
            echo '<div class="alert alert-primary">';
            echo '<h5>🚀 Enviando email de prueba...</h5>';
            echo '</div>';

            // Intentar enviar email de prueba
            $resultado = test_email_config($email_prueba);

            if ($resultado['success']) {
              echo '<div class="alert alert-success">';
              echo '<h5>✅ ¡Email enviado exitosamente!</h5>';
              echo '<p>' . htmlspecialchars($resultado['message']) . '</p>';
              echo '<p><strong>Revisa tu bandeja de entrada en:</strong> ' . htmlspecialchars($email_prueba) . '</p>';
              echo '<p class="mb-0"><small>Si no ves el email, revisa la carpeta de spam.</small></p>';
              echo '</div>';

              echo '<div class="alert alert-info">';
              echo '<h6>✨ ¡Sistema de email funcionando!</h6>';
              echo '<p class="mb-0">Ahora puedes probar la recuperación de contraseña completa.</p>';
              echo '</div>';
            } else {
              echo '<div class="alert alert-danger">';
              echo '<h5>❌ Error al enviar email</h5>';
              echo '<p>' . htmlspecialchars($resultado['message']) . '</p>';
              echo '<h6>Posibles causas:</h6>';
              echo '<ul>';
              echo '<li>Contraseña de aplicación de Google incorrecta en <code>config.php</code></li>';
              echo '<li>Email de Google no tiene "Verificación en 2 pasos" activada</li>';
              echo '<li>La contraseña en <code>config.php</code> no es una "Contraseña de aplicación"</li>';
              echo '<li>Firewall bloqueando conexión SMTP</li>';
              echo '</ul>';
              echo '<p><strong>Revisa:</strong> <code>src/config.php</code> y verifica las credenciales</p>';
              echo '</div>';
            }
          }
        }
        ?>

        <hr>

        <div class="d-grid gap-2">
          <a href="login.php" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Ir al Login
          </a>
          <a href="forgot_password.php" class="btn btn-outline-secondary">
            <i class="fas fa-key"></i> Probar Recuperación de Contraseña
          </a>
        </div>

        <div class="alert alert-warning mt-3 mb-0">
          <small><strong>⚠️ IMPORTANTE:</strong> Elimina este archivo (<code>test_email.php</code>) después de verificar que todo funciona.</small>
        </div>
      </div>
    </div>
  </div>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>

</html>