<?php
session_start();              // Inicia a sessão
session_unset();              // Remove todas as variáveis de sessão
session_destroy();            // Destrói a sessão

// Opcional: Redireciona para a página de login ou home
header("Location: login.php");
exit;
?>
