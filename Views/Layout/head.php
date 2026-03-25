<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- 🧠 TÍTULO DINÁMICO -->
    <title><?php echo $titulo ?? 'Liquour POS'; ?></title>

    <!-- 🔗 CSS DINÁMICO -->
    <?php if(isset($css)): ?>
        <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php endif; ?>

