<?php

/**
 * Funciones auxiliares para autenticación
 * Incluye helper para iniciar sesión de forma segura
 */

/**
 * Inicia sesión de forma segura (evita warnings de sesión ya activa)
 */
function iniciarSesionSegura()
{
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
}

/**
 * Verificar si el usuario está autenticado
 */
function estaAutenticado()
{
  iniciarSesionSegura();
  return isset($_SESSION['usuarioId']);
}

/**
 * Verificar si el usuario es admin
 */
function esAdmin()
{
  iniciarSesionSegura();
  return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

/**
 * Obtener datos del usuario actual
 */
function obtenerUsuarioActual()
{
  iniciarSesionSegura();
  if (!estaAutenticado()) {
    return null;
  }

  return [
    'id' => $_SESSION['usuarioId'] ?? null,
    'nombre' => $_SESSION['nombre'] ?? '',
    'apellido' => $_SESSION['apellido'] ?? '',
    'rol' => $_SESSION['rol'] ?? 'cliente',
    'personalId' => $_SESSION['personal_id'] ?? null,
    'clienteId' => $_SESSION['cliente_id'] ?? null
  ];
}

/**
 * Redirigir si no está autenticado
 */
function requiereAutenticacion($redireccionUrl = 'login.php')
{
  if (!estaAutenticado()) {
    header("Location: $redireccionUrl");
    exit;
  }
}

/**
 * Redirigir si no es admin
 */
function requiereAdmin($redireccionUrl = 'index.php')
{
  requiereAutenticacion();
  if (!esAdmin()) {
    header("Location: $redireccionUrl");
    exit;
  }
}
