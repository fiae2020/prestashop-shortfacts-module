<?php
/**
 * Security Index File
 * This file should be saved as: index.php (and in all subdirectories)
 *
 * @author    Dzemal Imamovic
 * @copyright 2025 Dzemal Imamovic
 * @license   MIT
 */
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

header('Location: ../');
exit;
